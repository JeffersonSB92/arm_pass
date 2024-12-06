<?php

require_once __DIR__ . '/constants.php';

try {
    $pdo = new PDO(
        "pgsql:host=" . DB_HOSTNAME . ";port=" . DB_PORT . ";dbname=" . DB_DATABASE, DB_USERNAME, DB_PASSWORD
    );

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    die("Erro ao conectar ao banco de dados: " . $e ->getMessage());
}