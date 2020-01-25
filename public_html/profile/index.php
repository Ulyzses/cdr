<?php

session_start();

if ( !(isset($_SESSION['logged_in']) && $_SESSION['logged_in'])) {
  header("Location: /cdr/public_html/");
}

require($_SERVER['DOCUMENT_ROOT'] . "/cdr/inc/db.php");

 ?>
<?php include $_SERVER['DOCUMENT_ROOT'] . "/cdr/inc/begin.php" ?>
  <link rel="stylesheet" href="/cdr/public_html/css/profile.css">
  <script src="/cdr/public_html/js/profile.js"></script>
  <title>Profile | UPIS CDR</title>
</head>
<body>
  <?php include $_SERVER['DOCUMENT_ROOT'] . "/cdr/inc/navbar.php" ?>
  <div class="container-fluid flex-grow-1">
    <form action="upload.php" enctype="multipart/form-data" id="uploadPicForm">
      Select Image File to Upload:
      <input type="hidden" name="request" value="upload">
      <input type="file" class="form-control" name="file" id="file">
      <input type="submit" class="btn btn-primary" name="submit" value="Upload">
    </form>
  </div>
</body>

</html>