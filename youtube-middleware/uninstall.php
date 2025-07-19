<?php

/**
 * Uninstall hook for YouTube Middleware plugin.
 *
 * This file is called when the plugin is uninstalled.
 * It ensures that all plugin-related data is removed from the database.
 *
 * @package YouTube-Middleware
 */

// Exit if accessed directly or if not called during uninstall.
if (! defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

delete_option('youtube_middleware_options');
