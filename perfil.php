<?php
require_once 'includes/config.php';

$username = $_GET['user'] ?? '';
if (empty($username)) {
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE username = ?");
$stmt->execute([$username]);
$usuario = $stmt->fetch();

if (!$usuario) {
    header('Location: index.php');
    exit;
}

// Obtener enlaces del usuario
$enlaces = $pdo->prepare("SELECT * FROM enlaces WHERE usuario_id = ? ORDER BY orden");
$enlaces->execute([$usuario['id']]);
$enlaces = $enlaces->fetchAll();

// Registrar visita si es un enlace específico
if (isset($_GET['link'])) {
    $enlaceId = (int)$_GET['link'];
    $ip = $_SERVER['REMOTE_ADDR'];
    
    $stmt = $pdo->prepare("INSERT INTO visitas (enlace_id, ip) VALUES (?, ?)");
    $stmt->execute([$enlaceId, $ip]);
    
    // Redirigir después de registrar el click
    $stmt = $pdo->prepare("SELECT url FROM enlaces WHERE id = ?");
    $stmt->execute([$enlaceId]);
    $enlace = $stmt->fetch();
    
    if ($enlace && !empty($enlace['url'])) {
        header("Location: " . $enlace['url']);
        exit;
    }
}

// Manejo seguro del título de la página
$pageTitle = (!empty($usuario['nombre_perfil']) ? htmlspecialchars($usuario['nombre_perfil'] ?? '') : htmlspecialchars($usuario['username'] ?? '')) . " - Mi Social";
include 'includes/header.php';
?>

<div class="profile-container" style="background-color: <?php echo !empty($usuario['tema_color']) ? htmlspecialchars($usuario['tema_color'] ?? '') : '#3498db'; ?>">
    <div class="profile-header">
        <img src="<?php echo !empty($usuario['avatar']) ? htmlspecialchars($usuario['avatar'] ?? '') : 'assets/img/default-avatar.png'; ?>" 
             alt="Avatar de <?php echo !empty($usuario['nombre_perfil']) ? htmlspecialchars($usuario['nombre_perfil'] ?? '') : htmlspecialchars($usuario['username'] ?? ''); ?>" 
             class="profile-avatar">
        <h1><?php echo !empty($usuario['nombre_perfil']) ? htmlspecialchars($usuario['nombre_perfil'] ?? '') : htmlspecialchars($usuario['username'] ?? ''); ?></h1>
        <?php if (!empty($usuario['bio'])): ?>
            <p class="profile-bio"><?php echo htmlspecialchars($usuario['bio'] ?? ''); ?></p>
        <?php endif; ?>
    </div>
    
    <?php if (!empty($enlaces)): ?>
        <div class="profile-links">
            <?php foreach ($enlaces as $index => $enlace): ?>
                <a href="perfil.php?user=<?php echo htmlspecialchars($username ?? ''); ?>&link=<?php echo !empty($enlace['id']) ? (int)$enlace['id'] : ''; ?>" 
                   class="profile-link"
                   target="_blank"
                   rel="noopener noreferrer"
                   style="animation-delay: <?php echo 0.4 + ($index * 0.1); ?>s">
                    <?php if (!empty($enlace['imagen'])): ?>
                        <img src="assets/img/<?php echo htmlspecialchars($enlace['imagen']); ?>" 
                             alt="<?php echo htmlspecialchars($enlace['titulo'] ?? ''); ?>" 
                             class="social-icon">
                    <?php else: ?>
                        <i class="fas fa-link"></i>
                    <?php endif; ?>
                    <div class="link-content">
                        <span class="link-title"><?php echo !empty($enlace['titulo']) ? htmlspecialchars($enlace['titulo'] ?? '') : 'Nuevo enlace'; ?></span>
                        <span class="link-url"><?php echo htmlspecialchars(parse_url($enlace['url'], PHP_URL_HOST) ?? $enlace['url']); ?></span>
                    </div>
                    <i class="fas fa-external-link-alt link-external"></i>
                </a>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="no-links animated" style="color: rgba(255,255,255,0.7); margin: 30px 0;">
            <i class="fas fa-info-circle" style="font-size: 2rem; margin-bottom: 15px;"></i>
            <p>Este usuario no tiene enlaces configurados todavía.</p>
        </div>
    <?php endif; ?>
    
    <div class="profile-footer animated delay-1">
        <p>Powered by <strong>Mi Social</strong></p>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Efecto de clic en los enlaces
    const links = document.querySelectorAll('.profile-link');
    links.forEach(link => {
        link.addEventListener('click', function(e) {
            // Solo prevenir el comportamiento por defecto si no hay URL o es inválida
            if (!this.getAttribute('href') || this.getAttribute('href') === '#' || this.getAttribute('href').indexOf('link=') === -1) {
                e.preventDefault();
                return;
            }
            
            // Agregar clase de clic
            this.classList.add('clicked');
            
            // Redirigir después de la animación
            setTimeout(() => {
                if (this.getAttribute('href') && this.getAttribute('href') !== '#') {
                    window.location.href = this.getAttribute('href');
                }
            }, 300);
        });
    });
    
    // Efecto de carga progresiva
    const elements = document.querySelectorAll('.animated');
    elements.forEach((el, index) => {
        setTimeout(() => {
            el.style.opacity = 1;
            el.style.transform = 'translateY(0)';
        }, 100 * index);
    });
});
</script>

<?php include 'includes/footer.php'; ?>