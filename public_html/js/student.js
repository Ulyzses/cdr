'use strict'

var active;
var scores = [];

var $scores = $("#scoresBody");

async function loadScores(subject) {
  const data = await dbLoadScores(subject);

  try {
    scores = JSON.parse(data);
    console.log(scores);
  } catch (e) {
    console.error(data);
    return
  }

  const filteredScores = scores.filter(score => score.class_code == subject); 

  // console.log(score.activity_code);

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

$(document).ready(() => {
  // Switch active class
  $('.subjects').on('click', 'li.subject:not(:last)', function() {
    $('.subjects').find('.active').removeClass('active');
    $(this).addClass('active');

    active = $(this).data('code');
    loadScores(active);
  })

  // Select first class
  $('li.subject:first-child').click();
});