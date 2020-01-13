<?php

session_start();

if ( !(isset($_SESSION['type']) && $_SESSION['type'] == 0) ) {
  header("Location: /cdr/public_html/");
}

require($_SERVER['DOCUMENT_ROOT'] . "/cdr/inc/db.php");

unset($error);

if ( isset($_POST['first']) ) {
  $_SESSION['reg']['username'] = trim($_POST['username']);

  $stage = 1;
  // Check if account type is chosen
  if ( isset($_POST['type']) ) {
    $_SESSION['reg']['type'] = intval($_POST['type']);
  } else {
    $error = "Please choose account type";
  }

  // Check if passwords match
  if ( $_POST['password'] != $_POST['password_confirm'] ) {
    $error = "Passwords do not match";
  }

  // Check if username already exists
  $query = "SELECT * FROM `users` WHERE `user_name` = '{$_SESSION['reg']['username']}'";
  $result = mysqli_query($conn, $query);

  if ( $result ) {
    if ( mysqli_num_rows($result) != 0 ) {
      $error = "Username already exists";
    }
  } else {
    die(mysqli_error($conn));
  }

  // No errors
  if ( !isset($error) ) {
    $stage = 2;

    $key = parse_ini_file($_SERVER['DOCUMENT_ROOT'] . "/cdr/inc/config.ini")['hash_key'];
    
    $_SESSION['reg']['hashed'] = hash_hmac("sha256", $_SESSION['reg']['username'], trim($_POST['password']));
  }
} else if ( isset($_POST['second']) ) {
  // Generate random user code
  $userCode = randomCode();

  // Sanitise shit
  $_SESSION['reg']['email'] = trim($_POST['email']);
  $_SESSION['reg']['first_name'] = trim($_POST['first_name']);
  $_SESSION['reg']['middle_name'] = trim($_POST['middle_name']);
  $_SESSION['reg']['last_name'] = trim($_POST['last_name']);

  // Insert into hashed table
  $query = "INSERT INTO
    `hashed`
    (
      `user_key`,
      `user_type`
    )
    VALUES
    (
      '{$_SESSION['reg']['hashed']}',
      {$_SESSION['reg']['type']}
    )
  ";

  $result = mysqli_query($conn, $query);
  if ( !$result ) die(mysqli_error($conn));

  // Insert into users table
  $id = mysqli_insert_id($conn);

  $query = "INSERT INTO
    `users`
    (
      `user_id`,
      `user_name`,
      `user_code`,
      `user_email`,
      `user_first_name`,
      `user_middle_name`,
      `user_last_name`
    )
    VALUES
    (
      $id,
      '{$_SESSION['reg']['username']}',
      '$userCode',
      '{$_SESSION['reg']['email']}',
      '{$_SESSION['reg']['first_name']}',
      '{$_SESSION['reg']['middle_name']}',
      '{$_SESSION['reg']['last_name']}'
    )
  ";

  $result = mysqli_query($conn, $query);
  if ( !$result ) die(mysqli_error($conn));

  $success = "User successfully registered";

  unset($_POST, $stage, $_SESSION['reg']);
}

function randomCode($size = 8) {
  $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
  $string = '';
  
  for ($i = 0; $i < $size; $i++) {
    $string .= $chars[rand(0, 61)];
  }
  
  return $string;
}

 ?>

<?php include $_SERVER['DOCUMENT_ROOT'] . "/cdr/inc/begin.php" ?>
  <link rel="stylesheet" href="../../css/register.css">
  <title>Register User | UPIS CDR</title>
</head>

<body>
  <div class="container h-100">
    <div class="row h-100 align-items-center">
      <div class="col-sm-9 col-md-7 col-lg-5 mx-auto">
        <div class="card card-register">
          <div class="card-body">

            <h4 class="card-title">Register User</h4>

            <!-- FIRST FORM -->
            <?php if ( !isset($_POST['first']) || $stage == 1 ) : ?>

              <form method="post" action="./">

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

                <input type="text" name="username" class="form-control" placeholder="Username" required autofocus>
                <input type="password" name="password" class="form-control" placeholder="Password" required>
                <input type="password" name="password_confirm" class="form-control" placeholder="Confirm Password">
                <select name="type" class="custom-select">
                  <option value="" disabled selected>Account Type</option>
                  <option value="0">Administrator</option>
                  <option value="1">Teacher</option>
                  <option value="2">Student</option>
                </select>

                <button name="first" class="btn btn-md btn-primary btn-block text-uppercase mt-4" type="submit">Register</button>

              </form>
            <?php endif; ?>

            <?php if ( isset($_POST['first']) && $stage == 2 ) : ?>
              <form method="post" action="./">

                <input type="text" name="email" class="form-control" placeholder="E-mail" required autofocus>
                <input type="text" name="first_name" class="form-control" placeholder="First Name" required>
                <input type="text" name="middle_name" class="form-control" placeholder="Middle Name">
                <input type="text" name="last_name" class="form-control" placeholder="Last Name" required>  

                <button name="second" class="btn btn-md btn-primary btn-block text-uppercase mt-4" type="submit">Finish</button>

              </form>

            <?php endif; ?>

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