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

    public function findUserByEmailAndAccount(string $email, int $idaccount): ?array
    {
        $query = 'SELECT * FROM public."user" WHERE email = :email AND idaccount = :idaccount';
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':idaccount', $idaccount, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function findUserById(int $iduser): ?array
    {
        $query = 'SELECT iduser, name, email, is_admin, is_enabled, idaccount 
              FROM public."user" 
              WHERE iduser = :iduser';

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':iduser', $iduser, PDO::PARAM_INT);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $user ?: null;
    }


    public function updateUser(array $fields, int $iduser): bool
    {
        // Base da query de atualização
        $query = 'UPDATE public."user" SET ';

        // Inicializa um array para os pares campo = :campo
        $setClauses = [];
        $params = [':iduser' => $iduser];

        // Adiciona os campos fornecidos dinamicamente na query e nos parâmetros
        foreach ($fields as $field => $value) {
            $setClauses[] = "$field = :$field";
            $params[":$field"] = $value;
        }

        // Junta os campos com vírgulas e adiciona na query
        $query .= implode(', ', $setClauses);
        $query .= " WHERE iduser = :iduser";

        // Prepara e executa a query
        $stmt = $this->pdo->prepare($query);

        return $stmt->execute($params);
    }


    public function getAllUsers()
    {
        $stmt = $this->pdo->query("SELECT * FROM public.user");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
