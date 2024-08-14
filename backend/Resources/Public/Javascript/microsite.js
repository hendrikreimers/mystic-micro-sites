// Add event helper
function addEvent(obj, evt, fn) {
  if (obj.addEventListener) {
    obj.addEventListener(evt, fn, false);
  }
  else if (obj.attachEvent) {
    obj.attachEvent("on" + evt, fn);
  }
}

// DevTools open detection
function devToolsDetector() {
  var devtoolsOpen = false;

  function isFunctionAllowed() {
    try {
      // Attempts to create a function dynamically
      new Function('debugger');
      return true; // If successful, it is allowed
    } catch (e) {
      return false; // If an error occurs, it is not allowed
    }
  }

  function detectDevTools() {
    // Method 1: Only execute if permitted by CSPs
    if (isFunctionAllowed()) {
      const devtools = new Function('debugger');
      try {
        devtools();
      } catch(e) {}
    }

    // Method 2: Monitoring the window sizes (if DevTools are docked)
    var widthThreshold = window.outerWidth - window.innerWidth > 160;
    var heightThreshold = window.outerHeight - window.innerHeight > 160;
    if (widthThreshold || heightThreshold) {
      devtoolsOpen = true;
    }

    // Method 3: Stack trace monitoring with 'debugger'
    const start = performance.now();
    debugger; // Provocation of a breakpoint in DevTools
    const end = performance.now();

    if (end - start > 100) {
      devtoolsOpen = true;
    }

    // Method 4: Recognition by image object and property manipulation
    var element = new Image();
    Object.defineProperty(element, 'id', {
      get: function() {
        devtoolsOpen = true;
      }
    });
    console.log(element);

    // If DevTools are open, hide the content
    if (devtoolsOpen) {
      window.location.href = '/404';
      // document.body.style.visibility = 'hidden';
    }
  }

  // Regular check (every second)
  setInterval(detectDevTools, 1000);

  // Also check for window size changes
  addEvent(window, 'resize', detectDevTools);

  // Also check for window size changes
  detectDevTools();
}

// Called if the load event or similar fired
function onLoad() {
  // JS is allowed. Remove the hidden class in body tag
  document.getElementsByTagName('body')[0].classList.remove('hidden');

  // Add event listener for visibility change
  // Reloads the site (to handle the timeout) after the site was out of view (minimized tab/app etc)
  addEvent(document, 'visibilitychange', () => {
    // Check if the document is now visible
    if (document.visibilityState === 'visible') {
      // Reload the page
      location.reload();
    }
  });

  const reloadAfterAttrElement = document.getElementById('reloadAfter');
  const reloadAfterMinutes = reloadAfterAttrElement ? parseInt(
    document.getElementById('reloadAfter').dataset.reloadAfterMinutes || '3'
  ) : 3;

  // Force reload after X minutes
  setTimeout(() => {
    window.location.reload();
  }, 1000 * 60 * reloadAfterMinutes); // 1ms * 60 = 1min * reloadAfter = X minutes

  // Prevents the context menu from opening (right-click)
  addEvent(document, 'contextmenu', (e) => {
    e.preventDefault();
  });

  // Prevents the highlighting of text
  addEvent(document, 'selectstart', (e) => {
    e.preventDefault();
  });

  // Prevents content from being dragged
  addEvent(document, 'dragstart', (e) => {
    e.preventDefault();
  });
  addEvent(document, "mouseout", function(e) {
    e = e ? e : window.event;
    var from = e.relatedTarget || e.toElement;
    if (!from || from.nodeName === "HTML") {
      // stop your drag event here
    }
  });

  // Prevents the execution of actions with the middle or right mouse button
  addEvent(document, 'mousedown', (e) => {
    if (e.button === 2 || e.button === 1) {
      e.preventDefault();
    }
  });

  // Detect developer tools and hide the content
  devToolsDetector();

  // Overwrite console outputs
  console.log = console.warn = console.error = console.info = () => {
    // Empty function
  };

  // Prevents screenshots using the PrintScreen button
  addEvent(document, 'keydown', (e) => {
    if (e.key === 'PrintScreen') {
      document.body.style.visibility = 'hidden';
    }
  });

  // Prevents printing
  window.onbeforeprint = (e) => { e.preventDefault(); };

  // Prevents copying
  addEvent(document, 'copy', (e) => { e.preventDefault(); });

  // Prevents insertion
  addEvent(document, 'paste', (e) => { e.preventDefault(); });

  // Prevents screenshots
  let visibilityTimeout = null;
  addEvent(document, 'keydown', (e) => {
    if (e.key === 'PrintScreen' || e.key === 'Control' || e.key === 'Shift' ) {
      document.body.style.visibility = 'hidden';
      if ( !visibilityTimeout ) {
        visibilityTimeout = setTimeout(() => {
          document.body.style.visibility = 'visible';
          visibilityTimeout = null;
        }, 5000);
      }
    }
  });

  // Auto-blur when leaving the mouse
  ['mouseleave','mouseout'].forEach((evtType) => addEvent(document, evtType, (e) => {
    e = e ? e : window.event;
    var from = e.relatedTarget || e.toElement;
    if (!from || from.nodeName === "HTML") {
      document.body.style.filter = 'blur(8px)';
    } else document.body.style.filter = 'blur(8px)';
  }));
  ['mouseenter','mouseover'].forEach((evtType) => addEvent(document, evtType, (e) => {
    e = e ? e : window.event;
    var from = e.relatedTarget || e.toElement;
    if (!from || from.nodeName === "HTML") {
      document.body.style.filter = 'none';
    } else document.body.style.filter = 'none';
  }));
}

// If you load this script through a different JS loader, its helpful not to wait for Load event
if (document.readyState === "loading") {
  addEvent(window, "DOMContentLoaded", () => onLoad());
} else onLoad();
