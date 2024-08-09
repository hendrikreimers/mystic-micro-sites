function downloadVCard(data) {
  data = JSON.parse(htmlDecode(data));
  const vcardData = generateVCard(data);

  const blob = new Blob([vcardData], { type: 'text/vcard' });
  const url = URL.createObjectURL(blob);
  const a = document.createElement('a');

  if (navigator.userAgent.match(/ipad|iphone/i)) {
    // iOS specific handling
    window.location.href = url;
  } else if (navigator.userAgent.match(/android/i)) {
    // Android specific handling
    a.href = url;
    a.download = 'contact.vcf';
    a.click();
  } else {
    // Standard Download
    a.href = url;
    a.download = 'contact.vcf';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
  }

  URL.revokeObjectURL(url);
  return false;
}

function htmlDecode(input) {
  const parser = new DOMParser();
  const doc = parser.parseFromString(input, 'text/html');
  return doc.documentElement.textContent || '';
}

function generateVCard(data) {
  return [
    `BEGIN:VCARD`,
    `VERSION:3.0`,
    `FN:${data.firstName} ${data.lastName}`,
    `N:${data.lastName};${data.firstName};;;`,
    `ORG:${data.companyName}`,
    `TEL;TYPE=WORK,VOICE:${data.phone}`,
    `TEL;TYPE=CELL,VOICE:${data.mobile}`,
    `ADR;TYPE=WORK,PREF:;;${data.address}`,
    `EMAIL:${data.email}`,
    `URL:${data.website}`,
    `END:VCARD`
  ].join("\n").trim();
}
