'use strict'

$(document).ready(() => {
  $("#logout").click(() => {
    if ( confirm("Do you wish to log out?") ) {
      location.replace("/cdr/public_html/logout");
    }
  });

  $('#notification-drop').click(e => {
    e.preventDefault();

    displayNotifications();
  })

  $('#read-notifications').click(e => {
    e.preventDefault();

    readAllNotifications();
  })

  $notifContainer.on('click', '.notification', function(e) {
    readNotification($(this).data('id'));
  });
});

var $notifContainer = $(".notifications");
var notifications;

async function displayNotifications() {
  let result = await dbGetNotifications();

  try {
    notifications = JSON.parse(result);
  } catch(err) {
    console.log(result);
    return;
  }

  if ( notifications.length == 0 ) return

  $notifContainer.empty();

  notifications.forEach(notification => {
    let $notifAnchor = $('<a>', {
      href: notification['link']
    }).appendTo($notifContainer);

    let $notification = ($('<li>', {
      "class": "notification",
      "data-id": notification['id']
    })).appendTo($notifAnchor);

    $notification.append($('<h4>', {
      "class": "notify-text notify-body",
      text: notification['message']
    })).append($('<h5>', {
      "class": "notify-text notify-time",
      text: `${timeAgo(notification['time'])} ago`
    }));

    if ( !Number(notification['status']) ) {
      $notification.addClass("unread");
    }
  });
}

function dbGetNotifications() {
  return $.ajax({
    type: "post",
    url: "/cdr/public_html/bridge.php",
    data: {
      request: "get_notifications"
    }
  });
}

async function readNotification(id) {
  let result = await dbReadNotification(id);

  console.log(result);
}

function dbReadNotification(id) {
  return $.ajax({
    type: "post",
    url: "/cdr/public_html/bridge.php",
    data: {
      request: "read_notification",
      id: id
    }
  });
}

async function readAllNotifications() {
  let result = await dbReadAllNotifications();

  console.log(result);
}

function dbReadAllNotifications() {
  return $.ajax({
    type: "post",
    url: "/cdr/public_html/bridge.php",
    data: {
      request: "read_all"
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