// Run with: node scripts/tests/home-carousel.cjs
// Check controller behavior; browser scrolling/rendering is verified separately.
const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const vm = require('node:vm');

class Events {
  listeners = {};
  addEventListener(type, callback) { (this.listeners[type] ||= []).push(callback); }
  emit(type, data = {}) {
    const event = { defaultPrevented: false, preventDefault() { this.defaultPrevented = true; }, stopPropagation() {}, ...data };
    (this.listeners[type] || []).forEach(callback => callback(event));
    return event;
  }
}
class Element extends Events {
  children = [];
  dataset = {};
  attributes = {};
  classes = new Set();
  classList = { contains: name => this.classes.has(name), add: name => this.classes.add(name), remove: name => this.classes.delete(name) };
  scrollLeft = 0;
  clientWidth = 1400;
  constructor(kind = '', image = null) { super(); this.kind = kind; this.image = image; if (kind === 'card') this.classes.add('construction-home-projects__card'); }
  appendChild(node) { node.remove(); node.parent = this; this.children.push(node); }
  insertBefore(node, sibling) { node.remove(); node.parent = this; this.children.splice(this.children.indexOf(sibling), 0, node); }
  remove() { if (this.parent) this.parent.children.splice(this.parent.children.indexOf(this), 1); this.parent = null; }
  setAttribute(key, value) { this.attributes[key] = value; }
  removeAttribute(key) { delete this.attributes[key]; }
  getBoundingClientRect() { return { width: this.children.length * 300 + Math.max(0, this.children.length - 1) * 20 }; }
  get scrollWidth() { return this.track.children.reduce((width, batch) => width + batch.getBoundingClientRect().width + 20, -20); }
  querySelectorAll(selector) {
    const all = this.children.flatMap(child => [child, ...child.querySelectorAll('*')]);
    if (selector === '*') return all;
    if (selector === '.construction-home-projects__media img') return this.image ? [this.image] : [];
    if (selector === 'a, button, [tabindex]') return all.filter(node => node.kind === 'link');
    if (selector === '[id]') return all.filter(node => node.attributes.id);
    if (selector === '.construction-home-projects__body, .construction-home-projects__gallery') return all.filter(node => node.kind === 'details');
    return [];
  }
  cloneNode() {
    const clone = new Element(this.kind, this.image && { ...this.image });
    clone.attributes = { ...this.attributes };
    this.children.forEach(child => clone.appendChild(child.cloneNode()));
    return clone;
  }
  contains(node) { return this === node || this.querySelectorAll('*').includes(node); }
  matches() { return !!this.keyboardFocused; }
  hasPointerCapture(id) { return this.capture === id; }
  setPointerCapture(id) { this.capture = id; }
}
async function fixture(cardCount = 3, width = 1400) {
  let now = 0, nextId = 0, decodeImages, intersection, mutation;
  const decoding = new Promise(resolve => { decodeImages = resolve; });
  const frames = new Map(), timers = new Map();
  const marquee = new Element(), track = new Element(), body = new Element();
  marquee.clientWidth = width;
  marquee.track = track;
  for (let i = 0; i < cardCount; i++) {
    const card = new Element('card', { loading: 'lazy', decode: () => decoding });
    card.appendChild(new Element('details'));
    const link = new Element('link');
    link.setAttribute('id', `project-${i}`);
    card.appendChild(link);
    track.appendChild(card);
  }
  const motion = Object.assign(new Events(), { matches: false });
  const window = Object.assign(new Events(), {
    innerWidth: width, matchMedia: () => motion,
    getComputedStyle: () => ({ columnGap: '20px' }),
    requestAnimationFrame: callback => { frames.set(++nextId, callback); return nextId; },
    cancelAnimationFrame: id => frames.delete(id),
    setTimeout: (callback, delay) => { timers.set(++nextId, { callback, at: now + delay }); return nextId; },
    clearTimeout: id => timers.delete(id),
  });
  const document = Object.assign(new Events(), {
    hidden: false, body,
    querySelector: selector => selector === '[data-home-projects-marquee]' ? marquee : track,
    createElement: () => new Element(),
  });
  const source = fs.readFileSync(path.join(__dirname, '../../theme/construction/assets/js/main.js'), 'utf8');
  vm.runInNewContext(source.slice(source.indexOf('\t// Native scrolling with preloaded repeats'), source.indexOf('\n\tconst gsap = window.gsap;')), {
    document, window, performance: { now: () => now },
    IntersectionObserver: class { constructor(callback) { intersection = callback; } observe() {} },
    MutationObserver: class { constructor(callback) { mutation = callback; } observe() {} },
  });
  const advance = milliseconds => {
    now += milliseconds;
    [...timers].filter(([, timer]) => timer.at <= now).forEach(([id, timer]) => { timers.delete(id); timer.callback(); });
  };
  const renderFrame = () => { now += 16; [...frames].forEach(([id, callback]) => { frames.delete(id); callback(now); }); };
  return { marquee, track, body, motion, window, document, frames, advance, renderFrame,
    visible: value => intersection([{ isIntersecting: value }]),
    modal: value => { if (value) body.classes.add('has-project-modal'); else body.classes.delete('has-project-modal'); mutation(); },
    decode: async () => { decodeImages(); await new Promise(resolve => setImmediate(resolve)); },
  };
}
async function run() {
  const f = await fixture();
  const { marquee: m, track, frames, advance } = f;
  f.visible(true);
  advance(1000);
  assert.equal(frames.size, 0, 'autoplay must wait for cover images');
  assert(track.children.every(batch => batch.children.every(card => card.image.loading === 'eager')));
  const original = track.children.find(batch => !batch.attributes['aria-hidden']);
  assert.equal(original.children[0].querySelectorAll('*').length, 2, 'original project details are retained');
  assert(track.children.filter(batch => batch !== original).every(batch => batch.querySelectorAll('*').every(node => node.kind !== 'details' && !node.attributes.id)));
  await f.decode();
  advance(700);
  assert.equal(frames.size, 1);
  f.renderFrame(); f.renderFrame(); f.renderFrame();
  assert(m.scrollLeft > 3840, 'autoplay moves along the native scroll container');

  m.emit('pointerenter', { pointerType: 'mouse' });
  advance(1000);
  assert.equal(frames.size, 0, 'hover pauses autoplay');
  m.emit('pointerleave', { pointerType: 'mouse' });
  advance(700);
  assert.equal(frames.size, 1);
  const pointer = { isPrimary: true, button: 0, pointerId: 1, pointerType: 'touch', clientX: 300, clientY: 100 };
  m.emit('pointerdown', pointer); m.emit('touchstart');
  f.window.emit('pointercancel', pointer);
  advance(1200);
  assert.equal(frames.size, 0, 'pointer cancellation must not restart while touch remains held');
  m.emit('touchend', { touches: [] });
  for (let i = 0; i < 8; i++) { advance(500); m.emit('scroll'); }
  advance(699);
  assert.equal(frames.size, 0, 'swipe momentum keeps autoplay paused');
  advance(1);
  assert.equal(frames.size, 1);

  const count = track.children.length;
  m.emit('wheel', { deltaX: 30 });
  for (const left of [0, -95.5, m.scrollWidth - m.clientWidth, 100000, 37.25]) {
    m.scrollLeft = left; m.emit('scroll');
    assert(m.scrollLeft >= 960 && m.scrollWidth - m.clientWidth - m.scrollLeft >= 960);
    const phase = value => ((value % 960) + 960) % 960;
    assert(Math.abs(phase(left) - phase(m.scrollLeft)) < .001, 'edge wrapping preserves the visible card position');
    assert.equal(track.children.length, count, 'swipes do not insert new slides');
  }
  m.emit('pointerdown', { ...pointer, pointerType: 'mouse' });
  const before = m.scrollLeft;
  f.window.emit('pointermove', { ...pointer, pointerType: 'mouse', clientX: 200 });
  f.window.emit('pointerup', { ...pointer, pointerType: 'mouse' });
  assert.equal(m.scrollLeft, before + 100, 'mouse drag follows the pointer');
  assert(m.emit('click', { detail: 1 }).defaultPrevented, 'drag release must not open a project');
  m.emit('pointerdown', { ...pointer, pointerType: 'mouse' });
  f.window.emit('pointerup', { ...pointer, pointerType: 'mouse' });
  assert(!m.emit('click', { detail: 1 }).defaultPrevented, 'the next ordinary click still opens a project');
  assert(!m.emit('click', { detail: 0 }).defaultPrevented, 'keyboard activation is never suppressed');

  advance(700);
  assert(!m.emit('wheel', { deltaX: 0, deltaY: 100 }).defaultPrevented, 'vertical wheel scrolling remains native');
  assert.equal(frames.size, 1);
  const link = original.children[0].children.find(node => node.kind === 'link');
  link.keyboardFocused = true;
  m.emit('focusin', { target: link });
  advance(1000);
  assert.equal(frames.size, 0, 'keyboard focus holds the card still');
  m.emit('focusout', { relatedTarget: null });
  advance(700);
  assert.equal(frames.size, 1);
  f.visible(false); advance(1000); assert.equal(frames.size, 0);
  f.visible(true); advance(700); assert.equal(frames.size, 1);
  f.modal(true); advance(1000); assert.equal(frames.size, 0);
  f.modal(false); advance(700); assert.equal(frames.size, 1);
  f.document.hidden = true; f.document.emit('visibilitychange'); assert.equal(frames.size, 0);
  f.document.hidden = false; f.document.emit('visibilitychange'); advance(700); assert.equal(frames.size, 1);
  f.motion.matches = true; f.motion.emit('change'); advance(1000); assert.equal(frames.size, 0);
  const reducedPosition = m.scrollLeft;
  m.emit('pointerdown', { ...pointer, pointerType: 'mouse' });
  f.window.emit('pointermove', { ...pointer, pointerType: 'mouse', clientX: 250 });
  f.window.emit('pointerup', { ...pointer, pointerType: 'mouse' });
  assert.equal(m.scrollLeft, reducedPosition + 50, 'reduced motion retains manual dragging');
  f.window.emit('resize'); advance(120); assert.equal(track.children.length, count, 'height-only resizing preserves the track');

  const single = await fixture(1, 3840);
  assert(single.marquee.scrollWidth - single.marquee.scrollLeft - single.marquee.clientWidth > 320, 'one card still fills a wide viewport with buffered repeats');
  console.log('Home carousel passed: preloading, repeat coverage, momentum, mouse dragging, click safety, hover/focus, modal/visibility and reduced motion.');
}
run().catch(error => { console.error(error); process.exitCode = 1; });
