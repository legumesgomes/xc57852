<?php
if( !defined('YOURLS_ABSPATH') ) die();

// DEBUG: sempre mostra esta linha
echo "<h1>Custom Header – Gestão de Menu</h1>";

// Só para ver se a conexão ao DB está ok
try {
    $ydb = yourls_get_db();
    echo "<p>Banco conectado.</p>";
} catch(Exception $e) {
    echo "<p>Erro DB: " . $e->getMessage() . "</p>";
}

// Listar plugins carregados
$plugins = (array) yourls_get_plugins();
if(!$plugins) {
    echo "<p>Nenhum plugin encontrado.</p>";
} else {
    echo "<ul>";
    foreach($plugins as $file=>$info) {
        $name = isset($info['Plugin Name']) ? $info['Plugin Name'] : $file;
        echo "<li>".htmlspecialchars($name)."</li>";
    }
    echo "</ul>";
}
