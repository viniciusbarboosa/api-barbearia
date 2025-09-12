// Simple polyfill for globalThis.crypto.getRandomValues on Node < 18
// Uses crypto.randomFillSync to fill typed arrays.
try {
  if (typeof globalThis.crypto === 'undefined' || typeof globalThis.crypto.getRandomValues !== 'function') {
    const { randomFillSync } = require('crypto');
    globalThis.crypto = {
      getRandomValues: function (buf) {
        if (!(buf instanceof Uint8Array)) {
          throw new TypeError('Expected Uint8Array');
        }
        return randomFillSync(buf);
      }
    };
  }
} catch (e) {
  // If anything goes wrong, fail silently — user should still prefer upgrading Node
}
