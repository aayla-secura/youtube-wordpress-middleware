<?php
/**
 * @package YouTube-Middleware
 */

if (! defined('ABSPATH') || ! isset($enabled_endpoints) || ! is_array($enabled_endpoints) || ! isset($args) || ! is_array($args)) {
    die; // Exit if accessed directly
}

foreach (YouTubeMiddleware::ENDPOINTS as $endpoint_key => $endpoint_label) {
    printf(
        '<label for="%1$s_%2$s"><input type="checkbox" id="%1$s_%2$s" name="%3$s[%4$s][%2$s]" value="1" %5$s /> %6$s</label><br>',
        esc_attr($args['label_for']),
        esc_attr($endpoint_key),
        esc_attr(YouTubeMiddleware::OPTION_NAME),
        esc_attr(YouTubeMiddleware::FIELD_ENABLED_ENDPOINTS),
        checked(1, $enabled_endpoints[ $endpoint_key ] ?? 0, false),
        esc_html($endpoint_label),
    );
}
?>
<p class="description">
  <?php echo __('Check the boxes for the API endpoints you wish to activate.', 'youtube-middleware'); ?>
</p>
