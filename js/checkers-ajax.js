var checks, services, check_url, check_links, i;

window.onload = function() {
    /* Indicate a change to user (helpful when submitting a 2nd URL). */
    document.getElementById( 'checkers-url' ).addEventListener( 'focus', function(){
        document.getElementById( 'checkers-results' ).style.backgroundColor = '#fffff3';
    });

    /* Process form submission */
    document.getElementById( 'checkers-form' ).addEventListener( 'submit', function( event ){
        event.preventDefault();
        check_links = '';
        let check_url_input = document.getElementById( 'checkers-url' ); // Get form input.

        if ( check_url_input && check_url_input.value ) { // Input field has value.
            check_url = check_url_input.value;

            // Select URL and copy into clipboard.
            check_url_input.select();
            document.execCommand( 'Copy' );
            check_url_input.blur(); // Remove select.

            /* Processed page information, with list of links to page checkers. */
            check_links += '<p>' + checkers_vars.checkers_p_top + '</p>';
            check_links += '<ol>';
            c_pages = checkers_vars.checkers_pages;
            for ( i = 0; i < c_pages.length; i++ ) { // Array of checker data.
                // Some checkers needed encoded URL.
                check_page_url = ( c_pages[i][2] ) ? encodeURIComponent( check_url ) : check_url;
                // Build HTML list of links.
                check_links += '<li class="dashicons-before dashicons-' + c_pages[i][3] + '"><a href="' + c_pages[i][1] + check_page_url + '" target="_blank">' + c_pages[i][0] + '</a></li>';
            }
            check_links += '</ol><hr>';
            check_links += '<p>' + checkers_vars.checkers_p_mid + '</p>';
            check_links += '<ol>';

            c_links = checkers_vars.checkers_links;
            for ( i = 0; i < c_links.length; i++ ) { // Array of checker data.
                check_links += '<li class="dashicons-before dashicons-' + c_links[i][3] + '"><a href="' + c_links[i][1] + '" target="_blank">' + c_links[i][0] + '</a></li>';
            }
            check_links += '</ol>';

        } else { // Input field empty.
            check_links = '<p class="description">' + checkers_vars.checkers_else + '</p>';
        }

    // Print list.
    document.getElementById( 'checkers-results' ).innerHTML = check_links;
    document.getElementById( 'checkers-results' ).style.backgroundColor = '#F7F7F7';
    });
};

/*
https://www.rjionline.org/stories/true-or-false-politifact-pols-and-pundits
http://current.org/2018/02/how-to-make-sure-your-website-works-for-all-users/

https://seositecheckup.com/seo-audit/www.rjionline.org/stories/true-or-false-politifact-pols-and-pundits
https://seositecheckup.com/seo-audit/www.rjionline.org/stories/true-or-false-politifact-pols-and-pundits

https://example.com/example-post-slug/
Example Post Title

Use URL raw (0):
https://www.linkedin.com/countserv/count/share?url=https://example.com/example-post-slug/
https://www.toptal.com/designers/colorfilter?orig_uri=https://example.com/example-post-slug/&process_type=protan
https://wave.webaim.org/report#/https://example.com/example-post-slug/
https://tenon.io/testNow.php?url=https://example.com/example-post-slug/
https://www.linkedin.com/countserv/count/share?url=https://example.com/example-post-slug/

SEOSiteCheckup: Score
https://seositecheckup.com/seo-audit/example.com/example-post-slug/

Use URL encoded (1):
https://developers.google.com/speed/pagespeed/insights/?url=https%3A%2F%2Fexample.com%2Fexample-post-slug%2F
https://search.google.com/test/mobile-friendly?url=https%3A%2F%2Fexample.com%2Fexample-post-slug%2F
https://twitter.com/search?q=https%3A%2F%2Fexample.com%2F2018%2F02%2Fexample-post-slug%2Fexample-post-slug%2F&src=typd
https://moz.com/researchtools/ose/links?site=https%3A%2F%2Fexample.com%2Fexample-post-slug%2F&filter=&source=external&target=page&group=0&page=1&sort=page_authority&anchor_id=&anchor_type=&anchor_text=&from_site=
https://validator.w3.org/checklink?hide_type=all&depth=&check=Check&uri=https%3A%2F%2Fexample.com%2Fexample-post-slug%2F&

https://app.buzzsumo.com/research/most-shared?type=articles&result_type=total&num_days=365&general_article&infographic&video&how_to_article&list&what_post&why_post&page=1&q=https:%2F%2Fexample.com%2Fexample-post-slug%2F

Use title, encoded (2):
https://www.facebook.com/search/str/%22Example+Post+Title%22/keywords_search
// ['Facebook: Search (post title)', 'https://www.facebook.com/search/str/%22', '%22/keywords_search', 2],

Site checks:
https://www.similarweb.com/website/example.com
https://w3techs.com/sites/info/example.com
https://www.quantcast.com/example.com
https://www.alexa.com/siteinfo/example.com
https://www.ssllabs.com/ssltest/analyze.html?d=current.org
https://sitecheck.sucuri.net/results/current.org
https://builtwith.com/example.com


API site checks:
https://www.webpagetest.org/runtest.php?k={API-key}&runs=1&web10=1&fvonly=1&f=xml&noopt=1&noimages=1&ignoreSSL=1&url=example.com
https://achecker.ca/checkacc.php?id={API-Key}&output=rest&guide=WCAG2-AA&uri=https%3A%2F%2Fexample.com%2F

Links (enter URL at remote site):
https://www.webpagetest.org/
https://sonarwhal.com/scanner
https://tools.pingdom.com/
https://cards-dev.twitter.com/validator
https://seositecheckup.com/tools
https://achecker.ca/checker/index.php


const url_obj = new URL('http://www.example.com/example-post-slug/');
console.log(url_obj.hostname + url_obj.pathname);

var url_str   = "https://www.w3schools.com/jsref/tryit.asp?filename=tryjsref_split";
var url_split = url_str.split("://");
var url_host_path = url_split[1];

_WP_Editors::wp_link_dialog();
https://gist.github.com/wp-kitten/c647cda5ddacc5b27f1db20a3a476ae2

https://seositecheckup.com/tools
mb/highness doubt curtain

*/
