<?php

session_start();

if ( !(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] && isset($_SESSION['type']) && $_SESSION['type'] <= 1) ) {
  header("Location: /cdr/public_html/");
}

require($_SERVER['DOCUMENT_ROOT'] . "/cdr/inc/db.php");

// Get classes
$query = "
  SELECT
    `class_subject`,
    `class_code`,
    `class_level`,
    `class_section`,
    `class_room`
  FROM `classes`
  WHERE `class_teacher` = '{$_SESSION['user_code']}'
";

$result = mysqli_query($conn, $query);

if ( $result ) {
  $classes = mysqli_fetch_all($result, MYSQLI_ASSOC);
} else {
  die(mysqli_error($conn));
}

 ?>
<?php include $_SERVER['DOCUMENT_ROOT'] . "/cdr/inc/begin.php" ?>
  <link rel="stylesheet" href="/cdr/public_html/css/teacher.css">
  <script src="/cdr/public_html/js/teacher.js" defer></script>
  <title>Teacher | UPIS CDR</title>
</head>
<body>
  <?php include $_SERVER['DOCUMENT_ROOT'] . "/cdr/inc/navbar.php" ?>
  <div class="container-fluid flex-grow-1">
    <div class="row h-100">

      <!-- Sidebar -->
      <div class="col-lg-2 col-md-3 d-none d-md-flex h-100 flex-column sidebar">
        <h1 class="sidebar-title">
          <i class="fas fa-chalkboard-teacher"></i>  
          Classes
        </h1>
        <ul class="nav flex-column classes">
          <?php foreach($classes as $class) : ?>
            <li class="kurasu" data-code="<?php echo $class['class_code'] ?>">
              <h3 class="title">
                <?php echo $class['class_level'] . "&ndash;" . $class['class_section'] ?>
              </h3>
              <p class="details">
                <?php echo "{$class['class_subject']} | {$class['class_code']}" ?>
              </p>
            </li>
          <?php endforeach; ?>
        </ul>
          
      </div>

      <!-- Content -->
      <div class="col-lg-10 col-md-9 flex-sm-column d-flex content">
        <div class="row table-nav">
          <ul class="nav nav-tabs activity-types">
            <li class="nav-item">
              <button class="nav-link active" data-toggle="tab" value="all">All</button>
            </li>
            <li class="nav-item">
              <button class="nav-link" data-toggle="tab" value="seatwork">Seatworks</button>
            </li>
            <li class="nav-item">
              <button class="nav-link" data-toggle="tab" value="homework">Homework</button>
            </li>
            <li class="nav-item">
              <button class="nav-link" data-toggle="tab" value="quiz">Quizzes</button>
            </li>
            <li class="nav-item">
              <button class="nav-link" data-toggle="tab" value="project">Projects</button>
            </li>
            <li class="nav-item">
              <button class="nav-link" data-toggle="tab" value="new">&plus;</button>
            </li>
          </ul>
          <button class="ml-auto new-activity" data-tooltip="tooltip" data-placement="left" title="Add Activity" data-toggle="modal" data-target="#addActivity">&plus;</button>
        </div>
        <div class="row position-relative flex-grow-1">
          <div class="main-content d-flex">
            <div class="table-div d-flex flex-grow-1">
              <h4 class="text-center align-self-center mx-auto">Please select a<br>class to start</h4>
            </div>
          </div>
        </div>

        <!-- New Annoucement Form -->
        <div class="modal fade add-form" id="newAnnouncement" tabindex="-1" role="dialog" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">Create Annoucnement</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <form id="newAnnouncementForm">
                <div class="modal-body">
                  <select id="announcement_scope" class="custom-select form-control" required>
                    <option value="current" selected>Current Class</option>
                    <option value="all">All Classes</option>
                  </select>
                  <input type="text" id="announcement_title" class="form-control" placeholder="Announcement Title" required>
                  <textarea class="form-control" id="announcement" rows="6" placeholder="New Announcement" required></textarea>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                  <button name="new_submit" type="submit" class="btn btn-primary" id="newAnnounce">Announce</button>
                </div>
              </form>
            </div>
          </div>
        </div>

        <!-- Add Activity Form -->
        <div class="modal fade add-form" id="addActivity" tabindex="-1" role="dialog" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">Add Activity</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <form id="newActivityForm">
                <div class="modal-body">
                  <input type="text" id="activity_name" class="form-control" placeholder="Name" required autofocus>
                  <select id="activity_type" class="custom-select form-control" required>
                    <option value="" disabled selected>Type</option>
                    <option value="seatwork">Seatwork</option>
                    <option value="homework">Homework</option>
                    <option value="groupwork">Groupwork</option>
                    <option value="quiz">Quiz</option>
                    <option value="project">Project</option>
                  </select>
                  <input type="number" id="activity_score" class="form-control" placeholder="Max Score" required>
                  <textarea id="scores" class="form-control" rows="5" placeholder="MS Excel data (Optional)"></textarea>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                  <button name="new_submit" type="submit" class="btn btn-primary" id="newActivity">Save changes</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>

</html>