<?php
/*
Plugin Name: Menu Head Manager
Plugin URI: ../user/plugins/campaign-manager/admin-page.php
Description: Gerenciar quais itens do menu ficam disponíveis para cada role
Version: 1.0
Author: Você
*/

if( !defined( 'YOURLS_ADMIN' ) ) define( 'YOURLS_ADMIN', true );
require_once( dirname( dirname( __DIR__ ) ) . '/includes/load-yourls.php' );

yourls_maybe_require_auth();

// Inclui funções do layout do admin (head/menu/footer)
require_once( YOURLS_ABSPATH . '/admin/plugins.php' );

// Roles
$roles = ['free', 'vip', 'paid', 'admin'];

// Plugins ativos
$active_plugins = yourls_get_option('active_plugins', []);

// Configuração atual
$permissions = yourls_get_option('menu_head_permissions', []);

// Salvar configurações
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_permissions = [];
    foreach($active_plugins as $plugin_file => $meta) {
        foreach($roles as $role) {
            if(isset($_POST['perm'][$plugin_file][$role])) {
                $new_permissions[$plugin_file][$role] = true;
            }
        }
    }
    yourls_update_option('menu_head_permissions', $new_permissions);
    $permissions = $new_permissions;
    yourls_add_notice('Configurações salvas com sucesso!');
}

// Início da página
bio6_html_head('plugins');
bio6_html_menu();
?>

<div class="flex justify-between items-center mb-4">
  <h2 class="text-3xl font-bold text-white">Gerenciar acesso ao menu por role</h2>
</div>

<form method="post">
  <div class="overflow-x-auto shadow border border-slate-700 rounded-lg">
    <table class="min-w-full divide-y divide-slate-700">
      <thead class="bg-slate-800">
        <tr>
          <th class="px-6 py-3 text-left text-sm font-semibold text-slate-200">Plugin</th>
          <?php foreach($roles as $role): ?>
            <th class="px-6 py-3 text-center text-sm font-semibold text-slate-200"><?php echo ucfirst($role) ?></th>
          <?php endforeach; ?>
        </tr>
      </thead>
      <tbody class="bg-slate-900 divide-y divide-slate-700">
        <?php foreach($active_plugins as $plugin_file => $meta): 
          $plugin_name = isset($meta['Name']) ? $meta['Name'] : basename($plugin_file);
        ?>
          <tr>
            <td class="px-6 py-4 text-sm text-slate-100"><?php echo htmlspecialchars($plugin_name); ?></td>
            <?php foreach($roles as $role): 
              $checked = isset($permissions[$plugin_file][$role]) ? 'checked' : '';
            ?>
              <td class="px-6 py-4 text-center">
                <input type="checkbox" name="perm[<?php echo $plugin_file ?>][<?php echo $role ?>]" value="1" <?php echo $checked ?>>
              </td>
            <?php endforeach; ?>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <div class="mt-6">
    <button type="submit" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow">
      Salvar Configurações
    </button>
  </div>
</form>

<?php bio6_html_footer(); ?>
