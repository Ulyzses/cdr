<?php

session_start();

if ( !(isset($_SESSION['logged_in']) && $_SESSION['logged_in']) ) {
  header("Location: /cdr/public_html/login");
  exit();
}

// Unset all of the session variables.
$_SESSION = array();

if (ini_get("session.use_cookies")) {
  $params = session_get_cookie_params();
  setcookie(session_name(), '', time() - 42000,
      $params["path"], $params["domain"],
      $params["secure"], $params["httponly"]
  );
}

session_destroy();

header("Location: /cdr/public_html");

exit();

?>