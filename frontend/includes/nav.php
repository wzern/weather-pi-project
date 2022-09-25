<?php session_start(); ?>

<!-- Page navigation -->
<div id="mySidenav" class="sidenav">
  <a href="javascript:void(0)" id="closebtn" onclick="closeNav()"> &times; </a>
  <a href="./">Dashboard</a>
  <a href="./settings.php">Settings</a>
  <a href=""></a>
  <?php echo (isset($_SESSION['loggedin'])) ? "<a href='./logout.php'>Log Out</a>" : "<a href='./login.php'>Log In</a>"; // Display relevent button depending on if the user is logged in or not ?>
  
</div>
