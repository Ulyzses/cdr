<?php

session_start();

if ( !(isset($_SESSION['type']) && $_SESSION['type'] <= 1) ) {
  die("Forbidden Access");
}

if ( isset($_POST['request']) ) {
  require($_SERVER['DOCUMENT_ROOT'] . "/cdr/inc/db.php");
  require($_SERVER['DOCUMENT_ROOT'] . "/cdr/inc/notification.php");

  switch ( $_POST['request'] ) {
    case 'load_class':
      loadClass($conn, $_POST['classCode']);
      break;
    case 'add_activity':
      addActivity($conn, $_POST['details']);
      break;
    case 'add_output':
      addOutput($conn, $_POST['output']);
      break;
    case 'modify_output':
      modifyOutput($conn, $_POST['output']);
      break;
    case 'delete_output':
      deleteOutput($conn, $_POST['output']);
      break;
    default:
      die("Unknown request");
  }
}

// Randomiser
function randomCode($size = 8) {
  $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
  $string = '';
  
  for ($i = 0; $i < $size; $i++) {
    $string .= $chars[rand(0, 61)];
  }
  
  return $string;
}

// Retrieves class data from the database
function loadClass($conn, $classCode) {
  // Get students
  $query = "
  SELECT
    `users`.`user_code`,
    `users`.`user_first_name`,
    `users`.`user_last_name`
  FROM `users`
  JOIN `enrolments`
  ON `users`.`user_code` = `enrolments`.`student_code`
  WHERE `enrolments`.`subject_code` = '$classCode'
  ORDER BY
    `users`.`user_last_name`,
    `users`.`user_first_name`
  ";

  $result = mysqli_query($conn, $query);
  
  if ( $result ) {
    $students = array();

    while ( $row = mysqli_fetch_assoc($result) ) {
      array_push($students, $row);
    }
  } else {
    die(mysqli_error($conn));
  }

  mysqli_free_result($result);

  // Get activities
  $query = "
    SELECT
      `activity_code`,
      `activity_name`,
      `activity_type`,
      `max_score`
    FROM `activities`
    WHERE `class_code` = '$classCode'
  ";

  $result = mysqli_query($conn, $query);

  if ( $result ) {
    $activities = array();
    
    while ( $row = mysqli_fetch_assoc($result) ) {
      settype($row['max_score'], "integer");
      array_push($activities, $row);
    }
  } else {
    die(mysqli_error($conn));
  }

  mysqli_free_result($result);

  // Get outputs
  $query = "
    SELECT
      `outputs`.`student_code`,
      `outputs`.`activity_code`,
      `outputs`.`score`
    FROM `outputs`
    JOIN `activities`
    ON `outputs`.`activity_code` = `activities`.`activity_code`
    WHERE `activities`.`class_code` = '$classCode'
  ";

  $result = mysqli_query($conn, $query);

  if ( $result ) {
    $outputs = array();
    
    while ( $row = mysqli_fetch_assoc($result) ) {
      settype($row['score'], "integer");
      array_push($outputs, $row);
    }
  } else {
    die(mysqli_error($conn));
  }
  
  mysqli_free_result($result);

  // Return the data
  $return = array(
    "students" => $students,
    "activities" => $activities,
    "outputs" => $outputs
  );

  die(json_encode($return));
}

// Adds a new activity to the database
function addActivity($conn, $details) {
  $classCode = $details['classCode'];
  $name = $details['name'];
  $type = $details['type'];
  $score = $details['score'];

  // Check if activity already exists
  $query = "
    SELECT *
    FROM `activities`
    WHERE `class_code` = '$classCode'
    AND `activity_name` = '$name'
  ";

  $result = mysqli_query($conn, $query);
  
  if ( $result ) {
    if ( mysqli_num_rows($result) != 0 ) {
      die("Activity already exists");
    }
  } else {
    die(mysqli_query($conn));
  }

  mysqli_free_result($result);

  // Insert activity
  $activityCode = randomCode();

  $query = "
    INSERT INTO
      `activities` (
        `activity_code`,
        `activity_name`,
        `activity_type`,
        `class_code`,
        `max_score`
      )
    VALUES (
      '$activityCode',
      '$name',
      '$type',
      '$classCode',
      '$score'
    )
  ";

  $result = mysqli_query($conn, $query);
  if ( !$result ) die(mysqli_error($conn));

  die(json_encode(array(
    "activity_code" => $activityCode,
    "activity_name" => $name,
    "activity_type" => $type,
    "max_score" => $score
  )));
}

// Adds a new output to the database
function addOutput($conn, $output) {
  $studentCode = $output['student_code'];
  $activityCode = $output['activity_code'];
  $score = $output['score'];

  // Check if output already exists
  $query = "
    SELECT `score`
    FROM `outputs`
    WHERE `student_code` = '$studentCode'
    AND `activity_code` = '$activityCode'
  ";

  $result = mysqli_query($conn, $query);

  if ( $result ) {
    if ( mysqli_num_rows($result) != 0 ) {
      die("Output already exists");
      /* Modify row */
    }
  } else {
    die(mysqli_error($query));
  }

  mysqli_free_result($result);

  // Insert output into database
  $query = "
    INSERT INTO
      `outputs` (
        `student_code`,
        `activity_code`,
        `score`
      )
    VALUES (
      '$studentCode',
      '$activityCode',
      $score
    )
  ";

  $result = mysqli_query($conn, $query);
  if ( !$result ) die(mysqli_error($conn));

  echo("Output successfully added\n");
  
  // Notify
  $notifDetails = array(
    "studentCode" => $studentCode,
    "teacherCode" => $_SESSION['user_code'],
    "activityCode" => $activityCode,
    "time" => time()
  );

  notifyStudentScore($conn, "add", $notifDetails);
}

// Modifies an existing output in the databse
function modifyOutput($conn, $output) {
  $studentCode = $output['student_code'];
  $activityCode = $output['activity_code'];
  $score = $output['score'];

  $query = "
    UPDATE `outputs`
    SET `score` = $score
    WHERE `student_code` = '$studentCode'
    AND `activity_code` = '$activityCode'
  ";

  $result = mysqli_query($conn, $query);
  if ( !$result ) die(mysqli_error($conn));
  
  echo("Output successfully altered");

  // Notify
  $notifDetails = array(
    "studentCode" => $studentCode,
    "teacherCode" => $_SESSION['user_code'],
    "activityCode" => $activityCode,
    "time" => time()
  );

  notifyStudentScore($conn, "modify", $notifDetails);

}

// Delete an existing output from the database
function deleteOutput($conn, $output) {
  $studentCode = $output['student_code'];
  $activityCode = $output['activity_code'];

  $query = "
    DELETE FROM `outputs`
    WHERE `student_code` = '$studentCode'
    AND `activity_code` = '$activityCode'
  ";

  $result = mysqli_query($conn, $query);
  if ( !$result ) die(mysqli_error($conn));
  
  echo("Output successfully deleted");
  
  // Notify
  $notifDetails = array(
    "studentCode" => $studentCode,
    "teacherCode" => $_SESSION['user_code'],
    "activityCode" => $activityCode,
    "time" => time()
  );

  notifyStudentScore($conn, "delete", $notifDetails);
}
 ?>