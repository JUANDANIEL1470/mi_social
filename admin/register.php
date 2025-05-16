<?php
require_once '../includes/config.php';

if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    
    // Validaciones básicas
    $errors = [];
    
    if (strlen($username) < 4) {
        $errors[] = "El nombre de usuario debe tener al menos 4 caracteres.";
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Por favor ingresa un email válido.";
    }
    
    if (strlen($password) < 8) {
        $errors[] = "La contraseña debe tener al menos 8 caracteres.";
    }
    
    if ($password !== $confirmPassword) {
        $errors[] = "Las contraseñas no coinciden.";
    }
    
    if (empty($errors)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        try {
            $stmt = $pdo->prepare("INSERT INTO usuarios (username, email, password, nombre_perfil) VALUES (?, ?, ?, ?)");
            $stmt->execute([$username, $email, $hashedPassword, $username]);
            
            $userId = $pdo->lastInsertId();
            
            // Iniciar sesión automáticamente después del registro
            $_SESSION['user_id'] = $userId;
            $_SESSION['username'] = $username;
            
            header('Location: dashboard.php');
            exit;
        } catch (PDOException $e) {
            $error = "Error al registrar: " . (strpos($e->getMessage(), 'Duplicate entry') !== false ? 
                    "El usuario o email ya está en uso" : "Error desconocido");
        }
    } else {
        $error = implode("<br>", $errors);
    }
}

$pageTitle = "Registro";
include '../includes/header.php';
?>

<div class="auth-container">
    <div class="auth-card">
        <h2>Crear Cuenta</h2>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="username">Nombre de Usuario</label>
                <input type="text" id="username" name="username" required autofocus>
            </div>
            
            <div class="form-group">
                <label for="email">Correo Electrónico</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="password">Contraseña</label>
                <div class="password-container">
                    <input type="password" id="password" name="password" required>
                    <button type="button" class="toggle-password" aria-label="Mostrar contraseña" title="Mostrar/ocultar contraseña">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                <small style="display: block; margin-top: 5px; opacity: 0.7;">Mínimo 8 caracteres</small>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirmar Contraseña</label>
                <div class="password-container">
                    <input type="password" id="confirm_password" name="confirm_password" required>
                    <button type="button" class="toggle-password" aria-label="Mostrar contraseña">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>
            
            <div class="form-group" style="margin-top: 20px;">
                <input type="checkbox" id="terms" name="terms" required style="width: auto; margin-right: 10px;">
                <label for="terms" style="margin-bottom: 0;">Acepto los <a href="terms.php" target="_blank">Términos de Servicio</a> y <a href="privacy.php" target="_blank">Política de Privacidad</a></label>
            </div>
            
            <button type="submit" class="btn btn-primary btn-auth">
                <i class="fas fa-user-plus"></i> Registrarse
            </button>
        </form>
        
        <div class="social-auth">
            <p>O regístrate con</p>
            <div class="social-buttons">
                <a href="#" class="social-btn google" aria-label="Google">
                    <i class="fab fa-google"></i>
                </a>
                <a href="#" class="social-btn facebook" aria-label="Facebook">
                    <i class="fab fa-facebook-f"></i>
                </a>
                <a href="#" class="social-btn twitter" aria-label="Twitter">
                    <i class="fas fa-twitter"></i>
                </a>
            </div>
        </div>
        
        <div class="auth-switch">
            ¿Ya tienes una cuenta? <a href="login.php">Inicia sesión</a>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle para mostrar/ocultar contraseña
    const togglePasswords = document.querySelectorAll('.toggle-password');
    
    togglePasswords.forEach(toggle => {
        toggle.addEventListener('click', function() {
            const input = this.parentElement.querySelector('input');
            const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
            input.setAttribute('type', type);
            this.innerHTML = type === 'password' ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
        });
    });
    
    // Validación de contraseña en tiempo real
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('confirm_password');
    
    function validatePassword() {
        if (password.value !== confirmPassword.value) {
            confirmPassword.setCustomValidity("Las contraseñas no coinciden");
        } else {
            confirmPassword.setCustomValidity('');
        }
    }
    
    password.addEventListener('input', validatePassword);
    confirmPassword.addEventListener('input', validatePassword);
    
    // Eliminamos el código del efecto de onda ya que ahora está en ripple.js
});
</script>

<?php include '../includes/footer.php'; ?>