<?php
require "DataBase.php";

// User Info
$userName	= "";
$userPhone	= "";
$userPre	= "";

// Course Info
$courseTitle = $_COOKIE["title"];
$courseWeekday = $_COOKIE["weekday"];
$courseTime = $_COOKIE["time"];

// User info
setcookie("name", $_POST["name"], time() + 3600);
setcookie("phone", $_POST["phone"], time() + 3600);
setcookie("pre", $_POST["pre"], time() + 3600);

// Session Data for Error Report
setcookie("nameErr", false, time() + 3600);
setcookie("phoneErr", false, time() + 3600);
setcookie("titleErr", false, time() + 3600);
setcookie("weekdayTimeErr", false, time() + 3600);

if (strcmp($courseTitle, "none") == 0 || strcmp($courseTime, "none") == 0 || strcmp($courseWeekday, "none") == 0)
{
    $_COOKIE["titleErr"] = (strcmp($courseTitle, "none") == 0);
    $_COOKIE["weekdayTimeErr"] = (strcmp($courseWeekday, "none") == 0) || (strcmp($courseTime, "none") == 0);
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
    setcookie("nameErr", true, time() + 3600);
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
    setcookie("phoneErr", true, time() + 3600);
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

// Reset all cookies
setcookie("title", "", time() - 3600);
setcookie("weekday", "", time() - 3600);
setcookie("time", "", time() - 3600);
setcookie("phone", "", time() - 3600);
setcookie("pre", "", time() - 3600);

setcookie("nameErr", "", time() - 3600);
setcookie("phoneErr", "", time() - 3600);
setcookie("titleErr", "", time() - 3600);
setcookie("weekdayTimeErr", "", time() - 3600);

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