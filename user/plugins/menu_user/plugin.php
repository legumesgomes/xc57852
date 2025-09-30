<?php
/*
Plugin Name: Plugin Access
Plugin URI:  https://bio6.click/admin/plugins.php?page=plugin_access
Description: Gerencia o acesso a plugins por role (free, paid, vip, admin)
Version:     1.0
Author:      Bio6
Author URI:  https://bio6.click
*/

if (!defined('YOURLS_ABSPATH')) {
    die('No direct call!');
}

// Cria a tabela no primeiro load, se não existir
yourls_add_action('activated_plugin_access_manager', 'pam_create_table');
function pam_create_table() {
    global $ydb;
    $table = YOURLS_DB_PREFIX . 'plugin_access';
    $ydb->query("CREATE TABLE IF NOT EXISTS `$table` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `plugin` VARCHAR(191) NOT NULL,
        `role` VARCHAR(50) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
}

// Registra a página no admin
yourls_add_action('plugins_loaded', 'pam_register_page');
function pam_register_page() {
    yourls_register_plugin_page('plugin_access', 'Gerenciar Acesso de Plugins', 'pam_render_admin_page');
}

// Renderiza a página
function pam_render_admin_page() {
    include __DIR__ . '/admin-page.php';
}
