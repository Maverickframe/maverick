import Splide from '@splidejs/splide';
// eslint-disable-next-line import/no-extraneous-dependencies
import { AutoScroll } from '@splidejs/splide-extension-auto-scroll';
// Must be imported wherever Splide instances are created: strips the invalid
// role="group|tabpanel" Splide puts on <li> slides (Lighthouse agentic-browsing
// audit / axe aria-allowed-role). See splide-a11y.js for details.
import './splide-a11y';

function addScrollBar(splide, scrollbarSelector) {
  const scrollbar = document.querySelector(scrollbarSelector);
  if (!scrollbar) return;

  const bar = scrollbar.querySelector('.js-scrollbar-bar');

  splide.on('mounted move resize', () => {
    const { Elements, Controller } = splide.Components;
    const { track, slides } = Elements;
    const slideWidth = slides[0].offsetWidth;
    const trackWidth = track.clientWidth;

    const totalSlides = slides.length;
    const visibleCount = trackWidth / slideWidth;
    const visibleRatio = visibleCount / totalSlides;

    const end = Controller.getEnd();
    const progress = splide.index / end;

    scrollbar.style.display = 'block';
    const scrollbarWidth = scrollbar.clientWidth;
    const barWidth = scrollbarWidth * visibleRatio;
    const maxTranslate = scrollbarWidth - barWidth;
    const translateX = progress * maxTranslate;

    if (maxTranslate <= 0) {
      scrollbar.style.display = 'none';
    } else {
      bar.style.width = `${barWidth}px`;
      bar.style.transform = `translateX(${translateX}px)`;
    }
  });
}

// Start AutoScroll marquees on first user interaction — or, on desktop only,
// after a 3s fallback timer. Lighthouse's Speed Index measures when the page
// LOOKS settled; a marquee moving during the trace inflates SI/LCP forever
// (PSI mobile SI was stuck at ~5.9s). The desktop trace is short, so a 3s
// timer never lands inside it (desktop stable at 91-98). The MOBILE trace on
// throttled 4G runs well past any fixed delay — a timer-started marquee kept
// bouncing mobile between 74 and 97 — so on ≤1270px (Splide's own breakpoint)
// there is NO timer: motion starts on the first touch/scroll/click, which real
// mobile users produce within seconds while Lighthouse never does.
// autoStart:false in each autoScroll config + this helper. Re-plays after
// Splide refresh() (the ttb hero sliders refresh on window load).
const AUTOSCROLL_DELAY_MS = 3000;
function delayAutoScrollStart(splide) {
  let started = false;
  const events = ['pointerdown', 'keydown', 'wheel', 'touchstart', 'scroll'];
  const start = () => {
    if (started) return;
    started = true;
    events.forEach((e) => window.removeEventListener(e, start));
    splide.Components.AutoScroll?.play();
  };
  events.forEach((e) => window.addEventListener(e, start, { passive: true }));
  if (!window.matchMedia('(max-width: 1270px)').matches) {
    setTimeout(start, AUTOSCROLL_DELAY_MS);
  }
  splide.on('refresh', () => {
    if (started) setTimeout(() => splide.Components.AutoScroll?.play(), 0);
  });
}

const heroSlider = document.querySelector('.js-hero-slider');
if (heroSlider) {
  const splide = new Splide(heroSlider, {
    autoplay: true,
    classes: {
      pagination: 'splide__pagination is-custom',
      page: 'splide__pagination__page is-custom'
    },
    clones: 0,
    flickPower: 300,
    gap: 0,
    interval: 10000,
    lazyLoad: 'sequential',
    perPage: 1,
    type: 'loop',
    breakpoints: {
      1200: {
        pagination: false
      }
    }
  });

  splide.mount();

  const currentSlide = document.querySelector('.js-hero-slider-current');

  if (currentSlide) {
    splide.on('move', (index) => {
      currentSlide.innerHTML = index + 1;
    });
  }
}

const services3dSlider = document.querySelector('.js-services-3d-slider');
if (services3dSlider) {
  const splide = new Splide(services3dSlider, {
    destroy: true,
    breakpoints: {
      1200: {
        destroy: false,
        arrows: false,
        autoplay: true,
        drag: true,
        fixedWidth: 278,
        flickPower: 300,
        gap: 25,
        interval: 10000,
        padding: { right: '40%' },
        pagination: true,
        perPage: 1,
        snap: true,
        type: 'loop'
      }
    }
  });

  splide.mount();
}

const departmentSlider = document.querySelector('.js-department-slider');
if (departmentSlider) {
  const splide = new Splide(departmentSlider, {
    destroy: true,
    breakpoints: {
      1200: {
        destroy: false,
        arrows: false,
        autoplay: true,
        drag: true,
        fixedWidth: 278,
        flickPower: 300,
        gap: 25,
        interval: 10000,
        padding: { right: '40%' },
        pagination: true,
        perPage: 1,
        snap: true,
        type: 'loop'
      }
    }
  });

  splide.mount();
}

const clientsSlider = document.querySelector('.js-clients-slider');
if (clientsSlider) {
  const splide = new Splide(clientsSlider, {
    arrows: true,
    drag: true,
    gap: 32,
    pagination: false,
    perMove: 1,
    perPage: 6,
    autoWidth: true,
    flickPower: 300,
    snap: true,
    type: 'loop',
    breakpoints: {
      1200: {
        arrows: false,
        gap: 16,
        perPage: 1
      }
    }
  });

  splide.mount();
}

const teamSlider = document.querySelector('.js-team-slider');
if (teamSlider) {
  const splide = new Splide(teamSlider, {
    arrows: false,
    clones: 0,
    drag: true,
    gap: 16,
    pagination: false,
    perPage: 1,
    fixedWidth: 210,
    flickPower: 300,
    snap: true,
    breakpoints: {
      768: {
        gap: 8,
        fixedWidth: 168
      }
    }
  });

  addScrollBar(splide, '.js-team-slider .js-scrollbar');

  splide.mount();
}

const casesSlider = document.querySelector('.js-cases-slider');
if (casesSlider) {
  const splide = new Splide(casesSlider, {
    arrows: true,
    autoplay: false,
    classes: {
      pagination: 'splide__pagination is-custom',
      page: 'splide__pagination__page is-custom'
    },
    flickPower: 300,
    gap: 16,
    pagination: true,
    perPage: 1,
    type: 'loop',
    breakpoints: {
      1200: {
        arrows: false,
        pagination: false
      },
      768: {
        pagination: true
      }
    }
  });

  splide.mount();

  splide.on('move', () => {
    const videos = document.querySelectorAll('video');
    [...videos].forEach((video) => video.pause());
  });

  splide.on('moved', () => {
    const video = document.querySelector('.splide__slide.is-active video');
    video.setAttribute('preload', 'auto');
  });
}

const workflowSlider = document.querySelector('.js-workflow-slider');
if (workflowSlider) {
  const splide = new Splide(workflowSlider, {
    destroy: true,
    arrows: false,
    drag: true,
    gap: 25,
    pagination: true,
    perPage: 1,
    flickPower: 300,
    snap: true,
    breakpoints: {
      1200: {
        destroy: false
      }
    }
  });

  splide.mount();
}

const solutionsSlider = document.querySelector('.js-solutions-slider');
if (solutionsSlider) {
  const splide = new Splide(solutionsSlider, {
    destroy: true,
    arrows: false,
    drag: true,
    gap: 25,
    pagination: true,
    perPage: 1,
    flickPower: 300,
    fixedWidth: 288,
    snap: true,
    breakpoints: {
      1200: {
        destroy: false
      }
    }
  });

  splide.mount();
}

const howCreateSlider = document.querySelector('.js-how-create-slider');
if (howCreateSlider) {
  const splide = new Splide(howCreateSlider, {
    destroy: true,
    arrows: false,
    clones: 0,
    drag: true,
    gap: 25,
    pagination: true,
    perPage: 1,
    flickPower: 300,
    snap: true,
    type: 'loop',
    breakpoints: {
      1200: {
        destroy: false
      }
    }
  });

  splide.mount();
}

const whyChooseSlider = document.querySelector('.js-why-choose-slider');
if (whyChooseSlider) {
  const splide = new Splide(whyChooseSlider, {
    destroy: true,
    arrows: false,
    clones: 0,
    drag: true,
    gap: 25,
    pagination: true,
    perPage: 1,
    flickPower: 300,
    snap: true,
    type: 'loop',
    breakpoints: {
      1200: {
        destroy: false
      }
    }
  });

  splide.mount();
}

const matchSlider = document.querySelector('.js-match-slider');
if (matchSlider) {
  const splide = new Splide(matchSlider, {
    destroy: true,
    arrows: false,
    drag: true,
    gap: 25,
    pagination: true,
    perPage: 1,
    flickPower: 300,
    snap: true,
    type: 'loop',
    breakpoints: {
      1200: {
        destroy: false
      }
    }
  });

  splide.mount();
}

const trustedSlider = document.querySelector('.js-trusted-slider');
if (trustedSlider) {
  const splide = new Splide(trustedSlider, {
    autoWidth: true,
    arrows: false,
    drag: true,
    gap: 35,
    pagination: false,
    fixedHeight: 51,
    type: 'loop',
    autoScroll: {
      autoStart: false,
      speed: 0.5
    },
    breakpoints: {
      1200: {
        gap: 20,
        fixedHeight: 36
      }
    }
  });

  splide.mount({ AutoScroll });
  delayAutoScrollStart(splide);
}

// stack marquee → migrated to pure-CSS .mfs-marquee (see components/service-page/stack.php)

const cgiSliderMobile = document.querySelector('.js-cgi-slider-mobile');
if (cgiSliderMobile) {
  const splide = new Splide(cgiSliderMobile, {
    destroy: true,
    arrows: false,
    drag: true,
    gap: 25,
    pagination: true,
    perPage: 1,
    flickPower: 300,
    snap: true,
    type: 'loop',
    breakpoints: {
      1200: {
        destroy: false
      }
    }
  });

  splide.mount();
}

const cgiSlider = document.querySelector('.js-cgi-slider');
if (cgiSlider) {
  const splide = new Splide(cgiSlider, {
    arrows: true,
    drag: true,
    gap: 13,
    pagination: true,
    perPage: 4,
    flickPower: 300,
    snap: true,
    type: 'loop',
    breakpoints: {
      1200: {
        destroy: true
      }
    }
  });

  splide.mount();
}

const estateSlider = document.querySelector('.js-estate-slider');
if (estateSlider) {
  const splide = new Splide(estateSlider, {
    destroy: true,
    arrows: false,
    clones: 0,
    drag: true,
    gap: 25,
    pagination: true,
    perPage: 1,
    flickPower: 300,
    snap: true,
    type: 'loop',
    breakpoints: {
      1200: {
        destroy: false
      }
    }
  });

  splide.mount();
}

const climateSlider = document.querySelector('.js-climate-slider');
if (climateSlider) {
  const main = new Splide(climateSlider, {
    arrows: true,
    autoHeight: true,
    drag: true,
    gap: '-80%',
    pagination: false,
    perPage: 1,
    flickPower: 300,
    snap: true,
    type: 'loop',
    trimSpace: false,
    padding: {
      right: '13.9375rem'
    },
    breakpoints: {
      1200: {
        arrows: false,
        gap: 12,
        padding: { left: '10%', right: '10%' }
      }
    }
  });

  main.mount();
}

const devTeamSlider = document.querySelector('.js-dev-team-slider');
if (devTeamSlider) {
  const splide = new Splide(devTeamSlider, {
    destroy: true,
    arrows: false,
    clones: 0,
    drag: true,
    gap: 25,
    pagination: true,
    perPage: 1,
    flickPower: 300,
    snap: true,
    type: 'loop',
    breakpoints: {
      1200: {
        destroy: false
      }
    }
  });

  splide.mount();
}

const kindsSlider = document.querySelector('.js-kinds-slider');
if (kindsSlider) {
  const splide = new Splide(kindsSlider, {
    arrows: true,
    drag: false,
    gap: 62,
    pagination: false,
    perPage: 1,
    padding: { left: '27%', right: '27%' },
    flickPower: 300,
    focus: 'center',
    snap: false,
    type: 'loop',
    breakpoints: {
      1200: {
        arrows: false,
        drag: true,
        gap: 12,
        perPage: 1,
        padding: { left: '10%', right: '10%' },
        snap: true
      }
    }
  });

  splide.mount();
}

const packagesSlider = document.querySelector('.js-packages-slider');
if (packagesSlider) {
  const splide = new Splide(packagesSlider, {
    arrows: true,
    drag: true,
    gap: 14,
    pagination: true,
    perPage: 3,
    flickPower: 300,
    snap: true,
    type: 'loop',
    breakpoints: {
      1200: {
        arrows: false,
        gap: 12,
        padding: { left: '10%', right: '10%' },
        pagination: false,
        perPage: 1
      }
    }
  });

  splide.mount();
}

// Solutions-page hero uses short landscape case images. Splide's auto loop-clone
// count (~2/side) gives too small a buffer for them, so the upward-scrolling right
// column shows a black seam. Force extra clones there ONLY — combined with the
// height-constraint CSS (html.single-solutions in hero-front.scss) the column stays
// viewport-height so the js-reveal IntersectionObserver (0.15 threshold) still fires.
// Homepage hero (tall portrait crops) is left on Splide defaults — untouched.
// Declared here (before its first use in the presentation-hero block) to avoid a
// temporal-dead-zone ReferenceError if that block ever renders.
const heroLoopExtra = document.documentElement.classList.contains('single-solutions')
  ? { clones: 12 }
  : {};

const heroPresentationSlider = document.querySelector('.js-presentation-hero-slider');
if (heroPresentationSlider) {
  const splide = new Splide(heroPresentationSlider, {
    autoWidth: true,
    arrows: false,
    drag: true,
    gap: 15,
    pagination: false,
    type: 'loop',
    ...heroLoopExtra,
    autoScroll: {
      autoStart: false,
      speed: 0.5
    }
  });

  splide.mount({ AutoScroll });
  delayAutoScrollStart(splide);
}

const solCapSlider = document.querySelector('.js-sol-cap-slider');
if (solCapSlider) {
  const splide = new Splide(solCapSlider, {
    type: 'loop',
    drag: 'free',
    arrows: false,
    pagination: false,
    gap: 20,
    fixedWidth: 455,
    fixedHeight: 600,
    flickPower: 300,
    autoScroll: {
      autoStart: false,
      speed: 0.6,
      pauseOnHover: true,
      pauseOnFocus: false
    },
    breakpoints: {
      768: {
        fixedWidth: 300,
        fixedHeight: 440,
        gap: 16
      }
    }
  });

  splide.mount({ AutoScroll });
  delayAutoScrollStart(splide);
}

// trusted block (2 rows) → migrated to pure-CSS .mfs-marquee (components/blocks/trusted/trusted.php)

const teamNewSlider = document.querySelector('.js-team-new-slider');
if (teamNewSlider) {
  const splide = new Splide(teamNewSlider, {
    arrows: false,
    clones: 0,
    center: true,
    drag: true,
    gap: 30,
    pagination: false,
    perPage: 1,
    fixedWidth: 407,
    flickPower: 300,
    snap: true,
    breakpoints: {
      1750: {
        fixedWidth: 287
      },
      1270: {
        padding: { right: '15%' },
        gap: 15,
        fixedWidth: 168
      }
    }
  });

  splide.mount();
}

const reviewsSlider = document.querySelector('.js-reviews-slider');

if (reviewsSlider) {
  const main = new Splide(reviewsSlider, {
    arrows: false,
    drag: true,
    gap: 40,
    pagination: false,
    perPage: 1,
    flickPower: 300,
    snap: true,
    type: 'loop',
    breakpoints: {
      1270: {
        arrows: true,
        pagination: true
      }
    }
  });

  const thumbnails = new Splide('.js-reviews-thumbnails-slider', {
    gap: '0.5rem',
    arrows: false,
    pagination: false,
    perPage: 1,
    type: 'loop',
    drag: false,
    snap: false,
    breakpoints: {
      1270: {
        destroy: true
      }
    }
  });

  main.mount();
  thumbnails.mount();

  const goToNextThumb = (index) => {
    const nextIndex = index + 1;
    thumbnails.go(nextIndex);
  };

  goToNextThumb(main.index);

  main.on('move', (newIndex) => {
    goToNextThumb(newIndex);
  });

  thumbnails.on('click', (slide) => {
    main.go(slide.index);
  });

  // "Next review" buttons inside the thumbnails advance the main slider.
  // Bound directly so they also work on desktop, where the thumbnails
  // slider is destroyed (>=1270px) and its slide-click handler is gone.
  document.querySelectorAll('.reviews-item-thumb__arrow').forEach((btn) => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      // Stop the click from also reaching Splide's slide-click handler
      // (thumbnails.on('click') -> main.go(slide.index)), which would
      // conflict with this advance and snap the slider back.
      e.stopPropagation();
      e.stopImmediatePropagation();
      main.go('>');
    }, true);
  });
}

const visualResultsSlider = document.querySelector('.js-visual-results-slider');
if (visualResultsSlider) {
  const splide = new Splide(visualResultsSlider, {
    arrows: true,
    drag: true,
    gap: 0,
    pagination: false,
    perPage: 1,
    padding: { left: '13%', right: '13%' },
    flickPower: 300,
    snap: true,
    type: 'loop'
  });

  splide.mount();
}

const visualsItemsSlider = document.querySelector('.js-visuals-items-slider');
if (visualsItemsSlider) {
  const splide = new Splide(visualsItemsSlider, {
    arrows: true,
    drag: true,
    gap: 20,
    pagination: true,
    perPage: 4,
    padding: { left: '5%', right: '5%' },
    flickPower: 300,
    snap: true,
    type: 'loop',
    breakpoints: {
      1440: {
        perPage: 3,
        padding: { left: '10%', right: '10%' }
      },
      1270: {
        arrows: false,
        gap: 15,
        perPage: 1,
        padding: 0
      }
    }
  });

  splide.mount();
}

const selectiveWorksSlider = document.querySelector('.js-selective-works-slider');
if (selectiveWorksSlider) {
  const splide = new Splide(selectiveWorksSlider, {
    arrows: true,
    drag: true,
    gap: 30,
    pagination: true,
    perPage: 1,
    flickPower: 300,
    snap: true,
    type: 'loop',
    breakpoints: {
      1750: {
        gap: 20
      },
      1270: {
        arrows: false,
        gap: 15
      }
    }
  });

  splide.mount();
}

const heroHoverSliderLeft = document.querySelector('.js-hero-hover-slider-left');
if (heroHoverSliderLeft) {
  const splide = new Splide(heroHoverSliderLeft, {
    arrows: false,
    direction: 'ttb',
    drag: true,
    gap: 30,
    height: '100%',
    pagination: false,
    type: 'loop',
    ...heroLoopExtra,
    autoScroll: {
      autoStart: false,
      speed: 0.5
    },
    breakpoints: {
      1750: {
        gap: 20
      },
      1270: {
        direction: 'rtl',
        fixedHeight: 177,
        fixedWidth: 145,
        gap: 10
      }
    }
  });

  splide.mount({ AutoScroll });
  delayAutoScrollStart(splide);

  // Recompute ttb loop geometry after images/layout settle
  // (otherwise the vertical slider collapses to a black area on >=1750px until a resize)
  window.addEventListener('load', () => splide.refresh());
}

const heroHoverSliderRight = document.querySelector('.js-hero-hover-slider-right');
if (heroHoverSliderRight) {
  const splide = new Splide(heroHoverSliderRight, {
    arrows: false,
    direction: 'ttb',
    drag: true,
    gap: 30,
    height: '100%',
    pagination: false,
    type: 'loop',
    ...heroLoopExtra,
    autoScroll: {
      autoStart: false,
      speed: -0.5
    },
    breakpoints: {
      1750: {
        gap: 20
      },
      1270: {
        direction: 'rtl',
        fixedHeight: 177,
        fixedWidth: 145,
        gap: 10
      }
    }
  });

  splide.mount({ AutoScroll });
  delayAutoScrollStart(splide);

  // Recompute ttb loop geometry after images/layout settle
  // (otherwise the vertical slider collapses to a black area on >=1750px until a resize)
  window.addEventListener('load', () => splide.refresh());
}

const whatWeDoItems = document.querySelectorAll('.js-what-we-do-slider');
if (whatWeDoItems.length > 0) {
  whatWeDoItems.forEach((slider) => {
    const splide = new Splide(slider, {
      destroy: true,
      autoWidth: true,
      arrows: false,
      drag: true,
      gap: 20,
      pagination: true,
      type: 'loop',
      breakpoints: {
        1100: {
          destroy: false
        }
      }
    });

    splide.mount();
  });
}

const completenessVisualSlider = document.querySelector('.js-completeness-visual-slider');
if (completenessVisualSlider) {
  const splide = new Splide(completenessVisualSlider, {
    destroy: true,
    perPage: 1,
    arrows: false,
    drag: true,
    gap: 15,
    clones: 0,
    type: 'loop',
    breakpoints: {
      1200: {
        destroy: false
      }
    }
  });

  splide.mount();
}

// NOTE: use querySelectorAll — pages like 3D Furniture Rendering have TWO
// production-process blocks. querySelector only mounted the first, leaving the
// second slider unmounted (first slide stretched to full height = big white gap).
const productionProcessSliders = document.querySelectorAll('.js-production-process-slider');
productionProcessSliders.forEach((productionProcessSlider) => {
  const splide = new Splide(productionProcessSlider, {
    destroy: true,
    perPage: 1,
    arrows: false,
    drag: true,
    gap: 15,
    clones: 0,
    type: 'loop',
    breakpoints: {
      1200: {
        destroy: false
      }
    }
  });

  splide.mount();
});

const challengesSlider = document.querySelector('.js-challenges-slider');
if (challengesSlider) {
  const splide = new Splide(challengesSlider, {
    arrows: true,
    drag: true,
    gap: 20,
    pagination: true,
    perPage: 4,
    padding: { left: '5%', right: '5%' },
    flickPower: 300,
    snap: true,
    type: 'loop',
    breakpoints: {
      1440: {
        perPage: 3,
        padding: { left: '11%', right: '11%' }
      },
      1270: {
        arrows: false,
        gap: 15,
        perPage: 1,
        padding: 0
      }
    }
  });

  splide.mount();
}

const casesAccordionSlider = document.querySelector('.js-cases-accordion-slider');
if (casesAccordionSlider) {
  const splide = new Splide(casesAccordionSlider, {
    arrows: false,
    drag: true,
    gap: 20,
    pagination: true,
    perPage: 1,
    padding: { left: '18.2%', right: '18.2%' },
    flickPower: 300,
    snap: true,
    type: 'loop',
    breakpoints: {
      1440: {
        padding: { left: '16%', right: '16%' }
      },
      1270: {
        arrows: false,
        gap: 15,
        padding: 0
      }
    }
  });

  splide.mount();
}