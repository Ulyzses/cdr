'use strict'

async function uploadPic(form) {
  let data = new FormData(form);

  let result = await dbUploadForm(data);

  console.log(result);
  updateImg();
}

function dbUploadForm(data) {
  return $.ajax({
    type: "post",
    url: "upload.php",
    data: data,
    contentType: false,
    processData: false,
  });
}

async function updateImg() {
  let result = await dbUpdateImg();
  let src = `../img/profile/${result}?${new Date().getTime()}`;

  $('#displayImg').attr('src', src);
}

function dbUpdateImg() {
  return $.ajax({
    type: "post",
    url: "upload.php",
    data: {
      request: "get_display"
    }
  });
}

$(document).ready(e => {
  $("#displayImg").click( function(e) {
    $("#file").click();
  })

  $("#file").change(function () {
    $("#uploadPicForm").submit();
  });

  $("#uploadPicForm").submit( function(e) {
    e.preventDefault();

    uploadPic($(this)[0]);
  });
});