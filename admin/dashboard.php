<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$currentUser = getCurrentUser($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Procesar actualización de perfil
    $nombre_perfil = $_POST['nombre_perfil'] ?? '';
    $bio = $_POST['bio'] ?? '';
    $tema_color = $_POST['tema_color'] ?? '';
    
    // Procesar avatar si se subió
    $avatarPath = $currentUser['avatar'] ?? '';
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../assets/uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        // Eliminar avatar anterior si existe
        if ($currentUser['avatar'] && file_exists('../' . $currentUser['avatar'])) {
            unlink('../' . $currentUser['avatar']);
        }
        
        $extension = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
        $filename = 'avatar_' . $currentUser['id'] . '_' . time() . '.' . $extension;
        $targetPath = $uploadDir . $filename;
        
        if (move_uploaded_file($_FILES['avatar']['tmp_name'], $targetPath)) {
            $avatarPath = 'assets/uploads/' . $filename;
        }
    }
    
    $stmt = $pdo->prepare("UPDATE usuarios SET nombre_perfil = ?, bio = ?, tema_color = ?, avatar = ? WHERE id = ?");
    $stmt->execute([$nombre_perfil, $bio, $tema_color, $avatarPath, $currentUser['id']]);
    
    // Actualizar enlaces
    if (isset($_POST['enlaces'])) {
        // Eliminar enlaces existentes
        $pdo->prepare("DELETE FROM enlaces WHERE usuario_id = ?")->execute([$currentUser['id']]);
        
        // Insertar nuevos enlaces
        foreach ($_POST['enlaces'] as $index => $enlace) {
            if (!empty($enlace['titulo']) && !empty($enlace['url'])) {
                // Verificar si es una red social predefinida
                $redSocial = null;
                if (!empty($enlace['imagen'])) {
                    $redSocial = $pdo->prepare("SELECT * FROM redes_sociales WHERE imagen = ? LIMIT 1");
                    $redSocial->execute([$enlace['imagen']]);
                    $redSocial = $redSocial->fetch();
                }
                
                $urlFinal = $enlace['url'];
                if ($redSocial) {
                    $urlFinal = construirUrlSocial($redSocial, $enlace['url']);
                }
                
                $stmt = $pdo->prepare("INSERT INTO enlaces (usuario_id, titulo, url, imagen, orden) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([
                    $currentUser['id'],
                    $enlace['titulo'] ?? '',
                    $urlFinal,
                    $enlace['imagen'] ?? '',
                    $index
                ]);
            }
        }
    }
    
    // Actualizar datos del usuario en la sesión
    $_SESSION['user_data_updated'] = true;
    
    header('Location: dashboard.php?success=1');
    exit;
}

// Obtener enlaces del usuario
$enlaces = $pdo->prepare("SELECT * FROM enlaces WHERE usuario_id = ? ORDER BY orden");
$enlaces->execute([$currentUser['id']]);
$enlaces = $enlaces->fetchAll();

// Obtener redes sociales predefinidas
$redesSociales = $pdo->query("SELECT * FROM redes_sociales ORDER BY nombre")->fetchAll();

$pageTitle = "Dashboard";
include '../includes/header.php';

// Mostrar alerta de éxito después de redirección
if (isset($_SESSION['user_data_updated'])) {
    unset($_SESSION['user_data_updated']);
    echo '<script>
        document.addEventListener("DOMContentLoaded", function() {
            setTimeout(() => {
                const alert = document.createElement("div");
                alert.className = "alert alert-success animated fadeIn";
                alert.innerHTML = \'<i class="fas fa-check-circle"></i> ¡Perfil actualizado correctamente!\';
                document.querySelector(".dashboard-container").prepend(alert);
                
                setTimeout(() => {
                    alert.classList.add("fadeOut");
                    setTimeout(() => alert.remove(), 500);
                }, 3000);
            }, 300);
        });
    </script>';
}
?>

<div class="dashboard-container">
    <h1>Panel de Control</h1>
    
    <div class="dashboard-grid">
        <div class="profile-section">
            <h2>Previsualización</h2>
            <div class="profile-preview" style="background-color: <?php echo $currentUser['tema_color'] ?? '#4361ee'; ?>">
                <img src="../<?php echo $currentUser['avatar'] ? htmlspecialchars($currentUser['avatar'] ?? '') : 'assets/img/default-avatar.png'; ?>" 
                     alt="Avatar de <?php echo htmlspecialchars($currentUser['nombre_perfil'] ?? $currentUser['username'] ?? ''); ?>" 
                     class="preview-avatar">
                <h3><?php echo htmlspecialchars($currentUser['nombre_perfil'] ?? $currentUser['username'] ?? ''); ?></h3>
                <p><?php echo htmlspecialchars($currentUser['bio'] ?? ''); ?></p>
                
                <div class="preview-links">
                    <?php if (!empty($enlaces)): ?>
                        <?php foreach ($enlaces as $enlace): ?>
                            <a href="<?php echo htmlspecialchars($enlace['url'] ?? ''); ?>" 
                               target="_blank"
                               class="preview-link">
                                <?php if ($enlace['imagen'] ?? ''): ?>
                                    <img src="../assets/img/<?php echo htmlspecialchars($enlace['imagen']); ?>" 
                                         alt="<?php echo htmlspecialchars($enlace['titulo'] ?? ''); ?>" 
                                         class="social-icon-img">
                                <?php else: ?>
                                    <i class="fas fa-link"></i>
                                <?php endif; ?>
                                <?php echo htmlspecialchars($enlace['titulo'] ?? ''); ?>
                            </a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="no-links" style="color: rgba(255,255,255,0.7); padding: 10px;">
                            <i class="fas fa-info-circle"></i> No hay enlaces configurados
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="share-box">
                <h3>Comparte tu perfil</h3>
                <div class="share-url">
                    <input type="text" 
                           id="profile-url" 
                           value="<?php echo "http://" . $_SERVER['HTTP_HOST'] . '/mi_social/perfil.php?user=' . htmlspecialchars($currentUser['username'] ?? ''); ?>"
                           readonly>
                    <button class="btn btn-primary copy-btn" data-clipboard-target="#profile-url">
                        <i class="fas fa-copy"></i> Copiar
                    </button>
                </div>
                <p>Agrega este enlace a tu biografía en Instagram, TikTok, YouTube, etc.</p>
                
                <div class="qr-code-container" style="margin-top: 20px; text-align: center;">
                    <div id="qrcode" style="display: inline-block; padding: 10px; background: white; border-radius: 8px;"></div>
                    <p style="margin-top: 10px; font-size: 0.9rem;">Escanea este código QR</p>
                </div>
            </div>
        </div>
        
        <div class="edit-section">
            <h2>Editar Perfil</h2>
            <form method="POST" enctype="multipart/form-data" id="profile-form">
                <div class="form-group">
                    <label for="nombre_perfil">Nombre para mostrar</label>
                    <input type="text" 
                           id="nombre_perfil" 
                           name="nombre_perfil" 
                           value="<?php echo htmlspecialchars($currentUser['nombre_perfil'] ?? ''); ?>"
                           required>
                </div>
                
                <div class="form-group">
                    <label for="bio">Biografía</label>
                    <textarea id="bio" 
                              name="bio" 
                              maxlength="200"
                              placeholder="Escribe algo sobre ti..."><?php echo htmlspecialchars($currentUser['bio'] ?? ''); ?></textarea>
                    <div class="char-counter" style="text-align: right; font-size: 0.8rem; color: var(--text-color); opacity: 0.7;">
                        <span id="bio-counter"><?php echo 200 - strlen($currentUser['bio'] ?? ''); ?></span> caracteres restantes
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="tema_color">Color de tema</label>
                    <div style="display: flex; align-items: center; gap: 15px;">
                        <input type="color" 
                               id="tema_color" 
                               name="tema_color" 
                               value="<?php echo $currentUser['tema_color'] ?? '#4361ee'; ?>">
                        <span id="color-value"><?php echo $currentUser['tema_color'] ?? '#4361ee'; ?></span>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="avatar">Avatar</label>
                    <div class="file-upload">
                        <label class="file-upload-label">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <span id="file-name"><?php echo $currentUser['avatar'] ? 'Cambiar imagen' : 'Seleccionar imagen'; ?></span>
                        </label>
                        <input type="file" 
                               id="avatar" 
                               name="avatar" 
                               accept="image/*">
                    </div>
                    <small style="display: block; margin-top: 5px; opacity: 0.7;">Recomendado: 500x500 px, formato JPG o PNG</small>
                </div>
                
                <h3>Tus Enlaces</h3>
                <div class="social-presets">
                    <h4>Redes sociales predefinidas</h4>
                    <div class="preset-grid">
                        <?php foreach ($redesSociales as $red): ?>
                            <div class="preset-item" 
                                 data-imagen="<?php echo htmlspecialchars($red['imagen']); ?>" 
                                 data-baseurl="<?php echo htmlspecialchars($red['base_url'] ?? ''); ?>"
                                 data-placeholder="<?php echo htmlspecialchars($red['placeholder']); ?>">
                                <img src="../assets/img/<?php echo htmlspecialchars($red['imagen']); ?>" 
                                     alt="<?php echo htmlspecialchars($red['nombre']); ?>" 
                                     class="social-icon-img">
                                <span><?php echo htmlspecialchars($red['nombre']); ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <div id="enlaces-container">
                    <?php if (!empty($enlaces)): ?>
                        <?php foreach ($enlaces as $index => $enlace): ?>
                            <div class="enlace-form" data-index="<?php echo $index; ?>">
                                <div class="form-group">
                                    <label>Título</label>
                                    <input type="text" 
                                           name="enlaces[<?php echo $index; ?>][titulo]" 
                                           value="<?php echo htmlspecialchars($enlace['titulo'] ?? ''); ?>"
                                           required>
                                </div>
                                <div class="form-group">
                                    <label>URL</label>
                                    <input type="url" 
                                           name="enlaces[<?php echo $index; ?>][url]" 
                                           value="<?php echo htmlspecialchars($enlace['url'] ?? ''); ?>"
                                           required>
                                </div>
                                <div class="form-group">
                                    <label>Ícono (Imagen)</label>
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <select name="enlaces[<?php echo $index; ?>][imagen]" class="social-icon-select">
                                            <option value="">Seleccionar imagen</option>
                                            <?php foreach ($redesSociales as $red): ?>
                                                <option value="<?php echo htmlspecialchars($red['imagen']); ?>" 
                                                    <?php echo ($enlace['imagen'] ?? '') === $red['imagen'] ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($red['nombre']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <?php if ($enlace['imagen'] ?? ''): ?>
                                            <img src="../assets/img/<?php echo htmlspecialchars($enlace['imagen']); ?>" 
                                                 alt="Icono" 
                                                 class="social-icon-preview" 
                                                 style="width: 24px; height: 24px;">
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <input type="hidden" 
                                       name="enlaces[<?php echo $index; ?>][orden]" 
                                       value="<?php echo $index; ?>">
                                <button type="button" 
                                        class="btn btn-danger remove-enlace">
                                    <i class="fas fa-trash"></i> Eliminar
                                </button>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                
                <button type="button" 
                        id="add-enlace" 
                        class="btn btn-secondary"
                        style="margin-bottom: 30px;">
                    <i class="fas fa-plus"></i> Añadir Enlace Personalizado
                </button>
                
                <button type="submit" 
                        class="btn btn-primary"
                        style="width: 100%; padding: 15px;">
                    <i class="fas fa-save"></i> Guardar Cambios
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Incluir la biblioteca QRCode.js -->
<script src="https://cdn.rawgit.com/davidshimjs/qrcodejs/gh-pages/qrcode.min.js"></script>
<!-- Incluir Clipboard.js para copiar al portapapeles -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.8/clipboard.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Generar código QR
    const profileUrl = document.getElementById('profile-url').value;
    new QRCode(document.getElementById("qrcode"), {
        text: profileUrl,
        width: 150,
        height: 150,
        colorDark : "#000000",
        colorLight : "#ffffff",
        correctLevel : QRCode.CorrectLevel.H
    });
    
    // Copiar URL al portapapeles
    new ClipboardJS('.copy-btn').on('success', function(e) {
        const btn = e.trigger;
        btn.innerHTML = '<i class="fas fa-check"></i> Copiado!';
        setTimeout(() => {
            btn.innerHTML = '<i class="fas fa-copy"></i> Copiar';
        }, 2000);
        e.clearSelection();
    });
    
    // Actualizar nombre del archivo seleccionado
    document.getElementById('avatar').addEventListener('change', function(e) {
        const fileName = e.target.files[0] ? e.target.files[0].name : 'Seleccionar imagen';
        document.getElementById('file-name').textContent = fileName;
    });
    
    // Contador de caracteres para la biografía
    const bioTextarea = document.getElementById('bio');
    const bioCounter = document.getElementById('bio-counter');
    
    bioTextarea.addEventListener('input', function() {
        const remaining = 200 - this.value.length;
        bioCounter.textContent = remaining;
    });
    
    // Mostrar valor del color seleccionado
    const colorPicker = document.getElementById('tema_color');
    const colorValue = document.getElementById('color-value');
    
    colorPicker.addEventListener('input', function() {
        colorValue.textContent = this.value;
        document.querySelector('.profile-preview').style.backgroundColor = this.value;
    });
    
    // Añadir nuevo campo de enlace personalizado
    let enlaceCount = <?php echo !empty($enlaces) ? count($enlaces) : 0; ?>;
    document.getElementById('add-enlace').addEventListener('click', function() {
        const container = document.getElementById('enlaces-container');
        const newEnlace = document.createElement('div');
        newEnlace.className = 'enlace-form';
        newEnlace.setAttribute('data-index', enlaceCount);
        newEnlace.innerHTML = `
            <div class="form-group">
                <label>Título</label>
                <input type="text" name="enlaces[${enlaceCount}][titulo]" required>
            </div>
            <div class="form-group">
                <label>URL</label>
                <input type="url" name="enlaces[${enlaceCount}][url]" required>
            </div>
            <div class="form-group">
                <label>Ícono (Imagen)</label>
                <div style="display: flex; align-items: center; gap: 10px;">
                    <select name="enlaces[${enlaceCount}][imagen]" class="social-icon-select">
                        <option value="">Seleccionar imagen</option>
                        <?php foreach ($redesSociales as $red): ?>
                            <option value="<?php echo htmlspecialchars($red['imagen']); ?>">
                                <?php echo htmlspecialchars($red['nombre']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <img src="" alt="Icono" class="social-icon-preview" style="width: 24px; height: 24px; display: none;">
                </div>
            </div>
            <input type="hidden" name="enlaces[${enlaceCount}][orden]" value="${enlaceCount}">
            <button type="button" class="btn btn-danger remove-enlace">
                <i class="fas fa-trash"></i> Eliminar
            </button>
        `;
        container.appendChild(newEnlace);
        enlaceCount++;
        
        // Desplazarse al nuevo enlace
        newEnlace.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    });
    
    // Selección de redes sociales predefinidas
    document.querySelectorAll('.preset-item').forEach(item => {
        item.addEventListener('click', function() {
            const container = document.getElementById('enlaces-container');
            const index = document.querySelectorAll('.enlace-form').length;
            const imagen = this.getAttribute('data-imagen');
            const baseUrl = this.getAttribute('data-baseurl') || '';
            const nombre = this.querySelector('span').textContent;
            const placeholder = this.getAttribute('data-placeholder');
            
            const newEnlace = document.createElement('div');
            newEnlace.className = 'enlace-form';
            newEnlace.setAttribute('data-index', index);
            newEnlace.innerHTML = `
                <div class="form-group">
                    <label>Título</label>
                    <input type="text" name="enlaces[${index}][titulo]" value="${nombre}" required>
                </div>
                <div class="form-group">
                    <label>URL</label>
                    <input type="url" name="enlaces[${index}][url]" value="${baseUrl}" placeholder="${placeholder}" required>
                </div>
                <div class="form-group">
                    <label>Ícono (Imagen)</label>
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <select name="enlaces[${index}][imagen]" class="social-icon-select">
                            <option value="">Seleccionar imagen</option>
                            <?php foreach ($redesSociales as $red): ?>
                                <option value="<?php echo htmlspecialchars($red['imagen']); ?>" ${imagen === '<?php echo htmlspecialchars($red['imagen']); ?>' ? 'selected' : ''}>
                                    <?php echo htmlspecialchars($red['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <img src="../assets/img/${imagen}" alt="Icono" class="social-icon-preview" style="width: 24px; height: 24px;">
                    </div>
                </div>
                <input type="hidden" name="enlaces[${index}][orden]" value="${index}">
                <button type="button" class="btn btn-danger remove-enlace">
                    <i class="fas fa-trash"></i> Eliminar
                </button>
            `;
            container.appendChild(newEnlace);
            
            // Desplazarse al nuevo enlace
            newEnlace.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        });
    });
    
    // Actualizar vista previa de iconos al cambiar selección
    document.addEventListener('change', function(e) {
        if (e.target && e.target.classList.contains('social-icon-select')) {
            const select = e.target;
            const preview = select.closest('.form-group').querySelector('.social-icon-preview');
            const selectedValue = select.value;
            
            if (selectedValue) {
                preview.src = `../assets/img/${selectedValue}`;
                preview.style.display = 'inline-block';
            } else {
                preview.style.display = 'none';
            }
        }
    });
    
    // Eliminar campo de enlace
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-enlace')) {
            const enlaceForm = e.target.closest('.enlace-form');
            enlaceForm.classList.add('fade-out');
            
            setTimeout(() => {
                enlaceForm.remove();
                // Reindexar los enlaces restantes
                const enlaces = document.querySelectorAll('.enlace-form');
                enlaces.forEach((enlace, index) => {
                    enlace.setAttribute('data-index', index);
                    enlace.querySelector('input[type="hidden"]').value = index;
                    enlace.querySelector('input[type="hidden"]').name = `enlaces[${index}][orden]`;
                    
                    // Actualizar los nombres de los campos
                    const inputs = enlace.querySelectorAll('input[type="text"], input[type="url"], select');
                    inputs[0].name = `enlaces[${index}][titulo]`;
                    inputs[1].name = `enlaces[${index}][url]`;
                    if (inputs[2]) inputs[2].name = `enlaces[${index}][imagen]`;
                });
                
                enlaceCount = enlaces.length;
            }, 300);
        }
    });
    
    // Animación al guardar
    const form = document.getElementById('profile-form');
    form.addEventListener('submit', function(e) {
        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';
        submitBtn.disabled = true;
    });
});
</script>

<style>
.fade-out {
    animation: fadeOut 0.3s ease-out forwards;
}

@keyframes fadeOut {
    from { opacity: 1; transform: translateY(0); }
    to { opacity: 0; transform: translateY(-20px); }
}

.social-presets {
    margin-bottom: 20px;
    padding: 15px;
    background: var(--card-bg);
    border-radius: 8px;
    border: 1px solid var(--border-color);
    box-shadow: var(--card-shadow);
}

.social-presets h4 {
    margin-top: 0;
    margin-bottom: 10px;
    color: var(--text-muted);
}

.preset-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    gap: 10px;
}

.preset-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 15px 10px;
    background: var(--item-bg);
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.2s ease;
    border: 1px solid var(--border-color);
    text-align: center;
}

.preset-item:hover {
    transform: translateY(-3px);
    box-shadow: var(--hover-shadow);
    border-color: var(--primary-color);
    background: var(--item-hover-bg);
}

.preset-item img {
    width: 32px;
    height: 32px;
    margin-bottom: 5px;
    object-fit: contain;
    filter: var(--icon-filter); /* Para ajustar brillo en modo oscuro */
}

.preset-item span {
    font-size: 0.85rem;
    margin-top: 5px;
    color: var(--text-color);
}

.preset-item.selected {
    background: var(--primary-light);
    border-color: var(--primary-color);
    box-shadow: 0 0 0 2px var(--primary-light);
}

.preset-item, .social-presets {
    transition: background-color 0.3s ease, border-color 0.3s ease;
}

.social-icon-select {
    flex: 1;
    padding: 8px 12px;
    border: 1px solid rgba(67, 97, 238, 0.2);
    border-radius: 8px;
    font-size: 0.9rem;
    background: var(--card-bg);
    color: var(--text-color);
}

.social-icon-preview {
    display: none;
    object-fit: contain;
}

.preview-link {
    display: flex;
    align-items: center;
    background: rgba(67, 97, 238, 0.1);
    color: var(--primary-color);
    text-decoration: none;
    padding: 12px 20px;
    border-radius: 10px;
    transition: var(--transition);
}

.preview-link:hover {
    background: rgba(67, 97, 238, 0.2);
    transform: translateY(-2px);
}

.preview-link img, .preview-link i {
    margin-right: 10px;
}

.no-links {
    text-align: center;
    padding: 15px;
    background: rgba(255,255,255,0.1);
    border-radius: 8px;
}

@media (max-width: 768px) {
    .preset-grid {
        grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
        gap: 8px;
    }
    
    .preset-item {
        padding: 10px 5px;
    }
}
</style>

<?php include '../includes/footer.php'; ?>