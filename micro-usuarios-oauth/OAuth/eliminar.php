<?php
require_once 'config.php';
requireAuth();
requireAdmin();

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Verificar que no se esté eliminando a sí mismo
    $usuario = callAPI('GET', API_USUARIOS_URL . '/' . $id, false, $_SESSION['token']);
    if ($usuario && $usuario['role'] === 'ADMIN') {
        header('Location: index.php?error=No se puede eliminar un usuario administrador');
        exit;
    }
    
    callAPI('DELETE', API_USUARIOS_URL . '/' . $id, false, $_SESSION['token']);
}

header('Location: index.php?success=Usuario eliminado exitosamente');
exit; 