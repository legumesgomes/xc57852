<?php
if (!defined('YOURLS_ABSPATH')) {
    die('No direct call!');
}

global $ydb;
$table = YOURLS_DB_PREFIX . 'plugin_access';

// Roles disponíveis
$roles = ['free','paid','vip','admin'];

// Processa o POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['plugins'])) {
    // Limpa registros existentes
    $ydb->query("TRUNCATE TABLE `$table`");

    foreach ($_POST['plugins'] as $plugin => $plugin_roles) {
        foreach ($plugin_roles as $role => $checked) {
            if ($checked == '1') {
                $ydb->fetchAffected(
                    "INSERT INTO `$table` (plugin, role) VALUES (:plugin, :role)",
                    ['plugin' => $plugin, 'role' => $role]
                );
            }
        }
    }
    echo "<div class='mb-4 p-4 bg-green-500/10 border border-green-500 text-green-300 rounded'>✔ Configurações salvas</div>";
}

// Carrega permissões atuais
$current_access = [];
$rows = $ydb->fetchObjects("SELECT * FROM `$table`");
foreach ($rows as $r) {
    $current_access[$r->plugin][$r->role] = true;
}

// Lista de plugins instalados
$plugins = yourls_get_plugins();

?>
<h1 class="text-2xl font-bold mb-6">🔧 Gerenciar Acesso de Plugins por Role</h1>

<form method="post">
    <table class="border-collapse w-full text-sm">
        <thead>
            <tr class="bg-slate-800 text-gray-200">
                <th class="p-3 text-left">Plugin</th>
                <?php foreach ($roles as $role): ?>
                    <th class="p-3 text-center"><?php echo ucfirst($role); ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($plugins as $plugin => $meta): ?>
                <tr class="border-b border-slate-700 hover:bg-slate-800/50">
                    <td class="p-3">
                        <strong><?php echo $meta['Name']; ?></strong><br>
                        <span class="text-xs text-gray-400"><?php echo $plugin; ?></span>
                    </td>
                    <?php foreach ($roles as $role): ?>
                        <td class="p-3 text-center">
                            <input type="checkbox" name="plugins[<?php echo $plugin; ?>][<?php echo $role; ?>]" value="1"
                                <?php echo isset($current_access[$plugin][$role]) ? 'checked' : ''; ?>>
                        </td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="mt-6">
        <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 rounded text-white">Salvar</button>
    </div>
</form>
