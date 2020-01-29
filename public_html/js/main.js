'use strict'

$(document).ready(() => {
  $("#logout").click(() => {
    if ( confirm("Do you wish to log out?") ) {
      location.replace("/cdr/public_html/logout");
    }
  });

  $('body').on('click', '.customAlert', removeAlert);

  $('#notification-drop').click(e => {
    e.preventDefault();

    displayNotifications();
  })
});

var $notifContainer = $(".notifications");
var notifications;

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

async function displayNotifications() {
  $notifContainer.empty();

  let result = await getNotifications();

  try {
    notifications = JSON.parse(result);
  } catch(err) {
    console.log(result);
    return;
  }

  notifications.forEach(notification => {
    let $notifAnchor = $('<a>', {
      href: notification['link']
    }).appendTo($notifContainer);

    let $notification = ($('<li>', {
      "class": "notification"
    })).appendTo($notifAnchor);

    $notification.append($('<h4>', {
      "class": "notify-text notify-body",
      text: notification['message']
    })).append($('<h5>', {
      "class": "notify-text notify-time",
      text: `${timeAgo(notification['time'])} ago`
    }));
  });
}

function getNotifications() {
  return $.ajax({
    type: "post",
    url: "/cdr/public_html/bridge.php",
    data: {
      request: "get_notifications"
    }
  });
}

function timeAgo(timestamp) {
  let curr = Math.round(new Date().getTime()/1000);

  let diff = curr - timestamp;
  let timeAgo = 0;
  let temp;

  let min = 60;
  let hour = min * 60;
  let day = hour * 24;
  let week = day * 7;

  if ( diff < min ) {
    temp = diff;
    timeAgo = `${temp} second`;
  } else if ( diff < hour ) {
    temp = Math.round(diff / min);
    timeAgo = `${temp} mintute`;
  } else if ( diff < day ) {
    temp = Math.round(diff/hour)
    timeAgo = `${temp} hour`;
  } else if ( diff < week ) {
    temp = Math.round(diff/day)
    timeAgo = `${temp} day`;
  } else {
    temp = Math.round(diff/week)
    timeAgo = `${temp} week`;
  }

  if ( temp > 1 ) timeAgo += "s";

  return timeAgo;
}