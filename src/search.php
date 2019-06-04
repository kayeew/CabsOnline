<?php
	// Kayee 2019


	// function to highlight status content letters/words that matches keyword
	function highlightKeywords($text, $keyword) {
		$wordsAry = explode(" ", $keyword);
		$wordsCount = count($wordsAry);

		for($i = 0 ; $i < $wordsCount; $i++) {
			$highlighted_text = "<span style='font-weight:bold;'>". $wordsAry[$i]."</span>";
			$text = str_ireplace($wordsAry[$i], $highlighted_text, $text);
		}
		return $text;
	}

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

		$keyword = "";
		$queryCondition = "";
		
		$keyword = $_GET["search"];

		$wordsAry = explode(" ", $keyword); // break keyword string into array
		$wordsCount = count($wordsAry); // count keyword

		$queryCondition = " WHERE ";
		for($i = 0; $i < $wordsCount; $i++) { // match keyword with status_content column from status table
			$queryCondition .= "bk_number LIKE '%" . $wordsAry[$i] . "%' OR 
			bk_name LIKE '%" . $wordsAry[$i] . "%' OR 
			bk_date LIKE '%" . $wordsAry[$i] . "%' OR 
			bk_time LIKE '%" . $wordsAry[$i] . "%' OR 
			bk_phone_contact LIKE '%" . $wordsAry[$i] . "%' OR 
			bk_num_of_passengers LIKE '%" . $wordsAry[$i] . "%' OR 
			bk_pickup_unit_no LIKE '%" . $wordsAry[$i] . "%' OR 
			bk_pickup_street_no LIKE '%" . $wordsAry[$i] . "%' OR 
			bk_pickup_street_name LIKE '%" . $wordsAry[$i] . "%' OR 
			bk_pickup_suburb LIKE '%" . $wordsAry[$i] . "%' OR 
			bk_destination LIKE '%" . $wordsAry[$i] . "%' OR 
			bk_pickup_time LIKE '%" . $wordsAry[$i] . "%' OR 
			bk_pickup_date LIKE '%" . $wordsAry[$i] . "%' OR 
			bk_status LIKE '%" . $wordsAry[$i] . "%'";

			if($i != $wordsCount - 1) {
				$queryCondition .= " OR ";
			}
		}

		$orderby = " ORDER BY bk_number";
		$sql_query = "SELECT * FROM booking " . $queryCondition . $orderby;

		$results = $db_conn->query($sql_query);

	    if ($results->num_rows > 0) {
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
		    	<th scope='col'>Time</th>
		    	<th scope='col'>Date</th>
		    	<th scope='col'>Status</th>
			<tr></thead><tbody>";

			while($row = mysqli_fetch_array($results)) {

				//call highlightKeywordsfunction to highlight where results match
				$highlighted_bk_number = highlightKeywords($row["bk_number"], $keyword);
				$highlighted_bk_name = highlightKeywords($row["bk_name"], $keyword);
				$highlighted_bk_date = highlightKeywords($row["bk_date"], $keyword);
				$highlighted_bk_time = highlightKeywords($row["bk_time"], $keyword);
				$highlighted_bk_phone_contact = highlightKeywords($row["bk_phone_contact"], $keyword);
				$highlighted_bk_num_of_passengers = highlightKeywords($row["bk_num_of_passengers"], $keyword);
				$highlighted_bk_pickup_unit_no = highlightKeywords($row["bk_pickup_unit_no"], $keyword);
				$highlighted_bk_pickup_street_no = highlightKeywords($row["bk_pickup_street_no"], $keyword);
				$highlighted_bk_pickup_street_name = highlightKeywords($row["bk_pickup_street_name"], $keyword);
				$highlighted_bk_pickup_suburb = highlightKeywords($row["bk_pickup_suburb"], $keyword);
				$highlighted_bk_destination = highlightKeywords($row["bk_destination"], $keyword);
				$highlighted_bk_pickup_time = highlightKeywords($row["bk_pickup_time"], $keyword);
				$highlighted_bk_pickup_date = highlightKeywords($row["bk_pickup_date"], $keyword);
				$highlighted_bk_status = highlightKeywords($row["bk_status"], $keyword);


	        	//format time & date before output
	        	$time = date('h:i a', strtotime($highlighted_bk_pickup_time));
	        	// trim leading 0 in time, eg. 09:30 am --> 9:30 am
	        	$time = ltrim($time, '0');
	        	
	        	$date = date('d/m/y', strtotime($highlighted_bk_pickup_date));

	            echo "<tr><td scope='row'>" . $highlighted_bk_number . "</td>" . 
	            	"<td>" . $highlighted_bk_name . "</td>" .
	            	"<td>" . $highlighted_bk_date . "</td>" .
	            	"<td>" . $highlighted_bk_time . "</td>" .
	            	"<td>" . $highlighted_bk_phone_contact . "</td>" .
	            	"<td>" . $highlighted_bk_num_of_passengers . "</td>" .
	            	"<td>" . $highlighted_bk_pickup_unit_no . "</td>" .
	            	"<td>" . $highlighted_bk_pickup_street_no . "</td>" .
	            	"<td>" . $highlighted_bk_pickup_street_name . "</td>" .
	            	"<td>" . $highlighted_bk_pickup_suburb . "</td>" .
	            	"<td>" . $highlighted_bk_destination . "</td>" .
	            	"<td>" . $time . "</td>" .
	            	"<td>" . $date . "</td>" .
	            	"<td>" . $highlighted_bk_status . "</td></tr>";
			}

	    	echo "</tbody></table>";
	    	
		} else {
			
			echo "<div class='container'>No bookings found.</div>";
		}


	} // end of if table exists

	$db_conn->close(); // close database connection

?>