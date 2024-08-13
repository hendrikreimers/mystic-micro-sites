// Add event listener for visibility change
// Reloads the site (to handle the timeout) after the site was out of view (minimized tab/app etc)
document.addEventListener('visibilitychange', () => {
  // Check if the document is now visible
  if (document.visibilityState === 'visible') {
    // Reload the page
    location.reload();
  }
});

// Called if the load event or similar fired
function onLoad() {
  const reloadAfterAttrElement = document.getElementById('reloadAfter');

  const reloadAfterMinutes = reloadAfterAttrElement ? parseInt(
    document.getElementById('reloadAfter').dataset.reloadAfterMinutes || '3'
  ) : 3;

  // Force reload after X minutes
  setTimeout(() => {
    window.location.reload();
  }, 1000 * 60 * reloadAfterMinutes); // 1ms * 60 = 1min * reloadAfter = X minutes
}

// If you load this script through a different JS loader, its helpful not to wait for Load event
if (document.readyState === "loading") {
  window.addEventListener("DOMContentLoaded", () => onLoad());
} else onLoad();
