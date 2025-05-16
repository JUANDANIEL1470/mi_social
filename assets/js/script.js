// Theme Toggle - versión corregida
document.addEventListener('DOMContentLoaded', function() {
    const themeToggle = document.getElementById('theme-toggle');
    let currentTheme = localStorage.getItem('theme') || 'light';
    
    // Aplicar el tema almacenado al cargar
    document.documentElement.setAttribute('data-theme', currentTheme);
    
    themeToggle.addEventListener('click', () => {
        currentTheme = currentTheme === 'light' ? 'dark' : 'light';
        document.documentElement.setAttribute('data-theme', currentTheme);
        localStorage.setItem('theme', currentTheme);
    });

    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            document.querySelector(this.getAttribute('href')).scrollIntoView({
                behavior: 'smooth'
            });
        });
    });

    // Add animation classes
    const animatedElements = document.querySelectorAll('.animated');
    animatedElements.forEach((el, index) => {
        el.classList.add(`delay-${index % 3 + 1}`);
    });

    // Efecto de header que aparece/desaparece al hacer scroll
    const header = document.querySelector('.header');
    let lastScroll = 0;
    
    window.addEventListener('scroll', function() {
        const currentScroll = window.pageYOffset;
        
        if (currentScroll <= 0) {
            header.classList.remove('hidden');
            return;
        }
        
        if (currentScroll > lastScroll && !header.classList.contains('hidden')) {
            // Scroll hacia abajo
            header.classList.add('hidden');
        } else if (currentScroll < lastScroll && header.classList.contains('hidden')) {
            // Scroll hacia arriba
            header.classList.remove('hidden');
        }
        
        lastScroll = currentScroll;
    });

    // Efecto de aparición al hacer scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animated');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    document.querySelectorAll('.hero-content h1, .hero-content p, .hero-content .cta-buttons, .hero-image, .features h2, .feature-card').forEach(el => {
        observer.observe(el);
    });

    // Efecto de hover en las tarjetas de características
    const featureCards = document.querySelectorAll('.feature-card');
    featureCards.forEach(card => {
        card.addEventListener('mouseenter', () => {
            const icon = card.querySelector('i');
            icon.style.transform = 'scale(1.2)';
        });
        
        card.addEventListener('mouseleave', () => {
            const icon = card.querySelector('i');
            icon.style.transform = 'scale(1)';
        });
    });

    // Efecto de aparición del footer al hacer scroll
    const footerSections = document.querySelectorAll('.footer-section');
    const footerObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.animation = `fadeInUp 0.6s ease-out ${entry.target.dataset.delay || '0s'} forwards`;
                footerObserver.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    });

    footerSections.forEach((section, index) => {
        section.dataset.delay = `${index * 0.2}s`;
        footerObserver.observe(section);
    });

    // Efecto hover en los badges de app store
    const badges = document.querySelectorAll('.badge');
    badges.forEach(badge => {
        badge.addEventListener('mouseenter', () => {
            const icon = badge.querySelector('i');
            icon.style.transform = 'scale(1.2)';
            icon.style.color = '#4361ee';
        });
        
        badge.addEventListener('mouseleave', () => {
            const icon = badge.querySelector('i');
            icon.style.transform = 'scale(1)';
            icon.style.color = '';
        });
    });

    // Dropdown Menu - Versión mejorada
    const userMenus = document.querySelectorAll('.user-menu');
    const body = document.body;
    
    // Crear overlay para móviles
    const overlay = document.createElement('div');
    overlay.className = 'dropdown-overlay';
    body.appendChild(overlay);
    
    // Función para cerrar todos los dropdowns
    function closeAllDropdowns() {
        userMenus.forEach(menu => {
            const dropdown = menu.querySelector('.dropdown');
            if (dropdown) dropdown.classList.remove('active');
        });
        overlay.classList.remove('active');
    }
    
    // Manejo del dropdown
    userMenus.forEach(menu => {
        const dropdown = menu.querySelector('.dropdown');
        
        if (!dropdown) return;
        
        // Para escritorio (hover)
        menu.addEventListener('mouseenter', () => {
            if (window.innerWidth > 768) {
                closeAllDropdowns();
                dropdown.classList.add('active');
            }
        });
        
        menu.addEventListener('mouseleave', () => {
            if (window.innerWidth > 768) {
                setTimeout(() => {
                    if (!dropdown.matches(':hover') && !menu.matches(':hover')) {
                        dropdown.classList.remove('active');
                    }
                }, 300);
            }
        });
        
        // Para móviles (click)
        menu.addEventListener('click', (e) => {
            if (window.innerWidth <= 768) {
                e.stopPropagation();
                const isActive = dropdown.classList.contains('active');
                closeAllDropdowns();
                
                if (!isActive) {
                    dropdown.classList.add('active');
                    overlay.classList.add('active');
                }
            }
        });
    });
    
    // Cerrar al hacer clic fuera o en el overlay
    overlay.addEventListener('click', closeAllDropdowns);
    document.addEventListener('click', (e) => {
        if (window.innerWidth <= 768 && !e.target.closest('.user-menu')) {
            closeAllDropdowns();
        }
    });
    
    // Cerrar al hacer scroll en móviles
    window.addEventListener('scroll', () => {
        if (window.innerWidth <= 768) {
            closeAllDropdowns();
        }
    });
    
    // Ajustar en redimensionamiento
    window.addEventListener('resize', () => {
        if (window.innerWidth > 768) {
            closeAllDropdowns();
        }
    });

    // Función para inicializar el tooltip de Bootstrap
    function initTooltips() {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }

    // Función para mostrar notificaciones
    function showNotification(message, type = 'success') {
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.innerHTML = `
            <div class="notification-content">
                <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i>
                <span>${message}</span>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.classList.add('show');
        }, 10);
        
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => {
                notification.remove();
            }, 300);
        }, 3000);
    }

    // Función para manejar el preview de imágenes
    function handleImagePreview(input, previewElement) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                previewElement.src = e.target.result;
                previewElement.style.display = 'block';
            }
            
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Manejar preview de imágenes para avatares
    const avatarInput = document.getElementById('avatar');
    const avatarPreview = document.getElementById('avatar-preview');
    
    if (avatarInput && avatarPreview) {
        avatarInput.addEventListener('change', function() {
            handleImagePreview(this, avatarPreview);
        });
    }
    
    // Validación de formularios
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const requiredFields = this.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.classList.add('is-invalid');
                    isValid = false;
                } else {
                    field.classList.remove('is-invalid');
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                showNotification('Por favor, completa todos los campos requeridos', 'error');
            }
        });
    });
    
    // Remover clases de error al escribir
    const inputs = document.querySelectorAll('input, textarea');
    inputs.forEach(input => {
        input.addEventListener('input', function() {
            this.classList.remove('is-invalid');
        });
    });

    // Inicializar tooltips
    initTooltips();
});

// Función para copiar texto al portapapeles
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        showNotification('Copiado al portapapeles', 'success');
    }, function() {
        showNotification('Error al copiar', 'error');
    });
}