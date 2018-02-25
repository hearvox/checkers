<?php
/*
Plugin Name:       Checkers
Plugin URI:        https://hearingvoices.com/tools/checkers
Description:       Test your webpages with online page checkers, for performance accessibility, and social shares.
Version:           0.1.1
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
define( 'CHECKERS_VERSION', '0.1.1' );
define( 'CHECKERS_BASENAME', plugin_basename( __FILE__ ) );
define( 'CHECKERS_DIR', trailingslashit( plugin_dir_path( __FILE__ ) ) );
define( 'CHECKERS_URL', trailingslashit( plugin_dir_url( __FILE__ ) ) );

/**
 * Adds "Settings" link on Plugins screen (next to "Activate").

 * @since  0.1.0
 *
 * @param   string  $links HTML links for Plugins screen.
 *
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
 * Site checking service APIs, filter in checkers_lists().
 * ------------------------------------------------------------------------ */
/*
 * Webpage-checking services that process results with URL in query string.
 *
 * array({Service-name}, {Service-URL-prefix}, {encode = 1}, {dashicon})
 */
$checkers_pages = array(
    array('Google: Pagespeed Insights', 'https://developers.google.com/speed/pagespeed/insights/?url=', 1, 'performance'),
    array('W3C: Markup Validation', 'https://validator.w3.org/checklink?hide_type=all&depth=&check=Check&uri=', 1, 'performance'),
    array('Twitter: Search', 'https://twitter.com/search?src=typd&q=', 1, 'share'),
    array('Facebook: Link Preview', 'https://developers.facebook.com/tools/debug/sharing/?q=', 1, 'share'),
    array('Facebook: Shares (data)', 'https://graph.facebook.com/?id=', 0, 'share'),
    array('WebAIM: WAVE Accessibility Tool', 'https://wave.webaim.org/report#/', 0, 'universal-access-alt'),
    array('Toptal: Colorblind Web Page Filter', 'https://www.toptal.com/designers/colorfilter?process_type=deutan&orig_uri=', 0, 'universal-access-alt'),
);

/*
 * Additional page-checking services that process results with URL in query string.
 *
 * array({Service-name}, {Service-URL-prefix}, {encode = 1}, {dashicon})
 */
$checkers_more = array(
    array('Google: Mobile-Friendly Test', 'https://search.google.com/test/mobile-friendly?url=', 1, 'performance'),
    array('Google: Structured Data Test', 'https://search.google.com/structured-data/testing-tool/u/0/#url=', 1, 'performance'),
    array('Moz: Open Site Explorer*', 'https://moz.com/researchtools/ose/links?filter=&source=external&target=page&group=0&page=1&sort=page_authority&anchor_id=&anchor_type=&anchor_text=&from_site=&site=', 1, 'share'),
    array('LinkedIn: Shares (data)', 'https://www.linkedin.com/countserv/count/share?url=', 0, 'share'),
    array('BuzzSumo: Shared', 'https://app.buzzsumo.com/research/most-shared?type=articles&result_type=total&num_days=365&general_article&infographic&video&how_to_article&list&what_post&why_post&page=1&q=', 1, 'share'),
    array('Internet Archive: Wayback Machine', 'https://web.archive.org/web/*/', 0, 'share'),
    array('Tenon: Accessibility Test*', 'https://tenon.io/testNow.php?url=', 0, 'universal-access-alt'),
);

/*
 * Webpage-checking services that need URL entered at site.
 *
 * array({Service-name}, {Service-URL}, {API = 1}, {dashicon})
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
 * array({Service-name}, {Service-URL-prefix}, {domain-only = 0}, {dashicon})
 */
$checkers_sites = array(
    array('SimilarWeb', 'https://www.similarweb.com/website/', 0, 'chart-line'),
    array('Alexa', 'https://www.alexa.com/siteinfo/', 0, 'chart-line'),
    array('Quantcast', 'https://www.quantcast.com/', 0, 'chart-line'),
    array('Sucuri', 'https://sitecheck.sucuri.net/results/', 0, 'lock'),
    array('SSL Labs', 'https://www.ssllabs.com/ssltest/analyze.html?d=', 0, 'lock'),
    array('W3Techs', 'https://w3techs.com/sites/info/', 0, 'editor-code'),
    array('BuiltWith', 'https://builtwith.com/', 0, 'editor-code'),
);

/**
 * Build HTML list items of webpage or website checking services.
 *
 * @since   0.1.1
 *
 * @param  string $sites_array  Array of sites and metadata (required)
 * @param  string $url_to_check Optional. User-entered URL for service query string.
 * @param  bool   $hostname     Optional. Whether to use just the domain name of user URL.
 * @param  bool   $sitelink     Optional. Whether to use checking site URL w/o user URL.
 *
 * @return string items HTML list items.
 */
function checkers_lists( $sites_array, $url_to_check = '', $hostname = 0, $sitelink = 0 ) {
    global $checkers_pages, $checkers_more, $checkers_links, $checkers_sites;

    $sites = $sites_array;
    $items = '<ol>';
    foreach ( $sites_array as $site ) {
        // Build HTML list of links.
        $items .= '<li class="dashicons-before dashicons-' . esc_attr( $site[3] ) . '">';

        // Add link for results if param passed.
        if ( $url_to_check ) {
            if ( $hostname ) { // For checker sites: domain name only.
                $url_to_use = parse_url( get_site_url(), PHP_URL_HOST);
            } else if ( $sitelink ) { // Use site link only (not user URL).
                $url_to_use = '';
            } else { // For checking webpages, some checkers need encoded URL.
                $url_to_use = ( $site[2] ) ? urlencode( $url_to_check ) : $url_to_check;
            }
            $items .= '<a href="' . esc_url( $site[1] . $url_to_use ) . '" target="_blank">';
            $items .= esc_textarea( $site[0] ) . '</a></li>';
        }  else // No URL to check provided.
        $items .= $site[0] . '</li>';
    }
    $items .= '</ol>';

    return $items;
}

/*
// Example of how to filter service lists:
function my_checkers_pages( $checkers_pages ) {
    // Unset by array item index number (starts with 0).
    unset( $checkers_pages[1] ); // Remove 2nd item.

    // Add new API service, array item content is:
    // array({Service-name}, {Service-URL-prefix}, {encode = 1}, {dashicon})
    // Add new service to end of the list.
    $checkers_pages[] = array('Example A11y Checker', 'https://api.example.com/a11y/?uri=', 1, 'a11y');
    // Add new service as the 5th item in the list.
    $checkers_pages[4] = array('Example Share Checker', 'https://api.example.com/share/?uri=', 1, 'share');


    ksort( $checkers_pages );

    return $checkers_pages;
}
add_filter( 'checkers_pages', 'my_checkers_pages' );

*/
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
    global $checkers_pages, $checkers_more, $checkers_links, $checkers_sites;

    /* If form data is submitted. */
    // Get post ID from search-posts form.
    if ( isset( $_POST['found_post_id'] ) ) {
        $url_to_check = get_permalink( $_POST['found_post_id'] );
    // Or get (and validate) user-entered URL from URL field.
    } elseif ( isset( $_POST['checkers_input_url'] )
        && filter_var( $_POST['checkers_input_url'], FILTER_VALIDATE_URL) ) {
        $url_to_check = $_POST['checkers_input_url'];
    } else {
        $url_to_check = '';
    }

    $style = ( $url_to_check ) ? ' card': '';

    ?>
    <div class="wrap">
        <h1>Checkers: <?php _e('Links to Results', 'checkers' ); ?></h1>

        <form name="checkers-posts-form" id="checkers-posts-form" method="post" action="">
            <fieldset>
                <legend><?php _e('Submit an URL to get results at online webpage checking services.', 'checkers') ?></legend>
                <?php wp_nonce_field('checkers_nonce'); ?>
                <?php find_posts_div(); // WP media-attach search-posts form ?>

                <p><label for="url"><?php _e('Enter URL (include https or http) or ', 'checkers') ?><a href="#checkers-url" onclick="findPosts.open( 'action','find_posts' ); return false;" id="find-posts-link" class="hide-if-no-js aria-button-if-js" aria-label="Open search-posts list form" role="button"><?php _e('Search Posts:', 'checkers') ?></a></label><br>
                <input type="url" required id="checkers-input-url" name="checkers_input_url" value="<?php echo esc_url( $url_to_check ); ?>" pattern="https?://.+" title="Please specify https:// or http://." />

                <p><input type="submit" value="<?php _e('Submit URL', 'checkers') ?>" class="button button-primary" />
            </fieldset>
        </form>

        <hr>

        <h2 id="checkers-page"><?php _e('Page checkers', 'checkers' ); ?></h2>
        <p><?php _e( 'Check a webpage for <span class="dashicons-before dashicons-performance">performance</span>, <span class="dashicons-before dashicons-universal-access-alt">accessibility</span>, and <span class="dashicons-before dashicons-share">shares</span>.', 'checkers' ); ?></p>

        <figure id="checkers-results" class="checkers-results<?php echo $style ?>">
            <?php if ( $url_to_check ) { // If webpage submitted via form. ?>
            <p><?php _e( 'Webpage:', 'checkers' ); ?> <span class="test-url"><?php echo $url_to_check; ?></span><br>
            <?php _e( 'Get your results (opens in a new window) at:', 'checkers' ); ?></p>
            <?php echo checkers_lists( $checkers_pages, $url_to_check  ); ?>
            <!-- Hidden by default; displayed by button click. -->
            <p class="js-expandmore"><?php _e( 'More checkers', 'checkers' ); ?></p>
            <aside id="checkers-more-links" class="js-to_expand">
            <section>
            <?php echo checkers_lists( $checkers_more, $url_to_check  ); ?>
            <p class="description"><?php _e('* Service limits the number of free daily checks.', 'checkers') ?></p>
            <p><?php _e( 'These services require you enter an URL at their site. Your URL is now in your clipboard, ready to paste into their field:', 'checkers' ); ?></p>
            <?php echo checkers_lists( $checkers_links, $url_to_check, 0, 1 ); ?>
            </aside><!-- #checkers-more-links -->

            <?php } else { ?>
            <?php echo checkers_lists( $checkers_pages ); ?>
            <?php } ?>
        </figure>

        <hr>

        <h2 id="checkers-site"><?php _e('Site checkers', 'checkers' ); ?></h2>
        <p><?php _e( 'Check this website for <span class="dashicons-before dashicons-chart-line">statistics</span>, <span class="dashicons-before dashicons-lock">security</span>, and <span class="dashicons-before dashicons-editor-code">technologies</span>.', 'checkers' ); ?></p>
        <figure id="checkers-results-sites" class="checkers-results card">
            <p><?php _e( 'Website:', 'checkers' ); ?> <span class="test-url"><?php echo parse_url( get_site_url(), PHP_URL_HOST); ?></span><br>
            <?php _e( 'Get your results (opens in a new window) at:', 'checkers' ); ?></p>
            <?php echo checkers_lists( $checkers_sites, $url_to_check, 1 ); ?>
            <?php // echo checkers_list_site_results_links(); ?>
        </figure>
        <pre><?php global $checkers_pages; print_r( $checkers_pages ) ?></pre>

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

    if ( is_admin() && $checkers_options_page == $hook ) { // Load only on this screen.
        // Files for the search-post modal form, called by find_posts_div().
        wp_enqueue_style('thickbox');
        wp_enqueue_script( 'thickbox' );
        wp_enqueue_script( 'media' );
        wp_enqueue_script( 'wp-ajax-response' );

        // Change form's default text.
        add_filter( 'gettext', 'checkers_text_strings', 20, 3 );

        // Set file versions to file modification time (cache-buster).
        $path_js  = CHECKERS_DIR . 'js/checkers-ajax.js';
        $path_css = CHECKERS_DIR . 'css/checkers.css';
        $vers_js = ( file_exists( $path_js ) ) ? filemtime( $path_js ) : get_bloginfo('version'); ;
        $vers_css = ( file_exists( $path_css ) ) ? filemtime( $path_css ) : get_bloginfo('version'); ;

    	wp_enqueue_script( 'checkers-js', CHECKERS_URL . 'js/checkers-ajax.js', array( 'jquery' ), $vers_js );
        wp_enqueue_style( 'checkers-css', CHECKERS_URL . 'css/checkers.css', array(), $vers_css );
    	wp_localize_script('checkers-js', 'checkers_vars', array(
    			'checkers_nonce' => wp_create_nonce('checkers-nonce'),
    		)
    	);

    }
}
add_action('admin_enqueue_scripts', 'checkers_load_admin_scripts');

/**
 * Change text of search-post form heading.
 *
 * Plugin uses WP media-attach form, called by find_posts_div().
 *
 * @link http://codex.wordpress.org/Plugin_API/Filter_Reference/gettext
 *
 * @since 0.1.1
 *
 * @param string $translation  Translated text.
 * @param string $text         Text to translate.
 * @param string $domain       Text domain. Unique identifier for retrieving translated strings.
 *
 * @return string $translated_text Translated text
 */
function checkers_text_strings( $translation, $text, $domain ) {
        switch ( $translation ) {
            case 'Attach to existing content' :
                $translation = __( 'Select a post', 'checkers' );
                break;
        }
        return $translation;
}




/**
 * Build HTML list of page-checking services (without links).
 *
 * @since   0.1.0
 *
 * @return string $links HTML ordered list.
 */
function checkers_list_page_services() {
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
 * Build HTML list of page-checking service links with user-entered URL in query.
 *
 * Following the link opens a new window and starts processing results.
 *
 * @since   0.1.0
 *
 * @return string $links HTML ordered list.
 */
function checkers_list_page_results_links( $url = '' ) {
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
 * Build HTML list of page-checking service links with user-entered URL in query.
 *
 * Following the link opens a new window and starts processing results.
 *
 * @since   0.1.0
 *
 * @return string $links HTML ordered list.
 */
function checkers_list_more_results_links( $url = '' ) {
    global $checkers_more;
    $links = '<ol>';
    foreach ( $checkers_more as $site ) {
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
 * Build HTML list of page-checking service links.
 *
 * Following the link does not process results (until user enters an URL).
 *
 * @since   0.1.0
 *
 * @return string $links HTML ordered list.
 */
function checkers_list_page_services_links() {
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
 * Build HTML list of site-checking service links with site domain name in query.
 *
 * Following the link opens a new window and starts processing results.
 *
 * @since   0.1.0
 *
 * @return string $links HTML ordered list.
 */
function checkers_list_site_results_links() {
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
