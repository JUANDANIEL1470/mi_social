<?php 
$pageTitle = "Inicio";
include 'includes/header.php'; 
?>

<section class="hero">
    <div class="container">
        <div class="hero-content">
            <h1 class="animated">Conecta todas tus redes en un solo lugar</h1>
            <p class="subtitle animated delay-1">Mi Social te permite compartir todos tus enlaces importantes con una sola URL en tu biografía, perfecto para Instagram, TikTok, YouTube y más.</p>
            <div class="cta-buttons animated delay-2">
                <?php if (isLoggedIn()): ?>
                    <a href="perfil.php?user=<?php echo $_SESSION['username']; ?>" class="btn btn-primary">
                        <i class="fas fa-external-link-alt"></i> Ver mi perfil
                    </a>
                    <a href="admin/dashboard.php" class="btn btn-outline">
                        <i class="fas fa-cog"></i> Personalizar
                    </a>
                <?php else: ?>
                    <a href="admin/login.php?register=1" class="btn btn-primary">
                        <i class="fas fa-user-plus"></i> Crear cuenta gratis
                    </a>
                    <a href="admin/login.php" class="btn btn-outline">
                        <i class="fas fa-sign-in-alt"></i> Iniciar sesión
                    </a>
                <?php endif; ?>
            </div>
        </div>
        <div class="hero-image animated delay-3">
            <img src="assets/img/social-media.png" alt="Redes sociales conectadas" loading="lazy">
        </div>
    </div>
</section>

<section class="features">
    <div class="container">
        <h2 class="animated">¿Por qué usar Mi Social?</h2>
        <div class="features-grid">
            <div class="feature-card animated delay-1">
                <i class="fas fa-link"></i>
                <h3>Un solo enlace</h3>
                <p>Agrega todos tus perfiles sociales, sitios web, tiendas online y más en un solo lugar accesible.</p>
            </div>
            <div class="feature-card animated delay-2">
                <i class="fas fa-palette"></i>
                <h3>Personalizable</h3>
                <p>Cambia colores, fondos, diseños y orden de enlaces para que coincida perfectamente con tu marca personal.</p>
            </div>
            <div class="feature-card animated delay-3">
                <i class="fas fa-chart-line"></i>
                <h3>Estadísticas</h3>
                <p>Mira cuántos clics reciben tus enlaces, desde dónde y optimiza tu contenido para tu audiencia.</p>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>