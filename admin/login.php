<?php
require_once '../includes/config.php';

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirigir si ya está logueado
if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
$usernameValue = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Mantener el valor del username para no borrarlo
    $usernameValue = htmlspecialchars($username);
    
    if (empty($username) || empty($password)) {
        $error = "Por favor, completa todos los campos.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE username = ? OR email = ? LIMIT 1");
        $stmt->execute([$username, $username]);
        $user = $stmt->fetch();
        
        if ($user) {
            if (password_verify($password, $user['password'])) {
                // Regenerar el ID de sesión para prevenir fijación
                session_regenerate_id(true);
                
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                
                // Establecer cookie de "Recuérdame" si está marcado
                if (isset($_POST['remember'])) {
                    $token = bin2hex(random_bytes(32));
                    $expiry = time() + 60 * 60 * 24 * 30; // 30 días
                    
                    setcookie('remember_token', $token, [
                        'expires' => $expiry,
                        'path' => '/',
                        'secure' => isset($_SERVER['HTTPS']),
                        'httponly' => true,
                        'samesite' => 'Lax'
                    ]);
                    
                    // Actualizar token en la base de datos
                    $stmt = $pdo->prepare("UPDATE usuarios SET remember_token = ?, token_expiry = ? WHERE id = ?");
                    $stmt->execute([$token, date('Y-m-d H:i:s', $expiry), $user['id']]);
                } else {
                    // Limpiar tokens si no se seleccionó "Recuérdame"
                    $stmt = $pdo->prepare("UPDATE usuarios SET remember_token = NULL, token_expiry = NULL WHERE id = ?");
                    $stmt->execute([$user['id']]);
                }
                
                // Redirigir al dashboard
                header('Location: dashboard.php');
                exit;
            } else {
                $error = "Contraseña incorrecta.";
            }
        } else {
            $error = "Usuario no encontrado.";
        }
    }
}

$pageTitle = "Iniciar Sesión";
include '../includes/header.php';
?>

<!-- El resto del código HTML permanece igual -->
<div class="auth-container">
    <div class="auth-card">
        <h2>Iniciar Sesión</h2>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_GET['registered'])): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                ¡Registro exitoso! Por favor inicia sesión.
            </div>
        <?php endif; ?>
        
        <form method="POST" autocomplete="on">
            <div class="form-group">
                <label for="username">Usuario o Email</label>
                <input type="text" id="username" name="username" value="<?php echo $usernameValue; ?>" required autofocus>
            </div>
            
            <div class="form-group">
                <label for="password">Contraseña</label>
                <div class="password-container">
                    <input type="password" id="password" name="password" required autocomplete="current-password">
                    <button type="button" class="toggle-password" aria-label="Mostrar contraseña" title="Mostrar/ocultar contraseña">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                <div class="text-right" style="margin-top: 5px;">
                    <a href="forgot-password.php" style="font-size: 0.9rem;">¿Olvidaste tu contraseña?</a>
                </div>
            </div>
            
            <div class="form-group" style="display: flex; align-items: center;">
                <input type="checkbox" id="remember" name="remember" style="width: auto; margin-right: 10px;">
                <label for="remember" style="margin-bottom: 0;">Recuérdame</label>
            </div>
            
            <button type="submit" name="login" class="btn btn-primary btn-auth">
                <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
            </button>
        </form>
        
        <div class="social-auth">
            <p>O inicia sesión con</p>
            <div class="social-buttons">
                <a href="#" class="social-btn google" aria-label="Google">
                    <i class="fab fa-google"></i>
                </a>
                <a href="#" class="social-btn facebook" aria-label="Facebook">
                    <i class="fab fa-facebook-f"></i>
                </a>
                <a href="#" class="social-btn twitter" aria-label="Twitter">
                    <i class="fab fa-twitter"></i>
                </a>
            </div>
        </div>
        
        <div class="auth-switch">
            ¿No tienes una cuenta? <a href="register.php">Regístrate</a>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle para mostrar/ocultar contraseña
    const togglePassword = document.querySelector('.toggle-password');
    const password = document.querySelector('#password');
    
    togglePassword.addEventListener('click', function() {
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);
        this.innerHTML = type === 'password' ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
    });
});
</script>

<?php include '../includes/footer.php'; ?>