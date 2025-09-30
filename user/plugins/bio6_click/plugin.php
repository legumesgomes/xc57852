<?php
/*
Plugin Name: Bio6.click
Plugin URI: ../user/plugins/bio6_click/admin-page.php
Description: Tema personalizado Tailwind para YOURLS (Bio6.click) — header, footer e administração de permissões por role.
Version: 1.0
Author: Bio6.click
Author URI: https://bio6.click
*/

// Bloqueia acesso direto
if( !defined('YOURLS_ABSPATH') ) {
    die('Acesso negado');
}

// --- Inclui arquivos auxiliares do plugin, se existirem
if ( file_exists( __DIR__ . '/includes/roles.php' ) ) {
    require_once __DIR__ . '/includes/roles.php';
}
if ( file_exists( __DIR__ . '/includes/layout.php' ) ) {
    require_once __DIR__ . '/includes/layout.php';
}

// --- Enfileira assets (CSS / JS) no head
yourls_add_action( 'html_head', 'bio6_click_enqueue_assets' );
function bio6_click_enqueue_assets() {
    // Garante a URL base do site + caminho para assets do plugin
    $plugin_assets = rtrim( YOURLS_SITE, '/' ) . '/user/plugins/bio6_click/assets';
    // CSS
    echo '<link rel="stylesheet" href="' . $plugin_assets . '/css/style.css?v=1" />' . PHP_EOL;
    // JS (defer)
    echo '<script src="' . $plugin_assets . '/js/script.js?v=1" defer></script>' . PHP_EOL;
}

// --- Registra a página administrativa do plugin (aparece como "Página do plugin")
yourls_add_action( 'plugins_loaded', 'bio6_click_register_admin_page' );
function bio6_click_register_admin_page() {
    yourls_register_plugin_page(
        'bio6_click',                      // slug
        'Configurações do Bio6.click',     // link/título
        'bio6_click_admin_page'            // callback
    );
}

// Callback que inclui o admin-page.php dentro do contexto do YOURLS
function bio6_click_admin_page() {
    $file = __DIR__ . '/admin-page.php';
    if ( file_exists( $file ) ) {
        include $file;
    } else {
        echo '<div style="padding:1rem;background:#fee;border:1px solid #fbb;color:#600;">Arquivo admin-page.php não encontrado em ' . htmlspecialchars($file) . '</div>';
    }
}

// --- (Opcional) injeta header personalizado se a função existir no includes/layout.php
if ( function_exists('bio6_click_custom_header') ) {
    // usa hook html_before_body para garantir posição correta
    yourls_add_action( 'html_before_body', 'bio6_click_custom_header' );
}

// --- (Opcional) injeta footer personalizado se a função existir no includes/layout.php
if ( function_exists('bio6_click_custom_footer') ) {
    yourls_add_action( 'html_footer', 'bio6_click_custom_footer' );
}
