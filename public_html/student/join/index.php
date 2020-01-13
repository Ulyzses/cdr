<?php 

session_start();

if ( !(isset($_SESSION['type']) && ($_SESSION['type'] == 0 || $_SESSION['type'] == 2 )) ) {
  header("Location: /cdr/public_html/");
  exit();
}

require($_SERVER['DOCUMENT_ROOT'] . "/cdr/inc/db.php");

unset($error);

if ( isset($_POST['submit']) ) {
  $_SESSION['join']['subject'] = trim($_POST['subject']);
  $_SESSION['join']['student'] = ( $_SESSION['type'] == 0 ) ? trim($_POST['student']) : $_SESSION['user_code'];
  
  // Check if class code is valid
  if ( strlen($_SESSION['join']['subject']) != 8 ) {
    $error = "Invalid class code";
    goto end;
  }

  // Check if student code is valid 
  if ( strlen($_SESSION['join']['student']) != 8 ) {
    $error = "Invalid user code";
    goto end;
  }

  // Check if student code exists and if it's a student
  $query = "SELECT `hashed`.`user_type` FROM `users` INNER JOIN `hashed` ON `hashed`.`user_id` = `users`.`user_id` WHERE `users`.`user_code` = '{$_SESSION['join']['student']}'";

  $result = mysqli_query($conn, $query);

  if ( $result ) {
    if ( mysqli_num_rows($result) == 1 ) {
      $userType = mysqli_fetch_assoc($result)['user_type'];
      mysqli_free_result($result);

      if ( $userType != 2 ) {
        $error = "User does not have a student profile";
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

  // Check if class code exists
  $query = "SELECT * FROM `classes` WHERE `class_code` = '{$_SESSION['join']['subject']}'";

  $result = mysqli_query($conn, $query);

  if ( $result ) {
    if ( mysqli_num_rows($result) != 1 ) {
      mysqli_free_result($result);
      $error = "Class code does not exist";
      goto end;
    }
  } else {
    mysqli_error($conn);
  }


  // Check if student is already enroled in class
  $query = "SELECT `enrolment_id` FROM `enrolments` WHERE `subject_code` = '{$_SESSION['join']['subject']}' AND `student_code` = '{$_SESSION['join']['student']}'";

  $result = mysqli_query($conn, $query);

  if ( $result ) {
    if ( mysqli_num_rows($result) == 1 ) {
      $enrolmentId = mysqli_fetch_assoc($result)['enrolment_id'];
      mysqli_free_result($result);
      $error = "Student already enroled with enrolment ID $enrolmentId";
      goto end;
    }
  } else {
    die(mysqli_error($conn));
  }

  end:

  // No errors
  if ( !isset($error) ) {
    $query = "INSERT INTO
      `enrolments`
      (
        `student_code`,
        `subject_code`
      )
      VALUES
      (
        '{$_SESSION['join']['student']}',
        '{$_SESSION['join']['subject']}'
      )
    ";

    $result = mysqli_query($conn, $query);
    if ( !$result ) die(myslqli_error($conn));

    $success = "Successfully enrolled student";

    unset($_SESSION['join']);
    $_POST = array();
  }
}

 ?>
<?php include $_SERVER['DOCUMENT_ROOT'] . "/cdr/inc/begin.php" ?>
  <link rel="stylesheet" href="../../css/join.css">
  <title>Join Class | UPIS CDR</title>
</head>

<body>
  <div class="container h-100">
    <div class="row h-100 align-items-center">
      <div class="col-sm-9 col-md-7 col-lg-5 mx-auto">
        <div class="card card-join">
          <div class="card-body">

            <h4 class="card-title">Join Class</h4>

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

              <input type="text" name="subject" class="form-control" placeholder="Subject Code" required autofocus>
              
              <?php if ( $_SESSION['type'] == 0 ) : ?>
                <input type="text" name="student" class="form-control" placeholder="Student Code" required>
              <?php endif; ?>

              <button name="submit" class="btn btn-md btn-primary btn-block text-uppercase mt-4" type="submit">Join</button>

            </form>

          </div>
        </div>
      </div>
    </div>
  </div>
</body>

</html>