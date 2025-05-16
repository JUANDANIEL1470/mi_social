// Efecto de onda para botones
document.addEventListener('DOMContentLoaded', function() {
    const buttons = document.querySelectorAll('.btn, .profile-link, .toggle-password');
    
    buttons.forEach(button => {
        button.addEventListener('click', function(e) {
            const x = e.clientX - e.target.getBoundingClientRect().left;
            const y = e.clientY - e.target.getBoundingClientRect().top;
            
            const ripple = document.createElement('span');
            ripple.classList.add('ripple-effect');
            ripple.style.left = x + 'px';
            ripple.style.top = y + 'px';
            
            this.appendChild(ripple);
            
            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
    });
});

// Desactivar efectos de onda en botones
document.addEventListener('DOMContentLoaded', function() {
    // Desactivamos los efectos de onda
    const buttons = document.querySelectorAll('.btn');
    buttons.forEach(button => {
        button.addEventListener('mousedown', function(e) {
            // No hacemos nada, solo prevenimos el comportamiento anterior
            e.stopPropagation();
        });
    });
});