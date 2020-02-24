<?php

session_start();

if ( isset($_SESSION['logged_in']) && $_SESSION['logged_in'] ) {
  header("Location: /cdr/public_html");
  
  exit();
}

require($_SERVER['DOCUMENT_ROOT'] . "/cdr/inc/db.php");

if ( isset($_POST['submit']) ) {
  require($_SERVER['DOCUMENT_ROOT'] . "/cdr/inc/login.php");

  $username = trim($_POST['username']);
  $password = trim($_POST['password']);

  $key = parse_ini_file($_SERVER['DOCUMENT_ROOT'] . "/cdr/inc/config.ini")['hash_key'];
  $hashed = hash_hmac("sha256", $username, $password);

  $login = login($hashed);

  if ( !$login['status'] ) {
    $error = $login['message'];
  }
}
 ?>
<?php include $_SERVER['DOCUMENT_ROOT'] . "/cdr/inc/begin.php" ?>
  <link rel="stylesheet" href="../css/login.css">
</head>
<body>
  <div class="container h-100">
    <div class="row h-100 align-items-center">
      <div class="col-sm-9 col-md-7 col-lg-5 mx-auto">
        <div class="card card-login">
          <div class="card-body">
            <h4 class="card-title">Login</h4>

            <form action="./" method="post">

              <!-- Error Message -->
              <?php if ( isset($error) ) : ?>
                <div class="alert alert-danger text-center" role="alert">
                  <?php echo $error ?>
                </div>
              <?php endif; ?>

              <input type="text" name="username" class="form-control" placeholder="Username" required autofocus>
              <input type="password" name="password" class="form-control" placeholder="Password" required>
              <button name="submit" class="btn btn-md btn-primary btn-block text-uppercase mt-4" type="submit">Login</button>
              <p id="forgot">
                <a href="/cdr/public_html/account_recovery">Forgot my password</a>
              </p>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    $('.alert').click(function() {
      $(this).hide(300, function() {
        $(this).remove();
      })
    });
  </script>
</body>

</html>