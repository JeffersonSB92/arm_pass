<?php

namespace App\Controller;

use App\Model\TokenModel;
use App\Model\UserModel;
use DateTime;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class UserController
{
    private $tokenModel;
    private $userModel;

    public function __construct(TokenModel $tokenModel, UserModel $userModel)
    {
        $this->tokenModel = $tokenModel;
        $this->userModel = $userModel;
    }

    public function getAll(Request $request, Response $response): Response
    {
        $tokenHeader = $request->getHeader('Authorization');
        $token = isset($tokenHeader[0]) ? str_replace('Bearer ', '', $tokenHeader[0]) : null;

        if(!$token) {
            $error = ['error' => 'O token é necessário!'];
            $response->getBody()->write(json_encode($error));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }

        $validToken = $this->tokenModel->findToken($token);

        if (!$validToken) {
            $error = ['error' => 'Token não autorizado!'];
            $response->getBody()->write(json_encode($error));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }

        $current_time = new DateTime();
        $expires_at = new DateTime($validToken['expires_at']);
        
        if ($current_time > $expires_at) {
            $error = ['error' => 'Token expirado, faça o login novamente!'];
            $response->getBody()->write(json_encode($error));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }

        try {
            $users = $this->userModel->getAllUsers();
            $response->getBody()->write(json_encode($users));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            $error = ['error' => 'Erro ao buscar usuários: ' . $e->getMessage()];
            $response->getBody()->write(json_encode($error));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function createUser(Request $request, Response $response): Response
    {
        $tokenHeader = $request->getHeader('Authorization');
        $token = isset($tokenHeader[0]) ? str_replace('Bearer ', '', $tokenHeader[0]) : null;

        if(!$token) {
            $error = ['error' => 'O token é necessário!'];
            $response->getBody()->write(json_encode($error));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }

        $validToken = $this->tokenModel->findToken($token);

        if (!$validToken) {
            $error = ['error' => 'Token não autorizado!'];
            $response->getBody()->write(json_encode($error));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }

        $body = $request->getParsedBody();
        $name = $body['name'] ?? '';
        $email = $body['email'] ?? '';
        $password = $body['password'] ?? '';
        $is_admin = $body['is_admin'] ?? '';
        $is_enabled = $body['is_enabled'] ?? '';
        $idaccount = $body['idaccount'] ?? '';

        if (!$name || !$email || !$password || !$is_admin || !$is_enabled || !$idaccount) {
            $error = ['error' => 'Os campos name, email, password e idaccount são obrigatórios.'];
            $response->getBody()->write(json_encode($error));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        try {
            $user = $this->userModel->createUser($name, $email, $password, (bool)$is_admin, (bool)$is_enabled, (int)$idaccount);

            $success = ['message' => 'Usuário criado com sucesso!'];
            $response->getBody()->write(json_encode($success));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
        } catch (\Exception $e) {
            $error = ['error' => 'Erro ao criar usuário: ' . $e->getMessage()];
            $response->getBody()->write(json_encode($error));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }
}