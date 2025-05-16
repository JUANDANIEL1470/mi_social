<?php
require_once 'config.php';
$currentUser = getCurrentUser($pdo);
?>
<!DOCTYPE html>
<html lang="es" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Social - <?php echo isset($pageTitle) ? $pageTitle : 'Conecta tus redes'; ?></title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Helvetica+Neue:wght@300;400;500;700;800&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/mi_social/assets/css/style.css">
    <script src="/mi_social/assets/js/ripple.js" defer></script>
</head>
<body>
    <header class="header">
        <div class="container">
            <a href="/mi_social/index.php" class="logo">Mi Social</a>
            
            <!-- En header.php -->
            <nav class="nav">
                <?php if ($currentUser): ?>
                    <div class="user-menu">
                        <img src="/mi_social/<?php echo $currentUser['avatar'] ? $currentUser['avatar'] : 'assets/img/default-avatar.png'; ?>" 
                            alt="Avatar" class="avatar">
                        <span><?php echo htmlspecialchars($currentUser['nombre_perfil'] ?? $currentUser['username']); ?></span>
                        <div class="dropdown">
                            <a href="/mi_social/perfil.php?user=<?php echo $currentUser['username']; ?>">
                                <i class="fas fa-user"></i> Mi Perfil
                            </a>
                            <a href="/mi_social/admin/dashboard.php">
                                <i class="fas fa-cog"></i> Dashboard
                            </a>
                            <a href="/mi_social/admin/logout.php">
                                <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="auth-buttons">
                        <a href="/mi_social/admin/login.php" class="btn btn-outline">Iniciar Sesión</a>
                        <a href="/mi_social/admin/register.php" class="btn btn-primary">Registrarse</a>
                    </div>
                <?php endif; ?>
                <button id="theme-toggle" class="theme-toggle">
                    <i class="fas fa-moon"></i>
                    <i class="fas fa-sun"></i>
                </button>
            </nav>
        </div>
    </header>
    <main class="main-content">