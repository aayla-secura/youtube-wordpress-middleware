<?php
/**
 * @package YouTube-Middleware
 */

if (! defined('ABSPATH') || ! isset($api_key) || ! is_string($api_key) || ! isset($args) || ! is_array($args)) {
    die; // Exit if accessed directly
}

printf(
    '<input type="text" id="%s" name="%s[%s]" value="%s" class="regular-text" placeholder="Enter your YouTube Data API v3 key" />',
    esc_attr($args['label_for']),
    esc_attr(YouTubeMiddleware::OPTION_NAME),
    esc_attr(YouTubeMiddleware::FIELD_API_KEY),
    esc_attr($api_key),
);
?>
<p class="description">
  <?php echo __('Enter your Google Cloud Platform YouTube Data API v3 key here.', 'youtube-middleware'); ?>
</p>
