const accordionWrapper = [...document.querySelectorAll('.accordion-wrapper')];
const openAll = [...document.querySelectorAll('.accordion__open-all')];

const accordionHeader = '.accordion__header';
const accordionContent = '.accordion__content';
const accordionOpenClass = 'accordion--open';
const accordionTopOpen = 'accordion__open-all--open';

export const initAccordion = (wrapper = accordionWrapper) => {
    wrapper.forEach((item) => {
        const accordions = [...item.querySelectorAll('.accordion')];

        accordions.forEach((accordion) => {
            const heading = accordion.querySelector('.accordion__header');
            const content = accordion.querySelector('.accordion__content');

            setAccordionContentFocusable(content, false);

            heading.addEventListener('click', () => {
                toggleAccordion(accordion);
            });
        });
    });
};

const openAccordion = (accordion) => {
    const header = accordion.querySelector(accordionHeader);
    const content = accordion.querySelector(accordionContent);

    accordion.classList.add(accordionOpenClass);
    header.setAttribute('aria-expanded', true);
    content.setAttribute('aria-hidden', false);

    setAccordionContentFocusable(content, true);

    content.style.maxHeight = `${content.scrollHeight}px`;
};

const closeAccordion = (accordion) => {
    const header = accordion.querySelector(accordionHeader);
    const content = accordion.querySelector(accordionContent);

    accordion.classList.remove(accordionOpenClass);
    header.setAttribute('aria-expanded', false);
    content.setAttribute('aria-hidden', true);

    setAccordionContentFocusable(content, false);

    content.style.maxHeight = `0px`;
};

const toggleAccordion = (accordion) => {
    const isOpen = accordion.classList.contains(accordionOpenClass);

    !isOpen ? openAccordion(accordion) : closeAccordion(accordion);
};

const toggleAll = (event) => {
    event.preventDefault();
    const target = event.target;
    const openAllWrapper = target.closest('.accordion__open-all');
    const accordions     = [...target.closest('.accordion-wrapper').querySelectorAll('.accordion')];

    const shouldOpen = !target.classList.contains('accordion__open-all--open');

    if (shouldOpen) {
        accordions.forEach((accordion) => {
            openAccordion(accordion);
        });

        openAllWrapper.classList.add(accordionTopOpen);
    } else {
        accordions.forEach((accordion) => {
            closeAccordion(accordion);
        });

        openAllWrapper.classList.remove(accordionTopOpen);
    }
};

const setAccordionContentFocusable = (el, focusable) => {
    // set tabindex of all "el" anchors and buttons to -1 or 0
    const elements = el.querySelectorAll('a,button');
    elements.forEach((e) => e.setAttribute('tabindex', focusable ? 0 : -1));
}

(() => {
    accordionWrapper.length > 0 && initAccordion();
})();

accordionWrapper &&
    openAll.forEach((acc) => {
        acc.addEventListener('click', toggleAll);
    });
