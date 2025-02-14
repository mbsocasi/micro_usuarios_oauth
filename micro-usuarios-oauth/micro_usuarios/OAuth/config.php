<?php
// Configuración de la API
define('API_URL', 'http://localhost:8004/api');
define('API_AUTH_URL', API_URL . '/auth');
define('API_USUARIOS_URL', API_URL . '/usuarios');

// Configuración de sesión
session_start();

// Función para hashear contraseñas
function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT);
}

// Función para realizar peticiones a la API
function callAPI($method, $url, $data = false, $token = null) {
    $curl = curl_init();

    $headers = ['Content-Type: application/json'];
    if ($token) {
        $headers[] = 'Authorization: Bearer ' . $token;
    }

    switch ($method) {
        case "POST":
            curl_setopt($curl, CURLOPT_POST, 1);
            if ($data)
                curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
            break;
        case "PUT":
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
            if ($data)
                curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
            break;
        case "DELETE":
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
            break;
        default:
            if ($data)
                $url = sprintf("%s?%s", $url, http_build_query($data));
    }

    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

    $result = curl_exec($curl);
    
    if (curl_errno($curl)) {
        echo 'Error en curl: ' . curl_error($curl);
    }
    
    curl_close($curl);

    return json_decode($result, true);
}

// Función para verificar si el usuario está autenticado
function isAuthenticated() {
    return isset($_SESSION['token']);
}

// Función para verificar si el usuario es admin
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'ADMIN';
}

// Función para redirigir si no está autenticado
function requireAuth() {
    if (!isAuthenticated()) {
        header('Location: login.php');
        exit;
    }
}

// Función para redirigir si no es admin
function requireAdmin() {
    if (!isAdmin()) {
        header('Location: index.php?error=Acceso denegado. Se requieren permisos de administrador.');
        exit;
    }
} 