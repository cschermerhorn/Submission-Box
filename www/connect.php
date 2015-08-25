<?php
$connection = mysql_connect('localhost', 'sb3webuser', 'USERPWD');
if (!$connection){
    die("Database Connection Failed" . mysql_error());
}
$select_db = mysql_select_db('SubmissionBox3');
if (!$select_db){
    die("Database Selection Failed" . mysql_error());
}
