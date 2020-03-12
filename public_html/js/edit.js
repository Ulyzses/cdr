'use strict'

$(document).ready(() => {
  let requirePassword = false;

  $('#editForm input').keydown(e => {
    if ( e.which != 9 ) {
      $('#saveButton').removeAttr('disabled');
    }
  });

  $('#usernameInput, #emailInput, #newPassInput').change(() => {
    requirePassword = true;
  })

  $('#editForm').submit(e => {
    e.preventDefault();

    if ( requirePassword ) {
      $('#oldPassword').modal({
        backdrop: 'static',
        keyboard: false
      });
    } else {
      updateData();
    }
  });

  $("#oldPasswordForm").submit(e => {
    e.preventDefault();

    updateData($("#oldPassInput").val());
  })
});

async function updateData(password = "") {
  let inputs = [
    "usernameInput",
    "emailInput",
    "firstInput",
    "middleInput",
    "lastInput",
    "newPassInput",
    "confirmPassInput"
  ].map(id => $(`#${id}`).val());

  let result = await dbUpdateData(inputs, password);

  try {
    var resultJSON = JSON.parse(result);
    $('.alert').remove();
    
    if ( resultJSON.status == 'success' ) {
      $('.show#oldPassword').modal('toggle');
      $('#editForm').prepend($('<div>', {
        'class': 'alert alert-success mt-5',
        text: resultJSON.message
      }));
      $(window).scrollTop(0);
    } else if ( resultJSON.message == 'Incorrect Password') {
      $('#oldPassInput').before($('<div>', {
        'class': 'alert alert-danger',
        text: resultJSON.message
      }));
    } else {
      $('.show#oldPassword').modal('toggle');
      $('#editForm').prepend($('<div>', {
        'class': 'alert alert-danger mt-5',
        text: resultJSON.message
      }));
      $(window).scrollTop(0);
    }
    console.log(resultJSON);
  } catch (e) {
    console.error(e, result);
  }
}

function dbUpdateData(inputs, password) {
  return $.ajax({
    type: "post",
    url: "/cdr/public_html/bridge.php",
    data: {
      request: "edit_user",
      details: inputs,
      password: password
    }
  });
}