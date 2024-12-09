<?php

namespace App\Helpers;

use Firebase\JWT\JWT;

class JwtHelper
{
    private static string $secretKey = 'testeChaveSecreta123';

    /**
     * @param array $user Informações do usuário
     * @param int $idaccount ID da conta
     * @param int $expiryTime Tempo de expiração em segundos (padrão: 1 hora)
     * @return string Token gerado
     */

     public static function generateToken(array $user, int $idaccount, int $expiryTime = 3600): string
     {
        $payload = [
            'sub' => $user['iduser'],
            'name' => $user['name'],
            'idaccount' => $idaccount,
            'iat' => time(),
            'exp' => time() + $expiryTime,
        ];

        return JWT::encode($payload, self::$secretKey, 'HS256');
     }
}