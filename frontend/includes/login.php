<!-- Login form for admin authentication -->
<form id="loginForm" action="./includes/authenticate.php" method="post">
  <h1 id="formH1">Admin Login</h1>
  <h2 id="formH2"><?=$_GET['err']?></h2>
  <input
    type="text"
    name="username"
    placeholder="Username"
    id="username"
    required
    autofocus 
  />
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
