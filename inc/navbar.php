<?php

// Load Notifications if user is logged in

if ( isset($_SESSION['logged_in']) && $_SESSION['logged_in'] ) {
  require($_SERVER['DOCUMENT_ROOT'] . "/cdr/inc/notification.php");
  
  $notifications = getNotifications($conn);
}

?>

<nav class="navbar navbar-dark bg-dark py-2 sticky-top">
  <a href="/cdr/public_html" class="navbar-brand">CDR</a>

  <!-- For average screens -->
  <div class="d-none d-md-flex">
    <ul class="navbar-nav flex-row">
      
      <!-- Control panel for administrators only -->
      <?php if ( isset($_SESSION['type']) && $_SESSION['type'] == 0 ) : ?>
        <li class="nav-item mr-0" title="Control Panel">
          <a href="/cdr/control.php" class="nav-link py-0 px-2">
            <i class="fas fa-sliders-h"></i>
          </a>
        </li>

        <li class="nav-item mr-0" title="Register User">
          <a href="/cdr/public_html/register/user" class="nav-link py-0 px-2">
            <i class="fa fa-user-plus" aria-hidden="true"></i>
          </a>
        </li>


      <?php endif; ?>
      
      <!-- Announcements -->
      <li class="navbar-item mr-0" title="Announcements">
        <a href="#" class="nav-link py-0 px-2">
          <i class="fas fa-bullhorn icon"></i>
        </a>
      </li>

      <!-- Classes for teachers only -->
      <?php if ( isset($_SESSION['type']) && $_SESSION['type'] == 1 ) : ?>
        <li class="nav-item mr-0" title="Classes">
          <a href="/cdr/public_html/teacher" class="nav-link py-0 px-2">
            My Classes
          </a>
        </li>
      <?php endif; ?>

      <!-- Subjects for students only -->
      <?php if ( isset($_SESSION['type']) && $_SESSION['type'] == 2 ) : ?>
        <li class="nav-item mr-0" title="Classes">
          <a href="/cdr/public_html/student" class="nav-link py-0 px-2">
            My Subjects
          </a>
        </li>
      <?php endif; ?>
      
      <!-- Notifications -->
      <li class="nav-item dropdown mr-0 px-2" title="Notifications">
        <a href="#" class="nav-link dropdown-toggle py-0" data-toggle="dropdown">
          <i class="fas fa-bell icon"></i>
        </a>
        <div class="dropdown-menu dropdown-menu-right position-absolute notify-drop">
          <h3 class="notify-text notify-title">Notifications</h3>
          <ul class="notifications">
            <?php foreach($notifications as $notification) : ?>
              <a href="<?php echo $notification['link'] ?>">
                <li class="notification">
                  <h4 class="notify-text notify-body">
                    <?php echo $notification['message'] ?>
                  </h4>
                  <h5 class="notify-text notify-time">
                    <?php echo timeAgo($notification['time']) ?> ago
                  </h5>
                </li>
              </a>
            <?php endforeach; ?>
          </ul>
        </div>
      </li>

      <!-- User -->
      <li class="nav-item dropdown mr-0 px-2" title="Profile">
        <a href="#" class="nav-link dropdown-toggle py-0" data-toggle="dropdown">
          <i class="fas fa-user icon"></i>
        </a>
        <div class="dropdown-menu dropdown-menu-right position-absolute">
          <a href="/cdr/public_html/profile" class="dropdown-item">My Profile</a>
          <a href="#" class="dropdown-item">Settings</a>
          <div class="dropdown-divider"></div>
          <a href="/cdr/public_html/about" class="dropdown-item">About</a>
          <a href="#" class="dropdown-item">Help Center</a>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item" id="logout">Log Out</a>
        </div>
      </li>
    </ul>
  </div>

  <!-- For Small Screens -->
  <div class="d-md-none">
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#smallScreen" aria-controls="smallScreen" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
  </div>
  <div class="collapse navbar-collapse d-md-none px-4" id="smallScreen">
    <ul class="navbar-nav">
      <li class="navbar-item"><a href="#" class="nav-link">Announcements</a></li>
      <li class="navbar-item"><a href="#" class="nav-link">Notifications</a></li>
          <div class="dropdown-divider"></div>
      <li class="navbar-item"><a href="/cdr/public_html/profile" class="nav-link">My Profile</a></li>

      <!-- Classes for teachers only -->
      <?php if ( isset($_SESSION['type']) && $_SESSION['type'] == 1 ) : ?>
        <li class="navbar-item"><a href="/cdr/public_html/teacher" class="nav-link">My Classes</a></li>
      <?php endif; ?>

      <!-- Subjects for students only -->
      <?php if ( isset($_SESSION['type']) && $_SESSION['type'] == 2 ) : ?>
        <li class="navbar-item"><a href="/cdr/public_html/student" class="nav-link">My Subjects</a></li>
      <?php endif; ?>
      
      <div class="dropdown-divider"></div>
      <li class="navbar-item"><a href="#" class="nav-link">User Settings</a></li>
      <div class="dropdown-divider"></div>
      <li class="navbar-item"><a href="/cdr/public_html/about" class="nav-link">About</a></li>
      <li class="navbar-item"><a href="#" class="nav-link">Help Center</a></li>
      <div class="dropdown-divider"></div>
      <li class="navbar-item"><a href="#" class="nav-link" id="logout">Log Out</a></li>
    </ul>
  </div>
</nav>