<?php
try {
    $pdo = new PDO("mysql:host=localhost; dbname=salaofy", "root", "");
    $pdo -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    return $pdo; 
} catch (PDOException $e) { 
    error_log("Erro na conexão PDO: " . $e -> getMessage());
    die(false);
} catch (Exception $e) { 
    error_log("Erro genérico na conexão: " . $e -> getMessage());
    die(false);
}