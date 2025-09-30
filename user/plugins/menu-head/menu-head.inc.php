<?php
if ( !defined('YOURLS_ABSPATH') ) {
    die('Forbidden');
}
?>
<header class="mh-header">
  <div class="mh-logo">
    <a href="<?php echo yourls_admin_url(); ?>">
      <img src="https://bio6.click/user/plugins/yourls-white-label-main/uploads/1758761163_68d490cb530b3.png" alt="Logo" />
    </a>
  </div>
  <nav class="mh-nav">
    <ul class="mh-menu-list">
      <?php
      // Reaproveita os links do YOURLS (respeita permissões)
      yourls_list_admin_links();
      ?>
    </ul>
  </nav>
</header>
