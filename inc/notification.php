<?php

require_once($_SERVER['DOCUMENT_ROOT'] . "/cdr/inc/db.php");

function notifyStudentScore($type, $details) {
  global $conn;

  $formats = array(
    "add" => '%s, %s (%s) has added scores for %s.',
    "modify" => '%s, %s (%s) has changed your score for %s.',
    "delete" => '%s, %s (%s) has deleted your score for %s.'
  );
  print_r($details);
  $studentCode = $details['studentCode'];
  $teacherCode = $details['teacherCode'] ?? $_SESSION['user_code'];
  $activityCode = $details['activityCode'];
  $time = $details['time'] ?? time();

  // Get teacher name
  $query = "
    SELECT
      `user_first_name` AS first_name,
      `user_last_name` AS last_name
    FROM `users`
    WHERE `user_code` = '$teacherCode'
  ";

  $result = mysqli_query($conn, $query);

  if ( $result ) {
    if ( mysqli_num_rows($result) == 0 ) {
      die("Teacher not found");
    } else {
      $row = mysqli_fetch_assoc($result);

      $teacherFirst = $row['first_name'];
      $teacherLast = $row['last_name'];
    }
  } else {
    die(mysqli_error($conn));
  }

  mysqli_free_result($result);

  // Get activity name and subject
  $query = "
    SELECT
      `activities`.`activity_name` AS activity,
      `classes`.`class_subject` AS class
    FROM `activities`
    JOIN `classes`
    ON `activities`.`class_code` = `classes`.`class_code`
    WHERE `activities`.`activity_code` = '$activityCode'
  ";

  $result = mysqli_query($conn, $query);

  if ( $result ) {
    if ( mysqli_num_rows($result) == 0 ) {
      die("Activity not found");
    } else {
      $row = mysqli_fetch_assoc($result);
      $activityName = $row['activity'];
      $className = $row['class'];
    }
  } else {
    die(mysqli_error($conn));
  }

  $message = sprintf($formats[$type], $teacherLast, $teacherFirst, $className, $activityName);

  // Add to database
  $query = "
    INSERT INTO 
      `notifications` (
        `receiver`,
        `sender`,
        `message`,
        `time`
      )
    VALUES (
      '$studentCode',
      '$teacherCode',
      '$message',
      $time
    )
  ";

  $result = mysqli_query($conn, $query);
  if ( !$result ) die(mysqli_error($conn));

  // Get student email
  $query = "
    SELECT `user_email`
    FROM `users`
    WHERE `user_code` = '$studentCode'
  ";

  $result = mysqli_query($conn, $query);

  if ( $result ) {
    if ( mysqli_num_rows($result) == 0 ) {
      die("Email not found");
    } else {
      $email = mysqli_fetch_assoc($result)['user_email'];
    }
  } else {
    die(mysqli_error($conn));
  }

  // Create the email
  $subject = "CDR Notification";
  $headers = array(
    'From' => 'master@upiscdr.com',
    'Reply-to' => 'master@upiscdr.com',
    'X-Mailer' => 'PHP/' . phpversion()
  );

  mail($email, $subject, $message, $headers);

  die("Mail successful");
}

function getNotifications() {
  global $conn;

  $query = "
    SELECT
      `id`,
      `message`,
      `time`,
      `link`,
      `status`
    FROM `notifications`
    WHERE `receiver` = '{$_SESSION['user_code']}'
    ORDER BY `time` DESC
  ";

  $result = mysqli_query($conn, $query);

  if  ( $result ) {
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
  } else {
    die(mysqli_error($conn));
  }
}

function readNotification($id) {
  global $conn;

  $query = "
    UPDATE `notifications`
    SET `status` = 1
    WHERE `id` = $id
  ";

  $result = mysqli_query($conn, $query);

  if ( !$result ) die(mysqli_error($conn));

  die("Successfully updated status");
}

function readAllNotifications() {
  global $conn;

  $query = "
    UPDATE `notifications`
    SET `status` = 1
    WHERE `receiver` = '{$_SESSION['user_code']}'
    AND `status` = 0
  ";

  $result = mysqli_query($conn, $query);

  if ( !$result ) die(mysqli_error($conn));

  die("Successfully updated status");
}

?>