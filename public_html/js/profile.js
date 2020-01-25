async function uploadPic(form) {
  data = new FormData(form);
  console.log(2);

  result = await dbUploadForm(data);
  console.log(3);

  console.log(result);
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

$(document).ready(e => {
  $("#uploadPicForm").submit( function(e) {
    e.preventDefault();

    uploadPic($(this)[0]);
  });
});