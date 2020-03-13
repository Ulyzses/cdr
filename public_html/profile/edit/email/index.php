<?php

session_start();

if ( !(isset($_GET['t'])) ) {
  header("Location: /cdr/public_html/profile/edit");
  
  exit();
}

require($_SERVER['DOCUMENT_ROOT'] . "/cdr/inc/db.php");

if ( isset($_GET['t']) ) {
  // check if token is valid
  $token = $_GET['t'];

  $query = "
    SELECT *
    FROM `confirm_email`
    WHERE `token` = '$token'
  ";

  $result = mysqli_query($conn, $query);

  if ( $result ) {
    if ( mysqli_num_rows($result) > 0 ) {
      $details = mysqli_fetch_assoc($result);
      mysqli_free_result($result);

      if ( intval($details['expiration']) < time() ) {
        $error = "Expired token";
        $errorMsg = "The token you are using has already expired. Your email was not changed.";
      }

      if ( intval($details['status']) ) {
        $error = "Used token";
        $errorMsg = "The token you are using has already been used. Your email was not changed.";
      }
    } else {
      $error = "Invalid token";
      $errorMsg = "The token you are using is invalid.";
    }
  } else {
    die(mysqli_error($conn));
  }
}

if ( !isset($error) ) {
  $token = $details['token'];
  $email = $details['email'];
  $user = $details['user_code'];

  // Update email
  $query = "
    UPDATE `users`
    SET `user_email` = '$email'
    WHERE user_code = '$user'
  ";

  $result = mysqli_query($conn, $query);
  if ( !$result ) die(mysqli_error($conn));

  // Update token status
  $query = "
    UPDATE `confirm_email`
    SET `status` = 1
    WHERE `token` = '$token'
  ";

  $result = mysqli_query($conn, $query);
  if ( !$result ) die(mysqli_error($conn));

  // Update session
  $_SESSION['user_data']['email'] = $email;
  
  header("Location: /cdr/public_html/profile/edit/?e=1");
  
  exit();
}

?>
<?php include $_SERVER['DOCUMENT_ROOT'] . "/cdr/inc/begin.php" ?>
  <title>Email Confirmation</title>
</head>
<body>
  <?php include $_SERVER['DOCUMENT_ROOT'] . "/cdr/inc/navbar.php" ?>
  <div class="container d-flex flex-column flex-grow-1">
    <div class="row h-100 align-items-center">
      <div class="col-md-10 col-lg-8 mx-auto">
        <h3 class="mt-5">Error:
          <?php echo $error ?>
        </h3>
        <p>
          <?php echo $errorMsg ?>
        </p>
      </div>
    </div>
  </div>
</body>

</html>