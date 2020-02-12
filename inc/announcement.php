<?php

require_once($_SERVER['DOCUMENT_ROOT'] . "/cdr/inc/db.php");

function retrieveAnnouncements() {
  global $conn;

  $query = "
    SELECT
      `announcements`.`sender` AS sender,
      `announcements`.`title` AS title,
      `announcements`.`message` AS message,
      `announcements`.`time` AS time,
      `users`.`user_first_name` AS sender_first,
      `users`.`user_last_name` AS sender_last
    FROM `announcements`
    JOIN `enrolments`
      ON `announcements`.`class_code` = `enrolments`.`subject_code`
    JOIN `users`
      ON `announcements`.`sender` = `users`.`user_code`
    WHERE `enrolments`.`student_code` = '{$_SESSION['user_code']}'
    ORDER BY `announcements`.`time` DESC
    LIMIT 3
  ";

  $result = mysqli_query($conn, $query);

  if ( $result ) {
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
  } else {
    die(mysqli_error($conn));
  }
}

// Creates a new annoucnement in the database
function createAnnouncement($details) {
  global $conn;

  $classCode = $details['classCode'];
  $scope = $details['scope'];
  $title = $details['title'];
  $message = $details['message'];
  $time = $details['time'] ?? time();
  $teacherCode = $details['teacherCode'] ?? $_SESSION['user_code'];

  if ( $scope == "current" ) {
    $query = "
      INSERT INTO
        `announcements` (
          `sender`,
          `class_code`,
          `title`,
          `message`,
          `time`
        )
      VALUES (
        '$teacherCode',
        '$classCode',
        '$title',
        '$message',
        $time
      )
    ";

    $result = mysqli_query($conn, $query);
    if ( !$result ) die(mysqli_error($conn));

    echo("Successfully created announcement");

  } else if ( $scope == "all" ) {
    // Get all classes with this teacher

    $query = "
      SELECT `class_code`
      FROM `classes`
      WHERE `class_teacher` = '$teacherCode'
    ";

    $result = mysqli_query($conn, $query);

    if ( mysqli_num_rows($result) > 0 ) {
      while ( $class = mysqli_fetch_row($result)[0]) {
        $newDetails = array(
          'classCode' => $class,
          'scope' => 'current',
          'title' => $title,
          'message' => $message,
          'time' => $time,
          'teacherCode' => $teacherCode
        );
        
        createAnnouncement($newDetails);
      }
    } else {
      die("No classes found");
    }

  } else {
    die("Unknown scope");
  }
}

 ?>