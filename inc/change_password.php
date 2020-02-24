<?php

function changePassword($userId, $newPassword) {
  global $conn;

  $userId = (int)$userId;

  // Get username
  $query = "
    SELECT `user_name`
    FROM `users`
    WHERE `user_id` = $userId
  ";

  $result = mysqli_query($conn, $query);

  if ( $result ) {
    $username = mysqli_fetch_array($result)[0];
  } else {
    die(mysqli_error($conn));
  }

  mysqli_free_result($result);

  $key = parse_ini_file($_SERVER['DOCUMENT_ROOT'] . "/cdr/inc/config.ini")['hash_key'];
  
  $hashed = hash_hmac("sha256", $username, $newPassword);

  // Modify into hashed table
  $query = "
    UPDATE `hashed`
    SET `user_key` = '$hashed'
    WHERE `user_id` = $userId
  ";

  $result = mysqli_query($conn, $query);
  if ( !$result ) die(mysqli_error($conn));

  return $hashed;
}

?>