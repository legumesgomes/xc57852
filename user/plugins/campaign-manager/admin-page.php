<?php
/*
Plugin Name: Campaign Manager
Plugin URI: ../user/plugins/campaign-manager/admin-page.php
Description: Permite associar campanhas (UTM) a links encurtados e visualizar relatórios. Apenas usuários paid e vip podem acessar.
Version: 1.1
Author: bio6.click
Author URI: https://bio6.click/
*/

if (!defined('YOURLS_ABSPATH')) {
    die('No direct call!');
}

// -----------------------------------------------------------------------------
// Criar tabela ao ativar plugin
// -----------------------------------------------------------------------------
yourls_add_action('activated_campaign-manager/plugin.php', 'cm_create_table');

function cm_create_table() {
    global $ydb;
    $table = YOURLS_DB_PREFIX . "link_campaigns";
    $sql = "CREATE TABLE IF NOT EXISTS `$table` (
        `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `keyword` VARCHAR(100) NOT NULL,
        `campaign_name` VARCHAR(191) NOT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    $ydb->fetchAffected($sql);
}

// -----------------------------------------------------------------------------
// Salvar campanha ao criar link
// -----------------------------------------------------------------------------
yourls_add_action('pre_add_new_link', 'cm_save_campaign', 10, 2);

function cm_save_campaign($url, $keyword = null) {
    global $ydb;
    if (isset($_POST['campaign_name']) && $_POST['campaign_name'] !== '') {
        $campaign = trim($_POST['campaign_name']);
        $table = YOURLS_DB_PREFIX . "link_campaigns";
        $sql = "INSERT INTO `$table` (keyword, campaign_name, created_at)
                VALUES (:keyword, :campaign, :created_at)";
        $ydb->fetchAffected($sql, [
            'keyword'    => $keyword,
            'campaign'   => $campaign,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }
}

// -----------------------------------------------------------------------------
// Registrar clique com UTM
// -----------------------------------------------------------------------------
yourls_add_action('redirect_shorturl', 'cm_log_click', 10, 2);

function cm_log_click($keyword, $url) {
    global $ydb;
    $table = YOURLS_DB_PREFIX . "link_campaigns";
    $campaign = isset($_GET['utm_campaign']) ? $_GET['utm_campaign'] : null;

    if ($campaign) {
        $sql = "INSERT INTO `$table` (keyword, campaign_name, created_at)
                VALUES (:keyword, :campaign, :created_at)";
        $ydb->fetchAffected($sql, [
            'keyword'    => $keyword,
            'campaign'   => $campaign,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }
}

// -----------------------------------------------------------------------------
// Adicionar campo "Campanha" no formulário de encurtar
// -----------------------------------------------------------------------------
yourls_add_action('html_addnew', 'cm_add_campaign_field');

function cm_add_campaign_field() {
    ?>
    <tr>
        <td><label for="campaign_name">Campanha (UTM):</label></td>
        <td><input type="text" id="campaign_name" name="campaign_name" class="text" placeholder="Ex: promocao_blackfriday"></td>
    </tr>
    <?php
}
