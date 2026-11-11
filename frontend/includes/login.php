<?php
// Error codes (set by authenticate.php) -> messages, so arbitrary text can't be injected via the URL
$loginErrors = [
    'db'      => 'Failed to connect to the database',
    'missing' => 'Please fill both the username and password fields!',
    'bad'     => 'Incorrect username and/or password!',
];
$loginError  = $loginErrors[get_str('err')] ?? '';
$loginReturn = safe_return(get_str('return'));
?>
<!-- Login form for admin authentication -->
<form id="loginForm" action="./includes/authenticate.php" method="post">
  <h1 id="formH1">Admin Login</h1>
  <h2 id="formH2"><?= e($loginError) ?></h2>
  <input
    type="text"
    name="username"
    placeholder="Username"
    id="username"
    autocomplete="username"
    required
    autofocus
  />
  <input
    type="password"
    name="password"
    placeholder="Password"
    id="password"
    autocomplete="current-password"
    required
  />
  <input type="hidden" name="return" value="<?= e($loginReturn) ?>" />
  <input type="submit" value="Login" />
</form>
