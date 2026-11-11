<?php require_once __DIR__ . '/helpers.php'; ?>

<!-- Page navigation -->
<nav id="mySidenav" class="sidenav" aria-label="Main navigation">
  <a href="javascript:void(0)" id="closebtn" onclick="closeNav()" aria-label="Close menu">&times;</a>
  <a href="./">Dashboard</a>
  <a href="./settings.php">Settings</a>
  <?php if (is_logged_in()): ?>
    <a href="./logout.php">Log Out</a>
  <?php else: ?>
    <a href="./login.php">Log In</a>
  <?php endif; ?>
</nav>
