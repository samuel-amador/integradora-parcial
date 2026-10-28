<?php

session_start();

define('NOMBRE_COMPLETO', 'TU NOMBRE COMPLETO');

$servidor = 'localhost';
$usuario = 'root';
$contrasena = '';
$baseDatos = 'pasteleria_espiga';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conexion = new mysqli(
        $servidor,
        $usuario,
        $contrasena,
        $baseDatos
    );

    $conexion->set_charset('utf8mb4');
} catch (mysqli_sql_exception $error) {
    error_log($error->getMessage());

    http_response_code(500);

    exit(
        'No se pudo conectar con la base de datos. ' .
        'Revisa la configuración y que MySQL esté iniciado.'
    );
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function escapar($valor): string
{
    return htmlspecialchars(
        (string) $valor,
        ENT_QUOTES | ENT_SUBSTITUTE,
        'UTF-8'
    );
}