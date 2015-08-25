<?php
$username=$_POST['username'];
$password=$_POST['password'];
$conn = mysql_connect("localhost","sb3webuser","USERPWD") or die("Failed to connect to database");
mysql_select_db("SubmissionBox3", $conn);
$query = "SELECT  STUDENTID,PASSWORD FROM Student WHERE PASSWORD =md5('$password') and STUDENTID='$username'";
//$query = "SELECT  STUDENTID,PASSWORD FROM Student WHERE PASSWORD ='$password' and STUDENTID='$username'";
$QueryResult = mysql_query($query)
     Or die("<error>Unable to execute the query " ."Error code " . mysql_errno($conn) ." : " . mysql_error($conn) . "</error></root>");
$num_rows = mysql_num_rows($QueryResult);
//echo $num_rows;
if($num_rows > 0){

// password match redirect to another page.
session_start();
// store session data
$_SESSION['username']=strtolower($username);
header("location: submit.php");
}else {
// redirect to login page if password not match.
echo $num_rows;
header("location: loginError.html");
}

?>
