// @todo: one day this shall be written in vanillaJS 🤞

const $ = jQuery;
const $window = $(window);
const $htmlBody = $('html, body');

const anchorActiveClass = 'view-faq__toc-link--active';
const anchorClass = 'view-faq__toc-link';

const $anchorItems = $('.view-faq__toc');
const anchors = $(`.${anchorClass}`);

let $anchorsSections = $('.view-content__item');

const scrollingOffset = 200;
const justifyOffset = 30;
const speed = 800;

const scrollSpy = () => {
    const currentSection = getActiveSections();
    setActive(currentSection);
};

function getActiveSections() {
    $anchorsSections = $('.view-content__item'); // update
    let current = -1;
    const sections = [];
    $anchorsSections.each((index) => {
        const scrollTop = $window.scrollTop();
        const sectionOffsetTop =
            $anchorsSections[index].offsetTop -
            scrollingOffset -
            justifyOffset;
        const sectionIsDefined = $anchorsSections[index].id !== '';

        if (scrollTop >= sectionOffsetTop && sectionIsDefined) {
            current++;
            sections[current] = $anchorsSections[index].id;
        }
    });

    return sections[current];
}

function setActive(key) {
    const $activeAnchor = $(`.${anchorClass}[href="#${key}"]`);
    if (!$activeAnchor.hasClass(anchorActiveClass)) {
        anchors.removeClass(anchorActiveClass);
        $activeAnchor.addClass(anchorActiveClass);
    }
}

function enableSmoothScrollingAnchors() {
    anchors.on('click', (e) => {
        e.preventDefault();
        let $target = $(e.currentTarget.hash);

        $htmlBody.animate({
            scrollTop: $target.offset().top - 100
        }, speed, null, () => {
            $target = $(e.currentTarget.hash);
            $target.attr('tabindex', '-1');
            $target.focus();

            return false;
        });
    });
}


if ($anchorItems.length > 0) {
    enableSmoothScrollingAnchors();
    $window.scroll(scrollSpy);
}

// scroll to hash if coming from another page
$(document).ready(function () {
    if (window.location.hash) {
        const $hash = $(window.location.hash);

        if ($hash.length > 0) {
            $htmlBody.stop().animate({
                scrollTop: $hash.offset().top - scrollingOffset,
            }, speed);
        }
    }
});
