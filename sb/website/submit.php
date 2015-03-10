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
<style type="text/css">
html, body, h1, h2, h3, h4, h5, h6, p, ol, ul, li, pre, code, address, variable, form, fieldset, blockquote {
 padding: 0;
 margin: 0;
 font-size: 100%;
 font-weight: normal;
}
table { border-collapse: collapse; border-spacing: 0; }
td, th, caption { font-weight: normal; text-align: left; }
img, fieldset { border: 0; }
ol { padding-left: 1.4em; list-style: decimal; }
ul { padding-left: 1.4em; list-style:square; }
q:before, q:after { content:''; }

body {
  color: #002D4B;
  font-family: Arial, Helvetica, sans-serif;
	font-size: 62.5%;
  background: #E1EEFD url(images/bg_body.png) repeat-x;
}

#main {
  width: 740px;
  height:800px ;
  margin: 0 auto;
  padding: 0 10px;
  border: 4px solid white;
  background: transparent url(images/sb_banner.jpg) no-repeat;
}

#main h1 {
  color: #FF6600;
	font-family: "Arial Black", Arial, Helvetica, sans-serif;
	font-size: 4em;
}

#main h1 strong {
  font-size: 150px;
  color: white;
  line-height: 1em;
  margin-right: -1.25em;
}


#main h2 {
	font: bold 3.5em "Hoefler Text", Garamond, Times, serif;
	border-bottom: 1px solid #002D4B;
	margin-top: 150px;
}

#main h3 {
	color: #F60;
	font-size: 1.9em;
	font-weight: bold;
	//text-transform: uppercase;
	margin-top: 25px;
	margin-bottom: 10px;
}

#main p {
	font-size: 1.5em;
	line-height: 150%;
	margin-left: 150px;
	margin-right: 50px;
	margin-bottom: 10px;
}

#main form {
font-size: 1.5em;
	line-height: 150%;
	margin-left: 150px;
	margin-right: 50px;
	margin-bottom: 10px;

}

#main p:first-line {
  font-weight: bold;
  color: #999;
}

#main ul {
	margin: 50px 0 25px 50px;
	width: 150px;
	float: right;
}
#main li {
	color: #207EBF;
	font-size: 1.5em;
	margin-bottom: 7px;
}

#main .byline {
  color: #999999;
	font-size: 1.6em;
	margin: 5px 0 25px 50px;
}

#main .byline strong {
	color: #207EBF;
	text-transform: uppercase;
	margin-left: 11px;
} 

</style>

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
//alert( XMLHttpRequestObject.responseText  );

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

function validateForm()
{
//To make sure that the form is completely filled
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
$con = mysql_connect("localhost","sbuser","sbpasswd") or die("Failed to connect to database");
mysql_select_db("SubmissionBox", $con);

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
<input type="submit" name="submit" value="Submit" style="width: 61px; height: 21px">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <br>

</form>
<?php
// JDT added to popular assignments list immediately when only one course
if ($numcourses == 1) {
  echo "<script>getData('assignments.php?courseID=' ,  'courseID'  );</script>";
}

if ($_GET['error'] !== "" )
echo "<p>" .$_GET['error'].  " <p>" ;
?>
<p id="error" > <p>
</div>

</body>
</html>
