$(document).ready(function() {
  $('[data-tooltip="tooltip"]').tooltip({
    trigger : 'hover'
  });

  $('.classes').on('click', 'li.kurasu', function() {
    $('.classes').find('.active').removeClass('active');
    $(this).addClass('active');
  });

  $('#newActivityForm').submit(e => {
    e.preventDefault();

    let details = [
      $('#activity_name').val(),
      $('#activity_type').val(),
      $('#activity_score').val()
    ]

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
        console.log(err);
      }
    });
  });
});