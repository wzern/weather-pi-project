// Small formatting helpers used by charts.js

// Format a number with thousands separators, e.g. 2000000 -> "2,000,000".
// Pass `decimals` for a fixed number of decimal places; leave it out for "at most 1".
function formatNumber(value, decimals) {
  const options =
    decimals === undefined
      ? { maximumFractionDigits: 1 }
      : { minimumFractionDigits: decimals, maximumFractionDigits: decimals };
  return Number(value).toLocaleString(undefined, options);
}

// Describe how long ago a unix timestamp (in seconds) was, e.g. "5 min ago"
function timeAgo(epochSeconds) {
  const seconds = Math.max(0, Math.round(Date.now() / 1000 - epochSeconds));
  if (seconds < 60) return "just now";
  const minutes = Math.floor(seconds / 60);
  if (minutes < 60) return minutes + " min ago";
  const hours = Math.floor(minutes / 60);
  if (hours < 24) return hours + (hours === 1 ? " hour ago" : " hours ago");
  const days = Math.floor(hours / 24);
  return days + (days === 1 ? " day ago" : " days ago");
}

// Describe an averaging bucket size in seconds, e.g. 1800 -> "30-minute", 7200 -> "2-hour"
function formatBucket(seconds) {
  if (seconds % 3600 === 0) return seconds / 3600 + "-hour";
  return Math.round(seconds / 60) + "-minute";
}
