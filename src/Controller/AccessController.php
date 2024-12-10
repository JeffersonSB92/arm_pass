<?php

namespace App\Controller;

use App\Model\AccessModel;
use App\Helpers\JwtHelper;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AccessController
{
    private $accessModel;

    public function __construct(AccessModel $accessModel)
    {
        $this->accessModel = $accessModel;
    }

    public function login(Request $request, Response $response): Response
    {
        $body = $request->getParsedBody();
        $email = $body['email'] ?? '';
        $password = $body['password'] ?? '';
        $company_name = $body['company_name'] ?? '';

        try {
            if (empty($company_name) || empty($email) || empty($password)) {
                $missingFields = [];
                if (empty($company_name)) $missingFields[] = 'company_name';
                if (empty($email)) $missingFields[] = 'email';
                if (empty($password)) $missingFields[] = 'password';

                $response->getBody()->write(json_encode(['error' => 'Campos Obrigatórios: ' . implode(', ', $missingFields)]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
            }

            $account = $this->accessModel->getAccountByCompanyName($company_name);

            if (!$account) {
                $response->getBody()->write(json_encode(['error' => 'Conta não encontrada']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
            }

            $user = $this->accessModel->getUserByCredential($email, $password, $account['idaccount']);

            if (!$user) {
                $response->getBody()->write(json_encode(['error' => 'Usuário ou senha inválidos']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
            }

            $token = JwtHelper::generateToken($user, $account['idaccount']);

            $existingToken = $this->accessModel->findExistingToken($user['iduser']);
            if ($existingToken) {
                $this->accessModel->deleteTokenById($existingToken['idtoken']);
            }

            $expiresAt = date('Y-m-d H:i:s', time() + 3600);
            $this->accessModel->insertToken($user['iduser'], $account['idaccount'], $token, $expiresAt);

            $response->getBody()->write(json_encode(['token' => $token]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => 'Erro ao processar login: ' . $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    } 
}