// Add event listener for visibility change
// Reloads the site (to handle the timeout) after the site was out of view (minimized tab/app etc)
document.addEventListener('visibilitychange', () => {
  // Check if the document is now visible
  if (document.visibilityState === 'visible') {
    // Reload the page
    location.reload();
  }
});
