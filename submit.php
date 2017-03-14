<?php
require "DataBase.php";
session_start();

// User Info
$userName	= "";
$userPhone	= "";
$userPre	= "";

// Course Info
$courseTitle = $_SESSION["title"];
$courseWeekday = $_SESSION["weekday"];
$courseTime = $_SESSION["time"];

// User info
$_SESSION["name"] = $_POST["name"];
$_SESSION["phone"] = $_POST["phone"];
$_SESSION["pre"] = $_POST["pre"];

// Session Data for Error Report
$_SESSION["nameErr"] = false;
$_SESSION["phoneErr"] = false;
$_SESSION["titleErr"] = false;
$_SESSION["weekdayTimeErr"] = false;

if (strcmp($courseTitle, "none") == 0 || strcmp($courseTime, "none") == 0 || strcmp($courseWeekday, "none") == 0)
{
    $_SESSION["titleErr"] = (strcmp($courseTitle, "none") == 0);
    $_SESSION["weekdayTimeErr"] = (strcmp($courseWeekday, "none") == 0) || (strcmp($courseTime, "none") == 0);
	header("Location: classes.php");
    exit();
}


$validCheck = true;

// Checking Names
$name = InputCheck($_POST["name"]);
if ($name != "" && preg_match("/^[a-zA-Z ]*[\-']{0,1}[a-zA-Z ]*[\-']{0,1}[a-zA-Z ]*$/",$name)){
	$userName = $name;
}
else
{
	$_SESSION["nameErr"] = true;
	header("Location: classes.php");
    exit();
}


// Checking Phone Numbers
$phone = InputCheck($_POST["phone"]);
$phone = str_replace(' ', '', $phone); // strip all the spaces
if( preg_match("/^0[0-9]{8,9}$/", $phone)) {
	$userPhone = $phone;
}
else
{
	$_SESSION["phoneErr"] = true;
	header("Location: classes.php");
    exit();
}

// Checking Previous Knowledge Input
$userPre = InputCheck($_POST["pre"]);

// Database Operations
$dbInstance = new DataBase();

if (!$dbInstance->Init())
{
	die("Init DB Error!");
}

if (!$dbInstance->CreateDatabase())
{
	die("Create DB Error!");
}
$hr = $dbInstance->RegisterCourse($userName, $userPhone, $userPre, $courseTitle, $courseWeekday, $courseTime);
if ($hr == 0) // Error
{
	echo "Register Course Failed!";
    header( "Refresh:3; url=classes.php", true, 303);
    exit();
} else
if ($hr == 2) // No places left
{
	echo "The course is not available at this timeslot!";
    header( "Refresh:3; url=classes.php", true, 303);
    exit();
}


$dbInstance->End();

// Reset all sessions
$_SESSION["title"] = "";
$_SESSION["weekday"] = "";
$_SESSION["time"] = "";
$_SESSION["name"] = "";
$_SESSION["phone"] = "";
$_SESSION["pre"] = "";

$_SESSION["nameErr"] = false;
$_SESSION["phoneErr"] = false;
$_SESSION["titleErr"] = false;
$_SESSION["weekdayTimeErr"] = false;

echo "Register Successfully! <br/> Will jump back in 3 seconds";
header( "Refresh:3; url=classes.php", true, 303);
exit();




/********************************************
*	Function & Class Definitions Start Here
********************************************/

// Discarding Spaces, Slashes, Anti-injection
function InputCheck($data){
	$data = trim($data);
	$data = stripslashes($data);
	$data = htmlspecialchars($data);

	return $data;
}

?>