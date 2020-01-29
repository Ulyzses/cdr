<?php

session_start();

if ( isset($_POST['request']) ) {
  switch ( $_POST['request'] ) {
    case 'get_notifications':
      require($_SERVER['DOCUMENT_ROOT'] . "/cdr/inc/notification.php");
      die(json_encode(getNotifications()));
  }
}

 ?>