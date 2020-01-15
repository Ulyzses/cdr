var active = "";

$(document).ready(function() {
  $('[data-tooltip="tooltip"]').tooltip({
    trigger : 'hover'
  });

  $('.classes').on('click', 'li.kurasu', function() {
    $('.classes').find('.active').removeClass('active');
    $(this).addClass('active');
    active = $(this).data('code');

    loadSheet(active);
  });

  $('#newActivityForm').submit(e => {
    e.preventDefault();

    let details = {
      code: active,
      name: $('#activity_name').val(),
      type: $('#activity_type').val(),
      score: $('#activity_score').val()
    };

    $.ajax({
      type: "post",
      url: "teacher.php",
      data: {
        request: "new_activity",
        details: details
      },
      dataType: "text",
      success: (data, status, xhr) => {
        console.log(data);
      },
      error: (xhr, status, err) => {
        alert(err);
      }
    });
  });
});

async function loadSheet(subject) {
  data = await getClassData(subject);

  try {
    ({ students, activities, outputs } = JSON.parse(data));
  } catch(err) {
    console.error(data);
    return;
  }

  console.log(outputs);
}

function getClassData(subject) {
  return $.ajax({
    type: "post",
    url: "teacher.php",
    data: {
      request: "load_class",
      subject: subject
    }
  });
}