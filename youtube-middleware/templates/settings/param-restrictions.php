<?php
/**
 * @package YouTube-Middleware
 */

if ( ! defined( 'ABSPATH' ) || ! isset( $param_restrictions ) || ! isset( $args ) ) {
  die; // Exit if accessed directly
}

?>
<div id="youtube-middleware-param-pairs-wrapper">
  <?php if ( ! empty( $param_restrictions ) ) : ?>
  <?php foreach ( $param_restrictions as $index => $pair ) : ?>
  <div class="youtube-middleware-param-pair">
    <input type="text" name="<?php echo esc_attr( YouTubeMiddleware::OPTION_NAME ); ?>[<?php echo esc_attr( YouTubeMiddleware::FIELD_PARAM_KEY_VALUE_PAIRS ); ?>][<?php echo esc_attr( $index ); ?>][key]" value="<?php echo esc_attr( $pair['key'] ); ?>" placeholder="Key" class="regular-text" />
    <input type="text" name="<?php echo esc_attr( YouTubeMiddleware::OPTION_NAME ); ?>[<?php echo esc_attr( YouTubeMiddleware::FIELD_PARAM_KEY_VALUE_PAIRS ); ?>][<?php echo esc_attr( $index ); ?>][value]" value="<?php echo esc_attr( $pair['value'] ); ?>" placeholder="Value" class="regular-text" />
    <button type="button" class="button youtube-middleware-remove-pair">Remove</button>
  </div>
  <?php endforeach; ?>
  <?php endif; ?>
</div>

<button type="button" id="youtube-middleware-add-pair" class="button button-secondary">Add New Pair</button>
<p class="description"><?php echo __( 'Define parameter key-value restrictions. Value is a comma-separated list of allowed values for each parameter. Click "Add New Pair" to add more.', 'youtube-middleware' ); ?></p>

<script type="text/javascript">
  document.addEventListener('DOMContentLoaded', function() {
    var wrapper = document.getElementById('youtube-middleware-param-pairs-wrapper');
    var addPairButton = document.getElementById('youtube-middleware-add-pair');
    var pairIndex = <?php echo count( $param_restrictions ); ?>; // Initialize with current count

    addPairButton.addEventListener('click', function() {
        var newPairHtml = `
            <div class="youtube-middleware-param-pair">
                <input type="text" name="<?php echo esc_attr( YouTubeMiddleware::OPTION_NAME ); ?>[<?php echo esc_attr( YouTubeMiddleware::FIELD_PARAM_KEY_VALUE_PAIRS ); ?>][${pairIndex}][key]" value="" placeholder="Key" class="regular-text" />
                <input type="text" name="<?php echo esc_attr( YouTubeMiddleware::OPTION_NAME ); ?>[<?php echo esc_attr( YouTubeMiddleware::FIELD_PARAM_KEY_VALUE_PAIRS ); ?>][${pairIndex}][value]" value="" placeholder="Value" class="regular-text" />
                <button type="button" class="button youtube-middleware-remove-pair">Remove</button>
            </div>
        `;
        wrapper.insertAdjacentHTML('beforeend', newPairHtml);
        pairIndex++;
    });

    // Event delegation for dynamically added remove buttons
    wrapper.addEventListener('click', function(event) {
        if (event.target && event.target.classList.contains('youtube-middleware-remove-pair')) {
            event.target.closest('.youtube-middleware-param-pair').remove();
        }
    });
  });
</script>
