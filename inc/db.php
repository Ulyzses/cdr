<?php

function db_connect() {
  static $conn;

  if ( !isset($conn) ) {
    // parse database variables from config.ini
    $config = parse_ini_file( $_SERVER['DOCUMENT_ROOT'] . '/cdr/inc/config.ini');

    // connect to database
    $conn = mysqli_connect($config['host'], $config['user'], $config['pass'], $config['name']);

    if ( mysqli_connect_errno() ) {
      die(mysqli_connect_error());
    }
  }

  return $conn;
}

$conn = db_connect();

 ?>
