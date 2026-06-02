document.addEventListener('DOMContentLoaded', () => {
  function playVideoItem(e) {
    if (e.target.classList.contains('js-video-item')) {
      e.target.classList.add('is-active');

      const video = e.target.querySelector('video');
      if (video?.paused) {
        video.setAttribute('controls', 'controls');
        video.play();
      }
      return;
    }

    if (e.target.classList.contains('js-video')) {
      e.target.classList.add('is-active');

      const video = e.target;

      if (video.paused) {
        video.play();
      }
    }
  }

  function hoverVideoItem(e) {
    if (!e.target.classList.contains('js-video-item-hover')) {
      return;
    }

    const video = e.target;
    if (video?.paused) video.play();
  }

  function pauseVideoItem(e) {
    if (!e.target.classList.contains('js-video-item-hover')) {
      return;
    }

    const video = e.target;

    if (!video?.paused) video.pause();
    if (video.closest && video.closest('.gallery-item')) video.currentTime = 0;
  }

  const autoplayVideos = document.querySelectorAll('.js-video-autoplay');

  const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      const video = entry.target;

      if (entry.isIntersecting) {
        if (!video.dataset.loaded && video.dataset.src) {
          video.src = video.dataset.src;
          video.load();
          video.dataset.loaded = 'true';
        }

        video.play().catch(() => {});
      } else {
        video.pause();
      }
    });
  }, {
    threshold: 0.5,
    rootMargin: '200px'
  });

  autoplayVideos.forEach((video) => observer.observe(video));

  document.body.addEventListener('touchstart', playVideoItem);
  document.body.addEventListener('click', playVideoItem);
  document.body.addEventListener('mouseover', hoverVideoItem);
  document.body.addEventListener('mouseout', pauseVideoItem);
});