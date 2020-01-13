<?php

session_start();

if ( !(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] && isset($_SESSION['type']) && $_SESSION['type'] <= 1) ) {
  header("Location: /cdr/public_html/");
}

 ?>
<?php include $_SERVER['DOCUMENT_ROOT'] . "/cdr/inc/begin.php" ?>
  <link rel="stylesheet" href="/cdr/public_html/css/teacher.css">
  <title>Teacher | UPIS CDR</title>
</head>
<body>
  <?php include $_SERVER['DOCUMENT_ROOT'] . "/cdr/inc/navbar.php" ?>
  <table class="table table-sm table-striped table-hover table-responsive table-bordered text-center" contenteditable="true">
    <thead>
      <tr>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
      </tr>
      <tr>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
      </tr>
    </tbody>
  </table>
  <button name="col" class="btn btn-primary">New Column</button>
</body>

</html>