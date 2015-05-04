<?php session_start();

include "connect.php"; //connects to the database
$servername = "localhost";
$username = "root";
$password = "letsgosb3";
$dbname = "test";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
if (isset($_POST['username'])){
    $username = $_POST['username'];
    $query="select * from Student where StudentID='$username'";
    $result   = mysql_query($query);
    $count=mysql_num_rows($result);
    // If the count is equal to one, we will send message other wise display an error message.

    if($count==1)
    {
      //Build random string password from alphabet below
      $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
      $pass = array(); //remember to declare $pass as an array
      $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
      for ($i = 0; $i < 8; $i++) {
          $n = rand(0, $alphaLength);
          $pass[] = $alphabet[$n];
      }

      //implode (or see legitimate string) in userpwd
	    $usrpwd = implode($pass);

      //immediately update pass in database using MD5
      $pwdreset = "UPDATE Student SET password= md5('$usrpwd') WHERE StudentID='$username'";
      if ($conn->query($pwdreset) === TRUE) {
          echo "The password for " .$username . " has been updated.  Check your email.";
      }
      else {
        echo "Error updating record.  Do you have an account? " . $conn->error;
      }
        $to = $username . "@strose.edu"; // change it to the receiver email address
        $subject = "SubmissionBox Recovery for: " . $username;
        $from = "sb-confirmation@teresco.org";
        $headers = "From:" . $from; // additional parameter to set From, Cc and Bcc
        $rows=mysql_fetch_array($result);
        $body  =  "Enter the following password next time you log into SubmissionBox.
        PASSWORD: " . implode($pass);
        "Sincerely,
        SB3";
        $from = "sb-password-reset@sb3.teresco.org";
        $subject = "Password recovered";
        $headers1 = "From: $from\n";
        $headers1 .= "Content-type: text/html;charset=iso-8859-1\r\n";
        $headers1 .= "X-Priority: 1\r\n";
        $headers1 .= "X-MSMail-Priority: High\r\n";
        $headers1 .= "X-Mailer: Just My Server\r\n";
        $sentmail = mail ( $to, $subject, $body, $headers1 );
    } else {
    if ($_POST ['email'] != "") {
    echo "<span style='color: #ff0000;'> Not found your email in our database</span>";
        }
        }
    //If the message is sent successfully, display sucess message otherwise display an error message.
    if($sentmail==1)
    {
        //echo "<span style='color: #ff0000;'> Your Password Has Been Sent To Your Email Address.</span>";
        echo '<script type="text/javascript">alert("Your Password Has Been Sent To Your Email Address.");</script>';
    }
        else
        {
        if($_POST['email']!="")
        echo "<span style='color: #ff0000;'> Cannot send password to your e-mail address.Problem with sending mail...</span>";
    }
}
?>
 <!DOCTYPE html>
<html>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="css/forgot-password.css" />
<title>Password Reset Form</title>
</head>
<body>

<div id="header">Forgot Password</div>
<div class="register-form">
<?php
	if(isset($msg) & !empty($msg)){
		echo $msg;
	}
 ?>
<form class="box login" action="" method="post">
  <fieldset class="boxBody">
     <label>Username : </label>
	   <input id="username" type="text" name="username" placeholder="username" />
  </fieldset>
  <footer>
    <input class="btnLogin" type="submit" name="submit" value="Submit" />
    <br><br><a href="/Authentication.html">Submission Box Main Page</a>
  </footer>
</form>
</div>
</body>
</html>
