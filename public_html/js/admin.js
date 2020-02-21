async function newQuery() {
  const query = $("#queryText").val();
  const password = $("#password").val();

  const passResult = await dbCheckPassword(password);

  if ( !passResult ) {
    alert("Incorrect password. Logging out for security purposes.");
    location.replace("/cdr/public_html/logout");
    return
  }
  
  if ( confirm("Do you wish to execute query?") ) {
    const queryResult = await dbRunQuery(query);
    console.log(queryResult);  
  } else {
    return
  }

}

function dbCheckPassword(password) {
  return $.ajax({
    type: "post",
    url: "/cdr/public_html/bridge.php",
    data: {
      request: 'check_password',
      password: password
    }
  });
}

function dbRunQuery(query) {
  return $.ajax({
    type: "post",
    url: "/cdr/public_html/bridge.php",
    data: {
      request: 'run_query',
      query: query
    }
  });
}

async function newAnnouncement() {
  const title = $("#announcementTitle").val();
  const message = $("#announcementMessage").val();

  const result = await dbNewAnnouncement(title, message);

  console.log(result);
}

function dbNewAnnouncement(title, message) {
  return $.ajax({
    type: "post",
    url: "/cdr/public_html/bridge.php",
    data: {
      request: 'create_announcement',
      details: {
        scope: 'current',
        classCode: 'global',
        title: title,
        message: message
      }
    }
  });
}

$(document).ready(() => {
  // Announcements listener
  $('#newAnnouncementForm').submit(e => {
    e.preventDefault();

    newAnnouncement();
  });

  // Query listener
  $('#newQueryForm').submit(e => {
    e.preventDefault();

    newQuery();
  });
});