<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "amanimed");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get form data
$name = $_POST['name'];
$age = $_POST['age'];
$gender = $_POST['gender'];
$birth_cert = $_POST['birth_cert'];
$location = $_POST['location'];
$vaccine = $_POST['vaccine'];

// Insert into database
$sql = "INSERT INTO vaccination (name, age, gender, birth_cert, location, vaccine) 
        VALUES ('$name', '$age', '$gender', '$birth_cert', '$location', '$vaccine')";

if ($conn->query($sql) === TRUE) {
    echo "<script>alert('Vaccination application successful!'); window.location.href='r.php';</script>";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

// Close connection
$conn->close();
?>
