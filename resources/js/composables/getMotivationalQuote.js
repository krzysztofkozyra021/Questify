
function getMotivationalQuote() {
  // This function fetches a motivational quote from the backend and returns the quote text.
  return fetch('/dashboard/motivational-quote')
    .then(response => response.json())
    .then(data => {
      // The backend returns an array with an object containing q (quote) and a (author)
      if (Array.isArray(data) && data.length > 0 && data[0].q) {
        return `${data[0].q} — ${data[0].a}`;
      }
      // Fallback if unexpected response
      return "Stay motivated!";
    })
    .catch(() => "Stay motivated!");
}


export { getMotivationalQuote };