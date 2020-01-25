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

        <h1 class="side-text side-header">User Code</h1>
        <h2 class="side-text side-details">
          <?php echo "{$_SESSION['user_code']}" ?>
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
              echo " What the fuk r u ";
            }
          ?>
        </h2>
      </div>
      <div class="col-lg-7 col-md-7 col-12 main">
      </div>
    </div>
    <form action="upload.php" enctype="multipart/form-data" id="uploadPicForm" class="d-none">
      <input type="hidden" name="request" value="upload">
      <input type="file" class="form-control d-none" name="file" id="file">
      <!-- <input type="submit" class="btn btn-primary" name="submit" value="Upload"> -->
    </form>
  </div>
</body>

</html>