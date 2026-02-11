<?php
/**
 * @package YouTube-Middleware
 */

if (! defined('ABSPATH') || ! isset($allowed_params) || ! is_array($allowed_params) || ! isset($args) || ! is_array($args)) {
    die; // Exit if accessed directly
}

?>
<div id="youtube-middleware-allowed-params-wrapper">
  <?php if (! empty($allowed_params)) : ?>
  <?php foreach ($allowed_params as $index => $param) : ?>
  <div class="youtube-middleware-allowed-param">
    <input type="text" name="<?php echo esc_attr(YouTubeMiddleware::OPTION_NAME); ?>[<?php echo esc_attr(YouTubeMiddleware::FIELD_ALLOWED_PARAMETERS); ?>][<?php echo esc_attr($index); ?>]" value="<?php echo esc_attr($param); ?>" placeholder="Parameter" class="regular-text" />
    <button type="button" class="button youtube-middleware-remove-allowed-param">Remove</button>
  </div>
  <?php endforeach; ?>
  <?php endif; ?>
</div>

<button type="button" id="youtube-middleware-add-allowed-param" class="button button-secondary">Add New Parameter</button>
<p class="description"><?php echo __('Add a list of parameters that are allowed to be passed through. If none, all parameters are sent to the YouTube API. Note that "key" is always allowed.', 'youtube-middleware'); ?></p>

<script type="text/javascript">
  document.addEventListener('DOMContentLoaded', function() {
    var wrapper = document.getElementById('youtube-middleware-allowed-params-wrapper');
    var addParamButton = document.getElementById('youtube-middleware-add-allowed-param');
    var paramIndex = <?php echo count($allowed_params); ?>; // Initialize with current count

    addParamButton.addEventListener('click', function() {
        var newParamHtml = `
            <div class="youtube-middleware-allowed-param">
                <input type="text" name="<?php echo esc_attr(YouTubeMiddleware::OPTION_NAME); ?>[<?php echo esc_attr(YouTubeMiddleware::FIELD_ALLOWED_PARAMETERS); ?>][${paramIndex}]" value="" placeholder="Parameter" class="regular-text" />
                <button type="button" class="button youtube-middleware-remove-allowed-param">Remove</button>
            </div>
        `;
        wrapper.insertAdjacentHTML('beforeend', newParamHtml);
        paramIndex++;
    });

    // Event delegation for dynamically added remove buttons
    wrapper.addEventListener('click', function(event) {
        if (event.target && event.target.classList.contains('youtube-middleware-remove-allowed-param')) {
            event.target.closest('.youtube-middleware-allowed-param').remove();
        }
    });
  });
</script>
