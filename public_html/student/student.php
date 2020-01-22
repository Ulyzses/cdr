<?php

session_start();

if ( !(isset($_SESSION['type']) && ($_SESSION['type'] == 0 || $_SESSION['type'] == 2)) ) {
  die("Forbidden Access");
}

if ( isset($_POST['request']) ) {
  require($_SERVER['DOCUMENT_ROOT'] . "/cdr/inc/db.php");

  switch ( isset($_POST['request']) ) {
    case 'get_scores':
      getScores($conn);
      break;
    default:
      die("Unknown request");
  }
}

// Retrieves scores from database
function getScores($conn) {
  $userCode = $_SESSION['user_code'];

  $query = "
    SELECT
      `activities`.`activity_code`,
      `activities`.`activity_name`,
      `activities`.`activity_type`,
      `activities`.`class_code`,
      `activities`.`max_score`,
      `outputs`.`score`
    FROM `activities`
    JOIN `outputs`
    ON `activities`.`activity_code` = `outputs`.`activity_code`
    AND `outputs`.`student_code` = '$userCode'
  ";

  $result = mysqli_query($conn, $query);

  if ( $result ) { 
    $scores = array();

    while( $row = mysqli_fetch_assoc($result) ) {
      settype($row['score'], "integer");
      settype($row['max_score'], "integer");

      array_push($scores, $row);
    }

    die(json_encode($scores));
  } else {
    die(mysqli_error($conn));
  }
  

}

 ?>