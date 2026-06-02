import * as THREE from 'three';
import { SVGLoader } from 'three/examples/jsm/loaders/SVGLoader';

const container = document.querySelector('.js-particles-wrapper');
const IMAGE_URL = container?.dataset?.img;

if (container && IMAGE_URL) {
  const MY_COLOR = '#2D40AE';

  const scene = new THREE.Scene();
  const aspectRatio = container.offsetWidth / container.offsetHeight;
  const camera = new THREE.PerspectiveCamera(60, aspectRatio, 0.1, 1000);

  const renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
  renderer.setSize(container.offsetWidth, container.offsetHeight);
  renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
  renderer.outputColorSpace = THREE.SRGBColorSpace;
  container.appendChild(renderer.domElement);
  container.classList.add('is-active');

  let points;
  let initialPos;
  const mouse = new THREE.Vector3(1000, 1000, 1000);

  const createCircleTexture = () => {
    const canvas = document.createElement('canvas');
    canvas.width = 128; canvas.height = 128;
    const ctx = canvas.getContext('2d');
    ctx.fillStyle = '#ffffff';
    ctx.beginPath();
    ctx.arc(64, 64, 60, 0, Math.PI * 2);
    ctx.fill();
    return new THREE.CanvasTexture(canvas);
  };

  const initParticles = (svgPoints) => {
    const count = svgPoints.length;
    const positions = new Float32Array(count * 3);
    initialPos = new Float32Array(count * 3);

    const box = new THREE.Box3().setFromPoints(svgPoints);
    const center = new THREE.Vector3();
    box.getCenter(center);
    const size = new THREE.Vector3();
    box.getSize(size);
    const scale = 10 / Math.max(size.x, size.y);

    svgPoints.forEach((p, i) => {
      const x = (p.x - center.x) * scale;
      const y = -(p.y - center.y) * scale;
      const z = 0;

      positions.set([x, y, z], i * 3);
      initialPos.set([x, y, z], i * 3);
    });

    const geometry = new THREE.BufferGeometry();
    geometry.setAttribute('position', new THREE.BufferAttribute(positions, 3));

    const material = new THREE.PointsMaterial({
      color: MY_COLOR,
      size: 0.12,
      map: createCircleTexture(),
      transparent: true,
      opacity: 1,
      depthWrite: false,
      blending: THREE.NormalBlending
    });

    points = new THREE.Points(geometry, material);
    scene.add(points);
  };

  const loader = new SVGLoader();
  loader.load(IMAGE_URL, (data) => {
    const allPoints = [];
    const STEP = 1; // Крок фільтрацыі: 1 - усе кропкі, 2 - кожная другая, 3 - кожная трэцяя

    data.paths.forEach((path, index) => {
      if (index % STEP === 0) {
        const shapes = SVGLoader.createShapes(path);
        shapes.forEach((shape) => {
          const center = new THREE.Vector2();
          shape.getPoint(0.5, center);
          allPoints.push(new THREE.Vector3(center.x, center.y, 0));
        });
      }
    });

    initParticles(allPoints);
  });

  camera.position.z = 10;

  container.addEventListener('mousemove', (e) => {
    const rect = container.getBoundingClientRect();
    const x = ((e.clientX - rect.left) / container.offsetWidth) * 2 - 1;
    const y = -((e.clientY - rect.top) / container.offsetHeight) * 2 + 1;
    mouse.set(x * 6, y * 6, 0);
  });

  container.addEventListener('mouseleave', () => {
    mouse.set(1000, 1000, 1000);
  });

  const animate = () => {
    requestAnimationFrame(animate);
    if (!points || !initialPos) return;

    const posAttr = points.geometry.attributes.position;
    const time = performance.now() * 0.001;

    for (let i = 0; i < posAttr.count; i += 1) {
      const i3 = i * 3;

      const noiseX = Math.sin(time + i) * 0.12 + Math.sin(time * 0.5 + i * 0.7) * 0.05;
      const noiseY = Math.cos(time + i * 1.1) * 0.12 + Math.sin(time * 0.3 + i * 0.5) * 0.05;

      let tx = initialPos[i3] + noiseX;
      let ty = initialPos[i3 + 1] + noiseY;

      const dx = tx - mouse.x;
      const dy = ty - mouse.y;
      const dist = Math.sqrt(dx * dx + dy * dy);

      if (dist < 1.4) {
        const force = (1.4 - dist) * 0.6;
        tx += (dx / dist) * force;
        ty += (dy / dist) * force;
      }

      posAttr.array[i3] += (tx - posAttr.array[i3]) * 0.08;
      posAttr.array[i3 + 1] += (ty - posAttr.array[i3 + 1]) * 0.08;
    }

    posAttr.needsUpdate = true;
    renderer.render(scene, camera);
  };

  window.addEventListener('resize', () => {
    camera.aspect = container.offsetWidth / container.offsetHeight;
    camera.updateProjectionMatrix();
    renderer.setSize(container.offsetWidth, container.offsetHeight);
  });

  animate();
}