<?php
define( 'YOURLS_ADMIN', true );
require_once( dirname( __DIR__ ).'/includes/load-yourls.php' );
yourls_maybe_require_auth();

// **********************************************************************************
// * INÍCIO: NOVAS FUNÇÕES PARA GERAR O LAYOUT MODERNO
// * Reutilizamos as mesmas funções do arquivo admin/index.php
// **********************************************************************************

/**
 * Gera o <head> e o início da página com o novo layout
 */
function bio6_html_head($context = 'admin') {
    $brand_name = yourls_get_option('wl_brand_name', 'bio6.click');
    ?>
    <!DOCTYPE html>
    <html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Gerenciar Plugins - <?php echo htmlspecialchars($brand_name); ?></title>
        <script src="https://cdn.tailwindcss.com"></script>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
        <style>
            body { font-family: 'Inter', sans-serif; }
            /* Estilos customizados para a tabela de admin do YOURLS */
            #main_table { width: 100%; border-collapse: collapse; background-color: #1e293b; border-radius: 0.75rem; overflow: hidden; }
            #main_table thead { background-color: rgba(30, 41, 59, 0.5); }
            #main_table th, #main_table td { padding: 12px 16px; text-align: left; border-bottom: 1px solid #334155; }
            #main_table th { font-weight: 600; color: #cbd5e1; }
            #main_table tbody tr { transition: background-color 0.2s ease-in-out; }
            #main_table tbody tr:hover { background-color: #334155; }
            #main_table tbody tr:last-child td { border-bottom: none; }
            #main_table td { color: #94a3b8; vertical-align: middle; }
            #main_table td strong, #main_table td a { color: #e2e8f0; font-weight: 500; text-decoration: none; }
             #main_table td a:hover { text-decoration: underline; }
            #main_table td small { color: #64748b; }
            #main_table td.actions a { color: #60a5fa; }

            /* Estilos específicos para a tabela de plugins */
            #main_table tr.active { background-color: rgba(34, 197, 94, 0.05); }
            #main_table tr.active .plugin_name { border-left: 3px solid #22c55e; padding-left: 13px; }
            #main_table tr.inactive { background-color: rgba(100, 116, 139, 0.05); }

            /* Estilos para o botão de filtro de plugins */
            #toggle_plugins {
                display: inline-block;
                background-color: #334155;
                color: #cbd5e1;
                padding: 0.25rem 0.75rem;
                border-radius: 9999px;
                font-size: 0.75rem;
                font-weight: 500;
                margin-left: 0.5rem;
                cursor: pointer;
                transition: background-color 0.2s;
            }
            #toggle_plugins:hover { background-color: #475569; }
        </style>
    </head>
    <body class="bg-slate-900 text-gray-200 antialiased">
    <div class="min-h-screen">
    <?php
}

/**
 * Gera o menu de navegação superior
 */
function bio6_html_menu() {
    $brand_name = yourls_get_option('wl_brand_name', 'bio6.click');
    $user = YOURLS_USER;
    ?>
    <header class="bg-slate-800/50 backdrop-blur-sm border-b border-slate-700 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center">
                    <a href="<?php echo yourls_admin_url(); ?>" class="text-2xl font-bold text-white"><?php echo htmlspecialchars($brand_name); ?></a>
                </div>
                <nav class="flex items-center space-x-4">
                    <a href="<?php echo yourls_admin_url(); ?>" class="text-gray-300 hover:bg-slate-700 hover:text-white px-3 py-2 rounded-md text-sm font-medium">Painel</a>
                    <a href="<?php echo yourls_admin_url('tools.php'); ?>" class="text-gray-300 hover:bg-slate-700 hover:text-white px-3 py-2 rounded-md text-sm font-medium">Ferramentas</a>
                    <a href="<?php echo yourls_admin_url('plugins.php'); ?>" class="text-gray-300 hover:bg-slate-700 hover:text-white px-3 py-2 rounded-md text-sm font-medium">Plugins</a>

                    <?php
                    // Aqui o plugin injeta os itens dos plugins ativos
                    // Chamada padrão para plugins externos injeterem seus itens no menu
                    yourls_do_action('bio6_menu_items');
                    ?>

                    <span class="text-gray-400">|</span>
                    <span class="text-gray-300 text-sm">Olá, <?php echo $user; ?></span>
                    <a href="?action=logout" class="text-gray-300 hover:text-white text-sm font-medium" title="Sair">[Sair]</a>
                </nav>
            </div>
        </div>
    </header>
    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <?php
}

/**
 * Gera o rodapé da página
 */
function bio6_html_footer() {
    ?>
    </main>
    <footer class="bg-slate-900 text-gray-500 py-6 text-center border-t border-slate-800 mt-8">
        <p>Powered by <a href="http://yourls.org/" class="text-blue-500 hover:underline">YOURLS</a></p>
    </footer>
    </div>
    <?php
}

// **********************************************************************************
// * FIM: NOVAS FUNÇES DE LAYOUT
// **********************************************************************************


// [INÍCIO DO CÓDIGO ORIGINAL DO YOURLS - LÓGICA MANTIDA]

// Handle plugin administration pages
if( isset( $_GET['page'] ) && !empty( $_GET['page'] ) ) {
	yourls_plugin_admin_page( $_GET['page'] );
    die();
}

// Handle activation/deactivation of plugins
if( isset( $_GET['action'] ) ) {
	yourls_verify_nonce( 'manage_plugins', $_REQUEST['nonce'] ?? '');
	if(isset( $_GET['plugin'] ) && yourls_is_a_plugin_file(YOURLS_PLUGINDIR . '/' . $_GET['plugin'] . '/plugin.php') ) {
		switch( $_GET['action'] ) {
			case 'activate':
				$result = yourls_activate_plugin( $_GET['plugin'].'/plugin.php' );
				if( $result === true ) {
                    yourls_redirect(yourls_admin_url('plugins.php?success=activated'), 302);
                    exit();
                }
				break;
			case 'deactivate':
				$result = yourls_deactivate_plugin( $_GET['plugin'].'/plugin.php' );
				if( $result === true ) {
                    yourls_redirect(yourls_admin_url('plugins.php?success=deactivated'), 302);
                    exit();
                }
				break;
			default:
				$result = yourls__( 'Unsupported action' );
				break;
		}
	} else {
		$result = yourls__( 'No plugin specified, or not a valid plugin' );
	}
	yourls_add_notice( $result );
}

// Handle message upon successful (de)activation
if( isset( $_GET['success'] ) && ( ( $_GET['success'] == 'activated' ) OR ( $_GET['success'] == 'deactivated' ) ) ) {
	if( $_GET['success'] == 'activated' ) {
		$message = yourls__( 'Plugin has been activated' );
	} elseif ( $_GET['success'] == 'deactivated' ) {
		$message = yourls__( 'Plugin has been deactivated' );
	}
	yourls_add_notice( $message );
}


// [FIM DO CÓDIGO ORIGINAL DO YOURLS]


// A PARTIR DAQUI, SUBSTITUÍMOS AS CHAMADAS `yourls_html_*` PELAS NOVAS FUNÇÕES `bio6_*`
bio6_html_head( 'plugins');
bio6_html_menu();
?>
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-3xl font-bold text-white"><?php yourls_e( 'Plugins' ); ?></h2>
    </div>

	<?php
	$plugins = (array)yourls_get_plugins();
	uasort( $plugins, 'yourls_plugins_sort_callback' );

	$count = count( $plugins );
	$plugins_count = sprintf( yourls_n( '%s plugin', '%s plugins', $count ), $count );
	$count_active = yourls_has_active_plugins();
	?>

	<div id="plugin_summary" class="mb-6 p-4 bg-slate-800 rounded-lg text-sm text-gray-300">
        <?php yourls_se( 'Você tem <strong>%1$s</strong> instalados, e <strong class="text-white">%2$s</strong> ativados', $plugins_count, $count_active ); ?>
    </div>

	<table id="main_table" class="tblSorter" cellpadding="0" cellspacing="1">
	<thead>
		<tr>
			<th><?php yourls_e( 'Plugin' ); ?></th>
			<th><?php yourls_e( 'Versão' ); ?></th>
			<th><?php yourls_e( 'Descrião' ); ?></th>
			<th><?php yourls_e( 'Autor' ); ?></th>
			<th><?php yourls_e( 'Ação' ); ?></th>
		</tr>
	</thead>
	<tbody>
	<?php

	$nonce = yourls_create_nonce( 'manage_plugins' );

	foreach( $plugins as $file=>$plugin ) {
		$fields = array(
			'name'       => 'Plugin Name',
			'uri'        => 'Plugin URI',
			'desc'       => 'Description',
			'version'    => 'Version',
			'author'     => 'Author',
			'author_uri' => 'Author URI'
		);
		foreach( $fields as $field=>$value ) {
			if( isset( $plugin[ $value ] ) ) {
				$data[ $field ] = $plugin[ $value ];
			} else {
				$data[ $field ] = yourls__('(sem info)');
                if( in_array( $field, array('uri', 'author_uri') ) ) {
                    $data[$field] = '#' . $data[$field];
                }
			}
			unset( $plugin[$value] );
		}

		$plugindir = trim( dirname( $file ), '/' );

		if( yourls_is_active_plugin( $file ) ) {
			$class = 'active';
			$action_url = yourls_nonce_url( 'manage_plugins', yourls_add_query_arg( array('action' => 'deactivate', 'plugin' => $plugindir ), yourls_admin_url('plugins.php') ) );
			$action_anchor = yourls__( 'Desativar' );
		} else {
			$class = 'inactive';
			$action_url = yourls_nonce_url( 'manage_plugins', yourls_add_query_arg( array('action' => 'activate', 'plugin' => $plugindir ), yourls_admin_url('plugins.php') ) );
			$action_anchor = yourls__( 'Ativar' );
		}

		if( $plugin ) {
			foreach( $plugin as $extra_field=>$extra_value ) {
				$data['desc'] .= "<br/>\n<em>$extra_field</em>: $extra_value";
				unset( $plugin[$extra_value] );
			}
		}

		$data['desc'] .= '<br/><small>' . yourls_s( 'localização: %s', $file) . '</small>';

		printf( "<tr class='plugin %s'><td class='plugin_name'><a href='%s'>%s</a></td><td class='plugin_version'>%s</td><td class='plugin_desc'>%s</td><td class='plugin_author'><a href='%s'>%s</a></td><td class='plugin_actions actions'><a href='%s'>%s</a></td></tr>",
			$class, $data['uri'], $data['name'], $data['version'], $data['desc'], $data['author_uri'], $data['author'], $action_url, $action_anchor
			);
	}
	?>
	</tbody>
	</table>

    <div class="mt-8 p-6 bg-slate-800 rounded-lg">
        <p class="text-sm text-gray-300"><?php yourls_e( 'Se algo der errado após ativar um plugin e você não conseguir usar o YOURLS ou acessar esta página, simplesmente renomeie ou delete a pasta do plugin, ou renomeie o arquivo do plugin para algo diferente de <code>plugin.php</code>.' ); ?></p>
        <h3 class="text-xl font-bold text-white mt-4"><?php yourls_e( 'Mais plugins' ); ?></h3>
        <p class="mt-2 text-sm text-gray-300"><?php yourls_se( 'Para mais plugins, visite a <a href="http://yourls.org/awesome" class="text-blue-400 hover:underline">Lista Oficial de Plugins</a>.' ); ?></p>
    </div>

	<script type="text/javascript">
	// Deixamos a lógica JS intacta, mas o estilo do botão de filtro é controlado via CSS
	<?php if ($count_active) { ?>
	$('#plugin_summary').append('<span id="toggle_plugins">Filtrar Ativos</span>');
	$('#toggle_plugins').attr('title', '<?php echo yourls_esc_attr__( 'Mostrar/esconder plugins inativos' ); ?>')
		.click(function(){
			$('#main_table tr.inactive').toggle();
		});
	<?php } ?>
	</script>

<?php 
bio6_html_footer(); 
?>