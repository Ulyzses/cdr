<?php

// if ( isset($_POST['request']) ) {
//   require_once($_SERVER['DOCUMENT_ROOT'] . "/cdr/inc/db.php");

//   switch($_POST['request']) {
//     case 'add_score':
//       notifyAddOutput($conn, $_POST['details']);
//       break;
//   }
// }

function notifyStudentScore($conn, $type, $details) {
  $formats = array(
    "add" => '%s, %s (%s) has added scores for %s.',
    "modify" => '%s, %s (%s) has changed your score for %s.',
    "delete" => '%s, %s (%s) has deleted your score for %s.'
  );
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
}

function getNotifications($conn) {
  $query = "
    SELECT
      `message`,
      `time`,
      `link`
    FROM `notifications`
    WHERE `receiver` = '{$_SESSION['user_code']}'
  ";

  $result = mysqli_query($conn, $query);

  if  ( $result ) {
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
  } else {
    die(mysqli_error($conn));
  }
}

function timeAgo($timestamp) {
  $diff = time() - $timestamp;

  $min = 60;
  $hour = 60 * 60;
  $day = 60 * 60 * 24;
  $week = 60 * 60 * 24 * 7;

  if ( $diff < $min ) {
    $timeAgo = $diff . " seconds";
  } else if ( $diff < $hour ) {
    $timeAgo = round($diff / $min) . " minutes";
  } else if ( $diff < $day ) {
    $timeAgo = round($diff / $hour) . " hours";
  } else if ( $diff < $week ) {
    $timeAgo = round($diff / $day) . " days";
  } else {
    $timeAgo = round($diff / $week) . " weeks";
  }

  return $timeAgo;
}

?>