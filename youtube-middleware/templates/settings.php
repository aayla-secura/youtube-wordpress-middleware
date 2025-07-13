<?php
/**
 * @package YouTube-Middleware
 */

if ( ! defined( 'ABSPATH' ) ) {
  die; // Exit if accessed directly
}

if ( ! current_user_can( 'manage_options' ) ) {
  wp_die( __( 'You do not have sufficient permissions to access this page.', 'youtube-middleware' ) );
}

?>
<div class="wrap">
  <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
  <form method="post" action="options.php">
    <?php
    settings_fields( self::SETTINGS_GROUP );
    do_settings_sections( self::PAGE_SLUG );
    submit_button();
    ?>
  </form>
</div>
