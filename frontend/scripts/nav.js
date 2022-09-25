// Set the width of the side navigation to 250px and the left margin of the page content to 250px
function openNav() {
  if (window.innerWidth <= 600) {
    document.getElementById("main").style.marginLeft = "0px";
  } else {
    document.getElementById("main").style.marginLeft = "250px";
  }
  document.getElementById("mySidenav").style.width = "250px";
  document.getElementById("openbtn").style.display = "none";
}

// Set the width of the side navigation to 0 and the left margin of the page content to 0
function closeNav() {
  document.getElementById("mySidenav").style.width = "0px";
  document.getElementById("main").style.marginLeft = "0px";
  document.getElementById("openbtn").style.display = "block";
}
