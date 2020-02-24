<?php

function login($hash) {
  global $conn;
  
  $query = "SELECT * FROM `hashed` WHERE `user_key` = '$hash'";
  $result = mysqli_query($conn, $query);

  if ( $result ) {
    if ( mysqli_num_rows($result) == 1 ) {
      $user = mysqli_fetch_assoc($result);
      
      $_SESSION['logged_in'] = true;
      $_SESSION['type'] = intval($user['user_type']);
      $_SESSION['id'] = intval($user['user_id']);

      mysqli_free_result($result);

      getUserInfo($_SESSION['id']);
      trackLogin($_SESSION['user_code'], time());
      
      header("Location: /cdr/public_html");
    } else {
      return array(
        "status" => false,
        "message" => "Incorrect username or password"
      );
    }
  } else {
    die(mysqli_error($conn));
  }

  return array(
    "status" => true
  );
}

function getUserInfo($id) {
  global $conn;

  $query = "SELECT user_name, user_code, user_email, user_first_name, user_middle_name, user_last_name FROM `users` WHERE `user_id` = $id";
  $result = mysqli_query($conn, $query);

  if ( $result ) {
    $info = mysqli_fetch_assoc($result);

    $_SESSION['username'] = $info['user_name'];
    $_SESSION['user_code'] = $info['user_code'];
    $_SESSION['user_data']['email'] = $info['user_email'];
    $_SESSION['user_data']['first_name'] = $info['user_first_name'];
    $_SESSION['user_data']['middle_name'] = $info['user_middle_name'];
    $_SESSION['user_data']['last_name'] = $info['user_last_name'];

    mysqli_free_result($result);
  } else {
    die(mysqli_error($conn));
  }
}

function trackLogin($id, $time) {
  global $conn;

  $query = "
    INSERT INTO
      `login` (
        `user_code`,
        `time`
      )
    VALUES (
      '$id',
      $time
    )
  ";

  $result = mysqli_query($conn, $query);
  if ( !$result ) die(mysqli_error($conn));
}

?>