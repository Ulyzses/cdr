$(document).ready(() => {
  $("#logout").click(() => {
    if ( confirm("Do you wish to log out?") ) {
      location.replace("/cdr/public_html/logout");
    }
  });

  $('body').on('click', '.customAlert', removeAlert);
});

function removeAlert() {
  $('.alertBox, .alertMessage, .alertOkButton').hide(300, () => {
    $('.customAlert').remove();
  });
}

function customAlert(msg) {
  $('body').append(`
    <div class="customAlert">
      <div class="col-lg-4 col-md-6 col-sm-8 col-9 alertBox">
        <h4 class="alertMessage">${msg}</h4>
        <button class="btn btn-primary alertOkButton" type="button" onclick="removeAlert()">OK</button>
      </div>
    </div>
  `);
}