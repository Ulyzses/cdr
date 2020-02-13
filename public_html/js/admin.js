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
  })
});