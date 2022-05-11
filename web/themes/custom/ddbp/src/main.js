import Vue from 'vue';

// polyfills
// import TrioAssets from 'trio-assets-js';
(() => {
    // TrioAssets.parallax();
})();

// stylings
import './scss/app.scss';

// helpers
import './js/helpers/smooth-scroll';
// scripts
// navigation
import './js/navigation';
// elements
import './js/elements/accordion';
import './js/elements/departments';
import './js/elements/faq-sidebar';
import './js/elements/footer-copyright';
import './js/elements/stable-link';
import './js/elements/view-header';

Vue.config.productionTip = false;

