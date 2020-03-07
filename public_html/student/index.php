<?php

session_start();

if ( !(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] && isset($_SESSION['type']) && ($_SESSION['type'] == 0 || $_SESSION['type'] == 2)) ) {
  header("Location: /cdr/public_html/");
}

require($_SERVER['DOCUMENT_ROOT'] . "/cdr/inc/db.php");

// Get subjects
$query = "
  SELECT
    `classes`.`class_subject` AS subject,
    `users`.`user_first_name` AS teacher_first,
    `users`.`user_last_name` AS teacher_last,
    `classes`.`class_code` AS code,
    `classes`.`class_level` AS level,
    `classes`.`class_room` AS room
  FROM `classes`
  INNER JOIN `enrolments`
    ON `classes`.`class_code` = `enrolments`.`subject_code`
  INNER JOIN `users`
    ON `classes`.`class_teacher` = `users`.`user_code`
  WHERE `enrolments`.`student_code` = '{$_SESSION['user_code']}'
  ORDER BY `classes`.`class_subject`
";

$result = mysqli_query($conn, $query);

if ( $result ) {
  $subjects = mysqli_fetch_all($result, MYSQLI_ASSOC);
} else {
  die(mysqli_error($conn));
}

mysqli_free_result($result);

// Retrieve announcements
require($_SERVER['DOCUMENT_ROOT'] . "/cdr/inc/announcement.php");

$announcements = retrieveAnnouncements(3);

 ?>
<?php include $_SERVER['DOCUMENT_ROOT'] . "/cdr/inc/begin.php" ?>
  <link rel="stylesheet" href="/cdr/public_html/css/student.css">
  <script src="/cdr/public_html/js/student.js" defer></script>
  <title>Student | UPIS CDR</title>
</head>
<body>
  <?php include $_SERVER['DOCUMENT_ROOT'] . "/cdr/inc/navbar.php" ?>
  <div class="container-fluid position-relative flex-grow-1">
    <div class="row h-100">
      
      <!-- Sidebar -->
      <div class="col-lg-3 col-md-4 d-none d-md-flex flex-column sidebar position-fixed h-100">
        <h1 class="sidebar-title">
          <i class="fas fa-chalkboard-teacher"></i>  
          Subjects
        </h1>
        <ul class="nav flex-column subjects">
          <?php foreach($subjects as $subject) : ?>
            <li class="subject" data-code="<?php echo $subject['code'] ?>">
              <h3 class="title">
                <?php echo "{$subject['subject']} {$subject['level']}" ?>
              </h3>
              <p class="details">
                <?php echo "{$subject['teacher_last']}, {$subject['teacher_first']} | {$subject['room']}" ?>
              </p>
            </li>
          <?php endforeach; ?>
        </ul>
        <hr>
        <form id="joinClassForm">
          <div class="input-group">
            <input type="text" id="joinCode" class="form-control" placeholder="Class Code">
            <div class="input-group-append">
              <button class="btn btn-sm btn-primary btn-block text-uppercase" type="submit">Join Class</button>
            </div>
          </div>
        </form>
      </div>

      <!-- Content -->
      <div class="col-lg-6 col-md-8 offset-lg-3 offset-md-4 flex-sm-column d-flex position-relative content">
        <div class="main-card">
          <h1 class="d-flex align-items-center card-title">
            <span id="title">All</span>
            <i class="fas fa-filter ml-auto" data-tooltip="tooltip" data-placement="left" title="Filter" data-toggle="modal" data-target="#filter"></i>
          </h1>
          <table class="table table-striped table-sm w-100" id="scoresCard">
            <thead>
              <tr>
                <th class="col-sm-4 col-5">Name</th>
                <th class="col-sm-4 col-4">Type</th>
                <th class="col-sm-2 col-3">Score</th>
                <th class="col-sm-2 d-none d-sm-block">Percent</th>
              </tr>
            </thead>
            <tbody id="scoresBody">
            </tbody>
          </table>
        </div>
      </div>

      <!-- Filter Form -->
      <div class="modal fade add-form" id="filter" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Filter Activities</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <form id="filterForm">
              <div class="modal-body">
                <input class="d-none filter-checkbox" type="checkbox" value="seatwork" id="filter1" checked>
                <label class="filter-label" for="filter1">
                  <div class="alert alert-dark">Seatworks</div>
                </label>
                
                <input class="d-none filter-checkbox" type="checkbox" value="homework" id="filter2" checked>
                <label class="filter-label" for="filter2">
                  <div class="alert alert-dark">Homework</div>
                </label>
                
                <input class="d-none filter-checkbox" type="checkbox" value="quiz" id="filter3" checked>
                <label class="filter-label" for="filter3">
                  <div class="alert alert-dark">Quizzes</div>
                </label>

                <input class="d-none filter-checkbox" type="checkbox" value="project" id="filter4" checked>
                <label class="filter-label" for="filter4">
                  <div class="alert alert-dark">Projects</div>
                </label>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button name="filter_submit" type="submit" class="btn btn-primary" id="filterSubmit">Apply Filter</button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <!-- Announcements -->
      <div class="col-lg-3 offset-lg-9 d-none d-lg-flex position-fixed h-100">
        <div class="d-flex flex-column w-100 announcement-container">
          <h1 class="announcements-title">Announcements</h1>
          <?php if ( count($announcements) > 0 ) : ?>
            <ul class="nav flex-column announcements">
              <?php foreach($announcements as $announcement) : ?>
                <li class="announcement w-100">
                  <a href="#">
                    <h2 class="announcement-title">
                      <?php echo $announcement['title'] ?>
                    </h2>
                  </a>
                  <h3 class="announcement-subtitle">
                    <?php echo "{$announcement['sender_last']}, {$announcement['sender_first']} on " . date('j F Y, H:i', $announcement['time']) ?>
                  </h3>
                  <p class="announcement-message">
                    <?php echo $announcement['message'] ?>
                  </p>
                </li>
              <?php endforeach; ?>
            </ul>
            <a href="#"><p class="mt-2 announcement-more">Read more &rarr;</p></a>
          <?php else : ?>
            <p class="announcement-message">
              No announcements yet
            </p>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</body>

</html>