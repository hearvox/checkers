window.onload = function() {
    /* Indicate a change to user (helpful when submitting a 2nd URL). */
    document.getElementById( 'checkers-input-url' ).addEventListener( 'focus', function() {
        document.getElementById( 'checkers-results' ).style.backgroundColor = '#fff';
    });

    document.getElementById( 'find-posts-link' ).addEventListener( 'click', function() {
        // document.getElementById( 'checkers-input-url' ).removeAttribute( "required" );
    });

    document.getElementById( 'find-posts-close' ).addEventListener( 'click', function() {
        // document.getElementById( 'checkers-input-url' ).setAttribute( "required", "required" );
    });

    document.getElementById( 'find-posts-submit' ).addEventListener( 'click', function() {
        document.getElementById( 'checkers-input-url' ).removeAttribute( "required" );
    });


    if ( document.getElementById( 'checkers-more-button' ) ) {

        // Style webpage results list (to match webiste results list).
        document.getElementById( 'checkers-results' ).classList.add( 'checkers-done' );

        document.getElementById( 'checkers-more-button' ).addEventListener( 'click', function( event ) {
            event.preventDefault();
            let input_url = document.getElementById( 'checkers-input-url' ); // Get form input.
            //Select URL and copy into clipboard.
            input_url.select();
            document.execCommand( 'Copy' );
            input_url.blur(); // Unfocus selection.
            document.getElementById( 'checkers-results' ).style.backgroundColor = 'transparent';

            document.getElementById( 'checkers-more-links' ).style.display = 'block';
            document.getElementById( 'checkers-more-button' ).style.display = 'none';
            // <p class="description">' + checkers_vars.checkers_error + '</p>';
        });
    }
};

/**
 * Check for valid URL (allows protocol-relative).
 *
 * Copyright (c) 2010-2013 Diego Perini, MIT licensed
 * @see https://gist.github.com/dperini/729294
 * @see https://mathiasbynens.be/demo/url-regex
 * @see https://github.com/jquery-validation/jquery-validation/
 *
 * @param  string  URL
 * @return boolean True if passed valid URL, else false.
 */
function checkers_validate_url( url ) {
    return /^(?:(?:(?:https?|ftp):)?\/\/)(?:\S+(?::\S*)?@)?(?:(?!(?:10|127)(?:\.\d{1,3}){3})(?!(?:169\.254|192\.168)(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z\u00a1-\uffff0-9]-*)*[a-z\u00a1-\uffff0-9]+)(?:\.(?:[a-z\u00a1-\uffff0-9]-*)*[a-z\u00a1-\uffff0-9]+)*(?:\.(?:[a-z\u00a1-\uffff]{2,})).?)(?::\d{2,5})?(?:[/?#]\S*)?$/i.test( url );
}
