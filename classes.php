<?php
require "DataBase.php";

$dbInstance = new DataBase();

if (!$dbInstance->Init())
{
    die("Init DB Error!");
}

if (!$dbInstance->CreateDatabase())
{
    die("Create DB Error!");
}

$availableCourses = $dbInstance->GetAvailabelCourses();

$dbInstance->End();

if (strcmp($availableCourses, "no data") == 0)
{
    die("No courses are available right now");
}

if (@$_REQUEST["title"])
{
    $GLOBALS["title"] = $_REQUEST["title"];
    setcookie("title", $_REQUEST["title"], time() + 3600); // save the value in cookies
}
else
{
    $GLOBALS["title"] = @$_COOKIE["title"]; // load value from cookies
}

if (@$_REQUEST["weekday"])
{
    $GLOBALS["weekday"] = $_REQUEST["weekday"];
    if (strpos($availableCourses, $_REQUEST["weekday"]) == false) // save the value in cookies
    {
        setcookie("weekday", "none", time() + 3600);
    }
    else
    {
        setcookie("weekday", $_REQUEST["weekday"], time() + 3600);
    }
}
else
{
	$GLOBALS["weekday"] = @$_COOKIE["weekday"]; // load value from cookies
}

if (@$_REQUEST["time"])
{
    $GLOBALS["time"] = $_REQUEST["time"]; 
    setcookie("time", $_REQUEST["time"], time() + 3600); // save the value in cookies
    if (strpos($availableCourses, $_REQUEST["time"]) == false)
    {
        setcookie("time", "none", time() + 3600 );
    }
    else
    {
        setcookie("time", $_REQUEST["time"], time() + 3600);
    }
}
else
{
	$GLOBALS["time"] = @$_COOKIE["time"]; // load value from cookies
}



function HiddenCheck($available, $val){
    if (strcmp($val, "none") != 0 && strpos($available, $val) == false)
    {
        echo "hidden";
    }
    else
    {
        if(strcmp(@$GLOBALS["title"], $val) == 0){
            echo "selected";
            return;
        } 
        if(strcmp(@$GLOBALS["weekday"], $val) == 0){
            echo "selected";
            return;
        } 
        if(strcmp(@$GLOBALS["time"], $val) == 0){
            echo "selected";
            return;
        }
    }
    
}

function DispErr($val){
    if (strcmp($val, "name") == 0)
    {
        if(@$_COOKIE["nameErr"]){
            echo "a-z, A-Z, -, ' and space";
        }
    } else
    if (strcmp($val, "phone") == 0)
    {
        if(@$_COOKIE["phoneErr"]){
            echo "9-10 digits starting with 0";
        }
    } else
    if (strcmp($val, "title") == 0)
    {
        if(@$_COOKIE["titleErr"]){
            echo "Please select a course";
        }
    } else
    if (strcmp($val, "weekdayTime") == 0)
    {
        if(@$_COOKIE["weekdayTimeErr"] ){
            echo "Please select a day/timeslot";
        }
    } 
}

?>
<!DOCTYPE html>
<html>
<head>
	<title>Course Booking</title>
    <style>
        .error {
            color: #FF0000;
        }
    </style>
</head>
<body>
	<h2 style="text-align: center;">
		<strong>Online Course Booking</strong>
	</h2>
	<form name="course" action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
		<table >
			<tbody>
				<tr>
					<td style="width: 300px; text-align: left;">Course Title</td>
					<td style="width: 500px;">Course Time</td>
				</tr>
				<tr>
					<td style="width: 300px; text-align: left;">
						<span class="error"><?php DispErr("title");?></span>
                    </td>
					<td style="width: 500px;">
						<span class="error"><?php DispErr("weekdayTime");?></span>
                    </td>
				</tr>
				<tr>
					<td style="width: 169.091px;">
						<select name="title" onchange="document.course.submit()" required>
							<option value="none" <?php HiddenCheck($availableCourses, "none"); ?> >Select a course</option>
							<option <?php HiddenCheck($availableCourses, "creative"); ?> value="creative" >Creative Cupcakes</option>
							<option <?php HiddenCheck($availableCourses, "digital"); ?> value="digital" >Digital Photography</option>
							<option <?php HiddenCheck($availableCourses, "family"); ?> value="family" >Family History for Beginners</option>
							<option <?php HiddenCheck($availableCourses, "fundamentals"); ?> value="fundamentals" >Fundamentals of Acrylic Painting</option>
							<option <?php HiddenCheck($availableCourses, "holiday"); ?> value="holiday" >Holiday French</option>
						</select>
					</td>
					<td style="width: 110.909px;">
						<select name="weekday" onchange="document.course.submit()" required>
                            <option value="none"<?php HiddenCheck($availableCourses, "none"); ?> >Select a day</option>
                            <option <?php HiddenCheck($availableCourses, "mon"); ?> value="mon"  >Monday</option>
                            <option <?php HiddenCheck($availableCourses, "tue"); ?> value="tue"  >Tuesday</option>
                            <option <?php HiddenCheck($availableCourses, "wed"); ?> value="wed"  >Wednesday</option>
                            <option <?php HiddenCheck($availableCourses, "thu"); ?> value="thu"  >Thursday</option>
                            <option <?php HiddenCheck($availableCourses, "fri"); ?> value="fri"  >Friday</option>
						</select>
						<select name="time" onchange="document.course.submit()" required>
                            <option value="none"<?php HiddenCheck($availableCourses, "none"); ?> >Select a timeslot</option>
                            <option <?php HiddenCheck($availableCourses, "1900"); ?> value="1900"  >19:00</option>
                            <option <?php HiddenCheck($availableCourses, "2000"); ?> value="2000"  >20:00</option>
						</select>
					</td>
				</tr>
			</tbody>
		</table>
	</form><br /><br />
	<form name="info" action="submit.php" method="post">
		<table>
			<tbody>
				<tr>
					<td style="width: 300px; text-align: left;">Input your information:</td>
					<td><p><span class="error">* required field.</span></p> </td>
				</tr>
				<tr>
					<td style="width: 169.091px;">Name:</td>
					<td style="width: 110.909px; text-align: left;">
						<input required type="text" name="name" value="<?php echo @$_COOKIE["name"]; ?>" /><span class="error">*</span><br />
						<span class="error"><?php DispErr("name");?></span>
					</td>
				</tr>
				<tr>
					<td style="width: 169.091px;">Phone/mobile No.:</td>
					<td style="width: 110.909px;">
						<input required type="tel" name="phone" value="<?php echo @$_COOKIE["phone"]; ?>" /><span class="error">*</span><br />
						<span class="error"><?php DispErr("phone");?></span>
					</td>
				</tr>
				<tr>
					<td style="width: 169.091px;">Previous knowledge:</td>
					<td style="width: 110.909px;"><textarea name="pre" rows="10" cols="30" >
						<?php echo @$_COOKIE["pre"]; ?>
					</textarea></td>
				</tr>
				<tr>
					<td></td>
					<td><input type="submit" value="submit"/></td>
				</tr>
			</tbody>
		</table>
	</form>
</body>
</html>
