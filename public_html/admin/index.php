<?php

session_start();

if ( !(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] && isset($_SESSION['type']) && ($_SESSION['type'] == 0)) ) {
  header("Location: /cdr/public_html");
}

 ?>
<?php include $_SERVER['DOCUMENT_ROOT'] . "/cdr/inc/begin.php" ?>
  <link rel="stylesheet" href="/cdr/public_html/css/admin.css">
  <script src="/cdr/public_html/js/admin.js" defer></script>
  <title>Admin | UPIS CDR</title>
</head>
<body class="d-flex flex-column">
  <?php include $_SERVER['DOCUMENT_ROOT'] . "/cdr/inc/navbar.php" ?>
  <div class="container flex-grow-1">
    <h1 class="title">
      Welcome, <span id="username">
        <?php echo $_SESSION['username'] ?>
      </span>
    </h1>
    <hr>
    <div>
      <h2 class="header">Announcements</h2>
      <form id="newAnnouncementForm">
        <input type="text" class="form-control" id="announcementTitle" placeholder="Announcement Title" required>
        <textarea class="form-control" id="announcementMessage" rows="10" placeholder="Announcement Message"></textarea>
        <button type="submit" class="btn btn-primary" id="newActivity">Announce</button>
      </form>
    </div>
    <div>
      <h2 class="header">MySQL Query</h2>
      <form id="newQueryForm">
        <textarea class="form-control" id="queryText" rows="10" placeholder="MySQL Query"></textarea>
        <input type="password" class="form-control" id="password" placeholder="Admin Password" required>
        <button type="submit" class="btn btn-primary" id="newQuery">Run Query</button>
      </form>
    </div>
  </div>
</body>