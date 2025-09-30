<?php
/*
Plugin Name: MH Head Menu
Plugin URI: https://seusite.com
Description: Plugin para injetar menu de cabeçalho (head menu) em todas as páginas YOURLS com lógica de visibilidade
Version: 1.0
Author: Seu Nome
*/

// Hook que roda no head HTML (antes do conteúdo) em páginas admin e pages
yourls_add_action( 'html_head', 'mh_include_menu' );

// Hook para enfileirar os assets CSS/JS
yourls_add_action( 'html_head', 'mh_enqueue_assets' );

function mh_include_menu() {
    // define caminho absoluto para o include
    $path = YOURLS_ABSPATH . 'includes/menu-head.inc.php';
    if ( file_exists( $path ) ) {
        include $path;
    }
}

function mh_enqueue_assets() {
    // URL base
    $base = yourls_site_url();
    // caminhos relativos ao servidor — ajuste se estiver em subdiretório
    echo "<link rel=\"stylesheet\" href=\"{$base}/includes/menu-head-style.css\" />\n";
    echo "<script src=\"{$base}/includes/menu-head-script.js\"></script>\n";
}
