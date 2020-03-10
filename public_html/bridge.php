<?php

session_start();

if ( isset($_POST['request']) ) {
  switch ( $_POST['request'] ) {
    case 'get_notifications':
      require($_SERVER['DOCUMENT_ROOT'] . "/cdr/inc/notification.php");
      die(json_encode(getNotifications()));
      break;
    case 'read_notification':
      require($_SERVER['DOCUMENT_ROOT'] . "/cdr/inc/notification.php");
      readNotification($_POST['id']);
      break;
    case 'read_all':
      require($_SERVER['DOCUMENT_ROOT'] . "/cdr/inc/notification.php");
      readAllNotifications();
      break;
    case 'create_announcement':
      require($_SERVER['DOCUMENT_ROOT'] . "/cdr/inc/announcement.php");
      createAnnouncement($_POST['details']);
      break;
    case 'check_password':
      checkPassword($_POST['password']);
      break;
    case 'run_query':
      runQuery($_POST['query']);
      break;
    case 'edit_user':
      require($_SERVER['DOCUMENT_ROOT'] . "/cdr/inc/edit_account.php");
      editDetails($_POST['details'], $_POST['password']);
      break;
    default:
      die("Unknown request");
  }
}

function runQuery($query) {
  require_once($_SERVER['DOCUMENT_ROOT'] . "/cdr/inc/db.php");

  $result = mysqli_query($conn, $query);
  if ( $result ) {
    die("Query executed successfully.");
  } else {
    die(mysqli_error($conn));
  }
}

function checkPassword($password) {
  require_once($_SERVER['DOCUMENT_ROOT'] . "/cdr/inc/db.php");

  $username = $_SESSION['username'];
  $password = trim($password);

  $key = parse_ini_file($_SERVER['DOCUMENT_ROOT'] . "/cdr/inc/config.ini")['hash_key'];
  $hashed = hash_hmac("sha256", $username, $password);

  $query = "SELECT * FROM `hashed` WHERE `user_key` = '$hashed'";
  $result = mysqli_query($conn, $query);

  if ( $result ) {
    if ( mysqli_num_rows($result) == 1 ) {
      die(true);
    } else {
      die(false);
    }
  } else {
    die(mysqli_error($conn));
  }
}

 ?>