const viewHeaderLinkWrapper = document.querySelector('.view-header__link-wrapper');
const viewHeaderLink = document.querySelector('.view-header__link');
const viewHeaderButton = viewHeaderLink && viewHeaderLink.querySelector('button');
const viewHeaderSelect = viewHeaderLink && viewHeaderLink.querySelector('.view-header__link-select');

const linkOpenClass = 'view-header__link--open';
let open = false;

const toggleDisplay = () => {
    open ? hide() : show();
};

const hide = () => {
    viewHeaderLink.style.height = `${viewHeaderSelect.scrollHeight}px`;

    viewHeaderButton.setAttribute('aria-expanded', false);
    viewHeaderSelect.setAttribute('aria-hidden', true);
    viewHeaderLink.classList.remove(linkOpenClass);
    open = false;
};

const show = () => {
    viewHeaderLink.style.height = `${viewHeaderSelect.scrollHeight * 2}px`;

    viewHeaderButton.setAttribute('aria-expanded', true);
    viewHeaderSelect.setAttribute('aria-hidden', false);
    viewHeaderLink.classList.add(linkOpenClass);
    open = true;
};

viewHeaderLink &&
    viewHeaderLink.addEventListener('click', toggleDisplay);

viewHeaderLink &&
    window.addEventListener('keyup', ({ key }) => {
        if (open && (key === 'Escape' || key === 'Esc')) {
            hide();
        }
    });

viewHeaderLink &&
    window.addEventListener('click', ({ target }) => {
        if (!viewHeaderLink.contains(target)) {
            hide();
        }
    });
