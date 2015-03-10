<?php
//This script is requested, using Ajax object, as response to onchange event triggered by 
//'courseID' select element in submit.php 
//to retrieve all the homework(s)  according to the selected  course
//and to pass back a call to processData function 
//which is executed by eval function in submit.php to populate the second select element


$selectedCourse =  $_GET['courseID'];
//Establish the database connection
$con = mysql_connect("localhost","sbuser","sbpasswd") or die("Failed to connect to database");
mysql_select_db("SubmissionBox", $con);

//Select all the homework(s)  according to the selected  course in which the student has been enrolled
$sql_command = "select  * from Assignment where CourseID = '$selectedCourse';";
$res = mysql_query($sql_command);

//The While loop will produce a semi-colon delimited list of assignments 
//$options is an array for the assignment Names that will be  options in second select element in submit.php
//$values is an array for the assignment IDs that will be  values of the options in second select element in submit.php 

$options = " new Array(" ; 
$values = " new Array(" ; 
while($row = mysql_fetch_array($res))
{
$values .= "'" . $row[0] . "'," ;
$options .= "'" . $row[2] . "'," ;
}
$options = rtrim($options, ",");
$values = rtrim($values , ",");
$options .= ")" ;
$values .= ")" ;

//This script passes a function call to submit.php
echo "processData( $options, $values )";
?>