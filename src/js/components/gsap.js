import { gsap } from 'gsap';

import { ScrollTrigger } from 'gsap/ScrollTrigger';

gsap.registerPlugin(ScrollTrigger);

function getActivePath() {
  if (window.matchMedia('(max-width: 1270px)').matches) {
    return document.querySelector('.path-anim[data-size="sm"]');
  }

  if (window.matchMedia('(max-width: 1750px)').matches) {
    return document.querySelector('.path-anim[data-size="md"]');
  }

  return document.querySelector('.path-anim[data-size="lg"]');
}

if (document.querySelector('.js-workflow-item')) {
  const items = document.querySelectorAll('.js-workflow-item');
  const activePath = getActivePath();
  const length = activePath.getTotalLength();
  let ticking = false;

  gsap.set(activePath, {
    strokeDasharray: length,
    strokeDashoffset: length
  });

  ScrollTrigger.create({
    trigger: '.workflow__items',
    start: 'top 50%',
    end: 'bottom 50%',
    fastScrollEnd: true,
    onUpdate(self) {
      if (ticking) return;

      ticking = true;
      requestAnimationFrame(() => {
        const { progress } = self;

        gsap.set(activePath, {
          strokeDashoffset: length * (1 - progress)
        });
        items.forEach((item, index) => {
          const itemProgress = index / (items.length - 1);
          item.classList.toggle('is-active', progress >= itemProgress);
        });
        ticking = false;
      });
    }
  });
}

const animations = {
  up: { y: 40, opacity: 0 },
  down: { y: -80, opacity: 0 },
  left: { x: -40, opacity: 0 },
  right: { x: 40, opacity: 0 },
  fade: { opacity: 0 }
};

const observer = new IntersectionObserver(
  (entries) => {
    entries.forEach((entry) => {
      if (!entry.isIntersecting) return;

      const el = entry.target;

      if (el.classList.contains('js-reveal-group')) {
        const items = Array.from(el.querySelectorAll('.js-reveal-item'));

        gsap.fromTo(
          items.reverse(),
          { x: -60, opacity: 0 },
          {
            x: 0,
            opacity: 1,
            duration: 0.6,
            ease: 'power2.out',
            stagger: 0.12,
            clearProps: 'transform'
          }
        );

        observer.unobserve(el);
        return;
      }

      if (el.classList.contains('js-highlight')) {
        gsap.delayedCall(0.2, () => {
          el.classList.add('is-animated');
        });

        observer.unobserve(el);
        return;
      }

      const type = el.dataset.anim || 'fade';

      gsap.fromTo(
        el,
        animations[type],
        {
          x: 0,
          y: 0,
          opacity: 1,
          duration: 0.6,
          ease: 'power2.out',
          clearProps: 'transform'
        }
      );

      observer.unobserve(el);
    });
  },
  {
    threshold: 0.15,
    rootMargin: '0px 0px -10% 0px'
  }
);

document.querySelectorAll('.js-reveal').forEach((el) => {
  if (el.classList.contains('js-reveal-init')) {
    const type = el.dataset.anim || 'fade';
    gsap.fromTo(
      el,
      animations[type],
      {
        x: 0,
        y: 0,
        opacity: 1,
        duration: 0.6,
        ease: 'power2.out',
        clearProps: 'transform'
      }
    );
  } else {
    observer.observe(el);
  }
});

document.querySelectorAll('.js-reveal-group').forEach((el) => {
  observer.observe(el);
});

document.querySelectorAll('.js-quote').forEach((el) => {
  gsap.to(el, {
    backgroundPositionX: '0%',
    ease: 'none',
    scrollTrigger: {
      trigger: el,
      start: 'top 75%',
      end: 'top 10%',
      scrub: true
    }
  });
});

if (document.querySelector('.js-video-anim')) {
  gsap.to('.js-video-anim', {
    scaleX: 1.1,
    scrollTrigger: {
      trigger: '.js-video-anim',
      start: 'top center',
      end: 'bottom center',
      scrub: true
    }
  });
}

document.querySelectorAll('.js-highlight').forEach((el) => {
  observer.observe(el);
});