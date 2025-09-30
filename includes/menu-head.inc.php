<?php

if ( !defined('YOURLS_ABSPATH') ) {

    die('Forbidden');

}



// Verifica se usuário é admin

$is_admin = function_exists('yourls_is_admin') && yourls_is_admin();



// Monta links

$links = [];

$links[] = ['label' => 'Dashboard', 'url' => yourls_admin_url('index.php')];

if ( $is_admin ) {

    $links[] = ['label' => 'Ferramentas', 'url' => yourls_admin_url('tools.php')];

    $links[] = ['label' => 'Plugins', 'url' => yourls_admin_url('plugins.php')];

}

$links[] = ['label' => 'Ver site', 'url' => yourls_site_url()];



// Render do menu

?>

<nav class="mh-nav">

  <ul class="mh-menu-list">

    <?php foreach ($links as $link): ?>

      <li class="mh-menu-item">

        <a href="<?php echo htmlspecialchars($link['url']); ?>">

          <?php echo htmlspecialchars($link['label']); ?>

        </a>

      </li>

    <?php endforeach; ?>

  </ul>

</nav>

<?php

?>

aaaaaaaaaaaaaaaaaaaaaaaaaaaaaa