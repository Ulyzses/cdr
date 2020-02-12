<?php

session_start();

// Retrieve announcements
require($_SERVER['DOCUMENT_ROOT'] . "/cdr/inc/announcement.php");

$announcements = retrieveAnnouncements();
 ?>
<?php include $_SERVER['DOCUMENT_ROOT'] . "/cdr/inc/begin.php" ?>
  <link rel="stylesheet" href="/cdr/public_html/css/announcements.css">
  <!-- <script src="/cdr/public_html/js/announcements.js" defer></script> -->
  <title>Announcements | UPIS CDR</title>
</head>
<body class="d-flex flex-column">
  <?php include $_SERVER['DOCUMENT_ROOT'] . "/cdr/inc/navbar.php" ?>
  <div class="container flex-grow-1">
    <div class="row h-100">
      <div class="col-lg-9 col-md-10 col-sm-11 mx-auto my-5">
        <h1 class="title">Announcements</h1>

        <?php  if ( count($announcements) > 0 ) : ?>
          <?php foreach ($announcements as $announcement) : ?>
            <div class="announcement">
              <h1 class="header">
                <?php echo $announcement['title'] ?>
              </h1>
              <h2 class="subheader">
                <?php echo "{$announcement['sender_last']}, {$announcement['sender_first']} on " . date('j F Y, H:i', $announcement['time']) ?>
              </h2>
              <p class="content">
                <?php echo $announcement['message'] ?>
              </p>
            </div>
          <?php endforeach ?>
        <?php else : ?>
          <p class="content">No announcements yet.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>
</body>