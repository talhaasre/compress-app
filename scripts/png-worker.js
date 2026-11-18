// Encodes RGBA pixels as a palette PNG off the main thread, so the page stays
// responsive while large PNGs are quantised. Uses UPNG.js (MIT) + pako (MIT).
// UPNG.js registers itself on `window`, which workers don't have.
self.window = self;
self.importScripts('../vendor/pako.min.js', '../vendor/UPNG.js');

self.onmessage = function (e) {
  const { id, buffer, width, height, colors } = e.data;
  try {
    const png = self.UPNG.encode([buffer], width, height, colors);
    self.postMessage({ id, png }, [png]);
  } catch (err) {
    self.postMessage({ id, error: String(err && err.message || err) });
  }
};
