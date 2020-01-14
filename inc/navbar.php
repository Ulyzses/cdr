<nav class="navbar navbar-dark bg-dark py-2 sticky-top">
  <a href="/cdr/public_html" class="navbar-brand">CDR</a>

  <!-- For average screens -->
  <div class="d-none d-md-flex">
    <ul class="navbar-nav flex-row">
      
      <!-- Control panel for administrators only -->
      <?php if ( isset($_SESSION['type']) && $_SESSION['type'] == 0 ) : ?>
        <div class="nav-item mr-0" title="Control Panel">
          <a href="/cdr/control.php" class="nav-link py-0 px-2">
            <i class="fas fa-sliders-h"></i>
          </a>
        </div>
      <?php endif; ?>
      
      <!-- Announcements -->
      <li class="navbar-item mr-0" title="Announcements">
        <a href="#" class="nav-link py-0 px-2">
          <i class="fas fa-bullhorn icon"></i>
        </a>
      </li>

      <!-- Notifications -->
      <li class="nav-item mr-0" title="Notifications">
        <a href="" class="nav-link py-0 px-2">
          <i class="fas fa-bell icon"></i>
        </a>
      </li>

      <!-- User -->
      <li class="nav-item dropdown mr-0 px-2">
        <a href="#" class="nav-link dropdown-toggle py-0" data-toggle="dropdown" title="Profile">
          <i class="fas fa-user icon"></i>
        </a>
        <div class="dropdown-menu dropdown-menu-right position-absolute">
          <a href="#" class="dropdown-item">My Profile</a>

          <!-- Classes for teachers only -->
          <?php if ( isset($_SESSION['type']) && $_SESSION['type'] == 1 ) : ?>
            <a href="/cdr/public_html/teacher" class="dropdown-item">My Classes</a>
          <?php endif; ?>

          <!-- Classes for students only -->
          <?php if ( isset($_SESSION['type']) && $_SESSION['type'] == 2 ) : ?>
            <a href="#" class="dropdown-item">My Subjects</a>
          <?php endif; ?>

          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">User Settings</a>
          <div class="dropdown-divider"></div>
          <a href="../about" class="dropdown-item">About</a>
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
      <li class="navbar-item"><a href="#" class="nav-link">My Profile</a></li>

      <!-- Classes for teachers only -->
      <?php if ( isset($_SESSION['type']) && $_SESSION['type'] == 1 ) : ?>
        <li class="navbar-item"><a href="/cdr/public_html/teacher" class="nav-link">My Classes</a></li>
      <?php endif; ?>

      <!-- Subjects for students only -->
      <?php if ( isset($_SESSION['type']) && $_SESSION['type'] == 2 ) : ?>
        <li class="navbar-item"><a href="#" class="nav-link">My Subjects</a></li>
      <?php endif; ?>
      
      <div class="dropdown-divider"></div>
      <li class="navbar-item"><a href="#" class="nav-link">User Settings</a></li>
      <div class="dropdown-divider"></div>
      <li class="navbar-item"><a href="#" class="nav-link">About</a></li>
      <li class="navbar-item"><a href="#" class="nav-link">Help Center</a></li>
      <div class="dropdown-divider"></div>
      <li class="navbar-item"><a href="#" class="nav-link" id="logout">Log Out</a></li>
    </ul>
  </div>
</nav>