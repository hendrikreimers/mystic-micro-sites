// Add event helper
function addEvent(obj, evt, fn) {
  if (obj.addEventListener) {
    obj.addEventListener(evt, fn, false);
  }
  else if (obj.attachEvent) {
    obj.attachEvent("on" + evt, fn);
  }
}

// OnLoad
function onLoad() {
  // Extract the hash part of the URL
  let hash = window.location.hash.slice(1).split('/'); // Remove the "#" from the beginning

  // Create an object from the hash parameters
  let params = new URLSearchParams('id=' + hash[1] + '&key=' + hash[0]);
  let fileId = params.get('id');
  let keyParts = params.get('key');

  let queryParams =  new URLSearchParams(window.location.search);
  let noVcard = queryParams.get('noVcard') || '0';

  // Send the data to the server via POST
  if (fileId && keyParts) {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", window.location.pathname, true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    xhr.onload = function () {
      if (xhr.status === 200) {
        // Parse the response JSON
        var response = JSON.parse(xhr.responseText);

        // Leitet weiter zu der URL, die der Server zurückgegeben hat
        window.location.replace( response.redirectUrl);
      }
    };

    xhr.send("id=" + encodeURIComponent(fileId) + "&key=" + encodeURIComponent(keyParts) + "&noVcard=" + encodeURIComponent(noVcard));
  } else {
    window.location.replace('/404');
  }
}

// If you load this script through a different JS loader, its helpful not to wait for Load event
if (document.readyState === "loading") {
  addEvent(window, "DOMContentLoaded", () => onLoad());
} else onLoad();
