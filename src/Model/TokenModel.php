<?php

namespace App\Model;

use PDO;

class TokenModel
{

    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findToken($token)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM public.token WHERE token_hash = :token");
        $stmt->bindParam(':token', $token, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}