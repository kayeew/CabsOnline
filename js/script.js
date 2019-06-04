// Kayee 2019

 function createRequest() {
    var xhr = false;
    if (window.XMLHttpRequest) {
        xhr = new XMLHttpRequest();
    }
    else if (window.ActiveXObject) {
        xhr = new ActiveXObject("Microsoft.XMLHTTP");
    }
    return xhr;
} // end function createRequest()

// post booking form data
function booking() {
    var xhr = createRequest();
    if(xhr) {
        var name = document.getElementById("name").value;

        // generate booking date/time
        // get client's current date/time for booking date & booking time
        var currentDate = new Date();
        var dd = String(currentDate.getDate()).padStart(2, '0');
        var mm = String(currentDate.getMonth() + 1).padStart(2, '0');
        var yyyy = currentDate.getFullYear();
        var bookingDate = yyyy + '-' + mm + '-' + dd; // formated this way because MySQL uses yyyy-mm-dd format

        var currentTime = new Date();
        var hours = currentTime.getHours();
        var minutes = currentTime.getMinutes();
        var seconds = currentTime.getSeconds();
        var bookingTime = hours + ':' + minutes + ':' + seconds; // formated this way because MySQL uses HH:MM:SS format

        var phoneContact = document.getElementById("phoneContact").value;
        var numPassengers = document.getElementById("numPassengers").value;
        var unitNum = document.getElementById("unitNum").value;
        var streetNum = document.getElementById("streetNum").value;
        var streetName = document.getElementById("streetName").value;
        var suburb = document.getElementById("suburb").value;
        var destination = document.getElementById("destination").value;
        var pickupTime = document.getElementById("pickupTime").value;
        var pickupDate = document.getElementById("pickupDate").value;

        var validated = false;

        // validate if pickupTime & pickupDate is not before current date & current time
        if(pickupDate == bookingDate) { // validate if pickupDate is today

            if(validateTime(pickupTime, bookingTime)) { // then check if time is not before current time

            	validated = true;
            }

        } else if(validateDate(pickupDate, bookingDate)) { // if pickupDate is after today

            validated = true;
        }

        if(validated) {

            var url = "src/bookingProcess.php";
            var params = "name=" + name
                + "&bookingDate=" + bookingDate
                + "&bookingTime=" + bookingTime
                + "&phoneContact=" + phoneContact
                + "&numPassengers=" + numPassengers
                + "&unitNum=" + unitNum
                + "&streetNum=" + streetNum
                + "&streetName=" + streetName
                + "&suburb=" + suburb
                + "&destination=" + destination
                + "&pickupTime=" + pickupTime
                + "&pickupDate=" + pickupDate;

            xhr.open("POST", url, true);

            //Send the proper header information along with the request
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() { // Call a function when the state changes.
                if(xhr.readyState == 4 && xhr.status == 200) {
                    resetForm();
                    $("#msg").html(xhr.responseText);
                    $('#bookingModal').modal('hide'); // hide booking form modal
                    $('#bookingConfirmationModal').modal('show'); // show confirmation message
                }
            }
            xhr.send(params);

        } else {
            nextPrev(-1);
        }
    }
}

// function to empty form after booking request
function resetForm() {
    document.getElementById("name").value = "";
    document.getElementById("phoneContact").value  = "";
    document.getElementById("numPassengers").value = "";
    document.getElementById("unitNum").value = "";
    document.getElementById("streetNum").value = "";
    document.getElementById("streetName").value = "";
    document.getElementById("suburb").value = "";
    document.getElementById("destination").value = "";
    document.getElementById("pickupTime").value = "";
    document.getElementById("pickupDate").value = "";
    document.getElementById("datetimeErrorMessage").value = "";

    resetMultiStepModal();
}

// function to assign taxi to a booking number
function assign() {
    var xhr = createRequest();

    if(xhr) {
        var bookingNumber = document.getElementById("bookingNumber").value;

        var url = "src/assignCab.php";
        var params = "bknumber=" + bookingNumber;

        xhr.open("GET", url+"?"+params, true);
        xhr.onreadystatechange = function() { // Call a function when the state changes.
            if(xhr.readyState == 4 && xhr.status == 200) {
                $("#assignMessages").html(xhr.responseText);
                $('#assignedModal').modal('show'); // show confirmation message
				displayUpdatedListAfterAssign(); // display updated bookings
            }
        }
        xhr.send(null);
    }
}

// function to search booking list
function search() {
    var xhr = createRequest();

    if(xhr) {
        var search = document.getElementById("searchKeyword").value;

        var url = "src/search.php";
        var params = "search=" + search;

        xhr.open("GET", url+"?"+params, true);
        xhr.onreadystatechange = function() { // Call a function when the state changes.
            if(xhr.readyState == 4 && xhr.status == 200) {
                $("#bookingList").html(xhr.responseText);
            }
        }
        xhr.send(null);
    }
}

// on change event listener for display options dropdown list to show filter booking list
document.querySelector("#displayOptions").addEventListener("change", function() {

    var xhr = createRequest();

    if(xhr) {
        var element = document.getElementById("displayOptions");
        var displayOptions = element.options[element.selectedIndex].value;

        var url = "src/displayOptions.php";
        var params = "option=" + displayOptions;

        xhr.open("GET", url+"?"+params, true);
        xhr.onreadystatechange = function() { // Call a function when the state changes.
            if(xhr.readyState == 4 && xhr.status == 200) {
                $("#bookingList").html(xhr.responseText);
            }
        }
        xhr.send(null);
    }

});

// displays updated list of bookings after assigning a booking
function displayUpdatedListAfterAssign() {

    var xhr = createRequest();

    if(xhr) {
        var displayOptions = "all";

        var url = "src/displayOptions.php";
        var params = "option=" + displayOptions;

        xhr.open("GET", url+"?"+params, true);
        xhr.onreadystatechange = function() { // Call a function when the state changes.
            if(xhr.readyState == 4 && xhr.status == 200) {
                $("#bookingList").html(xhr.responseText);
            }
        }
        xhr.send(null);
    }
}


function validateDate(date, todaysdate) {
    if (date < todaysdate) {
        var errorMessage = "The Pickup Date cannot be before today.";
    	document.getElementById("datetimeErrorMessage").innerHTML = errorMessage; // display error message
          document.getElementById("pickupDate").value = ""; // clear pickup date field
          return false;
     }
    return true;
}

function validateTime(inputTime, currentTime) {
    if (inputTime < currentTime) {
		var errorMessage = "The Pickup Time cannot be before current time.";
    	document.getElementById("datetimeErrorMessage").innerHTML = errorMessage; // display error message

		document.getElementById("pickupTime").value = ""; // clear pickup time field
		return false;
    }
    return true;
}
