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

		// Get values from post booking form
		$bk_number 				= uniqid(); // generates a unique ID based on the microtime 
		$bk_name 				= $_POST["name"];
		$bk_date 				= $_POST["bookingDate"];
		$bk_time 				= $_POST["bookingTime"];
		$bk_phone_contact 		= $_POST["phoneContact"];
		$bk_num_of_passengers 	= (int) $_POST["numPassengers"];
		$bk_pickup_unit_no 		= (int) $_POST["unitNum"];
		$bk_pickup_street_no 	= (int) $_POST["streetNum"];
		$bk_pickup_street_name 	= $_POST["streetName"];
		$bk_pickup_suburb 	   	= $_POST["suburb"];
		$bk_destination 	  	= $_POST["destination"];
		$bk_pickup_time 	  	= $_POST["pickupTime"];
		$bk_pickup_date 	  	= $_POST["pickupDate"];
		$bk_status 			  	= "unassigned"; // generate status

		// MySQL query to insert booking details into MySQL DB
		$sql_query = "INSERT INTO `booking` (`bk_number`, `bk_name`, `bk_date`, `bk_time`, `bk_phone_contact`, `bk_num_of_passengers`, `bk_pickup_unit_no`, `bk_pickup_street_no`, `bk_pickup_street_name`, `bk_pickup_suburb`, `bk_destination`, `bk_pickup_time`, `bk_pickup_date`, `bk_status`
			) VALUES (
				'$bk_number',
				'$bk_name',
				'$bk_date',
				'$bk_time',
				'$bk_phone_contact',
				'$bk_num_of_passengers',
				'$bk_pickup_unit_no',
				'$bk_pickup_street_no',
				'$bk_pickup_street_name',
				'$bk_pickup_suburb',
				'$bk_destination',
				'$bk_pickup_time',
				'$bk_pickup_date',
				'$bk_status'
			);";

		if ($db_conn->query($sql_query) == true) { // if booking details are inserted into MySQL DB successfully

			// format pickup date/time for client side display
        	$time = date('h:i a', strtotime($bk_pickup_time));
        	$time = ltrim($time, '0'); // trim leading 0 in time, eg. 09:30 am --> 9:30 am
        	$date = date('d/m/y', strtotime($bk_pickup_date));

        	// display booking confirmation message
	        echo "Thank you! Your booking reference number is <strong>$bk_number</strong>. 
	        	You will be picked up in front of your provided address 
	        	at <strong>$time</strong> on <strong>$date.</strong>";

	    } else { // if insert MySQL query fails output error message 
	        echo "SQL: " . $sql . "<br> Error:" . $conn->error;
	    }
	}

	$db_conn->close(); // close database connection


?>