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

html
{
  background: url(images/high_speed_tunnel.jpg) no-repeat center center fixed;
  /*background: #eff3f6;*/
  -webkit-background-size: cover;
  -moz-background-size: cover;
  -o-background-size: cover;
  background-size: cover;

  /*filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='.myBackground.jpg', sizingMethod='scale');
  -ms-filter: "progid:DXImageTransform.Microsoft.AlphaImageLoader(src='myBackground.jpg', sizingMethod='scale')";*/
}

#header
{
    position:fixed;
	left:0;
	top:40px;
	text-align:center;
	color: white;
	width:100%;

	-webkit-font-smoothing: antialiased;
	font-family: 'ks-book','ks-medium','HelveticaNeue-Light','HelveticaNeue',arial;
	font-weight: lighter;
	text-shadow: 0 2px 4px rgba(0,0,0,.2);
	color: white;
	font-weight: bold;
	font-size: 45px;
	text-align: center;
	line-height: 1.2;
}

#header_desc
{
    position:fixed;
	left:0;
	top:110px;
	text-align:center;
	color:#fff;
	width:100%;

	-webkit-font-smoothing: antialiased;
	font-family: 'ks-book','ks-medium','HelveticaNeue-Light','HelveticaNeue',arial;
	font-weight: lighter;
	text-shadow: 0 2px 4px rgba(0,0,0,.2);
	color: #fff;
	font-weight: bold;
	font-size: 25px;
	text-align: center;
	line-height: 1.2;
}

input{

    -webkit-border-radius:3px;
    -moz-border-radius:3px;
    border-radius:3px;
    -moz-box-shadow:2px 3px 3px rgba(0, 0, 0, 0.06) inset, 0 0 1px #95a2a7 inset;
    -webkit-box-shadow:2px 3px 3px rgba(0, 0, 0, 0.06) inset, 0 0 1px #95a2a7 inset;
    box-shadow:2px 3px 3px rgba(0, 0, 0, 0.06) inset, 0 0 1px #95a2a7 inset;
    display:block;
}

.input{

    /*border:6px solid #F7F9FA;
    -webkit-border-radius:3px;
    -moz-border-radius:3px;
    border-radius:3px;*/
    -moz-box-shadow:2px 3px 3px rgba(0, 0, 0, 0.06) inset, 0 0 1px #95a2a7 inset;
    -webkit-box-shadow:2px 3px 3px rgba(0, 0, 0, 0.06) inset, 0 0 1px #95a2a7 inset;
    box-shadow:2px 3px 3px rgba(0, 0, 0, 0.06) inset, 0 0 1px #95a2a7 inset;
    /*margin:3px 0 4px;*/
    /*padding:8px 6px;*/
    /*width:270px;*/
    /*display:block;*/
    border:6px solid #f0f7fc;
    -moz-box-shadow:2px 3px 3px rgba(0, 0, 0, 0.04) inset, 0 0 1px #0d6db6 inset;
    -webkit-box-shadow:2px 3px 3px rgba(0, 0, 0, 0.04) inset, 0 0 1px #0d6db6 inset;
    box-shadow:2px 3px 3px rgba(0, 0, 0, 0.04) inset, 0 0 1px #0d6db6 inset;
	color:#333;;
	padding:3px 2px 3px 6px;
    width:100px;
    border-width:3px !important;


}




.btn{
	-moz-border-radius:2px;
    -webkit-border-radius:2px;
    border-radius:15px;
    background:#a1d8f0;
    background:-moz-linear-gradient(top, #badff3, #7acbed);
    background:-webkit-gradient(linear, left top, left bottom, from(#badff3), to(#7acbed));
	-ms-filter: "progid:DXImageTransform.Microsoft.gradient(startColorStr='#badff3', EndColorStr='#7acbed')";
    border:1px solid #7db0cc !important;
    cursor: pointer;
    padding:11px 16px;
    font:bold 11px/14px Verdana, Tahomma, Geneva;
    text-shadow:rgba(0,0,0,0.2) 0 1px 0px;
    color:#fff;
    -moz-box-shadow:inset rgba(255,255,255,0.6) 0 1px 1px, rgba(0,0,0,0.1) 0 1px 1px;
    -webkit-box-shadow:inset rgba(255,255,255,0.6) 0 1px 1px, rgba(0,0,0,0.1) 0 1px 1px;
    box-shadow:inset rgba(255,255,255,0.6) 0 1px 1px, rgba(0,0,0,0.1) 0 1px 1px;
    margin-left:12px;
    float:right;
	padding:7px 21px;

}

.btn:hover,
.btn:focus,
.btn:active{
    background:#a1d8f0;
    background:-moz-linear-gradient(top, #7acbed, #badff3);
    background:-webkit-gradient(linear, left top, left bottom, from(#7acbed), to(#badff3));
	-ms-filter: "progid:DXImageTransform.Microsoft.gradient(startColorStr='#7acbed', EndColorStr='#badff3')";
}
.btn:active
{
    text-shadow:rgba(0,0,0,0.3) 0 -1px 0px;
}




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

}

.box_footer{
	background:#eff4f6;
    border-top:1px solid #fff;
    padding:22px 26px;
    overflow:hidden;
	height:32px;

	}


#main {
	background:#fefefe;
    border: 1px solid #C3D4DB;
    border-top:1px solid #dde0e8;
	/*border-top:1px;*/
    -webkit-border-radius:5px;
    -moz-border-radius:5px;
    border-radius:5px;
    -moz-box-shadow:rgba(0,0,0,0.15) 0 0 1px;
    -webkit-box-shadow:rgba(0,0,0,0.15) 0 0 1px;
    box-shadow:rgba(0,0,0,0.15) 0 0 1px;
    color:#5C8BAC;
    /*font:normal 12px/14px Arial, Helvetica, Sans-serif;*/
    margin:0 auto 30px;
	overflow:hidden;
    width:450px;
	position:absolute;
	left:45%;
	top:50%;
	margin:-130px 0 0 -166px;
  /*width: 740px;
  height:800px ;
  margin: 0 auto;
  padding: 0 10px;
  background-color:white;
  border: 4px solid white;
  background: transparent url(images/sb_banner.jpg) no-repeat;*/
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
	font: bold 1em "Hoefler Text", Garamond, Times, serif;
	border-bottom: 1px solid #002D4B;
}

#main h3 {
	color: #166BA5;
	font-size: 1.5em;
	font-weight: bold;
	//text-transform: uppercase;
	margin-top: 10px;
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

.auto-style1 {
	color: #166BA5;
}

    #submissions-table {
        width: 100%;
        border: 1px;
        color: #333333;
    }
    #submissions-table th {
        font-weight: bold;
    }

</style><script>
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
$con = mysql_connect("localhost","root","letsgosb3") or die("Failed to connect to database");
mysql_select_db("test", $con);

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
#if ($_GET['error'] !== "" )
#echo "<p>" .$_GET['error'].  " <p>"
?>
<p id="error" > <p>
<?php

$sql="SELECT a.name,DATE_FORMAT(a.duedate, '%m/%d/%Y %H:%i') as duedate,DATE_FORMAT(s.SubmissionDate, '%m/%d/%Y %H:%i') as submissiondate,s.grade, s.comments
FROM test.assignment a
join test.submission s on a.AssignmentID = s.AssignmentID and s.StudentId = '$studentID'";
$result= mysql_query($sql);
if(mysql_num_rows($result)>0) {
    echo "<h3>Previous Submissions</h3>";
    echo "<table id=\"submissions-table\" border=\"1\"><tr><th>Assignment Name</th><th>Due Date</th><th>Submission Date</th><th>Grade</th><th>Comment</th></tr>";
    // out put data for each row
    while ($row = mysql_fetch_assoc($result))
    {
        echo "<tr><td>" . $row["name"] . "</td><td>" . $row["duedate"] . "</td><td>". $row["submissiondate"]
            . "</td><td>" . $row["grade"] . "</td><td>" . $row["comments"] . "</td></tr>";

    }
    echo "</table>";
}else
{
    echo "No submissions yet!";
}

?>
</div>
</body>
</html>
