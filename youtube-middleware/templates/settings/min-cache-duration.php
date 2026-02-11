<?php
/**
 * @package YouTube-Middleware
 */

if (! defined('ABSPATH') || ! isset($min_cache_duration) || ! is_numeric($min_cache_duration) || ! isset($args) || ! is_array($args)) {
    die; // Exit if accessed directly
}

printf(
    '<input type="number" id="%s" name="%s[%s]" value="%s" class="regular-text" placeholder="Minimum allowed cache duration" />',
    esc_attr($args['label_for']),
    esc_attr(YouTubeMiddleware::OPTION_NAME),
    esc_attr(YouTubeMiddleware::FIELD_MIN_CACHE_DURATION),
    esc_attr($min_cache_duration . ''),
);
?>
<p class="description">
  <?php echo __('Enter the minimum allowed cache duration in seconds.', 'youtube-middleware'); ?>
</p>
