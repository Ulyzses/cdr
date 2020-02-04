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
    default:
      die("Unknown request");
  }
}

 ?>