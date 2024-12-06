<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Slim\Routing\RouteCollectorProxy;
use App\Helpers\JwtHelper;
use Slim\App;

return function (App $app, $pdo)
{
    $app->group('/access', function (RouteCollectorProxy $group) use ($pdo) {

        $group->post('/login', function ($request, $response) use ($pdo) {
            $body = $request->getParsedBody();
            $email = $body['email'] ?? '';
            $password = $body['password'] ?? '';
            $company_name = $body['company_name'] ?? '';
    
            try {
    
                if (empty($company_name)) {
                    $response->getBody()->write(json_encode(['error' => 'O campo company_name é obrigatório']));
                    return $response
                        ->withHeader('Content-Type', 'application/json')
                        ->withStatus(400);
                        
                }
    
                $stmt = $pdo->prepare("SELECT idaccount FROM public.account WHERE company_name = :company_name");
                $stmt->bindParam(':company_name', $company_name);
                $stmt->execute();
                $account = $stmt->fetch(PDO::FETCH_ASSOC);
    
                if (!$account) {
                    return $response
                        ->withHeader('Content-Type', 'application/json')
                        ->write(json_encode(['error' => 'Conta não encontrada']))
                        ->withStatus(404);
                }
    
                $idaccount = $account['idaccount'];
    
                $stmt = $pdo->prepare("
                SELECT iduser, name, password, is_enabled
                FROM public.user
                WHERE email = :email  AND idaccount = :idaccount
                ");
    
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':idaccount', $idaccount);
                $stmt->execute();
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
                $token = JwtHelper::generateToken($user, $idaccount);
    
                $stmt = $pdo->prepare("
                SELECT * FROM token WHERE iduser = :iduser
                ");
                $stmt->bindParam(':iduser', $user['iduser']);
                $stmt->execute();
                $existingToken = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($existingToken) {
        
                    $stmt = $pdo->prepare("DELETE FROM token WHERE idtoken = :idtoken");
                    $stmt->bindParam(':idtoken', $existingToken['idtoken']);
                    $stmt->execute();
                }
    
                $stmt = $pdo->prepare("
                    INSERT INTO token (iduser, idaccount, token_hash, created_at, expires_at)
                    VALUES (:iduser, :idaccount, :token_hash, NOW(), :expires_at)
                ");
    
                $stmt->bindParam(':iduser', $user['iduser']);
                $stmt->bindParam(':idaccount', $idaccount);
                $stmt->bindParam(':token_hash', $token);
                $stmt->bindParam(':expires_at', date('Y-m-d H:i:s', $payload['exp']));
                $stmt->execute();
    
                $response->getBody()->write(json_encode(['token' => $token]));
                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(200);
            } catch (PDOException $e) {
                return $response
                ->withHeader('Content-Type', 'application/json')
                ->write(json_encode(['error' => 'Erro ao processar login: ' . $e->getMessage()]))
                ->withStatus(500);
            }
        });
    });
};