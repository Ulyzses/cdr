<?php

session_start();

if ( !(isset($_SESSION['type']) && $_SESSION['type'] <= 1 ) ) {
  die("Forbidden Access");
}

if ( isset($_POST['request']) ) {

  require($_SERVER['DOCUMENT_ROOT'] . "/cdr/inc/db.php");
  
  switch( $_POST['request'] ) {
    case 'new_activity':
      newActivity($conn, $_POST['details'], $_SESSION['user_code']);
      break;
    case 'load_class':
      loadClass($conn, $_POST['subject']);
      break;
    default:
      die("What");
  }
} else {
  die("Forbidden Access");
}

function loadClass($conn, $classCode) {
  // Check if class code is valid
  if ( strlen($classCode) != 8 ) {
    die("Class code invalid");
  }

  // Get Students
  $query = "SELECT `users`.`user_code`, `users`.`user_first_name`, `users`.`user_last_name` FROM `users` JOIN `enrolments` ON `users`.`user_code` = `enrolments`.`student_code` WHERE `enrolments`.`subject_code` = '$classCode'";

  $result = mysqli_query($conn, $query);

  if ( $result ) {
    $students = array();

    while ( $row = mysqli_fetch_assoc($result) ) {
      array_push($students, $row);
    }

  } else {
    die(mysqli_error($conn));
  }

  // Get Activities
  $query = "SELECT `activity_code`, `activity_name`, `activity_type`, `max_score` FROM `activities` WHERE `class_code` = '$classCode'";

  $result = mysqli_query($conn, $query);

  if ( $result ) {
    $activities = array();

    while ( $row = mysqli_fetch_assoc($result) ) {
      array_push($activities, $row);
    }

  } else {
    die(mysqli_error($conn));
  }

  // Get Outputs
  $query = "SELECT `outputs`.`student_code`, `outputs`.`activity_code`, `outputs`.`score` FROM `outputs` JOIN `activities` ON `outputs`.`activity_code` = `activities`.`activity_code` WHERE `activities`.`class_code` = '$classCode'";

  $result = mysqli_query($conn, $query);

  if ( $result ) {
    $outputs = array();

    while ( $row = mysqli_fetch_assoc($result) ) {
      array_push($outputs, $row);
    }
  } else {
    die(mysqli_error($conn));
  }

  // Return data
  $return = array(
    "students" => $students,
    "activities" => $activities,
    "outputs" => $outputs
  );

  die(json_encode($return));
}

function newActivity($conn, $details) {
  $classCode = $details['code'];
  $name = $details['name'];
  $type = $details['type'];
  $score = $details['score'];

  // Check if class code is valid
  if ( strlen($classCode) != 8 ) {
    die("Class code invalid");
  }

  // Check if activity name already exists
  $query = "SELECT `activity_code` FROM `activities` WHERE `activity_name` = '$name' AND `class_code` = '$classCode'";

  $result = mysqli_query($conn, $query);

  if ( $result ) {
    if ( mysqli_num_rows($result) == 1 ) {
      $actCode = mysqli_fetch_array($result)[0];
      mysqli_free_result($result);
      die("Activity already exists with code $actCode");
    }
  } else {
    die(mysqli_error($conn));
  }

  // Insert into activities
  $actCode = randomCode();
  $query = "INSERT INTO
    `activities`
    (
      `activity_code`,
      `activity_name`,
      `activity_type`,
      `class_code`,
      `max_score`
    )
    VALUES
    (
      '$actCode',
      '$name',
      '$type',
      '$classCode',
      '$score'
    )
  ";

  $result = mysqli_query($conn, $query);

  if ( !$result ) die(mysqli_error($conn));

  die("SUCCESS");
}

function randomCode($size = 8) {
  $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
  $string = '';
  
  for ($i = 0; $i < $size; $i++) {
    $string .= $chars[rand(0, 61)];
  }
  
  return $string;
}

 ?>