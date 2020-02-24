<?php

session_start();

if ( isset($_SESSION['logged_in']) && $_SESSION['logged_in'] ) {
  header("Location: /cdr/public_html");
  
  exit();
}

if ( !(isset($_GET['t']) || isset($_POST['submit'])) ) {
  header("Location: /cdr/public_html");
  
  exit();
}

require($_SERVER['DOCUMENT_ROOT'] . "/cdr/inc/db.php");

if ( isset($_GET['t']) ) {
  // check if token is valid
  $token = $_GET['t'];

  $query = "
    SELECT
      `users`.`user_id` AS user_id,
      `users`.`user_name` AS user_name,
      `recovery`.`user` AS user_code,
      `recovery`.`token` AS token,
      `recovery`.`expiration` AS expiration,
      `recovery`.`status` AS status
    FROM `recovery`
    JOIN `users`
    ON `recovery`.`user` = `users`.`user_code`
    WHERE `recovery`.`token` = '$token'
  ";

  $result = mysqli_query($conn, $query);

  if ( $result ) {
    if ( mysqli_num_rows($result) > 0 ) {
      $_SESSION['recover'] = mysqli_fetch_assoc($result);

      if ( intval($_SESSION['recover']['expiration']) < time() ) {
        $error = "Expired token";
        $errorMsg = "The token you are using has already expired. You may request for a new token by clicking <a href=\"/cdr/public_html/account_recovery/\">here</a>.";
      }

      if ( intval($_SESSION['recover']['status'] ) ) {
        $error = "Used token";
        $errorMsg = "The token you are using has already been used. You may request for a new token by clicking <a href=\"/cdr/public_html/account_recovery/\">here</a>.";
      }
    } else {
      $error = "Invalid token";
      $errorMsg = "The token you are using is invalid. You may request for a new token by clicking <a href=\"/cdr/public_html/account_recovery/\">here</a>.";
    }
  } else {
    die(mysqli_error($conn));
  }
}

if ( isset($_POST['submit']) ) {
  $password = trim($_POST['password']);
  $confirm = trim($_POST['confirm_password']);

  // Check if password is valid
  if ( !ctype_alnum($_POST['password']) ) {
    $formError = "Password can only contain letters or numbers";
  }

  // Check if passwords match
  if ( $password != $confirm ) {
    $formError = "Passwords do not match";
  }

  // No errors
  if ( !isset($formError) ) {
    require($_SERVER['DOCUMENT_ROOT'] . "/cdr/inc/change_password.php");
    require($_SERVER['DOCUMENT_ROOT'] . "/cdr/inc/login.php");

    $hashed = changePassword($_SESSION['recover']['user_id'], $password);

    // Modify token table
    $query = "
      UPDATE `recovery`
      SET `status` = '1'
      WHERE `token` = '{$_SESSION['recover']['token']}'
    ";

    $result = mysqli_query($conn, $query);
    if ( !$result ) die(mysqli_error($conn));

    // Login
    $success = "Password change success. Logging in.";

    login($hashed);
  }
}

?>
<?php include $_SERVER['DOCUMENT_ROOT'] . "/cdr/inc/begin.php" ?>
  <link rel="stylesheet" href="../css/recover.css">
</head>
<body>
  <div class="container h-100">
    <div class="row h-100 align-items-center">
      <div class="col-md-10 col-lg-8 mx-auto">
        <?php if ( isset($error) ) : ?>
          <h3 class="title">Error:
            <?php echo $error ?>
          </h3>
          <p class="error-message">
            <?php echo $errorMsg ?>
          </p>
        <?php else : ?>
          <h3 class="title">Reset your password</h3>
          <form action="./" method="post">
            <!-- Error Message -->
            <?php if ( isset($formError) ) : ?>
              <div class="alert alert-danger text-center" role="alert">
                <?php echo $formError ?>
              </div>
            <?php endif; ?>

            <!-- Success Message -->
            <?php if ( isset($success) ) : ?>
              <div class="alert alert-success text-center" role="alert">
                <?php echo $success ?>
              </div>
            <?php endif; ?>

            <input type="password" name="password" class="form-control" placeholder="New Password" required>
            <input type="password" name="confirm_password" class="form-control" placeholder="Confirm New Password" required>
            <button name="submit" class="btn btn-md btn-primary text-uppercase mt-4" type="submit">Submit</button>
          </form>
        <?php endif; ?>
      </div>
    </div>
  </div>
</body>