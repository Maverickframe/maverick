import Splide from '@splidejs/splide';

// Lighthouse's agentic-browsing audit ("Accessibility tree is not well-formed",
// axe rule aria-allowed-role) flags Splide v4: it sets role="group" (or
// role="tabpanel" when pagination is on) on every slide, but our slides are <li>,
// and neither role is allowed on <li>. Splide has no option to skip slide roles,
// so patch mount() once: strip the role from <li> slides after mounting. They fall
// back to the implicit `listitem` role, which keeps aria-roledescription="slide"
// and aria-label valid. <div>-based slides are untouched (role="group" is fine
// there). Re-applied on refresh/updated/resized because Splide re-inits slides
// (and their attributes) on those events; our listener is registered after the
// internal ones, so it runs last. Breakpoint destroy/re-mount cycles also go
// through this patched mount(), so the fix survives them.
const originalMount = Splide.prototype.mount;

function stripLiSlideRoles(root) {
  root
    .querySelectorAll('li.splide__slide[role="group"], li.splide__slide[role="tabpanel"]')
    .forEach((li) => li.removeAttribute('role'));
}

Splide.prototype.mount = function mount(...args) {
  const result = originalMount.apply(this, args);
  const fix = () => stripLiSlideRoles(this.root);
  fix();
  this.on('refresh updated resized', fix);
  return result;
};