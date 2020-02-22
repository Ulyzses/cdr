<?php

session_start();

if ( isset($_SESSION['logged_in']) && $_SESSION['logged_in'] ) {
  header("Location: /cdr/public_html");
  
  exit();
}

require($_SERVER['DOCUMENT_ROOT'] . "/cdr/inc/db.php");

if ( isset($_POST['submit']) ) {
  $username = trim($_POST['username']);
  $password = trim($_POST['password']);

  $key = parse_ini_file($_SERVER['DOCUMENT_ROOT'] . "/cdr/inc/config.ini")['hash_key'];
  $hashed = hash_hmac("sha256", $username, $password);

  $query = "SELECT * FROM `hashed` WHERE `user_key` = '$hashed'";
  $result = mysqli_query($conn, $query);

  if ( $result ) {
    if ( mysqli_num_rows($result) == 1 ) {
      $user = mysqli_fetch_assoc($result);
      
      $_SESSION['logged_in'] = true;
      $_SESSION['type'] = intval($user['user_type']);
      $_SESSION['id'] = intval($user['user_id']);

      mysqli_free_result($result);

      getUserInfo($_SESSION['id']);
      trackLogin($_SESSION['user_code'], time());
      
      header("Location: /cdr/public_html");
    } else {
      $error = "Incorrect username or password";
    }
  } else {
    die(mysqli_error($conn));
  }
}

function getUserInfo($id) {
  global $conn;

  $query = "SELECT user_name, user_code, user_email, user_first_name, user_middle_name, user_last_name FROM `users` WHERE `user_id` = $id";
  $result = mysqli_query($conn, $query);

  if ( $result ) {
    $info = mysqli_fetch_assoc($result);

    $_SESSION['username'] = $info['user_name'];
    $_SESSION['user_code'] = $info['user_code'];
    $_SESSION['user_data']['email'] = $info['user_email'];
    $_SESSION['user_data']['first_name'] = $info['user_first_name'];
    $_SESSION['user_data']['middle_name'] = $info['user_middle_name'];
    $_SESSION['user_data']['last_name'] = $info['user_last_name'];

    mysqli_free_result($result);
  } else {
    die(mysqli_error($conn));
  }
}

function trackLogin($id, $time) {
  global $conn;

  $query = "
    INSERT INTO
      `login` (
        `user_code`,
        `time`
      )
    VALUES (
      '$id',
      $time
    )
  ";

  $result = mysqli_query($conn, $query);
  if ( !$result ) die(mysqli_error($conn));
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