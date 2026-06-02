// eslint-disable-next-line import/no-unresolved
import svgSpriteString from 'virtual:svg-sprite';

const sprite = new DOMParser()
  .parseFromString(svgSpriteString, 'image/svg+xml')
  .documentElement;

document.body.appendChild(sprite);