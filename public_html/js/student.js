'use strict'

var active;
var filter = ['seatwork', 'homework', 'quiz', 'project'];
var scores = [];

var $title = $("#title")
var $scores = $("#scoresBody");

async function loadScores(subject = active, _filter = filter) {
  const data = await dbLoadScores(subject);

  try {
    scores = JSON.parse(data);
    console.log(scores);
  } catch (e) {
    console.error(data);
    return
  }

  if ( _filter.length == 4 ) {
    $title.text("All Activities");
  } else if ( _filter.length == 0 ) {
    $title.text("None Selected");
  } else {
    $title.text("Filtered Activities");
  }

  const filteredScores = scores.filter(score => {
    return score.class_code == subject &&
      _filter.includes(score.activity_type);
  });

  console.log(filteredScores);

  $scores.empty();
  
  if ( filteredScores.length == 0 ) {
    $("<tr>").append($("<td>", {
      "class": "col-12 text-center",
      text: "No activities for this subject yet"
    })).appendTo($scores);
  }

  filteredScores.forEach(item => {
    let $scoreRow = $("<tr>").appendTo($scores);

    $scoreRow
      .append($("<td>", {
        "class": "col-sm-4 col-5",
        text: item.activity_name
      }))
      .append($("<td>", {
        "class": "col-sm-4 col-4",
        text: item.activity_type
      }))
      .append($("<td>", {
        "class": "col-sm-2 col-3",
        text: `${item.score}/${item.max_score}`
      }))
      .append($("<td>", {
        "class": "col-sm-2 d-none d-sm-block",
        text: `${Math.round(100 * item.score / item.max_score)}%`
      }))
  });

}

function dbLoadScores(subject = active) {
  return $.ajax({
    type: "post",
    url: "student.php",
    data: {
      request: "get_scores",
      subjectCode: subject
    }
  });
}

async function joinClass(code) {
  let result = await dbJoinClass(code);

  console.log(result);
}

function dbJoinClass(code) {
  return $.ajax({
    type: "post",
    url: "/cdr/public_html/student/join/",
    data: {
      request: "join",
      subject: code
    }
  });
}

$(document).ready(() => {
  // Initialise the tooltip
  $('[data-tooltip="tooltip"]').tooltip({
    trigger: "hover"
  });

  // Switch active class
  $('.subjects').on('click', 'li.subject', function() {
    $('.subjects').find('.active').removeClass('active');
    $(this).addClass('active');

    active = $(this).data('code');
    loadScores();
  })

  // Join Class
  $('#joinClassForm').submit(e => {
    e.preventDefault();

    let classCode = $('#joinCode').val();

    joinClass(classCode);
  })

  // Select first class
  $('li.subject:first-child').click();

  // Filter checkbox toggle
  $('.filter-label').click(function() {
    $(this).children(":first").toggleClass('alert-dark alert-light');
  });

  // Filter
  $('#filterForm').submit(e => {
    e.preventDefault();

    filter = $('.filter-checkbox:checkbox:checked').map(function() {
      return $(this).val();
    }).get();

    loadScores(active);

    $("#filter").modal('toggle');
  });
});