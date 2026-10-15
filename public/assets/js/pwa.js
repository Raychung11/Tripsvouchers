// SLV Tour Guide — PWA registration + Add-to-Home-Screen prompt.
// Registers the service worker, catches Chrome's beforeinstallprompt event,
// and shows a dismissible banner. iOS/Safari gets its own banner with the
// Share → Add to Home Screen instructions.

(function () {
  'use strict';

  if (!('serviceWorker' in navigator)) return;

  window.addEventListener('load', function () {
    navigator.serviceWorker.register('/sw.js', { scope: '/' }).catch(function (err) {
      console.warn('[SLV PWA] SW registration failed:', err);
    });
  });

  // Already launched from the home-screen? No prompt needed.
  var displayMode = window.matchMedia('(display-mode: standalone)').matches ||
                    window.navigator.standalone === true;
  if (displayMode) return;

  // Honour a previous dismissal for 14 days.
  try {
    var snoozed = parseInt(localStorage.getItem('slv_install_snooze_until') || '0', 10);
    if (snoozed && Date.now() < snoozed) return;
  } catch (e) {}

  var deferredPrompt = null;

  function makeBanner(bodyHtml, actionLabel, onAction) {
    var bar = document.createElement('div');
    bar.className = 'slv-install-banner';
    bar.innerHTML =
      '<div class="slv-install-inner">' +
        '<img src="/assets/img/icon-192.png" alt="" class="slv-install-icon">' +
        '<div class="slv-install-text">' +
          '<div class="slv-install-title">Install SLV Tour Guide</div>' +
          '<div class="slv-install-sub">' + bodyHtml + '</div>' +
        '</div>' +
        (actionLabel ? '<button class="slv-install-btn" type="button">' + actionLabel + '</button>' : '') +
        '<button class="slv-install-close" type="button" aria-label="Dismiss">×</button>' +
      '</div>';

    bar.querySelector('.slv-install-close').addEventListener('click', function () {
      try {
        localStorage.setItem('slv_install_snooze_until', String(Date.now() + 14 * 24 * 60 * 60 * 1000));
      } catch (e) {}
      bar.remove();
    });

    if (actionLabel && onAction) {
      bar.querySelector('.slv-install-btn').addEventListener('click', onAction);
    }

    document.body.appendChild(bar);
  }

  // Chrome / Edge / Android — native install prompt.
  window.addEventListener('beforeinstallprompt', function (e) {
    e.preventDefault();
    deferredPrompt = e;
    makeBanner(
      'Save it to your home screen for one-tap access.',
      'Install',
      function () {
        if (!deferredPrompt) return;
        deferredPrompt.prompt();
        deferredPrompt.userChoice.finally(function () {
          deferredPrompt = null;
          document.querySelector('.slv-install-banner')?.remove();
        });
      }
    );
  });

  window.addEventListener('appinstalled', function () {
    try { localStorage.setItem('slv_install_snooze_until', String(Date.now() + 365 * 24 * 60 * 60 * 1000)); } catch (e) {}
    document.querySelector('.slv-install-banner')?.remove();
  });

  // iOS Safari — no programmatic install, so show the share-sheet hint instead.
  var ua = window.navigator.userAgent || '';
  var isIOS = /iPad|iPhone|iPod/.test(ua) && !window.MSStream;
  var isSafari = /Safari/.test(ua) && !/CriOS|FxiOS|EdgiOS/.test(ua);
  if (isIOS && isSafari) {
    // Delay a touch so it doesn't fight page load.
    setTimeout(function () {
      makeBanner(
        'Tap <strong>Share</strong> <span aria-hidden="true">⬆︎</span> then <strong>Add to Home Screen</strong>.',
        null,
        null
      );
    }, 1500);
  }
})();
