<?php

session_start();

if ( !(isset($_SESSION['type']) && $_SESSION['type'] <= 1) ) {
  header("Location: /cdr/public_html/");
  exit();
}

require($_SERVER['DOCUMENT_ROOT'] . "/cdr/inc/db.php");

unset($error);

if ( isset($_POST['submit']) ) {
  $_SESSION['reg']['subject'] = trim($_POST['subject']);
  $_SESSION['reg']['level'] = intval($_POST['level']);
  $_SESSION['reg']['section'] = trim($_POST['section']);
  $_SESSION['reg']['room'] = trim($_POST['room']);

  $_SESSION['reg']['teacher'] = ( $_SESSION['type'] == 0 ) ? trim($_POST['teacher']) : $_SESSION['user_code'];
  
  // Check if teacher code is valid 
  if ( strlen($_SESSION['reg']['teacher']) != 8 ) {
    $error = "Invalid user code";
    goto end;
  }

  // Check if teacher code exists and if it's a teacher
  $query = "SELECT `hashed`.`user_type` FROM `users` INNER JOIN `hashed` ON `hashed`.`user_id` = `users`.`user_id` WHERE `users`.`user_code` = '{$_SESSION['reg']['teacher']}'";

  $result = mysqli_query($conn, $query);

  if ( $result ) {
    if ( mysqli_num_rows($result) == 1 ) {
      $userType = mysqli_fetch_assoc($result)['user_type'];
      mysqli_free_result($result);

      if ( $userType != 1 ) {
        $error = "User does not have a teacher profile";
        goto end;
      }

    } else {
      mysqli_free_result($result);
      $error = "User code does not exist";
      goto end;
    }
  } else {
    die(mysqli_error($conn));
  }

  // Check if class already exists
  $query = "SELECT `class_code` FROM `classes` WHERE `class_subject` = '{$_SESSION['reg']['subject']}' AND `class_teacher` = '{$_SESSION['reg']['teacher']}' AND `class_section` = '{$_SESSION['reg']['section']}'";

  $result = mysqli_query($conn, $query);

  if ( $result ) {
    if ( mysqli_num_rows($result) == 1 ) {
      $classCode = mysqli_fetch_assoc($result)['class_code'];
      mysqli_free_result($result);
      $error = "Subject already exists with class code $classCode";
      goto end;
    }
  } else {
    die(mysqli_error($conn));
  }

  end:

  // No errors
  if ( !isset($error) ) {
    $classCode = randomCode();
    $query = "INSERT INTO
      `classes`
      (
        `class_subject`,
        `class_teacher`,
        `class_code`,
        `class_level`,
        `class_section`,
        `class_room`
      )
      VALUES
      (
        '{$_SESSION['reg']['subject']}',
        '{$_SESSION['reg']['teacher']}',
        '$classCode',
        '{$_SESSION['reg']['level']}',
        '{$_SESSION['reg']['section']}',
        '{$_SESSION['reg']['room']}'
      )
    ";

    $result = mysqli_query($conn, $query);
    if ( !$result ) die(mysqli_error($conn));

    $success = "Class successfully added";

    unset($_SESSION['reg']);
    $_POST = array();
  }
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
    <row class="row h-100 align-items-center">
      <div class="col-sm-9 col-md-7 col-lg-5 mx-auto">
        <div class="card card-register">
          <div class="card-body">

            <h4 class="card-title">Add Class</h4>

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

              <input type="text" name="subject" class="form-control" placeholder="Subject" required autofocus>

              <div class="input-group">

                <input type="number" name="level" class="form-control level" placeholder="Level" required>

                <input type="text" name="section" class="form-control flex-grow-1" placeholder="Section" required>

              </div>

              <input type="text" name="room" class="form-control" placeholder="Classroom" required>

              <!-- Teacher code for administration -->
              <?php if ( $_SESSION['type'] == 0 ) : ?>
                <input type="text" name="teacher" class="form-control" placeholder="Teacher Code" required>
              <?php endif; ?>

              <button name="submit" class="btn btn-md btn-primary btn-block text-uppercase mt-4" type="submit">Register</button>

            </form>

          </div>
        </div>
      </div>
    </row>
  </div>
</div>