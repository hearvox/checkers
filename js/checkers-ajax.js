var checks, services, check_url, check_links, i;

/*
 * Online page checkers that can process results with URL in query string.
 *
 * [{Service-name}, {Service-URL-prefix}, {encode?}, {dashicon} ]
 */
checks = [
      ['Google: Pagespeed Insights', 'https://developers.google.com/speed/pagespeed/insights/?url=', 1, 'performance'],
      ['Google: Mobile-Friendly Test', 'https://search.google.com/test/mobile-friendly?url=', 1, 'performance'],
      ['W3C: Markup Validation', 'https://validator.w3.org/checklink?hide_type=all&depth=&check=Check&uri=', 1, 'performance'],
      ['Twitter: Search (post URL)', 'https://twitter.com/search?src=typd&q=', 1, 'share'],
      ['Facebook: Link Preview', 'https://developers.facebook.com/tools/debug/sharing/?q=', 1, 'share'],
      ['Facebook: Shares (data)', 'https://graph.facebook.com/?id=', 0, 'share'],
      ['LinkedIn: Shares (data)', 'https://www.linkedin.com/countserv/count/share?url=', 0, 'share'],
      ['Moz: Open Site Explorer*', 'https://moz.com/researchtools/ose/links?filter=&source=external&target=page&group=0&page=1&sort=page_authority&anchor_id=&anchor_type=&anchor_text=&from_site=&site=', 1, 'share'],
      ['WebAIM: WAVE Accessibility Tool', 'https://wave.webaim.org/report#/', 0, 'universal-access-alt'],
      ['Toptal: Colorblind Web Page Filter', 'https://www.toptal.com/designers/colorfilter?process_type=deutan&orig_uri=', 0, 'universal-access-alt'],
      ['Tenon: Accessibility Test*', 'https://tenon.io/testNow.php?url=', 0, 'universal-access-alt'],
];

/*
 * Online page checking services that need URL entered at site,
 *
 * [{Service-name}, {Service-URL}, {API?}, {dashicon} ]
 */
services = [
    ['WebPagetest', 'https://www.webpagetest.org/', 1, 'performance'],
    ['Pingdom: Website Speed Test', 'https://tools.pingdom.com/', 0, 'performance'],
    ['Sonarwhal: Scanner', 'https://sonarwhal.com/scanner', 0, 'performance'],
    ['Twitter: Card Validator', 'https://cards-dev.twitter.com/validator', 0, 'share'],
    ['AChecker: Web Accessibility Checker', 'https://achecker.ca/checker/index.php', 1, 'universal-access-alt'],
];

window.onload = function() {
    /* Initial page information, with list of page checkers. */
    check_links = '';
    check_links += '<p>' + checkers_vars.checkers_p + '</p>';
    check_links += '<ol>';
    for ( i = 0; i < checks.length; i++ ) { // Array of checker data.
        check_links += '<li class="dashicons-before dashicons-' + checks[i][3] + '">' + checks[i][0] + '</li>';
    }
    check_links += '</ol>';
    check_links += '<p class="description">' + checkers_vars.checkers_note + '</p>';

    document.getElementById('checkers-results').innerHTML = check_links;

    /* Indicate a change to user (needed when submitting a 2nd URL). */
    document.getElementById('checkers-url').addEventListener('focus', function(){
        document.getElementById('checkers-results').style.backgroundColor = '#fffff3';
    });

    /* Process form submission */
    document.getElementById('checkers-form').addEventListener('submit', function( event ){
        event.preventDefault();
        check_links = '';
        let check_url_input = document.getElementById('checkers-url'); // Get form input.

        if ( check_url_input && check_url_input.value ) { // Input field has value.
            check_url = check_url_input.value;

            // Select URL and copy into clipboard.
            check_url_input.select();
            document.execCommand("Copy");

            /* Processed page information, with list of links to page checkers. */
            check_links += '<p>' + checkers_vars.checkers_p_top + '</p>';
            check_links += '<ol>';
            for ( i = 0; i < checks.length; i++ ) { // Array of checker data.
                // Some checkers needed encoded URL.
                check_url = ( checks[i][2] ) ? encodeURIComponent( check_url ) : check_url;
                // Build HTML list of links.
                check_links += '<li class="dashicons-before dashicons-' + checks[i][3] + '"><a href="' + checks[i][1] + check_url + '" target="_blank">' + checks[i][0] + '</a></li>';
            }
            check_links += '</ol>';
            check_links += '<p class="description">' + checkers_vars.checkers_note + '</p>';
            check_links += '<p>' + checkers_vars.checkers_p_mid + '</p>';
            check_links += '<ol>';
            for ( i = 0; i < services.length; i++ ) { // Array of checker data.
                check_links += '<li class="dashicons-before dashicons-' + services[i][3] + '"><a href="' + services[i][1] + '" target="_blank">' + services[i][0] + '</a></li>';
            }
            check_links += '</ol>';

        } else { // Input field empty.
            check_links = '<p class="description">' + checkers_vars.checkers_else + '</p>';
        }

    // Print list.
    document.getElementById('checkers-results').innerHTML = check_links;
    document.getElementById('checkers-results').style.backgroundColor = '#F7F7F7';
    });
};

function checkers_format_url( url_to_check, encode_url = 0, hostname_only = 0) {
    // Some checkers needed encoded URL.
    url_formatted = ( encode_url ) ? encodeURIComponent( url_to_check ) : url_to_check;
    // Remove URL protocal; use hostname-only.
    url_formatted = ( hostname_only ) ? encodeURIComponent( url_to_check ) : url_to_check;

    return url_formatted;
}

function checkers_link_url( url_complete, service_name, dashicon) {
    html = '<li class="dashicons-before dashicons-' + dashicon + '"><a href="' + url_complete + '" target="_blank">' + service_name + '</a></li>';

    return html;
}

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


Use title, encoded (2):
https://www.facebook.com/search/str/%22Example+Post+Title%22/keywords_search
// ['Facebook: Search (post title)', 'https://www.facebook.com/search/str/%22', '%22/keywords_search', 2],

Site checks:
https://www.similarweb.com/website/example.com
https://w3techs.com/sites/info/example.com
https://www.quantcast.com/example.com
https://www.alexa.com/siteinfo/example.com
https://www.ssllabs.com/ssltest/analyze.html?d=current.org&latest
https://sitecheck.sucuri.net/results/current.org/2018/02/how-to-make-sure-your-website-works-for-all-users/
https://app.buzzsumo.com/research/most-shared?type=articles&result_type=total&num_days=365&general_article&infographic&video&how_to_article&list&what_post&why_post&page=1&q=http:%2F%2Fcurrent.org%2F2018%2F02%2Fhow-to-make-sure-your-website-works-for-all-users%2F

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
