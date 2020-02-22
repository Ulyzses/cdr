<?php

session_start();

if ( isset($_SESSION['logged_in']) && $_SESSION['logged_in'] ) {
  header("Location: /cdr/public_html");
  
  exit();
}

require($_SERVER['DOCUMENT_ROOT'] . "/cdr/inc/db.php");

if ( isset($_POST['submit']) ) {
  $info = trim($_POST['userInfo']);

  $query = "
    SELECT `user_email`
    FROM `users`
    WHERE `user_name` = '$info'
    OR `user_email` = '$info'
  ";

  $result = mysqli_query($conn, $query);

  if ( $result ) {
    $user = mysqli_fetch_array($result)[0];

    echo $user;
  } else {
    die(mysqli_error($conn));
  }
}

?>
<?php include $_SERVER['DOCUMENT_ROOT'] . "/cdr/inc/begin.php" ?>

</head>
<body>
  <div class="container h-100">
    <div class="row h-100 align-items-center">
      <div class="col-md-10 col-lg-8 mx-auto">
        <form action="./" method="post">
          <!-- Error Message -->
          <?php if ( isset($error) ) : ?>
            <div class="alert alert-danger text-center" role="alert">
              <?php echo $error ?>
            </div>
          <?php endif; ?>

          <h2>Account Recovery</h2>
          <p>Enter the username or email you used to register. You will receive an email with a link to reset your password.</p>
          <input type="text" name="userInfo" class="form-control" placeholder="Username or Email" required>
          <button name="submit" class="btn btn-md btn-primary btn-block text-uppercase mt-4" type="submit">Submit</button>
        </form>
      </div>
    </div>
  </div>
</body>