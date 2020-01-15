<?php

session_start();

if ( !(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] && isset($_SESSION['type']) && $_SESSION['type'] <= 1) ) {
  header("Location: /cdr/public_html/");
}

require($_SERVER['DOCUMENT_ROOT'] . "/cdr/inc/db.php");

// Get classes
$query = "SELECT `class_subject`, `class_code`, `class_level`, `class_section`, `class_room` FROM `classes` WHERE `class_teacher` = '{$_SESSION['user_code']}'";

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
      <div class="col-lg-3 col-md-4 d-none d-md-flex h-100 flex-column sidebar">
        <h1 class="sidebar-title">
          <i class="fas fa-chalkboard-teacher"></i>  
          Classes
        </h1>
        <ul class="nav flex-column classes">
          <?php foreach($classes as $class) : ?>
            <li class="kurasu" data-code="<?php echo $class['class_code'] ?>">
              <h3 class="title">
                <span class="level"><?php echo $class['class_level'] ?></span>&ndash;<span class="section"><?php echo $class['class_section'] ?></span>
              </h3>
              <p class="details">
                <span class="subject"><?php echo $class['class_subject'] ?></span> | <span class="room"><?php echo $class['class_room'] ?></span>
              </p>
            </li>
          <?php endforeach; ?>
        </ul>
          
      </div>

      <!-- Content -->
      <div class="col-lg-9 col-md-8 flex-sm-column content">
        <div class="row h-100 position-relative">
          
          <!-- Add Activity Form -->
          <div class="modal fade add-form" id="addActivity" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                    <input type="text" id="activity_name" class="form-control" placeholder="Name" required>
                    <select id="activity_type" class="custom-select form-control" required>
                      <option value="" disabled selected>Type</option>
                      <option value="seatwork">Seatwork</option>
                      <option value="homework">Homework</option>
                      <option value="groupwork">Groupwork</option>
                      <option value="quiz">Quiz</option>
                      <option value="project">Project</option>
                    </select>
                  <input type="number" id="activity_score" class="form-control" placeholder="Max Score" required>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button name="new_submit" type="submit" class="btn btn-primary" id="newActivity">Save changes</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
          
          <!-- Add Activity Button -->
          <button type="button" class="add-button" data-tooltip="tooltip" data-placement="left" title="Add Activity"  data-toggle="modal" data-target="#addActivity">&plus;</button>
          
          <div class="main-content w-100 d-flex">
            <h4 class="text-center align-self-center mx-auto">Please select a<br>class to start</h4>
            
            <!-- <table class="table table-sm table-bordered table-striped table-responsive text-center">
              <thead>
                <tr>
                  <th>Student</th>
                  <th>Seatwork 1</th>
                  <th>Seatwork 2</th>
                </tr>
              </thead>
              <tbody contenteditable="true">
                <tr>
                  <td></td>
                  <td></td>
                  <td></td>
                </tr>
                <tr>
                  <td></td>
                  <td></td>
                  <td></td>
                </tr>
              </tbody>
            </table> -->
          </div>
          
        </div>
      </div>
    </div>
  </div>
</body>

</html>