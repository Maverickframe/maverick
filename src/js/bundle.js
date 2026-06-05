import 'lazysizes';
import 'lazysizes/plugins/unveilhooks/ls.unveilhooks';

import './components/animated-scroll';
import './components/svg-sprite';

import './components/accordeon';
import './components/blogFilter';
import './components/contacts';
import './components/counters';
import './components/filters';
import './components/header';
import './components/intersectionObserver';
import './components/lightbox';
import './components/menu';
import './components/modals';

import './components/scrollTop';
import './components/services';
import './components/showMore';
import './components/sliders';
import './components/tabs';
import './components/toc';
import './components/videoPlay';
import './components/visualResultsGallery';
import './components/select';

import './components/collapse';
import './components/gallery';
import './components/gsap';
// three.js (~600KB) loaded lazily — only on pages with the particles block (worldwide-rendering)
if (document.querySelector('.js-particles-wrapper')) {
  import('./components/particlesAnimation');
}
import './components/showMoreText';
import './components/showSidebar';