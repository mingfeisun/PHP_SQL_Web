<?php
class DataBase
{
	var $m_ServerName = "mysql";
	var $m_Database = "x4yx";
	var $m_UserName = "x4yx";
	var $m_UserPassword = "12345678";
	var $m_DBCon = null;

	function Init(){
		$this->m_DBCon = new mysqli($this->m_ServerName, $this->m_UserName, $this->m_UserPassword, $this->m_Database);
		if ($this->m_DBCon->connect_error)
		{
			return false;
		}
		else
		{
			return true;
		}
	}

	function End(){
		mysqli_close($this->m_DBCon);
	}

	function CreateDatabase(){
		// Check Whether the DB has been created already
		if (mysqli_query($this->m_DBCon, "USE x4yx"))
		{
			$queryResult = mysqli_query($this->m_DBCon, "SELECT * FROM course");
			if ($queryResult != false && mysqli_num_rows($queryResult) > 0)
			{
				return true;
			}
			else
			{
				return false;
			}
		}
        

		// Create DB test
        //$sql = "CREATE DATABASE test";
        //if (!mysqli_query($this->m_DBCon, $sql))
        //{
        //    return false;
        //}

		// Use DB test
        //$sql = "USE x4yx";
        //if (!mysqli_query($this->m_DBCon, $sql))
        //{
        //    return false;
        //}

		// Create Table course
		$sql = "CREATE TABLE course (
					id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
					title VARCHAR(50) NOT NULL,
					weekday VARCHAR(30) NOT NULL,
					timeslot VARCHAR(30) NOT NULL,
					capacity INT NOT NULL,
					remain INT NOT NULL
				)";
		if (!mysqli_query($this->m_DBCon, $sql))
		{
			return false;
		}

		// Create Table booking
		$sql = "CREATE TABLE booking (
					id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
					name VARCHAR(50) NOT NULL,
					phone VARCHAR(30) NOT NULL,
					title VARCHAR(50) NOT NULL,
					weekday VARCHAR(30) NOT NULL,
					timeslot VARCHAR(30) NOT NULL,
					pre VARCHAR(500)
				)";
		if (!mysqli_query($this->m_DBCon, $sql))
		{
			return false;
		}

		// Insert Five Data Entries
		$sql = array(
			// Creative Cupcakes
			"INSERT INTO course (title, weekday, timeslot, capacity, remain) VALUES ('creative', 'mon', '1900', 2, 2)",
			"INSERT INTO course (title, weekday, timeslot, capacity, remain) VALUES ('creative', 'wed', '1900', 2, 2)",
			"INSERT INTO course (title, weekday, timeslot, capacity, remain) VALUES ('creative', 'fri', '1900', 2, 2)",
			// Digital Photography
			"INSERT INTO course (title, weekday, timeslot, capacity, remain) VALUES ('digital', 'tue', '1900', 4, 4)",
			"INSERT INTO course (title, weekday, timeslot, capacity, remain) VALUES ('digital', 'thu', '1900', 4, 4)",
			// Family History for Beginners
			"INSERT INTO course (title, weekday, timeslot, capacity, remain) VALUES ('family', 'mon', '2000', 3, 3)",
			"INSERT INTO course (title, weekday, timeslot, capacity, remain) VALUES ('family', 'tue', '2000', 3, 3)",
			// Fundamentals of Acrylic Painting
			"INSERT INTO course (title, weekday, timeslot, capacity, remain) VALUES ('fundamentals', 'wed', '2000', 2, 2)",
			"INSERT INTO course (title, weekday, timeslot, capacity, remain) VALUES ('fundamentals', 'fri', '2000', 2, 2)",
			// Holiday French
			"INSERT INTO course (title, weekday, timeslot, capacity, remain) VALUES ('holiday', 'thu', '2000', 2, 2)"
		);

		for ($i = 0; $i < sizeof($sql); $i++)
		{
			if (!mysqli_query($this->m_DBCon, $sql[$i]))
			{
				// die("Inserting error". mysqli_error($this->m_DBCon));
				return false;
			}
		}


		return true;
	}

	function RegisterCourse($userName, $userPhone, $userPre, $courseTitle, $courseWeekday, $courseTime){

		// Check Whether the Course is Available
		// Return value: 0 --> error; 1 --> success; 2 --> course no available

		$sqlCondition = " WHERE title='" . $courseTitle . "'AND weekday='" . $courseWeekday . "'AND timeslot='" . $courseTime . "'";

		$sql = "SELECT remain FROM course " . $sqlCondition;
		$queryResult = mysqli_query($this->m_DBCon, $sql);
		if ($queryResult == false || mysqli_num_rows($queryResult) == 0)
	    {
			// die("Select error". mysqli_error($this->m_DBCon));
			return 0;
	    }

		$row = mysqli_fetch_assoc($queryResult);
		if ($row["remain"] <= 0)
	    {
			return 2; // No courses available
	    }
		$remainNumber = $row["remain"] - 1;

        // SQL injection defense
        if ($stmt = mysqli_prepare($this->m_DBCon, "INSERT INTO booking (name,phone,title,weekday,timeslot,pre) VALUES(?,?,?,?,?,?)")) {
            mysqli_stmt_bind_param($stmt, 'ssssss', $userName,$userPhone,$courseTitle,$courseWeekday,$courseTime,$userPre);
            $success = mysqli_stmt_execute($stmt);
            if (!$success)
            {
                return 0;
            }
            mysqli_stmt_close($stmt);
        }

		// Update Table course, booking
		$sql = "UPDATE course SET remain='". $remainNumber . "'" . $sqlCondition;
		if (!mysqli_query($this->m_DBCon, $sql))
		{
			// die("Update error". mysqli_error($this->m_DBCon));
			return 0;
		}

		return 1;
	}

	function GetAvailabelCourses(){
		$sql = "SELECT * FROM course WHERE remain > 0";

        $queryStr = ";;;"; // start from these
		if (@$_REQUEST["title"] && strcmp(@$_REQUEST["title"], "none"))
		{
			$sql = $sql . " AND title='" . $_REQUEST["title"] . "'";
        }

        $queryResult = mysqli_query($this->m_DBCon, $sql);
        if ($queryResult == null || mysqli_num_rows($queryResult) == 0)
        {
            return "no data";
        }

        while($row = mysqli_fetch_assoc($queryResult)) {
            $queryStr .= $row["title"] . ", " . $row["weekday"] . ", " . $row["timeslot"] . "; ";
        }

		$sql = "SELECT title FROM course WHERE remain > 0";
        $queryResult = mysqli_query($this->m_DBCon, $sql);
        if ($queryResult == null || mysqli_num_rows($queryResult) == 0)
        {
            return "no data";
        }

        while($row = mysqli_fetch_assoc($queryResult)) {
            $queryStr .= $row["title"];
        }

		return $queryStr;
	}
}
?>