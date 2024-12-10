<?php

namespace App\Model;

use PDO;

class AccessModel
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAccountByCompanyName(string $company_name)
    {
        $stmt = $this->pdo->prepare("SELECT idaccount FROM public.account WHERE company_name = :company_name");
        $stmt->bindParam(':company_name', $company_name);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getUserByCredential(string $email, string $password, int $idaccount)
    {
        $stmt = $this->pdo->prepare("
            SELECT iduser, name, password, is_enabled
            FROM public.user
            WHERE email = :email AND password = :password AND idaccount = :idaccount
        ");
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':idaccount', $idaccount);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    //TODO pensar em filtrar apenas o token ao invés de * - ver se irá funcionar
    public function findExistingToken(int $iduser)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM token WHERE iduser = :iduser");
        $stmt->bindParam(':iduser', $iduser);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function deleteTokenById(int $idtoken)
    {
        $stmt = $this->pdo->prepare("DELETE FROM token WHERE idtoken = :idtoken");
        $stmt->bindParam(':idtoken', $idtoken);
        $stmt->execute();
    }

    public function insertToken(int $iduser, int $idaccount, string $token_hash, string $expires_at)
    {
        $stmt = $this->pdo->prepare("
        INSERT INTO token (iduser, idaccount, token_hash, created_at, expires_at)
        VALUES (:iduser, :idaccount, :token_hash, NOW(), :expires_at)
        ");
        $stmt->bindParam(':iduser', $iduser);
        $stmt->bindParam(':idaccount', $idaccount);
        $stmt->bindParam(':token_hash', $token_hash);
        $stmt->bindParam(':expires_at', $expires_at);
        $stmt->execute();
    }
}