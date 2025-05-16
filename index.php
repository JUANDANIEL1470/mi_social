<?php 
$pageTitle = "Inicio";
include 'includes/header.php'; 
?>

<section class="hero">
    <div class="container">
        <div class="hero-content">
            <h1 class="animated">Conecta todas tus redes sociales en un solo lugar</h1>
            <p class="animated delay-1">Crea tu página de perfil personalizada y comparte todos tus enlaces con un solo clic.</p>
            <div class="cta-buttons animated delay-2">
                <?php if (!isLoggedIn()): ?>
                    <a href="/mi_social/admin/login.php" class="btn btn-primary">Iniciar Sesión</a>
                    <a href="/mi_social/admin/register.php" class="btn btn-outline">Registrarse</a>
                <?php else: ?>
                    <a href="/mi_social/admin/dashboard.php" class="btn btn-primary">Mi Dashboard</a>
                    <a href="/mi_social/perfil.php?user=<?php echo htmlspecialchars($currentUser['username']); ?>" class="btn btn-outline">Ver Mi Perfil</a>
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