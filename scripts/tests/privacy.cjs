const assert = require('node:assert/strict');
const fs = require('node:fs');
const vm = require('node:vm');
const path = require('node:path');
const source = fs.readFileSync(path.join(__dirname, '../../theme/construction/assets/js/privacy.js'), 'utf8');

// An in-memory DOM tests tag loading without contacting Google or sending telemetry.
function visitor({id = 'G-TEST123', choice, analyticsCookie = false} = {}) {
  const jar = new Map();
  const scripts = [];
  const events = {};
  let reloads = 0;
  function node(dataset = {}) {
    return {dataset, hidden: true, events: {}, setAttribute() {}, focus() {},
      addEventListener(name, callback) { this.events[name] = callback; }};
  }
  const reject = node({cookieChoice: 'false'});
  const accept = node({cookieChoice: 'true'});
  const close = node();
  const settings = node();
  const banner = node();
  banner.querySelector = selector => selector === '[data-cookie-close]' ? close : reject;
  banner.querySelectorAll = () => [reject, accept];
  banner.contains = () => false;
  if (choice !== undefined) jar.set('construction_consent', encodeURIComponent(JSON.stringify(choice)));
  if (analyticsCookie) jar.set('_ga', 'old-cookie');
  const document = {
    getElementById: () => banner,
    querySelectorAll: () => [settings],
    createElement: () => ({}),
    head: {append: script => scripts.push(script)},
    addEventListener: (name, callback) => { events[name] = callback; },
    referrer: 'https://example.com/start?email=private@example.com#secret',
  };
  Object.defineProperty(document, 'cookie', {
    get: () => [...jar].map(([key, value]) => `${key}=${value}`).join('; '),
    set: text => {
      const [entry] = text.split(';');
      const split = entry.indexOf('=');
      const key = entry.slice(0, split);
      if (/Max-Age=0(?:;|$)/.test(text)) jar.delete(key);
      else jar.set(key, entry.slice(split + 1));
    },
  });
  const window = {constructionPrivacy: {measurementId: id, language: 'lv', version: 1},
    addEventListener(name, callback) { events[name] = callback; }};
  const context = {document, window, URL, Date, location: {
    href: 'https://oaksbuild.lv/contact/?email=private@example.com#secret',
    hostname: 'oaksbuild.lv', protocol: 'https:', reload: () => reloads++,
  }, localStorage: {setItem() {}, removeItem() {}}};
  vm.runInNewContext(source, context);
  events.DOMContentLoaded();
  return {window, banner, scripts, jar, reject, accept, settings, events, reloads: () => reloads};
}
const first = visitor({analyticsCookie: true});
assert.equal(first.banner.hidden, false);
assert.equal(first.scripts.length, 0);
assert.equal(first.jar.has('_ga'), false);
first.reject.events.click();
assert.equal(first.scripts.length, 0);
assert.equal(first.banner.hidden, true);
const rejected = JSON.parse(decodeURIComponent(first.jar.get('construction_consent')));
assert.equal(visitor({choice: rejected}).banner.hidden, true);
assert.equal(visitor({choice: rejected}).scripts.length, 0);
first.settings.events.click();
first.accept.events.click();
assert.equal(first.scripts.length, 1);
first.accept.events.click();
assert.equal(first.scripts.length, 1, 'acceptance cannot load a duplicate tag');
const commands = first.window.dataLayer.map(args => [...args]);
const consent = commands.find(command => command[0] === 'consent' && command[1] === 'update')[2];
assert.equal(consent.analytics_storage, 'granted');
for (const field of ['ad_storage', 'ad_user_data', 'ad_personalization']) assert.equal(consent[field], 'denied');
const config = commands.find(command => command[0] === 'config')[2];
assert.equal(config.page_location, 'https://oaksbuild.lv/contact/');
assert.equal(config.page_referrer, 'https://example.com/start');
first.jar.set('_ga', 'new-cookie');
first.reject.events.click();
assert.equal(first.window['ga-disable-G-TEST123'], true);
assert.equal(first.jar.has('_ga'), false);
assert.equal(first.reloads(), 1, 'withdrawal unloads the tag');
const valid = {v: 1, analytics: true, at: Date.now()};
assert.equal(visitor({choice: valid}).scripts.length, 1);
assert.equal(visitor({id: '', choice: valid}).scripts.length, 0, 'Local configuration never loads Google');
for (const id of ['', 'invalid']) {
  const unconfigured = visitor({id, analyticsCookie: true});
  assert.equal(unconfigured.banner.hidden, true, 'no prompt before collection is ready');
  assert.equal(unconfigured.settings.hidden, true, 'no settings control without optional collection');
  assert.equal(unconfigured.jar.has('construction_consent'), false, 'no unnecessary consent cookie');
  assert.equal(unconfigured.jar.has('_ga'), false, 'old statistics cookies are cleared');
  assert.equal(unconfigured.scripts.length, 0);
}
for (const choice of [{...valid, v: 0}, {...valid, at: 0}, {...valid, at: Date.now() + 86400000}, {...valid, analytics: 'true'}]) {
  const current = visitor({choice});
  assert.equal(current.banner.hidden, false);
  assert.equal(current.scripts.length, 0);
}
const otherTab = visitor({choice: valid});
otherTab.jar.set('construction_consent', encodeURIComponent(JSON.stringify(rejected)));
otherTab.events.storage({key: 'construction_consent_changed'});
assert.equal(otherTab.reloads(), 1, 'withdrawal also stops another tab');
console.log('PASS: readiness gating, consent persistence, invalid preferences, one tag, advertising disabled, URL redaction, withdrawal, cross-tab withdrawal and Local exclusion');
