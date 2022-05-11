const $ = jQuery;
const $bodyHtml = $('body, html');
const speed = 200;

const smoothScroll = (target) => {
    if (Object.keys(target).length === 0) return;

    $bodyHtml.animate({
        scrollTop: target.offset().top - 100
    }, speed);

    target.focus();
    if (target.is(":focus")) {
        return false;
    } else {
        target.attr('tabindex', '-1');
        target.focus();
    };

    return false;
};


$('a[href*="#"]:not([href="#"]):not(.view-faq__toc-link)')
    .click(function () {
        if (location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') && location.hostname == this.hostname) {
            let target = $(this.hash);
            target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');

            if (target.length) {
                smoothScroll(target);
            }
        }
    });

// scroll to hash if coming from another page
$(window).on('load', () => {
    if (window.location.hash) {
        const $hash = $(window.location.hash);
        smoothScroll($hash);
    }
});
