<?php
// Include database connection (adjust as needed)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "amanimed";  // Updated to 'cheboin_disp', which should be your actual database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Capture user input
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = $_POST['role']; // Capture the role from the form

    // Check if passwords match
    if ($password !== $confirm_password) {
        echo "<script>alert('Passwords do not match');</script>";
    } else {
        // Insert into database with role
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (name, email, password, role) VALUES ('$name', '$email', '$hashed_password', '$role')";

        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Registration successful');</script>";
        } else {
            echo "<script>alert('Error: " . $conn->error . "');</script>";
        }
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Hospital</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body class="flex justify-center items-center min-h-screen" style="background-image: url('cheboin-dispensary-background.jpg'); background-size: cover; background-position: center center; background-attachment: fixed;">
    <div class="w-full max-w-md bg-white p-6 rounded-lg shadow-md opacity-90">
        <!-- Dispensary Icon and Title -->
        <div class="flex justify-center mb-6">
            <span class="material-icons text-6xl text-teal-600">
                local_pharmacy
            </span>
        </div>
        <h2 class="text-center text-2xl font-semibold text-gray-700 mb-6">Welcome to United Hospital</h2>

        <!-- Registration Form -->
        <form method="POST" action="register.php">
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-600">Full Name</label>
                <input type="text" id="name" name="name" class="w-full mt-2 px-4 py-2 border border-gray-300 rounded-md" required>
            </div>

            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-600">Email Address</label>
                <input type="email" id="email" name="email" class="w-full mt-2 px-4 py-2 border border-gray-300 rounded-md" required>
            </div>

            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-600">Password</label>
                <input type="password" id="password" name="password" class="w-full mt-2 px-4 py-2 border border-gray-300 rounded-md" required>
            </div>

            <div class="mb-4">
                <label for="confirm_password" class="block text-sm font-medium text-gray-600">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" class="w-full mt-2 px-4 py-2 border border-gray-300 rounded-md" required>
            </div>

            <!-- Role Selection (Patient or Doctor) -->
            <div class="mb-4">
                <label for="role" class="block text-sm font-medium text-gray-600">Register as</label>
                <select id="role" name="role" class="w-full mt-2 px-4 py-2 border border-gray-300 rounded-md">
                    <option value="patient" selected>Patient</option>
                    <option value="doctor">Hosiptal</option>
                </select>
            </div>

            <button type="submit" class="w-full bg-teal-600 text-white py-2 rounded-md hover:bg-teal-700">Register</button>
        </form>

        <!-- Additional Information -->
        <div class="mt-6 text-center text-sm text-gray-600">
            <p>Already have an account? <a href="login.php" class="text-teal-600 hover:text-teal-700">Login</a></p>
        </div>
    </div>
</body>
</html>
