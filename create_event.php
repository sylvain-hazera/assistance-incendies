<?php

include_once "./includes/dbConnect.php";


// Get form data
$id = uniqid();
$name = $_POST["name"];
$state = $_POST["state"];
$start_date = $_POST["start_date"];

// Insert event into database
$sql = "INSERT INTO event (id, name, state, start_date) VALUES ('$id','$name','$state','$start_date')";

if ($conn->query($sql) === TRUE) {
    echo "Event created successfully";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
