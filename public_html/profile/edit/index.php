<?php

session_start();

?>
<?php include $_SERVER['DOCUMENT_ROOT'] . "/cdr/inc/begin.php" ?>
  <link rel="stylesheet" href="/cdr/public_html/css/edit.css">
  <script src="/cdr/public_html/js/edit.js"></script>
  <title>Edit Profile | UPIS CDR</title>
</head>
<body>
  <?php include $_SERVER['DOCUMENT_ROOT'] . "/cdr/inc/navbar.php" ?>
  <div class="container flex-grow-1">
    <div class="row">
      <div class="col-12 col-md-10 col-lg-7 mx-auto">
        <form class="w-100" id="editForm">
          <h3 class="header">Account Details</h2>
          <div class="row form-group-row">
            <label for="usernameInput" class="col-4 col-form-label">Username</label>
            <div class="col-8">
              <input type="text" id="usernameInput" class="form-control" value="<?php echo $_SESSION['username']; ?>">
            </div>
          </div>
          <div class="row form-group-row">
            <label for="emailInput" class="col-4 col-form-label">Recovery Email</label>
            <div class="col-8">
              <input type="email" id="emailInput" class="form-control" value="<?php echo $_SESSION['user_data']['email']; ?>">
            </div>
          </div>

          <h3 class="header">Personal Details</h2>
          <p class="description">Details not related to your account settings. The last name may not be edited to avoid confusion. To change it, please contact administrators at <a href="https://upiscdr.com/contact/">upiscdr.com/contact</a>.</p>
          <div class="row form-group-row">
            <label for="firstInput" class="col-4 col-form-label">First Name</label>
            <div class="col-8">
              <input type="text" id="firstInput" class="form-control" value="<?php echo $_SESSION['user_data']['first_name']; ?>">
            </div>
          </div>
          <div class="row form-group-row">
            <label for="middleInput" class="col-4 col-form-label">Middle Name</label>
            <div class="col-8">
              <input type="text" id="middleInput" class="form-control" value="<?php echo $_SESSION['user_data']['middle_name']; ?>">
            </div>
          </div>
          <div class="row form-group-row">
            <label for="lastInput" class="col-4 col-form-label">Last Name</label>
            <div class="col-8">
              <input type="text" id="lastInput" class="form-control" value="<?php echo $_SESSION['user_data']['last_name']; ?>" readonly>
            </div>
          </div>

          <h3 class="header">Change Password</h3>
          <div class="row form-group-row">
            <label for="newPassInput" class="col-4 col-form-label">New Password</label>
            <div class="col-8">
              <input type="password" id="newPassInput" class="form-control">
            </div>
          </div>
          <div class="row form-group-row">
            <label for="confirmPassInput" class="col-4 col-form-label">Confirm Password</label>
            <div class="col-8">
              <input type="password" id="confirmPassInput" class="form-control">
            </div>
          </div>

          <hr>
          <div class="w-100 d-flex">
            <button type="submit" class="btn btn-primary ml-auto" id="saveButton" disabled>Save changes</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Old Password Form -->
  <div class="modal fade" id="oldPassword" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Enter Password</h5>
        </div>
        <form id="oldPasswordForm">
          <div class="modal-body">
            <p>It seems you are trying to change an account setting. To continue, please enter your old password.</p>
            <input type="password" id="oldPassInput" class="form-control" placeholder="Old Password" required autofocus>
            <p><a href="/cdr/public_html/account_recovery">Forgot My Password</a></p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Submit</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</body>

</html>