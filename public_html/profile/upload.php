<?php

session_start();

$allowed = array('jpg', 'png', 'jpeg', 'svg');

if ( isset($_POST['request']) && $_POST['request'] == "upload" ) {
  require($_SERVER['DOCUMENT_ROOT'] . "/cdr/inc/db.php");

  $type = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);

  // Check if file is a valid image
  if ( in_array($type, $allowed) ) {
    $user = $_SESSION['user_code'];
    $fileName = "display_$user.$type";
    $dir = "{$_SERVER['DOCUMENT_ROOT']}/cdr/public_html/img/profile/";

    // Upload to server
    if ( move_uploaded_file($_FILES['file']['tmp_name'], $dir . $fileName) ) {
      
      // Check if file name already exists

      // Add to database
      $query = "
        INSERT INTO
          `images` (
            `file_name`,
            `user_code`,
            `uploaded`
          )
        VALUES (
          '$fileName',
          '$user',
          now()
        )
        ON DUPLICATE KEY
        UPDATE
          `file_name` = '$fileName',
          `uploaded` = now()
      ";

      $result = mysqli_query($conn, $query);

      if ( $result ) {
        die("Image successfully uploaded");
      } else {
        die(mysqli_error($conn));
      }
    } else {
      die("There was a problem uploading your image");
    }
  } else {
    die("Please select a valid file");
  }
}
 ?>