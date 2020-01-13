<?php

session_start();

if ( !(isset($_SESSION['logged_in']) && $_SESSION['logged_in']) ) {
  header("Location: ./login/");
}

 ?>
<?php include $_SERVER['DOCUMENT_ROOT'] . "/cdr/inc/begin.php" ?>

</head>
<body>
  <?php include $_SERVER['DOCUMENT_ROOT'] . "/cdr/inc/navbar.php" ?>
</body>