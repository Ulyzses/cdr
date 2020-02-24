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
    SELECT
      `user_code`,
      `user_email`,
      `user_name`
    FROM `users`
    WHERE `user_email` = '$info'
    OR `user_name` = '$info'
  ";

  $result = mysqli_query($conn, $query);

  if ( $result ) {
    if ( mysqli_num_rows($result) > 0 ) {
      $details = mysqli_fetch_array($result);

      $token = createToken($details);

      if ( sendEmail($details, $token) ) {
        $success = "Success! Please check your email";
      } else {
        $error = "Email not sent. Please try again.";
      }
    } else {
      $error = "Username or email not found in database";
    }
  } else {
    die(mysqli_error($conn));
  }
}

function createToken($details) {
  global $conn;

  $token = generateToken();

  $userCode = $details[0];
  $expiration = time() + 3600;

  // Insert token to database
  $query = "
    INSERT INTO
      `recovery` (
        `user`,
        `token`,
        `expiration`
      )
    VALUES (
      '$userCode',
      '$token',
      '$expiration'
    )
  ";

  $result = mysqli_query($conn, $query);
  if ( !$result ) die(mysqli_error($conn));

  return $token;
}

function sendEmail($details, $token) {
  $toEmail = $details[1];
  $toUser = $details[2];

  $subject = "Password reset request";

  // Email Headers
  $headers = "MIME-Version: 1.0\r\n";
  $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
  $headers .= "From: CDR <master@upiscdr.com>\r\n";

  // Body
  $body = "
    <h2>Password reset</h2>
    <p>You are receiving this email because you requested a password reset for $toUser. Click the link below to be redirected. If you did not make this request, you may ignore this email.</p>
    <p><a href=\"localhost/cdr/public_html/recover/?t=$token\">Reset Password</a></p>
    <p>This link will expire in <strong>1 hour</strong>.</p>
  ";

  return mail($toEmail, $subject, $body, $headers);
}

function generateToken($size = 64) {
  $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ-_';
  $string = '';
  
  for ($i = 0; $i < $size; $i++) {
    $string .= $chars[rand(0, 63)];
  }
  
  return $string;
}

?>
<?php include $_SERVER['DOCUMENT_ROOT'] . "/cdr/inc/begin.php" ?>
  <link rel="stylesheet" href="../css/recovery.css">
</head>
<body>
  <div class="container h-100">
    <div class="row h-100 align-items-center">
      <div class="col-md-10 col-lg-8 mx-auto">
        <h2>Account Recovery</h2>
        <p>Enter the username or email you used to register. You will receive an email with a link to reset your password.</p>

        <form action="./" method="post">
          <!-- Error Message -->
          <?php if ( isset($error) ) : ?>
            <div class="alert alert-danger text-center" role="alert">
              <?php echo $error ?>
            </div>
          <?php endif; ?>

          <!-- Success Message -->
          <?php if ( isset($success) ) : ?>
            <div class="alert alert-success text-center" role="alert">
              <?php echo $success ?>
            </div>
          <?php endif; ?>
          
          <input type="text" name="userInfo" class="form-control" placeholder="Username or Email" required>
          <button name="submit" class="btn btn-md btn-primary text-uppercase mt-4" type="submit">Submit</button>
        </form>
      </div>
    </div>
  </div>
</body>