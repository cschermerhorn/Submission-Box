<?php
/*
This script implements the submission form. There are two lists. The first one is  populated dynamically with the student's registered course(s).
The other one shows the assignments related to the selected course. This means the second list will not be populated until a course selection is made.
The form is submitted to mkdir.php via post method.
*/
session_start();
if(!isset($_SESSION['username'])){
  header("location: Authentication.html");
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <title>Submission Box</title>

  <!--Move css to separate folder.  Cleans up code and cuts back on
  significant amount of code here-->
  <link rel="stylesheet" type="text/css" href="css/submit.css">
  <script>
  function getData(dataSource,  courseID )
  {

    var XMLHttpRequestObject = false;
    if (window.XMLHttpRequest)
    {
      XMLHttpRequestObject = new XMLHttpRequest();
      if (XMLHttpRequestObject.overrideMimeType)
      XMLHttpRequestObject.overrideMimeType("text/xml");
    }
    else if (window.ActiveXObject)
    {
      XMLHttpRequestObject = new ActiveXObject("Microsoft.XMLHTTP");
    }
    if (XMLHttpRequestObject)
    {

      XMLHttpRequestObject.open("GET", dataSource + document.getElementById(courseID ).value );
      XMLHttpRequestObject.onreadystatechange =
      function()
      {
        if (XMLHttpRequestObject.readyState == 4 &&
          XMLHttpRequestObject.status == 200)
          {
            eval(XMLHttpRequestObject.responseText);
          }
        }
        XMLHttpRequestObject.send(null);
      }
    }

    //Dynamically populate the dropdown list with the retrieved assignments
    //the SQL statment in assignment.php
    function processData(data, values )
    {
      var targetSelect = document.getElementById("targetSelect");
      options_array = data.unique();
      values_array = values.unique();
      str = "";
      for (i = 0; i < options_array.length; i++)
      {
        str += "<option value=\"" + values_array[i] +  "\">" + options_array[i] + "</option>";
      }
      targetSelect.innerHTML = "<option value=\"" + "0" +  "\">" + "Select an Assignment" + "</option>" + str;
    }

    // From http://www.jslab.dk/library/ JavaScript Standard Library
    // Return new array with duplicate values removed
    Array.prototype.unique =
    function()
    {
      var a = [];
      var l = this.length;
      for (var i=0; i<l; i++)
      {
        for (var j=i+1; j<l; j++)
        {
          // If this[i] is found later in the array
          if (this[i] === this[j])
          j = ++i;
        }
        a.push(this[i]);
      }
      return a;
    };

    /*
      This function will validate that all fields are selected prior to
      submitting a file.  Error if not all complete.
    */
    function validateForm()
    {
      var course =document.forms["submitForm"]["courseName"].value;
      var assignment =document.forms["submitForm"]["assignmentID"].value;
      var userfile =document.forms["submitForm"]["userfile"].value;
      var error = document.getElementById("error") ;

      if (course  == 0 || assignment == 0 || userfile == '' )
      {
        alert("Please fill out the form" );
        error.innerHTML= "Please fill out the form" ;
        return false;
      }
    }
    </script>
  </head>
  <body>
    <div id="main">
      <?php
        echo "<h2>" .$_SESSION['username'] . " assignment submission <div style='float:right;font-size:15px;margin-top:20px'><a href='logout.php'>Logout<a></div></h2>" ;
      ?>
      <h3>Select the assignment to be submitted</h3>
      <form name="submitForm" action="mkdir.php" onsubmit="return validateForm()"  method="post" enctype="multipart/form-data">
        <select name="courseName" id="courseID"  onchange="getData('assignments.php?courseID=' ,  'courseID'  )" >
          <?php
            $studentID = $_SESSION['username'] ;

            //Establish the database connection
            $con = mysql_connect("localhost","sb3webuser","USERPWD") or die("Failed to connect to database");
            mysql_select_db("SubmissionBox3", $con);

            //Select all the courses in which the student has been enrolled
            $sql_command = "select CourseID from Enrollment where StudentID = '$studentID';";
            $res = mysql_query($sql_command);

            // JDT added 1/12/14 to add "Select a course" only if there's more than one course for this student
            $numcourses = mysql_num_rows($res);
            if ($numcourses > 1) {
              echo '<option value="0">Select a Course</option>';
            }

            //Dynamically populate the dropdown list with the courses
            while($row = mysql_fetch_array($res))
            {
              echo "<option value=\"" . $row[0] . "\">" . $row[0] . "</option>";
            }
          ?>
        </select><br />
        <br />
        <select id="targetSelect" name="assignmentID">
          <option value="0">Select an Assignment</option>
        </select>
      </br></br>
      <input type="file" name="userfile" id="userfile">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
      <br /><br />
      <input type="submit" class="btnLogin" name="submit" value="Submit" style="width: 61px; height: 21px">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <br>
    </form>
    <?php
      // JDT added to populate assignments list immediately when only one course
      if ($numcourses == 1) {
        echo "<script>getData('assignments.php?courseID=' ,  'courseID'  );</script>";
      }
      if ($_GET['error'] !== "" )
      echo "<p id=\"error\">" .$_GET['error'].  " <p>" ;
      ?>
      <p id="error" > <p>
    <p>
      <?php

        /*
          SB3 addition.  Output a submission table for past submissions.  Pull
          data from the database and join with submissions that the student has
          uploaded.

          Will only show most current submission, and not multiple submissions.
        */

        //Build sql to pull data from database
        $sql="SELECT a.courseid,a.name,DATE_FORMAT(a.duedate, '%m/%d/%Y %H:%i') as duedate,DATE_FORMAT(s.SubmissionDate, '%m/%d/%Y %H:%i') as submissiondate,s.grade, s.comments
        FROM Assignment a
        join Submission s on a.AssignmentID = s.AssignmentID and s.StudentId = '$studentID'";
	echo "<!-- SQL: ".$sql." -->\n";
        $result= mysql_query($sql);

        //If there is data to print in the table, format it here
        if($result && mysql_num_rows($result)>0)
        {
          echo "<h3>Previous Submissions</h3>";
          echo "<table id=\"submissions-table\" border=\"1\"><tr><th>Course</th><th>Assignment Name</th><th>Due Date</th><th>Submission Date</th><th>Grade</th><th>Comment</th></tr>";
          // output data for each row
          while ($row = mysql_fetch_assoc($result))
          {
            echo "<tr><td>" . $row["courseid"] . "</td><td>" . $row["name"] . "</td><td>" . $row["duedate"] . "</td><td>". $row["submissiondate"]
            . "</td><td>" . $row["grade"] . "</td><td>" . $row["comments"] . "</td></tr>";

          }
          echo "</table>";
        }

        //Else student has no submission, show that here
        //This will likely only be shown once, unless a student's submission
        //history is erased
        else
        {
          echo "No submissions yet!";
        }
      ?>
    </div>
  </body>
</html>
