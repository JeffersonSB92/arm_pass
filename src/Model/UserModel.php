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

    public function createUser(string $name, string $email, string $password, bool $is_admin, bool $is_enabled, int $idaccount)
    {
        $stmt = $this->pdo->prepare('
        INSERT INTO public."user" (name, email, password, is_admin, is_enabled, idaccount)
        VALUES (:name, :email, :password, :is_admin, :is_enabled, :idaccount)
        ');
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':is_admin', $is_admin);
        $stmt->bindParam(':is_enabled', $is_enabled);
        $stmt->bindParam(':idaccount', $idaccount);
        $stmt->execute();
    }

    public function getAllUsers()
    {
        $stmt = $this->pdo->query("SELECT * FROM public.user");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
