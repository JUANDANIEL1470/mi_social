<?php

function url($path = '') {
    return BASE_URL . ltrim($path, '/');
}
function construirUrlSocial($redSocial, $valorUsuario) {
    // Si no hay base_url (como Discord o PayPal), usar el valor directamente
    if (empty($redSocial['base_url'])) {
        return $valorUsuario;
    }
    
    // Si ya es una URL completa, no modificarla
    if (filter_var($valorUsuario, FILTER_VALIDATE_URL)) {
        return $valorUsuario;
    }
    
    // Construir URL según la red social
    $url = rtrim($redSocial['base_url'], '/');
    
    // Casos especiales para algunas redes sociales
    switch ($redSocial['nombre']) {
        case 'WhatsApp':
            // WhatsApp espera el número en formato internacional sin +, espacios o guiones
            $numero = preg_replace('/[^0-9]/', '', $valorUsuario);
            return $url . '/' . $numero;
        case 'TikTok':
            // TikTok usa @ en la URL base pero no lo queremos duplicado
            return $url . ltrim($valorUsuario, '@');
        default:
            return $url . '/' . ltrim($valorUsuario, '/');
    }
}

// Función para limpiar datos de entrada
function cleanInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Función para generar un token seguro
function generateToken($length = 32) {
    return bin2hex(random_bytes($length));
}