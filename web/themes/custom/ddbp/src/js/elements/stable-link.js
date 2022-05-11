const stableLink = document.querySelector('.stable-link');
const stableLinkToggle = document.querySelector('.stable-link__toggle');
const stableLinkContent = document.querySelector('.stable-link__tooltip-content');
const copyBtn = document.querySelector('.stable-link__tooltip-copy');

const showClass = 'stable-link--show';
const copiedClass = 'stable-link__tooltip-copy--copied';
let open = false;

const addUrl = () => {
    const location = window.location;
    stableLinkContent.value = location;
    stableLinkContent.parentNode.dataset.value = location;
};

const copyOnClick = () => {
    copyBtn.addEventListener('click', () => {
        stableLinkContent.select();
        stableLinkContent.setSelectionRange(0, 99999);
        // navigator clipboard api needs a secure context (https)
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(stableLinkContent.value);

            copyBtn.classList.add(copiedClass);

            setTimeout(() => {
                copyBtn.classList.remove(copiedClass);
            }, 1100);
        } else {
            console.error('Navigator clipboard api needs a secure context (https)!');
        }
    });
};

const show = () => {
    stableLink.classList.add(showClass);
    open = true;
};

const hide = () => {
    stableLink.classList.remove(showClass);
    open = false;
};

const toggleDisplay = () => {
    stableLinkToggle.addEventListener('click', () => {
        open ? hide() : show();
    });
};

(() => {
    if (stableLink) {
        addUrl();
        copyOnClick();
        toggleDisplay();
    }
})();

stableLink &&
    window.addEventListener('keyup', ({ key }) => {
        if (open && (key === 'Escape' || key === 'Esc')) {
            hide();
        }
    });

stableLink &&
    window.addEventListener('click', ({ target }) => {
        if (!stableLink.contains(target)) {
            hide();
        }
    });
