window.onload = function() {
    // When submitting URL vis Search Posts modal form, don't require page's URL field.
    document.getElementById( 'find-posts-submit' ).addEventListener( 'click', function() {
        document.getElementById( 'checkers-input-url' ).removeAttribute( "required" );
    });

    // Indicate a change to user (helpful when submitting a 2nd URL). */
    // Add style when user enters URL field.
    document.getElementById( 'checkers-input-url' ).addEventListener( 'focus', function() {
        document.getElementById( 'checkers-results' ).style.backgroundColor = '#f7f7f7';
    });

    // If form data submitted, this hide/show button element exists.
    if ( document.getElementById( 'label_expand_1' ) ) {

        // Restore style when user exits URL field.
        document.getElementById( 'checkers-input-url' ).addEventListener( 'blur', function() {
            document.getElementById( 'checkers-results' ).style.backgroundColor = '#fff';
        });

        // Copy user-entered URL into clipboard.
        document.getElementById( 'label_expand_1' ).addEventListener( 'click', function( event ) {
            event.preventDefault();
            // Get form input.
            let input_url = document.getElementById( 'checkers-input-url' );
            //Select URL and copy into clipboard.
            input_url.select();
            document.execCommand( 'Copy' );
            input_url.blur(); // Unfocus selection.
            document.getElementById( 'checkers-results' ).style.backgroundColor = '#fff';
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

jQuery(document).ready(function($) {

    /*
     * jQuery simple and accessible hide-show system (collapsible regions), using ARIA
     * @version v1.8.0
     * Website: https://a11y.nicolas-hoffmann.net/hide-show/
     * License MIT: https://github.com/nico3333fr/jquery-accessible-hide-show-aria/blob/master/LICENSE
     */
    // loading expand paragraphs
    // these are recommended settings by a11y experts. You may update to fulfill your needs, but be sure of what you’re doing.
    var attr_control = 'data-controls',
        attr_expanded = 'aria-expanded',
        attr_labelledby = 'data-labelledby',
        attr_hidden = 'data-hidden',
        $expandmore = $('.js-expandmore'),
        $body = $('body'),
        delay = 1500,
        hash = window.location.hash.replace("#", ""),
        multiexpandable = true,
        expand_all_text = 'Expand All',
        collapse_all_text = 'Collapse All';


    if ($expandmore.length) { // if there are at least one :)
        $expandmore.each(function(index_to_expand) {
            var $this = $(this),
                index_lisible = index_to_expand + 1,
                options = $this.data(),
                $hideshow_prefix_classes = typeof options.hideshowPrefixClass !== 'undefined' ? options.hideshowPrefixClass + '-' : '',
                $to_expand = $this.next(".js-to_expand"),
                $expandmore_text = $this.html();

            // BG added 'button' class.
            $this.html('<button type="button" class="' + $hideshow_prefix_classes + 'expandmore__button js-expandmore-button button"><span class="' + $hideshow_prefix_classes + 'expandmore__symbol" aria-hidden="true"></span>' + $expandmore_text + '</button>');
            var $button = $this.children('.js-expandmore-button');

            $to_expand.addClass($hideshow_prefix_classes + 'expandmore__to_expand').stop().delay(delay).queue(function() {
                var $this = $(this);
                if ($this.hasClass('js-first_load')) {
                    $this.removeClass('js-first_load');
                }
            });

            $button.attr('id', 'label_expand_' + index_lisible);
            $button.attr(attr_control, 'expand_' + index_lisible);
            $button.attr(attr_expanded, 'false');

            $to_expand.attr('id', 'expand_' + index_lisible);
            $to_expand.attr(attr_hidden, 'true');
            $to_expand.attr(attr_labelledby, 'label_expand_' + index_lisible);

            // quick tip to open (if it has class is-opened or if hash is in expand)
            if ($to_expand.hasClass('is-opened') || (hash !== "" && $to_expand.find($("#" + hash)).length)) {
                $button.addClass('is-opened').attr(attr_expanded, 'true');
                $to_expand.removeClass('is-opened').removeAttr(attr_hidden);
            }


        });


    }


    $body.on('click', '.js-expandmore-button', function(event) {
        var $this = $(this),
            $destination = $('#' + $this.attr(attr_control));

        if ($this.attr(attr_expanded) === 'false') {

            if (multiexpandable === false) {
                $('.js-expandmore-button').removeClass('is-opened').attr(attr_expanded, 'false');
                $('.js-to_expand').attr(attr_hidden, 'true');
            }

            $this.addClass('is-opened').attr(attr_expanded, 'true');
            $destination.removeAttr(attr_hidden);
        } else {
            $this.removeClass('is-opened').attr(attr_expanded, 'false');
            $destination.attr(attr_hidden, 'true');
        }

        event.preventDefault();

    });

    $body.on('click keydown', '.js-expandmore', function(event) {
        var $this = $(this),
            $target = $(event.target),
            $button_in = $this.find('.js-expandmore-button');

        if (!$target.is($button_in) && !$target.closest($button_in).length) {

            if (event.type === 'click') {
                $button_in.trigger('click');
                return false;
            }
            if (event.type === 'keydown' && (event.keyCode === 13 || event.keyCode === 32)) {
                $button_in.trigger('click');
                return false;
            }

        }


    });

    $body.on('click keydown', '.js-expandmore-all', function(event) {
        var $this = $(this),
            is_expanded = $this.attr('data-expand'),
            $all_buttons = $('.js-expandmore-button'),
            $all_destinations = $('.js-to_expand');

        if (
            event.type === 'click' ||
            (event.type === 'keydown' && (event.keyCode === 13 || event.keyCode === 32))
        ) {
            if (is_expanded === 'true') {

                $all_buttons.addClass('is-opened').attr(attr_expanded, 'true');
                $all_destinations.removeAttr(attr_hidden);
                $this.attr('data-expand', 'false').html(collapse_all_text);
            } else {
                $all_buttons.removeClass('is-opened').attr(attr_expanded, 'false');
                $all_destinations.attr(attr_hidden, 'true');
                $this.attr('data-expand', 'true').html(expand_all_text);
            }

        }


    });


});
