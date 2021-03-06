<?php

/**
 * Username
 * Email
 * First Name
 * Middle Name
 * Last Name
 * New Password
 * Confirm Password
 */

require_once('db.php');

function editDetails($details, $password) {
  global $conn;

  $changeUsername = false;
  $changeEmail = false;
  $changePassword = false;

  if ( $password ) {
    // Check if Password is Correct
    if ( !checkPass($password, true) ) {
      response(1, 'Incorrect Password');
    }

    // Check for password attempt change
    // and if they match
    if ( $details[5] != "" && $details[5] == $details[6] ) {
      $changePassword = true;
    }

    // Check for username attempt change
    if ( $details[0] != $_SESSION['username'] ) {
      // Check for empty username
      if ( $details[0] == "" ) {
        response(1, 'Username cannot be empty');
      }

      // Check for valid characters
      if ( !ctype_alnum($details[0]) ) {
        response(1, 'Username must only contain letters and numbers');
      }

      // Check if username already exists
      $query = "SELECT * FROM `users` WHERE `user_name` = '{$details[0]}'";
      $result = mysqli_query($conn, $query);

      if ( $result ) {
        if ( mysqli_num_rows($result) != 0 ) {
          response(1, 'Username already exists');
        }
      } else {
        response(2, mysqli_error($conn));
      }

      mysqli_free_result($result);
      $changeUsername = true;
    }

    // Check if email change attempt
    if ( $details[1] != $_SESSION['user_data']['email'] ) {
      $changeEmail = true;
    }
  }

  /* UPDATE PERSONAL DETAILS */
  if ( !ctype_alpha($details[2] . $details[3]) ) {
    response(1, 'Names must only contain letters');
  }

  $first = mysqli_real_escape_string($conn, $details[2]);
  $middle = mysqli_real_escape_string($conn, $details[3]);

  $query = "
    UPDATE `users`
    SET
      `user_first_name` = '$first',
      `user_middle_name` = '$middle'
    WHERE `user_code` = '{$_SESSION['user_code']}'
  ";

  $result = mysqli_query($conn, $query);

  if ( $result ) {
    $_SESSION['user_data']['first_name'] = $first;
    $_SESSION['user_data']['middle_name'] = $middle;
  } else {
    response(2, mysqli_error($conn));
  }

  // mysqli_free_result($result);

  /* UPDATE EMAIL */
  if ( $changeEmail ) {
    $email = mysqli_real_escape_string($conn, $details[1]);

    if ( !confirmEmail($email) ) {
      response(1, "Email not sent.");
    }
  }

  if ( $changeUsername ) {
    $username = mysqli_real_escape_string($conn, $details[0]);

    // Update in users table
    $query = "
      UPDATE `users`
      SET `user_name` = '$username'
      WHERE `user_code` = '{$_SESSION['user_code']}'
      AND LAST_INSERT_ID(`user_id`)
    ";

    $result = mysqli_query($conn, $query);
    if ( !$result ) response(2, mysqli_error($conn));

    // Update in hashed table
    $userId = mysqli_insert_id($conn);
    $key = parse_ini_file($_SERVER['DOCUMENT_ROOT'] . "/cdr/inc/config.ini")['hash_key'];
  
    $hashed = hash_hmac("sha256", $username, $password);

    $query = "
      UPDATE `hashed`
      SET `user_key` = '$hashed'
      WHERE `user_id` = $userId
    ";

    $result = mysqli_query($conn, $query);
    if ( !$result ) response(2, mysqli_error($conn));

    $_SESSION['username'] = $username;
  }

  if ( $changePassword ) {
    require_once("change_password.php");
    changePassword($userId, $details[5]);
  }

  $message = "Successfully updated details.";
  if ( $changeEmail ) $message .= " Please check your email to confirm.";

  response(0, $message);
}

function confirmEmail($email) {
  $token = createToken($email, $_SESSION['user_code']);
  $toEmail = $email;
  $firstName = $_SESSION['user_data']['first_name'];
  $lastName = $_SESSION['user_data']['last_name'];
  
  $subject = "Confirm your email";

  // Email Headers
  $headers = "MIME-Version: 1.0\r\n";
  $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
  $headers .= "From: CDR <master@upiscdr.com>\r\n";

  // Body
  $body = "
    <p>This email was set as a recovery email for $firstName $lastName. Please confirm by clicking on the link below.</p>

    <p>If this was not you, you may safely ignore this email.</p>

    <p><a href=\"localhost/cdr/public_html/profile/edit/email/?t=$token\">Confirm your email.</a> This link will expire in <strong>1 day</strong>.</p>
  ";

  return mail($toEmail, $subject, $body, $headers);
}

function createToken($email, $user) {
  global $conn;

  $token = generateToken();
  $expiration = time() + 24 * 60 * 60;

  // Insert token to database
  $query = "
    INSERT INTO
      `confirm_email` (
        `user_code`,
        `email`,
        `token`,
        `expiration`
      )
    VALUES (
      '$user',
      '$email',
      '$token',
      '$expiration'
    )
  ";
  
  $result = mysqli_query($conn, $query);
  if ( !$result ) die(mysqli_error($conn));

  return $token;
}

function generateToken($size = 64) {
  $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ-_';
  $string = '';
  
  for ($i = 0; $i < $size; $i++) {
    $string .= $chars[rand(0, 63)];
  }
  
  return $string;
}

function response(int $status, $message) {
  $statusList = ['success', 'fail', 'db_fail', 'debug'];

  die (
    json_encode(
      array(
        'status' => $statusList[$status],
        'message' => $message
      )
    )
  );
}

function checkPass($password) {
  global $conn;

  $username = $_SESSION['username'];
  $password = trim($password);

  $key = parse_ini_file($_SERVER['DOCUMENT_ROOT'] . "/cdr/inc/config.ini")['hash_key'];
  $hashed = hash_hmac("sha256", $username, $password);

  $query = "SELECT * FROM `hashed` WHERE `user_key` = '$hashed'";
  $result = mysqli_query($conn, $query);

  if ( $result ) {
    if ( mysqli_num_rows($result) == 1 ) {
      return true;
    } else {
      return false;
    }
  } else {
    die(mysqli_error($conn));
  }
}

?>