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
	<?php
	session_start();
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
        $_SESSION["title"] = $_REQUEST["title"];
    }
    if (@$_REQUEST["weekday"])
    {
        if (strpos($availableCourses, $_REQUEST["weekday"]) == false)
        {
            $_SESSION["weekday"] = "none";
        }
        else
        {
            $_SESSION["weekday"] = $_REQUEST["weekday"];
        }
    }
    if (@$_REQUEST["time"])
    {
        $_SESSION["time"] = $_REQUEST["time"];
        if (strpos($availableCourses, $_REQUEST["time"]) == false)
        {
            $_SESSION["time"] = "none";
        }
        else
        {
            $_SESSION["time"] = $_REQUEST["time"];
        }
    }
    

    function HiddenCheck($available, $val){
        if (strpos($available, $val) == false)
        {
            echo "hidden";
        }
    }

	function DispErr($val){
		if (strcmp($val, "name") == 0)
		{
			if(@$_SESSION["nameErr"]){
				echo "a-z, A-Z, -, ' and space";
			}
		} else
		if (strcmp($val, "phone") == 0)
		{
			if(@$_SESSION["phoneErr"]){
				echo "9-10 digits starting with 0";
			}
		} else
		if (strcmp($val, "title") == 0)
		{
			if(@$_SESSION["titleErr"]){
				echo "Please select a course";
			}
		} else
		if (strcmp($val, "weekdayTime") == 0)
		{
			if(@$_SESSION["weekdayTimeErr"] ){
				echo "Please select a day/timeslot";
			}
		} 
	}

	function SelectValue($val){
		if(strcmp(@$_SESSION["title"], $val) == 0){
			echo "selected";
            return;
		} 
		if(strcmp(@$_SESSION["weekday"], $val) == 0){
			echo "selected";
            return;
		} 
		if(strcmp(@$_SESSION["time"], $val) == 0){
			echo "selected";
            return;
		}
	}

	?>
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
							<option value="none" <?php SelectValue("none") ?> >Select a course</option>
							<option <?php HiddenCheck($availableCourses, "creative"); ?> value="creative" <?php SelectValue("creative") ?>>Creative Cupcakes</option>
							<option <?php HiddenCheck($availableCourses, "digital"); ?> value="digital" <?php SelectValue("digital") ?>>Digital Photography</option>
							<option <?php HiddenCheck($availableCourses, "family"); ?> value="family" <?php SelectValue("family") ?>>Family History for Beginners</option>
							<option <?php HiddenCheck($availableCourses, "fundamentals"); ?> value="fundamentals" <?php SelectValue("fundamentals") ?>>Fundamentals of Acrylic Painting</option>
							<option <?php HiddenCheck($availableCourses, "holiday"); ?> value="holiday" <?php SelectValue("holiday") ?>>Holiday French</option>
						</select>
					</td>
					<td style="width: 110.909px;">
						<select name="weekday" onchange="document.course.submit()" required>
                            <option value="none" <?php SelectValue("none"); ?>>Select a day</option>
                            <option <?php HiddenCheck($availableCourses, "mon"); ?> value="mon" <?php SelectValue("mon"); ?> >Monday</option>
                            <option <?php HiddenCheck($availableCourses, "tue"); ?> value="tue" <?php SelectValue("tue"); ?> >Tuesday</option>
                            <option <?php HiddenCheck($availableCourses, "wed"); ?> value="wed" <?php SelectValue("wed"); ?> >Wednesday</option>
                            <option <?php HiddenCheck($availableCourses, "thu"); ?> value="thu" <?php SelectValue("thu"); ?> >Thursday</option>
                            <option <?php HiddenCheck($availableCourses, "fri"); ?> value="fri" <?php SelectValue("fri"); ?> >Friday</option>
						</select>
						<select name="time" onchange="document.course.submit()" required>
                            <option value="none" <?php SelectValue("none"); ?>>Select a timeslot</option>
                            <option <?php HiddenCheck($availableCourses, "1900"); ?> value="1900" <?php SelectValue("1900"); ?> >19:00</option>
                            <option <?php HiddenCheck($availableCourses, "2000"); ?> value="2000" <?php SelectValue("2000"); ?> >20:00</option>
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
						<input required type="text" name="name" value="<?php echo @$_SESSION["name"]; ?>" /><span class="error">*</span><br />
						<span class="error"><?php DispErr("name");?></span>
					</td>
				</tr>
				<tr>
					<td style="width: 169.091px;">Phone/mobile No.:</td>
					<td style="width: 110.909px;">
						<input required type="tel" name="phone" value="<?php echo @$_SESSION["phone"]; ?>" /><span class="error">*</span><br />
						<span class="error"><?php DispErr("phone");?></span>
					</td>
				</tr>
				<tr>
					<td style="width: 169.091px;">Previous knowledge:</td>
					<td style="width: 110.909px;"><textarea name="pre" rows="10" cols="30" >
						<?php echo @$_SESSION["pre"]; ?>
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
