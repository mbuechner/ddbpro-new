const breakpoints = { // Defined by generated-variables.scss
    s: 421,
    m: 701,
    l: 1181,
    xl: 1481,
};
export const isMobile = () => {
    return window.innerWidth <= breakpoints.l - 1;
};
