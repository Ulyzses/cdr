/**
 *  CODE OVERVIEW
 * 
 *  MAIN FEATURES
 *  > switch subjects
 *  > view grades
 *  > add activities
 * 
 *  document.ready
 *  > initialise tooltip
 *  > initialise sidebar clicking
 *  > new activity form submission
 * 
 *  GLOBAL VARIABLES
 *    active - class code of the current selected class
 *    
 *    activities - array of activity objects
 *    students - array of student objects
 *    outputs - array of output objects
 * 
 *    $table - the table as a jQuery object
 *    $thead - the thead as a jQuery object
 *    $tbody - the tbody as a jQuery object
 * 
 *    $addButton - add button as a jQuery object
 */

/* DECLARE GLOBAL VARIABLES */

'use strict'

// Current active class code
var active = "";

// Current active class details
var activities = [];
var students = [];
var outputs = [];

// Table elements
var $table = $("<table>", {
  "class": "table table-sm table-bordered table-striped table-responsive text-center"
})

var $thead = $("<thead>").appendTo($table);
var $tbody = $("<tbody>").appendTo($table);

var $headRow = $("<tr>").appendTo($thead);
var $rows;

// var $addButton = $("<button>", {
//   type: "button",
//   "class": "add-button",
//   "data-toggle": "modal",
//   "data-target": "#addActivity",
//   text: "Add Activity"
// });

/* FUNCTIONS */

// Get the corresponding output object
function getOutput(student, activity) {
  const out = outputs.find(o => {
    return o.activity_code == activity
      && o.student_code == student
  });

  return ( out ) ? out : false;
}

// Load the sheet of a newly selected class
async function loadSheet(code) {
  // Get class data
  let data = await dbGetClass(code);

  try {
    ({ activities, students, outputs } = JSON.parse(data));
  } catch (e) {
    console.error(data);
    return
  }

  // Initial table
  $('.table-div').empty().append($table);

  loadHeaders();
  loadBody();
}

// Get the information of the newly selected class
// from the database
function dbGetClass(code) {
  return $.ajax({
    type: "post",
    url: "teacher.php",
    data: {
      request: "load_class",
      classCode: code
    }
  });
}

function loadHeaders(filter = "all") {
  $headRow
    .empty()  
    .append($("<th>", {text: "Students"}));

  let filteredActs = ( filter == "all" ) ? activities : filterActivities(filter);

  filteredActs.forEach(activity => {
    $headRow.append($("<th>", {
      text: `${activity.activity_name} (${activity.max_score})`,
      "data-code": activity.activity_code
    }));
  });
}

function loadBody(filter = "all") {
  $tbody.empty();
  $rows = [];

  students.forEach(student => {
    let studentCode = student.user_code;

    let $studentRow = $("<tr>", {
      "data-code": studentCode
    })
      .appendTo($tbody) 
      .append($("<td>", {
        text: `${student.user_last_name.toUpperCase()}, ${student.user_first_name}` 
      }));

    $rows.push($studentRow);

    let filteredActs = ( filter == "all" ) ? activities : filterActivities(filter);

    filteredActs.forEach(activity => {
      let activityCode = activity.activity_code;
      let out = getOutput(studentCode, activityCode);

      $studentRow.append($("<td>", {
        "class": "cell",
        contenteditable: true,
        text: ( out ) ? out.score : "",
        focusout: function() {
          updateScore($(this), studentCode, activityCode);
        }
      }));
    });
  })
}

function addColumn(activity) {
  $headRow.append($("<th>", {
    text: `${activity.activity_name} (${activity.max_score})`,
    "data-code": activity.activity_code
  }));

  $rows.forEach(row => {
    row.append($("<td>", {
      "class": "cell",
      contenteditable: true,
      focusout: function() {
        updateScore($(this), row.data('code'), activity.activity_code);
      }
    }));
  });
}

function filterActivities(filter) {
  return activities.filter(activity => activity.activity_type == filter);
}

async function newActivity() {
  const name = $('#activity_name').val();
  const type = $('#activity_type').val();
  const score = Number($('#activity_score').val());

  const result = await dbNewActivity(name, type, score);

  try {
    let newActivity = JSON.parse(result);
    activities.push(newActivity);
    addColumn(newActivity);
    $('#addActivity').modal('hide');
  } catch (e) {
    console.error(result);
  }
}

function dbNewActivity(name, type, score) {
  return $.ajax({
    type: "post",
    url: "teacher.php",
    data: {
      request: 'add_activity',
      details: {
        classCode: active,
        name: name,
        type: type,
        score: score
      }
    }
  });
}

async function newAnnouncement() {
  const scope = $("#announcement_scope").val();
  const title = $("#announcement_title").val();
  const message = $("#announcement").val();

  const result = await dbNewAnnouncement(scope, title, message);

  $('#newAnnouncement').modal('hide');

  console.log(result);
}

function dbNewAnnouncement(scope, title, message) {
  return $.ajax({
    type: "post",
    url: "/cdr/public_html/bridge.php",
    data: {
      request: 'create_announcement',
      details: {
        classCode: active,
        scope: scope,
        title: title,
        message: message
      }
    }
  });
}

function moveFocus(curr, key) {
  let x = curr.index();
  let y = curr.parent()[0].rowIndex - 1;

  switch (key) {
    case 37: // left
      $rows[y].children()[x - 1].focus();
      break;
    case 38: // up
      $rows[y - 1].children()[x].focus();      
      break;
    case 39: // right
      $rows[y].children()[x + 1].focus();
      break;
    case 40: // down
      $rows[y + 1].children()[x].focus();   
      break;
    default:
      return;
  }
}

// Fires whenever a cell is focused out and updates
// scores if there are any changes
async function updateScore(cell, studentCode, activityCode) {
  let output = getOutput(studentCode, activityCode);
  let result;

  if ( output ) {
    // Check for changes in the score
    if ( output.score == cell.text() ) return

    // Check for invalid inputs
    if ( isNaN(cell.text()) ) {
      console.error("Invalid input");
      return
    }

    // Delete object if score is now deleted
    if ( cell.text() == "" ) {
      outputs.splice(outputs.indexOf(output), 1);
      result = await dbUpdateScore("delete_output", output);
    } else {
      output.score = Number(cell.text());
      result = await dbUpdateScore("modify_output", output);
    }
  } else {
    // Check if a score is added
    if ( cell.text() == "" ) return

    // Check if score is valid and create a new output object
    if ( isNaN(cell.text()) ) {
      console.error("Invalid input");
      cell.text("");
      return;
    } else {
      // Create a new object if score is valid and insert into database
      output = {
        student_code: studentCode,
        activity_code: activityCode,
        score: Number(cell.text())
      }

      outputs.push(output);
      result = await dbUpdateScore("add_output", output);
    }
  }

  console.log(result);
}

function dbUpdateScore(request, output) {
  return $.ajax({
    type: "post",
    url: "teacher.php",
    data: {
      request: request,
      output: output
    }
  });
}

/* EVENT LISTENERS */

$(document).ready(() => {
  // Initialise the tooltip
  $('[data-tooltip="tooltip"]').tooltip({
    trigger: "hover"
  });

  // Switch active class when clicking on sidebar
  $('.classes').on('click', 'li.kurasu', function() {
    $('.classes').find('.active').removeClass('active');
    $(this).addClass('active');
    
    active = $(this).data('code');
    loadSheet(active);
  });

  // New activity listener
  $('#newActivityForm').submit(e => {
    e.preventDefault();

    newActivity();
  })

  // Announcements listener
  $('#newAnnouncementForm').submit(e => {
    e.preventDefault();

    newAnnouncement();
  })

  // Modal open listener
  $(document).on('shown.bs.modal', function () {
    // console.log($(this));
    $(this).find("input:visible:first").focus();
  });

  // Switch tabs
  $(document).on('click', '.activity-types', function() {
    let filter = $(this).find('.nav-link.active').val();
    loadHeaders(filter);
    loadBody(filter);
  });

  // Arrow key presses
  $tbody.on('keydown', '.cell', function(e) {
    if ( e.which <= 40 && e.which >= 37 ) {
      e.preventDefault();
      moveFocus($(this), e.which);
    }
  });
});