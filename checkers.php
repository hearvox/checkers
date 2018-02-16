<?php
/*
Plugin Name:       Checkers
Plugin URI:        https://hearingvoices.com/tools/checkers
Description:       Test your webpages with online page checkers, for performance accessibility, and social shares.
Version:           0.1.0
Author:            Barrett Golding
Author URI:        https://hearingvoices.com/bg/
License:           GPL-2.0+
License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
Text Domain:       checkers
Prefix:            checkers
*/

/* ------------------------------------------------------------------------ *
 * Plugin init
 * ------------------------------------------------------------------------ */
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

if ( defined( 'checkers_VERSION' ) ) {
    return;
}

/**
 * Adds "Settings" link on Plugins screen (next to "Activate").

 * @since  0.1.0
 *
 * @param   string  $links HTML links for Plugins screen.
 * @return  string  $links HTML links for Plugins screen.
 */
function checkers_plugin_settings_link( $links ) {
  $settings_link = '<a href="' . esc_url( admin_url( 'options-general.php?page=checkers' ) ) . '">' . __( 'Settings', 'checkers' ) . '</a>';
  array_unshift( $links, $settings_link );
  return $links;
}
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'checkers_plugin_settings_link' );

/* ------------------------------------------------------------------------ *
 * Constants: plugin path, URI, dir, filename, and version.
 *
 * CHECKERS_BASENAME     checkers/checkers.php
 * CHECKERS_DIR          /path/to/wp-content/plugins/checkers/
 * CHECKERS_DIR_BASENAME checkers/
 * CHECKERS_URI https://example.com/wp-content/plugins/checkers/
 * ------------------------------------------------------------------------ */
define( 'CHECKERS_VERSION', '0.1.0' );
define( 'CHECKERS_BASENAME', plugin_basename( __FILE__ ) );
define( 'CHECKERS_DIR', trailingslashit( plugin_dir_path( __FILE__ ) ) );
define( 'CHECKERS_URI', trailingslashit( plugin_dir_url( __FILE__ ) ) );
define(
    'CHECKERS_DIR_BASENAME',
    trailingslashit( dirname( plugin_basename( __FILE__ ) ) )
);

/**
 * Load the plugin text domain for translation.
 *
 * @since   0.1.0
 *
 * @return void
 */
function checkers_load_textdomain() {
    load_plugin_textdomain(
            'checkers',
            false,
            dirname( plugin_basename( __FILE__ ) ) . '/languages'
        );
}
add_action( 'plugins_loaded', 'checkers_load_textdomain' );

/**
 * Redirect to Settings screen upon plugin activation.
 *
 * @param  string $plugin Plugin basename (e.g., "my-plugin/my-plugin.php")
 * @return void
 */
function checkers_activation_redirect( $plugin ) {
    if ( $plugin === CHECKERS_BASENAME ) {
        $redirect_uri = add_query_arg(
            array(
                'page' => 'checkers' ),
                admin_url( 'options-general.php' )
            );
        wp_safe_redirect( $redirect_uri );
        exit;
    }
}
add_action( 'activated_plugin', 'checkers_activation_redirect' );

/* ------------------------------------------------------------------------ *
 * Settings screen
 * ------------------------------------------------------------------------ */
/**
 * Adds submenu item to Settings dashboard menu.
 *
 * @since   0.1.0
 *
 * Sets Settings screen ID: 'checkers_options_page'.
 */
function checkers_settings_menu() {
    global $checkers_options_page;
    $checkers_options_page = add_options_page(
        __( 'Checkers: Settings', 'checkers' ),
        __( 'Checkers', 'checkers' ),
        'manage_options',
        'checkers',
        'checkers_settings_display'
    );
}
add_action('admin_menu', 'checkers_settings_menu');

/**
 * Renders settings menu page.
 *
 * Uses modal form to search posts, based on on WP native find_posts_div().
 * @see https://developer.wordpress.org/reference/functions/find_posts_div/
 * @see https://shibashake.com/wordpress-theme/find-posts-dialog-box
 *
 * @since   0.1.0
 */
function checkers_settings_display() {
    ?>
    <div class="wrap">
        <h1>Checkers: <?php _e('Links', 'checkers' ); ?></h1>

        <!-- Modal based on find_posts_div() -->
        <form name="plugin_form" id="plugin_form" method="post" action="">
            <?php wp_nonce_field('plugin_nonce'); ?>
            <div id="find-posts" class="find-box" style="display: none;">
                <div id="find-posts-head" class="find-box-head">
                    <?php _e( 'Select a post', 'checkers' ); ?>
                    <button type="button" id="find-posts-close"><span class="screen-reader-text"><?php _e( 'Close media attachment panel', 'checkers' ); ?></span></button>
                </div>
                <div class="find-box-inside">
                    <div class="find-box-search">
                        <?php // if ( $found_action ) { ?>
                            <input type="hidden" name="found_action" value="<?php // echo esc_attr($found_action); ?>" />
                        <?php // } ?>
                        <input type="hidden" name="affected" id="affected" value="" />
                        <?php wp_nonce_field( 'find-posts', '_ajax_nonce', false ); ?>
                        <label class="screen-reader-text" for="find-posts-input"><?php _e( 'Search', 'checkers' ); ?></label>
                        <input type="text" id="find-posts-input" name="ps" value="" />
                        <span class="spinner"></span>
                        <input type="button" id="find-posts-search" value="<?php esc_attr_e( 'Search', 'checkers' ); ?>" class="button" />
                        <div class="clear"></div>
                    </div>
                    <div id="find-posts-response"></div>
                </div>
                <div class="find-box-buttons">
                    <?php submit_button( __( 'Select', 'checkers' ), 'primary alignright', 'find-posts-submit', false ); ?>
                    <div class="clear"></div>
                </div>
            </div><!-- #find-posts -->
        </form>

        <p><?php _e( ' Check a webpage for <span class="dashicons-before dashicons-performance">performance</span>, <span class="dashicons-before dashicons-universal-access-alt">accessibility</span>, and <span class="dashicons-before dashicons-share">social shares</span>.', 'checkers' ); ?></p>
        <form id="checkers-form">
            <?php $checkers_url_val = ( isset( $_POST['found_post_id'] ) ) ? get_permalink( $_POST['found_post_id'] ) : ''; ?>
            <p><label for="url">Enter URL (or <a href="#checkers-url" onclick="findPosts.open( 'action','find_posts' ); return false;" class="hide-if-no-js aria-button-if-js" aria-label="Open search-posts list form" role="button">Search Posts</a>):</label><br>
            <input type="url" id="checkers-url" name="checkers-url" value="<?php echo esc_url( $checkers_url_val ); ?>" style="width: 40rem;" /></p>
            <input type="submit" value="Submit URL" class="button button-primary" />
        </form>
        <figure id="checkers-results" style="margin: 0; max-width: 40rem;">
        </figure>
    </div><!-- .wrap -->
    <?php
}

/**
 * Load scripts and styles on admin settings page.
 *
 * @since   0.1.0
 *
 * @param int $hook Hook suffix for the current admin page.
 */
function checkers_load_admin_scripts( $hook ) {
	global $checkers_options_page; // Hook for this screen.

    if ( $checkers_options_page == $hook ) { // Load only on this screen.
        // Used to display and process the search-post modal form.
        wp_enqueue_style('thickbox');
        wp_enqueue_script('thickbox');
        wp_enqueue_script('media');
        wp_enqueue_script('wp-ajax-response');

        // Used to process the page checkers form.
        $file_path = plugin_dir_path( __FILE__ ) . 'js/checkers-ajax.js';
    	wp_enqueue_script( 'checkers-ajax', plugin_dir_url( __FILE__ ) . 'js/checkers-ajax.js', array('jquery'), filemtime( $file_path ) );
    	wp_localize_script('checkers-ajax', 'checkers_vars', array(
    			'checkers_nonce' => wp_create_nonce('checkers-nonce'),
                'checkers_p'     => __('Submit an URL to get results from these online webpage checking services:', 'checkers'),
                'checkers_p_top' => __('These links open a new browser window with your results from:', 'checkers'),
                'checkers_p_mid' => __('The above links start processing your results. The services below require entering an URL at their site. Your URL is in your clipboard, ready to paste into their field.', 'checkers'),
                'checkers_note'  => __('* Service limits the number of daily checks.', 'checkers'),
                'checkers_else'  => __('Enter a valid URL.', 'checkers'),
    		)
    	);
    }
}
add_action('admin_enqueue_scripts', 'checkers_load_admin_scripts');
