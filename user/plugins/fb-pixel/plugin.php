<?php
/*
Plugin Name: Facebook Pixel per User
Plugin URI:  ../user/plugins/fb-pixel/admin-page.php
Description: Permite que cada usuário configure seu próprio Pixel do Facebook e insere automaticamente o código em suas páginas.
Version:     1.1
Author:      bio6.click
Author URI:  https://bio6.click/
*/

if (!defined('YOURLS_ABSPATH')) die();

// Hook para injetar o pixel no head
yourls_add_action('html_head', 'fb_pixel_inject_fb');

function fb_pixel_inject_fb() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $ydb = yourls_get_db();
    $user_id = $_SESSION['ua_user_id'] ?? null;

    // Se não houver usuário logado, não injeta nada
    if (!$user_id) {
        return;
    }

    // Buscar pixel do usuário pelo ID
    $pixel_id = $ydb->fetchValue(
        "SELECT pixel_id FROM yourls_users_custom WHERE id = :id",
        [ 'id' => $user_id ]
    );

    if ($pixel_id) {
        echo <<<HTML
<!-- Facebook Pixel Code -->
<script>
  !function(f,b,e,v,n,t,s)
  {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
  n.callMethod.apply(n,arguments):n.queue.push(arguments)};
  if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
  n.queue=[];t=b.createElement(e);t.async=!0;
  t.src=v;s=b.getElementsByTagName(e)[0];
  s.parentNode.insertBefore(t,s)}(window, document,'script',
  'https://connect.facebook.net/en_US/fbevents.js');
  fbq('init', '{$pixel_id}');
  fbq('track', 'PageView');
</script>
<noscript>
  <img height="1" width="1" style="display:none"
  src="https://www.facebook.com/tr?id={$pixel_id}&ev=PageView&noscript=1"/>
</noscript>
<!-- End Facebook Pixel Code -->
HTML;
    }
}
