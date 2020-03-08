<?php

session_start();

if ( !(isset($_SESSION['logged_in']) && $_SESSION['logged_in'])) {
  header("Location: /cdr/public_html/");
}

require($_SERVER['DOCUMENT_ROOT'] . "/cdr/inc/db.php");

// Get Image
$query = "
  SELECT `file_name`
  FROM `images`
  WHERE `user_code` = '{$_SESSION['user_code']}'
  ORDER BY `uploaded` DESC
";

$result = mysqli_query($conn, $query);

if ( $result ) {
  if ( mysqli_num_rows($result) == 0 ) {
    $displayImg = "null";
  } else {
    $displayImg = "../img/profile/" . mysqli_fetch_row($result)[0];
  }
} else {
  die(mysqli_error($conn));
}

mysqli_free_result($result);

// For teachers
if ( $_SESSION['type'] == 1 ) {
  $query = "
    SELECT
      `class_subject` AS subject,
      `class_code` AS code,
      `class_level` AS level,
      `class_section` AS section,
      `class_room` AS room
    FROM `classes`
    WHERE `class_teacher` = '{$_SESSION['user_code']}'
  ";

  $result = mysqli_query($conn, $query);

  if ( $result ) {
    $classes = array();

    while ($row = mysqli_fetch_assoc($result)) {
      $classes[] = $row;
    }
  } else {
    die(mysqli_error($conn));
  }
} else if ( $_SESSION['type'] == 2 ) {
  $query = "
    SELECT
      `classes`.`class_subject` AS subject,
      `classes`.`class_room` AS room,
      `users`.`user_first_name` AS teacher_first,
      `users`.`user_last_name` AS teacher_last
    FROM `enrolments`
    JOIN `classes`
    ON `enrolments`.`subject_code` = `classes`.`class_code`
    JOIN `users`
    ON `classes`.`class_teacher` = `users`.`user_code`
    WHERE `enrolments`.`student_code` = '{$_SESSION['user_code']}'
  ";

  $result = mysqli_query($conn, $query);

  if ( $result ) {
    $subjects = array();

    while ($row = mysqli_fetch_assoc($result)) {
      $subjects[] = $row;
    }
  } else {
    die(mysqli_error($conn));
  }
}

 ?>
<?php include $_SERVER['DOCUMENT_ROOT'] . "/cdr/inc/begin.php" ?>
  <link rel="stylesheet" href="/cdr/public_html/css/profile.css">
  <script src="/cdr/public_html/js/profile.js"></script>
  <title>Profile | UPIS CDR</title>
</head>
<body>
  <?php include $_SERVER['DOCUMENT_ROOT'] . "/cdr/inc/navbar.php" ?>
  <div class="container-fluid flex-grow-1">
    <div class="row justify-content-center h-100">
      <div class="col-lg-2 col-md-3 col-12 side">
        <!-- load image -->
        <img src="<?php echo $displayImg ?>" alt="Display Photo" id="displayImg">

        <h1 class="side-text side-header">Name</h1>
        <h2 class="side-text side-details">
          <?php echo "{$_SESSION['user_data']['first_name']} {$_SESSION['user_data']['last_name']}" ?>
        </h2>

        <h1 class="side-text side-header">Username</h1>
        <h2 class="side-text side-details">
          <?php echo "{$_SESSION['username']}" ?>
        </h2>
        
        <h1 class="side-text side-header">Email Address</h1>
        <h2 class="side-text side-details">
          <?php echo $_SESSION['user_data']['email']; ?>
        </h2>

        <h1 class="side-text side-header">Account Type</h1>
        <h2 class="side-text side-details">
          <?php 
            if ( $_SESSION['type'] === 0 ) {
              echo "Administrator";
            } else if ( $_SESSION['type'] === 1) {
              echo "Teacher";
            } else if ( $_SESSION['type'] === 2) {
              echo "Student";
            } else {
              echo "Unknown";
            }
          ?>
        </h2>

        <a class="btn btn-outline-secondary w-100" id="editButton" href="edit">Edit Account Details</a>
      </div>

      <!-- Right Content -->
      <div class="col-lg-7 col-md-7 col-12 main">
        <?php if ( $_SESSION['type'] == 1 ) : ?>
          <h1 class="body-header">My Classes</h1>

          <?php foreach($classes as $class) : ?>
            <div class="body-div">
              <h2 class="body-text body-div-header">
                <?php echo "{$class['level']}&ndash;{$class['section']}"; ?>
              </h2>
              <h3 class="body-text body-div-details">
                <span id="subject">
                  <?php echo $class['subject']; ?>
                </span> | <span>
                  <?php echo $class['room']; ?>
                </span>
              </h3>
            </div>
          <?php endforeach; ?>

        <?php elseif ( $_SESSION['type'] == 2 ) : ?>
          <h1 class="body-header">My Subjects</h1>

          <?php foreach($subjects as $subject) : ?>
            <div class="body-div">
              <h2 class="body-text body-div-header">
                <?php echo $subject['subject']; ?>
              </h2>
              <h3 class="body-text body-div-details">
                  <?php echo "{$subject['teacher_last']}, {$subject['teacher_first']}"; ?>
              </h3>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>
    <form action="upload.php" enctype="multipart/form-data" id="uploadPicForm" class="d-none">
      <input type="hidden" name="request" value="upload">
      <input type="file" class="form-control d-none" name="file" id="file">
    </form>
  </div>
</body>

</html>