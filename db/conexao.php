<?php
try {
    $pdo = new PDO("mysql:host=localhost; dbname=salaofy", "root", "");
    $pdo -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    return $pdo;
} catch (PDOException $e) { echo "Erro na conexão: " . $e -> getMessage(); }
catch (Exception $e) { echo "Erro genérico: " . $e -> getMessage(); }