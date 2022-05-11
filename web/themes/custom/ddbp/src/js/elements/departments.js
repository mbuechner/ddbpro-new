import { isMobile } from '../helpers/is-mobile';

const departments = document.querySelector('.departments');
const departmentsDep = departments && departments.querySelector('.departments__departments');
const departmentImages = departments && [...departments.querySelectorAll('.departments__images .department-teaser-mini__image')];
const departmentLinks = departments && [...departments.querySelectorAll('.departments__departments .department-teaser-mini a')];

const departmentShowClass = 'department-teaser-mini--show';

const setActive = () => {
    // make the first item active
    departmentLinks[0].classList.add(departmentShowClass);
    departmentImages[0].classList.add(departmentShowClass);
};

const addHover = () => {
    departmentsDep.addEventListener('mouseover', ({ target }) => {
        // remove active image styles
        [...departmentImages, ...departmentLinks]
            .forEach((item) => item.classList.remove(departmentShowClass));

        // get hovered element
        const activeLinkId = target.getAttribute('data-id')
            ? target.dataset.id
            : target.closest('[data-id]').dataset.id;

        // select active image and add styles to it
        const activeImage =
            document.querySelector(`.departments__images .department-teaser-mini__image[data-id="${activeLinkId}"]`);
        const activeLink =
            document.querySelector(`.departments__departments a[data-id="${activeLinkId}"]`);

        activeImage.classList.add(departmentShowClass);
        activeLink.classList.add(departmentShowClass);
    });
};

(() => {
    if (departments && !isMobile()) {
        setActive();
        addHover();
    }
})();
