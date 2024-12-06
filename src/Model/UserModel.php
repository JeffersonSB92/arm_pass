<?php

namespace App\Model;

use PDO;

class UserModel
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAllUsers()
    {
        $stmt = $this->pdo->query("SELECT * FROM public.user");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}