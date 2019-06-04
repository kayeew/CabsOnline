<?php
	// Kayee 2019


 	require_once("DBConnection.php");

	// error message when Database connection fails
	if(!$db_conn) {
		die("Connection Failed: " . mysqli_connect_error());
	}

	// Check if status table exist else create it
	$table_exist = $db_conn->query("SELECT 1 FROM `booking` LIMIT 1");

	if($table_exist == false) { // booking table does not exist, so create booking table

		$create_table_sql_query = "CREATE TABLE `booking` (
		  `bk_number` varchar(50) NOT NULL,
		  `bk_date` date NOT NULL,
		  `bk_time` time(6) NOT NULL,
		  `bk_name` varchar(20) NOT NULL,
		  `bk_phone_contact` varchar(15) NOT NULL,
		  `bk_num_of_passengers` int(2) NOT NULL,
		  `bk_pickup_unit_no` int(5) NOT NULL,
		  `bk_pickup_street_no` int(5) NOT NULL,
		  `bk_pickup_street_name` varchar(20) NOT NULL,
		  `bk_pickup_suburb` varchar(20) NOT NULL,
		  `bk_destination` varchar(30) NOT NULL,
		  `bk_pickup_time` time(6) NOT NULL,
		  `bk_pickup_date` date NOT NULL,
		  `bk_status` varchar(10) NOT NULL
		) ENGINE=InnoDB DEFAULT CHARSET=latin1;";

		if ($db_conn->query($create_table_sql_query) == TRUE) {
			
			// status table created successfully
			// echo "Booking Table Created"; for testing
			
		} else {
		    echo "Error: " . $sql . "<br>" . $db_conn->error;
		}

	} else { // table exists 
		
		$bknumber = $_GET["bknumber"];

		$sql_query = "SELECT * from booking WHERE bk_number='$bknumber'";

		$result = $db_conn->query($sql_query);
	    // If there are results from database
	    if ($result->num_rows > 0) {
	        while($row = $result->fetch_assoc()) {

	            if($row["bk_status"] == "assigned") {
	            	echo "Booking number <strong>" . $bknumber . "</strong> has already been assigned.";

	            } else if($row["bk_status"] == "unassigned") {

	            	// update status
	            	$sql_query = "UPDATE booking SET bk_status='assigned' WHERE bk_number='$bknumber' AND bk_status='unassigned'";

					if ($db_conn->query($sql_query) == true) {

				        echo "Assigned Taxi to booking number <strong>" . $bknumber . "</strong>.";

				    } else {
				        echo "SQL: " . $sql . "<br> Error:" . $conn->error;
				    }
	            }

	        } // end of while loop

	    } else {
	    	echo "Booking number <strong>" . $bknumber . "</strong> does not exist. Please enter a valid booking number.";
	    }
	}

	$db_conn->close(); // close database connection
?>