'use strict'

async function uploadPic(form) {
  let data = new FormData(form);

  let result = await dbUploadForm(data);

  console.log(result);
  updateImg($("#displayImg")[0]);
}

function dbUploadForm(data) {
  console.log(data.get("file"));

  return $.ajax({
    type: "post",
    url: "upload.php",
    data: data,
    contentType: false,
    processData: false,
  });
}

function updateImg(img) {
  let src = img.src;
  src.replace(/\?.*/g, "");
  src += "?" + new Date().getTime()
  img.src = src;
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