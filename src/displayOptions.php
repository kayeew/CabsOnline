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

		$option = $_GET["option"];

		if($option == "all") {

			$sql_query = "SELECT * from booking";

		} else if($option == "unassigned" || $option == "assigned") {

			$sql_query = "SELECT * from booking WHERE bk_status='$option'";

		} else if($option == "within2Hours") {

			$sql_query = "SELECT * from booking
				WHERE DATE(bk_date) = DATE(NOW())
				AND bk_pickup_time > TIME(LOCALTIME())
				AND bk_pickup_time <= TIME(DATE_ADD(NOW(), INTERVAL 2 HOUR))";
		}

		$result = $db_conn->query($sql_query);

	    if ($result->num_rows > 0) { // if results exists from the MySQL query
	    	echo "<table class='table'>
	    	<thead><tr>
		    	<th scope='col'>Booking No.</th>
		    	<th scope='col'>Name</th>
		    	<th scope='col'>Booking Date</th>
		    	<th scope='col'>Booking Time</th>
		    	<th scope='col'>Phone Contact</th>
		    	<th scope='col'>No. of Passengers</th>
		    	<th scope='col'>Unit No.</th>
		    	<th scope='col'>Street No.</th>
		    	<th scope='col'>Street Name</th>
		    	<th scope='col'>Suburb</th>
		    	<th scope='col'>Destination</th>
		    	<th scope='col'>Pickup Time</th>
		    	<th scope='col'>Pickup Date</th>
		    	<th scope='col'>Status</th>
			<tr></thead><tbody>";

	        while($row = $result->fetch_assoc()) {

	        	// format pickup date/time for client side display
	        	$booking_time = date('h:i a', strtotime($row['bk_time']));
	        	// trim leading 0 in time, eg. 09:30 am --> 9:30 am
	        	$booking_time = ltrim($booking_time, '0');

	        	$booking_date = date('d/m/y', strtotime($row['bk_date']));

	        	// format pickup date/time for client side display
	        	$pickup_time = date('h:i a', strtotime($row['bk_pickup_time']));
	        	// trim leading 0 in time, eg. 09:30 am --> 9:30 am
	        	$pickup_time = ltrim($pickup_time, '0');

	        	$pickup_date = date('d/m/y', strtotime($row['bk_pickup_date']));

	            echo "<tr><td scope='row'>" . $row["bk_number"] . "</td>" .
	            	"<td>" . $row["bk_name"] . "</td>" .
	            	"<td>" . $booking_date . "</td>" .
	            	"<td>" . $booking_time . "</td>" .
	            	"<td>" . $row["bk_phone_contact"] . "</td>" .
	            	"<td>" . $row["bk_num_of_passengers"] . "</td>" .
	            	"<td>" . $row["bk_pickup_unit_no"] . "</td>" .
	            	"<td>" . $row["bk_pickup_street_no"] . "</td>" .
	            	"<td>" . $row["bk_pickup_street_name"] . "</td>" .
	            	"<td>" . $row["bk_pickup_suburb"] . "</td>" .
	            	"<td>" . $row["bk_destination"] . "</td>" .
	            	"<td>" . $pickup_time . "</td>" .
	            	"<td>" . $pickup_date . "</td>" .
	            	"<td>" . $row["bk_status"] . "</td></tr>";
	        }

	    	echo "</tbody></table>";

		} else {
			echo "<div class='container'>No bookings found.</div>";
		}


	} // end of if table exists

	$db_conn->close(); // close database connection

?>
