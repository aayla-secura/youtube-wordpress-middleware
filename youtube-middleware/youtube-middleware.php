<?php

/**
 * Plugin Name:       YouTube Middleware
 * Plugin URI:        https://github.com/aayla-secura/youtube-wordpress-middleware
 * Version:           1.0.1
 * Author:            Aayla Secura
 * Author URI:        https://github.com/aayla-secura/
 * Requires at least: 6.5
 * Requires PHP:      7.4
 * Tested up to:      6.8.1
 *
 * @package YouTube-Middleware
 */

if (! defined('ABSPATH')) {
    exit;
}

add_action(
    'init',
    function () {
        YouTubeMiddleware::init();
    },
);

/**
 * Main singleton class.
 */
class YouTubeMiddleware
{
    public const SETTINGS_GROUP = 'youtube_middleware_settings_group';
    public const OPTION_NAME = 'youtube_middleware_options';
    public const FIELD_API_KEY = 'api_key';
    public const FIELD_ENABLED_ENDPOINTS = 'enabled_endpoints';
    public const FIELD_ALLOWED_PARAMETERS = 'allowed_parameters';
    public const FIELD_PARAM_KEY_VALUE_PAIRS = 'param_key_value_pairs';
    public const PAGE_SLUG = 'youtube-middleware';
    public const ENDPOINTS = [
      'captions' => 'Captions: list',
      'channelSections' => 'ChannelSections: list',
      'channels' => 'Channels: list',
      'comments' => 'Comments: list',
      'commentThreads' => 'CommentThreads: list',
      'playlistItems' => 'PlaylistItems: list',
      'playlists' => 'Playlists: list',
      'search' => 'Search: list',
      'videoCategories' => 'VideoCategories: list',
      'videos' => 'Videos: list',
    ];

    /**
     * Stores the single instance of the class.
     *
     * @var YouTubeMiddleware|null
     */
    private static $instance = null;

    /**
     * @return void
     */
    public static function init()
    {
        if (! self::$instance instanceof self) {
            self::$instance = new self();
        }
    }

    /**
     * Private constructor to prevent direct instantiation.
     */
    private function __construct()
    {
        add_action('admin_menu', fn () => $this->add_admin_menu());
        add_action('admin_init', fn () => $this->register_settings());
        add_action('rest_api_init', fn () => $this->register_endpoints());
    }

    /**
     * Adds the plugin settings page to the WordPress admin menu.
     *
     * @return void
     */
    private function add_admin_menu()
    {
        add_menu_page(
            'YouTube Middleware Settings', // Page title
            'YouTube Middleware', // Menu title
            'manage_options', // Capability required to access
            self::PAGE_SLUG, // Menu slug
            fn () => $this->display_settings_page(), // Callback function to render the page
            'dashicons-youtube', // Icon URL or Dashicon
            6 // Position in the menu
        );
    }

    /**
     * Registers the plugin settings, sections, and fields.
     *
     * @return void
     */
    private function register_settings()
    {
        register_setting(
            self::SETTINGS_GROUP,
            self::OPTION_NAME,
            fn ($input) => $this->sanitize_settings_callback($input)
        );

        add_settings_section(
            'youtube_middleware_general_section',
            'General Settings',
            fn () => $this->general_section_callback(),
            self::PAGE_SLUG
        );

        add_settings_field(
            self::FIELD_API_KEY,
            'YouTube API Key',
            fn ($args) => $this->api_key_callback($args),
            self::PAGE_SLUG,
            'youtube_middleware_general_section',
            [
            'label_for' => self::FIELD_API_KEY,
            'class' => 'youtube-middleware-api-key-field',
      ]
        );

        add_settings_field(
            self::FIELD_ENABLED_ENDPOINTS,
            'Enabled API Endpoints',
            fn ($args) => $this->enabled_endpoints_callback($args),
            self::PAGE_SLUG,
            'youtube_middleware_general_section',
            [
            'label_for' => self::FIELD_ENABLED_ENDPOINTS,
            'class' => 'youtube-middleware-enabled-endpoints-field',
      ]
        );

        add_settings_field(
            self::FIELD_ALLOWED_PARAMETERS,
            'Allowed Parameters',
            fn ($args) => $this->allowed_parameters_callback($args),
            self::PAGE_SLUG,
            'youtube_middleware_general_section',
            [
            'label_for' => self::FIELD_ALLOWED_PARAMETERS,
            'class' => 'youtube-middleware-allowed-parameters-field',
      ]
        );

        add_settings_field(
            self::FIELD_PARAM_KEY_VALUE_PAIRS,
            'Parameter Restrictions',
            fn ($args) => $this->param_key_value_pairs_callback($args),
            self::PAGE_SLUG,
            'youtube_middleware_general_section',
            [
            'label_for' => self::FIELD_PARAM_KEY_VALUE_PAIRS,
            'class' => 'youtube-middleware-param-key-value-pairs-field',
      ]
        );
    }

    /**
     * Renders the description for the General Settings section.
     *
     * @return void
     */
    private function general_section_callback()
    {
        // echo '<p>General settings.</p>';
    }

    /**
     * Renders the input field for the YouTube API Key.
     *
     * @param array $args Arguments passed from add_settings_field.
     * @return void
     */
    private function api_key_callback($args)
    {
        $api_key = $this->get_api_key();
        include $this->get_plugin_path('templates/settings/api-key.php');
    }

    /**
     * Renders the checkboxes for enabled API endpoints.
     *
     * @param array $args Arguments passed from add_settings_field.
     * @return void
     */
    private function enabled_endpoints_callback($args)
    {
        $enabled_endpoints = $this->get_enabled_endpoints();
        include $this->get_plugin_path('templates/settings/enabled-endpoints.php');
    }

    /**
     * Renders the input fields for user-defined allowed parameters.
     *
     * @param array $args Arguments passed from add_settings_field.
     * @return void
     */
    private function allowed_parameters_callback($args)
    {
        $allowed_params = $this->get_allowed_parameters();
        include $this->get_plugin_path('templates/settings/allowed-params.php');
    }

    /**
     * Renders the input fields for user-defined key-value pairs.
     *
     * @param array $args Arguments passed from add_settings_field.
     * @return void
     */
    private function param_key_value_pairs_callback($args)
    {
        $param_restrictions = $this->get_param_key_value_pairs();
        include $this->get_plugin_path('templates/settings/param-restrictions.php');
    }

    /**
     * Sanitization callback for the plugin settings.
     * This function is called when the settings are saved.
     *
     * @param array $input The unsanitized array of input values from the form.
     * @return array The sanitized array of values.
     */
    private function sanitize_settings_callback($input)
    {
        $new_input = [];

        // Sanitize the API key field
        if (isset($input[ self::FIELD_API_KEY ])) {
            $new_input[ self::FIELD_API_KEY ] = sanitize_text_field($input[ self::FIELD_API_KEY ]);
        }

        // Sanitize enabled endpoints
        $new_input[ self::FIELD_ENABLED_ENDPOINTS ] = [];
        if (isset($input[ self::FIELD_ENABLED_ENDPOINTS ]) && is_array($input[ self::FIELD_ENABLED_ENDPOINTS ])) {
            foreach (self::ENDPOINTS as $endpoint_key => $endpoint_label) {
                $new_input[ self::FIELD_ENABLED_ENDPOINTS ][ $endpoint_key ] = empty($input[ self::FIELD_ENABLED_ENDPOINTS ][ $endpoint_key ]) ? 0 : 1;
            }
        } else {
            // If no checkboxes are submitted, set all to 0
            foreach (self::ENDPOINTS as $endpoint_key => $endpoint_label) {
                $new_input[ self::FIELD_ENABLED_ENDPOINTS ][ $endpoint_key ] = 0;
            }
        }

        // Sanitize allowed parameters
        $new_input[ self::FIELD_ALLOWED_PARAMETERS ] = [];
        if (isset($input[ self::FIELD_ALLOWED_PARAMETERS ]) && is_array($input[ self::FIELD_ALLOWED_PARAMETERS ])) {
            foreach ($input[ self::FIELD_ALLOWED_PARAMETERS ] as $param) {
                $new_input[ self::FIELD_ALLOWED_PARAMETERS ][] = sanitize_text_field($param);
            }
        }

        // Sanitize parameter restrictions key-value pairs
        $new_input[ self::FIELD_PARAM_KEY_VALUE_PAIRS ] = [];
        if (isset($input[ self::FIELD_PARAM_KEY_VALUE_PAIRS ]) && is_array($input[ self::FIELD_PARAM_KEY_VALUE_PAIRS ])) {
            foreach ($input[ self::FIELD_PARAM_KEY_VALUE_PAIRS ] as $pair) {
                if (isset($pair['key']) && isset($pair['value'])) {
                    $sanitized_key = sanitize_text_field($pair['key']);
                    $sanitized_value = sanitize_text_field($pair['value']);
                    if (! empty($sanitized_key)) { // Only save if key is not empty
                        $new_input[ self::FIELD_PARAM_KEY_VALUE_PAIRS ][] = [
                          'key'   => $sanitized_key,
                          'value' => $sanitized_value,
                        ];
                    }
                }
            }
        }

        return $new_input;
    }

    /**
     * Displays the plugin's settings page using the Settings API.
     *
     * @return void
     */
    private function display_settings_page()
    {
        include $this->get_plugin_path('templates/settings.php');
    }

    /**
     * Registers the API endpoints
     *
     * @return void
     */
    private function register_endpoints()
    {
        $enabled_endpoints = $this->get_enabled_endpoints();

        foreach (self::ENDPOINTS as $endpoint => $description) {
            if (empty($enabled_endpoints[ $endpoint ])) {
                continue;
            }

            register_rest_route('youtube-middleware/v1', '/' . $endpoint, [
              'methods' => 'GET',
              'callback' => fn ($request) => $this->get_list_results($endpoint, $request),
              'permission_callback' => '__return_true', // public
              'args' => array_merge(
                  $this->get_param_defs(),
              ),
            ]);
        }
    }

    /**
     * Callback function for the /youtube-middleware/v1/$endpoint GET endpoint.
     * Queries the YouTube Data API v3 endpoints via GET (list).
     *
     * @param  string          $endpoint The base googleapis.com/youtube/v3 API endpoint to query, e.g. "search"
     * @param  WP_REST_Request $request  The request object.
     * @return WP_REST_Response|WP_Error
     */
    private function get_list_results($endpoint, $request)
    {
        $all_params = $this->parse_params($request);
        if (is_wp_error($all_params)) {
            return $all_params;
        }

        $cache_duration = $all_params[ 'cacheDuration' ];
        $timeout = $all_params[ 'timeout' ];
        $args = $all_params[ '_args' ];

        // Check if we have a cached result
        $cache_key = 'youtube_middleware_' . md5($endpoint . json_encode($args));
        $cached_data = get_transient($cache_key);
        if ($cached_data !== false) {
            return $cached_data;
        }

        $base_url = 'https://www.googleapis.com/youtube/v3/' . $endpoint;
        $api_url = add_query_arg($args, $base_url);

        $response = wp_remote_get($api_url, [
          'timeout' => $timeout,
        ]);
        $http_code = wp_remote_retrieve_response_code($response);

        if (is_wp_error($response)) {
            return new WP_Error(
                'youtube_api_request_failed',
                sprintf(
                    'YouTube API request failed: %s',
                    $response->get_error_message()
                ),
                [ 'status' => $http_code ]
            );
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if ($http_code !== 200) {
            $error_message = 'Unknown API error.';
            $error_details = [];

            if (! empty($data['error']['message'])) {
                $error_message = sprintf(
                    'YouTube API returned an error: %s',
                    sanitize_text_field($data['error']['message'])
                );
            }

            if (! empty($data['error']['errors'])) {
                foreach ($data['error']['errors'] as $error) {
                    $error_details[] = sprintf(
                        'Reason: %s, Domain: %s',
                        sanitize_text_field($error['reason'] ?? ''),
                        sanitize_text_field($error['domain'] ?? '')
                    );
                }
            }

            return new WP_Error(
                'youtube_api_response_error',
                $error_message,
                [
                'status' => $http_code,
                'details' => $error_details,
                'raw_response' => $body, // Include raw response for debugging
        ]
            );
        }

        if (json_last_error() !== JSON_ERROR_NONE) {
            return new WP_Error(
                'youtube_api_json_decode_error',
                sprintf(
                    'Failed to decode YouTube API response: %s',
                    json_last_error_msg()
                ),
                [
                'status' => 500,
                'raw_response' => $body,
        ]
            );
        }

        set_transient($cache_key, $data, $cache_duration);
        return new WP_REST_Response($data, 200);
    }

    /**
     * Retrieves the API key from the settings.
     *
     * @return string
     */
    private function get_api_key()
    {
        $options = get_option(self::OPTION_NAME);
        return $options[ self::FIELD_API_KEY ] ?? '';
    }

    /**
     * Retrieves the enabled API endpoints from the settings.
     *
     * @return array
     */
    private function get_enabled_endpoints()
    {
        $options = get_option(self::OPTION_NAME);
        return $options[ self::FIELD_ENABLED_ENDPOINTS ] ?? [];
    }

    /**
     * Retrieves the user-defined list of allowed parameters pairs from the settings.
     *
     * @return array
     */
    private function get_allowed_parameters()
    {
        $options = get_option(self::OPTION_NAME);
        return $options[ self::FIELD_ALLOWED_PARAMETERS ] ?? [];
    }

    /**
     * Retrieves the user-defined parameter restrictions key-value pairs from the settings.
     *
     * @return array
     */
    private function get_param_key_value_pairs()
    {
        $options = get_option(self::OPTION_NAME);
        return $options[ self::FIELD_PARAM_KEY_VALUE_PAIRS ] ?? [];
    }

    /**
     * Retrieves the user-defined parameter restrictions as a flat array.
     *
     * @return array
     */
    private function get_param_restrictions()
    {
        $pairs = $this->get_param_key_value_pairs();
        $flat = [];
        foreach ($pairs as $index => $pair) {
            $flat[ $pair[ 'key' ] ] = $pair[ 'value' ];
        }
        return $flat;
    }

    /**
     * Defines our parameters for all the /youtube/v1/REST endpoints.
     *
     * @return array An array of parameter definitions.
     */
    private function get_param_defs()
    {
        return [
          'key' => [
            'description' => __('The API key. If missing, uses the globally saved one.', 'youtube-middleware'),
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'required' => false,
          ],
          'cacheDuration' => [
            'description' => __("The duration in seconds to cache the results for. Only relevant if there's no cached result (default: 15 minutes).", 'youtube-middleware'),
            'type' => 'integer',
            'default' => MINUTE_IN_SECONDS * 30,
            'sanitize_callback' => 'absint',
            'required' => false,
          ],
          'timeout' => [
            'description' => __('Request timeout in seconds (default: 10 seconds)', 'youtube-middleware'),
            'type' => 'integer',
            'default' => 10,
            'sanitize_callback' => 'absint',
            'required' => false,
          ],
        ];
    }

    /**
     * Parses the common parameters for all the /youtube/v1/REST endpoints.
     *
     * @param  WP_REST_Request $request The request object.
     * @return { cacheDuration: int, timeout: int, args: array }|WP_Error
     */
    private function parse_params($request)
    {
        $query_params = $request->get_query_params();

        $grouped_result = [
          'cacheDuration' => MINUTE_IN_SECONDS * 30,
          'timeout' => 10,
          '_args' => [
            'key' => $this->get_api_key(),
          ],
        ];

        foreach ($query_params as $name => $value) {
            if (is_null($value)) {
                continue;
            }

            if (array_key_exists($name, $grouped_result)) {
                $grouped_result[ $name ] = $value;
            } else {
                if (! $this->is_param_allowed($name)) {
                    return new WP_Error(
                        'youtube_middleware_forbidden_param',
                        'Forbidden parameter',
                        [ 'status' => '400' ]
                    );
                }

                if (! $this->is_param_valid($name, $value)) {
                    return new WP_Error(
                        'youtube_middleware_forbidden_value',
                        'Forbidden value for parameter',
                        [ 'status' => '400' ]
                    );
                }

                $grouped_result[ '_args' ][ $name ] = $value;
            }
        }

        return $grouped_result;
    }

    /**
     * Returns true if the given parameter name is allowed. 'key' is always
     * allowed.
     *
     * @param string $param The parameter name.
     *
     * @return bool
     */
    private function is_param_allowed($param)
    {
        $allowed_params = $this->get_allowed_parameters();
        return ! $allowed_params || $param === 'key' || in_array($param, $allowed_params, true);
    }

    /**
     * Validates a comma-separated input against a comma-separated list of allowed values.
     *
     * @param string $param The parameter name.
     * @param string $input The user-supplied value.
     *
     * @return bool
     */
    private function is_param_valid($param, $input)
    {
        $param_restrictions = $this->get_param_restrictions();
        $allowed = $param_restrictions[ $param ] ?? null;

        if (is_null($allowed)) { // any allowed
            return true;
        }

        $given_values = preg_split('/\s*,\s*/', $input, -1, PREG_SPLIT_NO_EMPTY);
        $allowed_values = preg_split('/\s*,\s*/', $allowed, -1, PREG_SPLIT_NO_EMPTY);

        if (! $allowed_values) { // none allowed
            return false;
        }

        $invalid = array_diff($given_values, $allowed_values);
        return empty($invalid);
    }

    /**
     * @param string $subpath
     *
     * @return string
     */
    private function get_plugin_path($subpath = '')
    {
        return plugin_dir_path(__FILE__) . $subpath;
    }
}
