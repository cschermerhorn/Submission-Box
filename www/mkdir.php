<?php
error_reporting(E_ALL^ E_DEPRECATED);
/*
This script is an action value of the submission form in submit.php.
This script builds the submission path. For example  ./sb/courses/csc501/Assignment1/John
For this to work correctly,before runing this script the ..... script must be run to create particular course directory,csc501 for example, owned by apache with rewritable  permission
Note:./sb/courses/ is statically created with rewritable permission

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
  <link rel="stylesheet" type="text/css" href="css/mkdir.css">

</head>

<body>

  <div id="main">
    <?php

    echo "<h2>Hello " .$_SESSION['username'] . "<div style='float:right;font-size:15px;margin-top:21px'><a href='logout.php'>Logout<a></div></h2>" ;
    ?>

    <h3>Confirmation</h3>

    <?php

    # from http://stackoverflow.com/questions/834303/php-startswith-and-endswith-functions
    function endsWith($haystack, $needle)
    {
      return $needle === "" || substr($haystack, -strlen($needle)) === $needle;
    }

    # new function to report an error and wrap up the HTML properly, then die
    function error_and_die($msg) {
      echo ('<p><span style="color:#ff0000; font-size:1.5em;">SubmissionBox Error Encountered</span></p>');
      echo ('<p><span style="color:#ff0000;">Message: ' . $msg . '</span></p></body></html>');
      die;
    }

    //database connection
    $con = mysql_connect("localhost","root","letsgosb3") or error_and_die("Failed to connect to database");
    mysql_select_db("test", $con);

    //Name field is the assignment name. It is used for appropriate path building.
    //gFlag is selected to check if the assignment is set to be automatically executed
    $sql_command = "select AssignmentId, Name , gFlag, FileTypes, DueDate from Assignment where AssignmentID = " . $_POST['assignmentID'] . ";";
    $res = mysql_query($sql_command);
    $row = mysql_fetch_array($res);

    $course = $_POST['courseName'];
    $student = $_SESSION['username'] ;
    $assignmentId = $row[0];
    $assignment = $row[1];
    $isGraded = $row[2];
    $allowedFileTypesStr = $row[3];
    $dueDate = strtotime($row[4]);

    $iCopy = 0 ;

    // JDT added 1/11/14
    if (strlen($course) == 0) error_and_die ("Course not set properly!  Please try again or contact your instructor.");
    if (strlen($student) == 0) error_and_die ("Student name not set properly!  Please try again or contact your instructor.");
    if (strlen($assignment) == 0) error_and_die ("Assignment not set properly!  Please try again or contact your instructor.");

    if (strlen($allowedFileTypesStr) == 0) error_and_die("Allowed file types not set properly.  Please try again or contact your instructor.");
    $allowedFileTypes = explode(",", $allowedFileTypesStr);


    //Configuration - Your Options

    $max_filesize = 5000000; // Maximum filesize in BYTES (currently almost 5 MB).
    //mkdir( dirname(__FILE__) . '/sb/courses/'.$course . '/' . $assignment  , 0777); //creates assignment directory and makes it rewritable. /sb/courses/ could be changed to match your server settings

    $upload_path = dirname(__FILE__) . '/sb/courses/'.$course . '/' . $assignment . '/' . $student ;
    $temp = $upload_path ;
    $copy = 0;
    while (file_exists($upload_path))//Checks if the student has submited something for the selected assignment
    {				     //if so, we append the number of submission attempt at the end of the directory  name
      $copy += 1 ;			     //For example, John's third submissin for Assignment1, csc501 lives in /sb/courses/csc501/Assignment1/John-3
      $upload_path = $temp . "-" . $copy;
    }

    mkdir( $upload_path, 0777);


    $filename = $_FILES['userfile']['name']; // Get the name of the file (including file extension).
    $idx = strpos($filename, ".", 0);
    $fileExt = substr($filename, $idx + 1);
    $isFileTypeAllowed = false;
    foreach($allowedFileTypes as $allowedFileType) {
      if ($allowedFileType == $fileExt) {
        $isFileTypeAllowed = true;
        break;
      }
    }
    if (!$isFileTypeAllowed) {
      error_and_die("Your submission is not the correct file type");
    }



    // Now check the filesize, if it is too large then ERROR_AND_DIE and inform the user.
    if(filesize($_FILES['userfile']['tmp_name']) > $max_filesize)
    error_and_die('The file you attempted to upload is too large.');
    /**
    // Check if we can upload to the specified path, if not ERROR_AND_DIE and inform the user.
    if(!is_writable($upload_path))
    {
    echo $upload_path ;
    error_and_die('You cannot upload to the specified directory, please report this error to your instructor.');
  }
  */
  // JDT added for common error checks 1/11/14, expanded 1/25/15
  if (strcmp($filename,"package.bluej") == 0) error_and_die("package.bluej is not a valid file for submission!  If you are attempting to submit a single Java file, please choose your .java file instead.  If you are trying to submit an entire folder for your project, please create a .zip or .7z file of your entire project folder if you have not already done so, then choose the .zip or .7z file that you created instead.");
  if (endsWith($filename,'.class')) error_and_die ("Please choose your .java file instead of your .class file!");

  // Upload the file to your specified path.
  if(move_uploaded_file($_FILES['userfile']['tmp_name'],$upload_path . "/" . $filename))
  {
    $run;
    $ProgramName = $upload_path . "/" . $filename;
    $dotPos      = strripos($filename, "." );
    $CompiledFilreName = substr($filename , 0, $dotPos ); // Get the name of the file (excluding file extension).

    if ( $isGraded == '1')
    {

      //To compile submited source code set javac path to match your server settings
      //The resulted .class file is redirected using -d parameter to the same path where the uploaded .java file lives which is $upload_path
      //$ProgramName is the fully qualified path/name of the submitted program
      shell_exec("/usr/local/jdk1.7.0_17/bin/javac  -d $upload_path   $ProgramName");

      //To execute the submitted program set java path to match your server settings
      //If the uploaded program takes an input, then the test case input will be taken from in.txt
      //in.txt lies in the same directory where makdir.php lives
      //Any generated output will be saved at outPhp.txt which lies in the same path where the uploaded .java file lives
      $run =  shell_exec("/usr/local/jdk1.7.0_17/bin/java -classpath $upload_path  $CompiledFilreName < " . "in.txt" .  " >&$upload_path" ."/outPhp.txt");

      $isJavaFile = stripos($ProgramName , "java") ;
      if ( $isJavaFile === false )// Checks if the uploaded file is a java program
      {

        //if NOT we remove the wrong file and delete the recently created directory
        unlink($upload_path . "/" . $filename);
        unlink($upload_path . "/" . $CompiledFilreName . ".class" );
        rmdir($upload_path );
        header("location: submit.php?error= Please upload your source code file");

      }

      /*$correctOutputFile=fopen(dirname(__FILE__) . '/output.txt',"r") or error_and_die("Grading failed.  Please contact your instructor");
      if ( $correctOutputFile === $runi )
      $result = "pass" ;
      else
      $result = "fail" ;

      */

    }

    // Create/update submission record
    $submissionTime = time();
    $mysqltime = date ("Y-m-d H:i:s", $submissionTime);

    // in case the submission is already submitted, just add  another directory for the new one
    $submissionStatement = "Insert into Submission (StudentID, AssignmentID, SubmissionDate) VALUES (\"$student\",$assignmentId,\"$mysqltime\")
    ON DUPLICATE KEY UPDATE SubmissionDate=\"$mysqltime\";";
    //    mysqli_stmt_bind_param($submissionStatement, "sis", $student, $assignment, $mysqltime);
    //    if (!mysqli_stmt_execute($submissionStatement)) {
    echo $student;
    echo " / " . $_SESSION["username"];
    echo " / " . $submissionStatement;
    if (!mysql_query($submissionStatement)) {
      echo mysql_error();
      error_and_die("There was a problem recording your submission. Please try again or contact your instructor");
    }

    $submissionTimeDelta = $dueDate - $submissionTime;
    $lateMessage = "";
    if ($submissionTimeDelta < 0) {
      $lateMessage = "\nThe assignment was submitted " . ($submissionTimeDelta / 60.0 / 60.0) . " hours late.";
    }

    $to = "schermerhornc485@strose.edu,alshammarir845@strose.edu,nassirr961@strose.edu,wuyyurub543@strose.edu," . $student . "@strose.edu"; // change it to the receiver email address
    $subject = "SubmissionBox confirmation: " . $course . " " . $assignment . ", for " . $student;
    $body = "SubmissionBox confirmation: " . $assignment . "\" for " . $course . " has been uploaded by " . $student . ".\n
    The following file was submitted: " . $filename . $lateMessage;
    //$from = $student. "@strose.edu"; // student strose e-mail
    $from = "sb-confirmation@teresco.org";
    $headers = "From:" . $from; // additional parameter to set From, Cc and Bcc
    if (mail($to, $subject, $body , $headers  )) {
      echo("<p>Your assignment for " . $assignment . " for course " . $course . " has been submitted using file " . $filename . ", and a notification email has been sent to you and to your instructor.</p><p> <a href ='submit.php'> Back to submission page. </a></p>" );
    }
    else {
      echo("<p>Message delivery failed...  Please contact your instructor to verify successful submission of your assignment.</p>");
    }
  }
  else{
    echo 'There was an error during the file upload.  Please try again.'; // It failed :(.
    echo  $upload_path ."/" . $filename ;
  }
  ?>
  </body>
</html>
