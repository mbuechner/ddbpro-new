const allImages = [...document.querySelectorAll('.main-content .responsive-image__copyright')];

const copyFooter = document.querySelector('.footer__copyright');
const copySection = copyFooter && copyFooter.querySelector('.copyright__items');
const copy = document.querySelector('.copyright');
const copyHeader = copy && copy.querySelector('.copyright__header');
const copyContent = copy && copy.querySelector('.copyright__content');

const copyOpenClass = 'copyright--open';
let open = false;
let images = {};

const addToCopy = () => {
    if (copySection) {
        allImages.forEach((image) => {
            const imgUrl = image
                .querySelector('.responsive-image__copyright-image')
                .style
                .backgroundImage;
            images[imgUrl] = image;
        });

        if (allImages.length) {
            appendToHtml(images);

            // display section if there are any items
            copyFooter.style.display = 'block';
        }
    }
};

const appendToHtml = (images) => {
    Object.values(images).forEach((image) => {
        const href = image.dataset.link;

        // wrap in link if exists
        if (href) {
            const orgHtml = image.innerHTML;
            const newHtml = `
                <a href="${href}" class="responsive-image__copyright-link" target="_blank">
                    ${orgHtml}
                </a>`;
            image.innerHTML = newHtml;
        }

        image.classList.add('copyright__item');
        copySection.appendChild(image);
    });
};

const toggleDisplay = () => {
    copyHeader.addEventListener('click', () => {
        if (open) {
            copy.classList.remove(copyOpenClass);
            copyHeader.setAttribute('aria-expanded', false);
            copyContent.setAttribute('aria-hidden', true);

            copyContent.style.maxHeight = `0px`;
            open = false;
        } else {
            copy.classList.add(copyOpenClass);
            copyHeader.setAttribute('aria-expanded', true);
            copyContent.setAttribute('aria-hidden', false);

            copyContent.style.maxHeight = `${copyContent.scrollHeight}px`;
            open = true;
        }
    });
};

(() => {
    if (allImages.length) {
        addToCopy();
    }

    if (copy) {
        toggleDisplay();
    }
})();
