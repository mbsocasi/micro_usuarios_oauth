<?php
require_once 'config.php';
requireAuth();
requireAdmin();

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id = $_GET['id'];
$error = '';
$usuario = callAPI('GET', API_USUARIOS_URL . '/' . $id, false, $_SESSION['token']);

if (!$usuario) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuarioActualizado = [
        'id' => intval($id),
        'nombre' => $_POST['nombre'],
        'apellido' => $_POST['apellido'],
        'email' => $_POST['email'],
        'telefono' => $_POST['telefono'],
        'fechaNacimiento' => $_POST['fechaNacimiento'] . 'T00:00:00.000Z',
        'role' => $_POST['role']
    ];

    if (!empty($_POST['password'])) {
        $usuarioActualizado['password'] = hashPassword($_POST['password']);
    }

    // Mantener los campos que no se actualizan
    $usuarioActualizado['creadoEn'] = $usuario['creadoEn'];

    $result = callAPI('PUT', API_USUARIOS_URL . '/' . $id, $usuarioActualizado, $_SESSION['token']);

    if ($result && isset($result['id'])) {
        header('Location: index.php?success=Usuario actualizado exitosamente');
        exit;
    } else {
        $error = 'Error al actualizar el usuario: ' . ($result['message'] ?? 'Error desconocido');
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">Sistema de Usuarios</a>
            <div class="collapse navbar-collapse justify-content-end">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <span class="nav-link">
                            <i class="fas fa-user"></i> 
                            Rol: <?php echo $_SESSION['role']; ?>
                        </span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Cerrar Sesión</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <div class="card">
                    <div class="card-header">
                        <h4>Editar Usuario</h4>
                    </div>
                    <div class="card-body">
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" 
                                       value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="apellido" class="form-label">Apellido</label>
                                <input type="text" class="form-control" id="apellido" name="apellido" 
                                       value="<?php echo htmlspecialchars($usuario['apellido']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?php echo htmlspecialchars($usuario['email']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Contraseña (dejar en blanco para mantener la actual)</label>
                                <input type="password" class="form-control" id="password" name="password">
                            </div>
                            <div class="mb-3">
                                <label for="telefono" class="form-label">Teléfono</label>
                                <input type="tel" class="form-control" id="telefono" name="telefono" 
                                       value="<?php echo htmlspecialchars($usuario['telefono']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="fechaNacimiento" class="form-label">Fecha de Nacimiento</label>
                                <input type="date" class="form-control" id="fechaNacimiento" name="fechaNacimiento" 
                                       value="<?php echo date('Y-m-d', strtotime($usuario['fechaNacimiento'])); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="role" class="form-label">Rol</label>
                                <select class="form-select" id="role" name="role" required>
                                    <option value="USER" <?php echo $usuario['role'] === 'USER' ? 'selected' : ''; ?>>Usuario</option>
                                    <option value="ADMIN" <?php echo $usuario['role'] === 'ADMIN' ? 'selected' : ''; ?>>Administrador</option>
                                </select>
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Actualizar
                                </button>
                                <a href="index.php" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Cancelar
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 