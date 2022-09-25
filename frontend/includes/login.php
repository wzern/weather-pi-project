<!-- Login form for admin authentication -->
<form id="loginForm" action="./authenticate.php" method="post">
  <h1 id="loginH1">Admin Login</h1>
  <h2 id="loginH2"><?=$_GET['err']?></h2>
  <label for="username">
    <i class="fas fa-user"></i>
  </label>
  <input
    type="text"
    name="username"
    placeholder="Username"
    id="username"
    required
  />
  <label for="password">
    <i class="fas fa-lock"></i>
  </label>
  <input
    type="password"
    name="password"
    placeholder="Password"
    id="password"
    required
  />
  <input type="hidden" name="return" value="<?=$_GET['return']?>"/>
  <input type="submit" value="Login" />
</form>
