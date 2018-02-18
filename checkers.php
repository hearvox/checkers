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

/* ------------------------------------------------------------------------ *
 * Constants: plugin version, name, and the path and URL to directory.
 *
 * CHECKERS_BASENAME checkers/checkers.php
 * CHECKERS_DIR      /path/to/wp-content/plugins/checkers/
 * CHECKERS_URL      https://example.com/wp-content/plugins/checkers/
 * ------------------------------------------------------------------------ */
define( 'CHECKERS_VERSION', '0.1.0' );
define( 'CHECKERS_BASENAME', plugin_basename( __FILE__ ) );
define( 'CHECKERS_DIR', trailingslashit( plugin_dir_path( __FILE__ ) ) );
define( 'CHECKERS_URL', trailingslashit( plugin_dir_url( __FILE__ ) ) );

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
 * Site checking services
 * ------------------------------------------------------------------------ */
/*
 * Webpage-checking services that process results with URL in query string.
 *
 * array({Service-name}, {Service-URL-prefix}, {encode?}, {dashicon})
 */
$checkers_pages = array(
    array('Google: Pagespeed Insights', 'https://developers.google.com/speed/pagespeed/insights/?url=', 1, 'performance'),
    array('Google: Mobile-Friendly Test', 'https://search.google.com/test/mobile-friendly?url=', 1, 'performance'),
    array('W3C: Markup Validation', 'https://validator.w3.org/checklink?hide_type=all&depth=&check=Check&uri=', 1, 'performance'),
    array('Twitter: Search', 'https://twitter.com/search?src=typd&q=', 1, 'share'),
    array('Facebook: Link Preview', 'https://developers.facebook.com/tools/debug/sharing/?q=', 1, 'share'),
    array('Facebook: Shares (data)', 'https://graph.facebook.com/?id=', 0, 'share'),
    array('LinkedIn: Shares (data)', 'https://www.linkedin.com/countserv/count/share?url=', 0, 'share'),
    array('Moz: Open Site Explorer*', 'https://moz.com/researchtools/ose/links?filter=&source=external&target=page&group=0&page=1&sort=page_authority&anchor_id=&anchor_type=&anchor_text=&from_site=&site=', 1, 'share'),
    array('Internet Archive: Wayback Machine', 'https://web.archive.org/web/*/', 0, 'share'),
    array('WebAIM: WAVE Accessibility Tool', 'https://wave.webaim.org/report#/', 0, 'universal-access-alt'),
    array('Toptal: Colorblind Web Page Filter', 'https://www.toptal.com/designers/colorfilter?process_type=deutan&orig_uri=', 0, 'universal-access-alt'),
    array('Tenon: Accessibility Test*', 'https://tenon.io/testNow.php?url=', 0, 'universal-access-alt'),
);

/*
 * Webpage-checking services that need URL entered at site.
 *
 * array({Service-name}, {Service-URL}, {API?}, {dashicon})
 */
$checkers_links = array(
    array('WebPagetest', 'https://www.webpagetest.org/', 1, 'performance'),
    array('Pingdom: Website Speed Test', 'https://tools.pingdom.com/', 0, 'performance'),
    array('Sonarwhal: Scanner', 'https://sonarwhal.com/scanner', 0, 'performance'),
    array('Twitter: Card Validator', 'https://cards-dev.twitter.com/validator', 0, 'share'),
    array('AChecker: Web Accessibility Checker', 'https://achecker.ca/checker/index.php', 1, 'universal-access-alt'),
);

/*
 * Site-checking services that process results with domain in query string.
 *
 * array({Service-name}, {Service-URL-prefix}, {domain?}, {dashicon})
 */
$checkers_sites = array(
    array('SimilarWeb', 'https://www.similarweb.com/website/', 0, 'chart-line'),
    array('Alexa', 'https://www.alexa.com/siteinfo/', 0, 'chart-line'),
    array('Quantcast', 'https://www.quantcast.com/', 0, 'chart-line'),
    array('Sucuri', 'https://sitecheck.sucuri.net/', 0, 'lock'),
    array('SSL Labs', 'https://www.ssllabs.com/ssltest/analyze.html?d=', 0, 'lock'),
    array('W3Techs', 'https://w3techs.com/sites/info/', 0, 'editor-code'),
    array('BuiltWith', 'https://builtwith.com/', 0, 'editor-code'),
);

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
    /* Get form date, if submitted. */
    if ( isset( $_POST['found_post_id'] ) ) { // Post ID from search-posts form.
        $url_to_check = get_permalink( $_POST['found_post_id'] );
    } elseif ( isset( $_POST['checkers_input_url'] ) ) { // User-entered URL.
        $url_to_check = $_POST['checkers_input_url'];
    } else {
        $url_to_check = '';
    }

    ?>
    <div class="wrap">
        <h1>Checkers: <?php _e('Links', 'checkers' ); ?></h1>
        <p><?php _e( 'Get results from online webpage and website checkers.', 'checkers' ); ?></p>


        <h2 id="checkers-page"><?php _e('Page checkers', 'checkers' ); ?></h2>
        <form name="checkers-posts-form" id="checkers-posts-form" method="post" action="">
            <?php wp_nonce_field('checkers_nonce'); ?>

            <!-- Modal based on find_posts_div() -->
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

            <p><?php _e( 'Check a webpage for <span class="dashicons-before dashicons-performance">performance</span>, <span class="dashicons-before dashicons-universal-access-alt">accessibility</span>, and <span class="dashicons-before dashicons-share">shares</span>.', 'checkers' ); ?></p>

            <p><label for="url"><?php _e('1. Enter URL (or ', 'checkers') ?><a href="#checkers-url" onclick="findPosts.open( 'action','find_posts' ); return false;" id="find-posts-link" class="hide-if-no-js aria-button-if-js" aria-label="Open search-posts list form" role="button"><?php _e('Search Posts', 'checkers') ?></a><?php _e('):', 'checkers') ?></label><br>
            <input type="url" required id="checkers-input-url" name="checkers_input_url" value="<?php echo esc_url( $url_to_check ); ?>" /></p>


            <p><?php _e('2.', 'checkers') ?> <input type="submit" value="<?php _e('Submit URL', 'checkers') ?>" class="button button-primary" />
        </form>
        <figure id="checkers-results" class="checkers-results checkers-page-results">

        <?php if ( $url_to_check ) { // If webpage submitted via form. ?>

            <p><?php _e( 'These links open a new browser window which starts processing your results from:', 'checkers' ); ?></p>
            <?php echo checkers_page_services_with_url( $url_to_check ); ?>
            <p class="description"><?php _e('* Service limits the number of daily checks.', 'checkers') ?></p>
            <button id="checkers-more-button" class="button"><?php _e( 'More checkers&hellip;', 'checkers' ); ?></button></p>
            <aside id="checkers-more-links" style="display: none;">
            <p><?php _e( 'These services require you enter an URL at their site. Your URL is now in your clipboard, ready to paste into their field.', 'checkers' ); ?></p>
            <?php echo checkers_page_services_links(); ?>
            </aside>
            <?php } else { ?>

            <p><?php _e('Submit an URL to get results from these online webpage checking services:', 'checkers') ?></p>
            <?php echo checkers_page_services(); ?>
            <?php } ?>

        </figure>
        <hr>

        <figure id="checkers-site-results" class="checkers-results">
            <h2 id="checkers-site"><?php _e('Site checkers', 'checkers' ); ?></h2>
            <p><?php _e( '3. Check this website for <span class="dashicons-before dashicons-chart-line">statistics</span>, <span class="dashicons-before dashicons-lock">security</span>, and <span class="dashicons-before dashicons-editor-code">technologies</span>.', 'checkers' ); ?></p>
            <?php echo checkers_site_services(); ?>
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
    global $checkers_pages, $checkers_links, $checkers_sites;

    if ( $checkers_options_page == $hook ) { // Load only on this screen.
        // Used to display and process the search-post modal form.
        wp_enqueue_style('thickbox');
        wp_enqueue_script('thickbox');
        wp_enqueue_script('media');
        // wp_enqueue_script('wp-ajax-response');

        // Set files versions to file modification time (cache-buster).
        $path_js  = CHECKERS_DIR . 'js/checkers-ajax.js';
        $path_css = CHECKERS_DIR . 'css/checkers.css';
        $vers_js = ( file_exists( $path_js ) ) ? filemtime( $path_js ) : get_bloginfo('version'); ;
        $vers_css = ( file_exists( $path_css ) ) ? filemtime( $path_css ) : get_bloginfo('version'); ;

    	wp_enqueue_script( 'checkers-js', CHECKERS_URL . 'js/checkers-ajax.js', array( 'jquery' ), $vers_js );
        wp_enqueue_style( 'checkers-css', CHECKERS_URL . 'css/checkers.css', array(), $vers_css );
    	wp_localize_script('checkers-js', 'checkers_vars', array(
    			'checkers_nonce' => wp_create_nonce('checkers-nonce'),
                'checkers_error'  => __('Enter a valid URL.', 'checkers'),
    		)
    	);
    }
}
add_action('admin_enqueue_scripts', 'checkers_load_admin_scripts');

/**
 * Build HTML list of page-checking services.
 *
 * @since   0.1.0
 *
 * @return string $links HTML ordered list.
 */
function checkers_page_services() {
    global $checkers_pages;
    $links = '<ol>';
    foreach ( $checkers_pages as $site ) {
        $links .= '<li class="dashicons-before dashicons-' . esc_attr( $site[3] ) . '">';
        $links .= esc_html( $site[0]) . '</li>';
    }
    $links .= '</ol>';

    return $links;
}

/**
 * Build HTML list of page-checking services with user-entered URL.
 *
 * @since   0.1.0
 *
 * @return string $links HTML ordered list.
 */
function checkers_page_services_with_url( $url = '' ) {
    global $checkers_pages;
    $links = '<ol>';
    foreach ( $checkers_pages as $site ) {
        // Some checkers needed encoded URL.
        $url_to_check = ( $site[2] ) ? urlencode( $url ) : $url;
        $links .= '<li class="dashicons-before dashicons-' . esc_attr( $site[3] ) . '">';
        $links .= '<a href="' . esc_url( $site[1] . $url_to_check ) . '" target="_blank">';
        $links .= esc_html( $site[0]) . '</a></li>';
    }
    $links .= '</ol>';

    return $links;
}

/**
 * Build HTML list of page-checking services with user-entered URL.
 *
 * @since   0.1.0
 *
 * @return string $links HTML ordered list.
 */
function checkers_page_services_links() {
    global $checkers_links;
    $links = '<ol>';
    foreach ( $checkers_links as $site ) {
        $links .= '<li class="dashicons-before dashicons-' . esc_attr( $site[3] ) . '">';
        $links .= '<a href="' . esc_url( $site[1] ) . '" target="_blank">';
        $links .= esc_html( $site[0]) . '</a></li>';
    }
    $links .= '</ol>';

    return $links;
}

/**
 * Build HTML list of site-checking service links.
 *
 * @since   0.1.0
 *
 * @return string $links HTML ordered list.
 */
function checkers_site_services() {
    global $checkers_sites;
    $host = parse_url( get_site_url(), PHP_URL_HOST);
    $links = '<ol>';
    foreach ( $checkers_sites as $site ) {
        $links .= '<li class="dashicons-before dashicons-' . esc_attr( $site[3] ) . '">';
        $links .= '<a href="' . esc_url( $site[1] . $host ) . '" target="_blank">';
        $links .= esc_html( $site[0]) . '</a></li>';
    }
    $links .= '</ol>';

    return $links;
}
