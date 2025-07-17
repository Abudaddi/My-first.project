<?php
session_start(); // Start the session

// Database connection (adjust as needed)
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

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Query to check if user exists in the database
    $sql = "SELECT id, name, email, password, role FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $name, $email_db, $hashed_password, $role);

    if ($stmt->num_rows > 0) {
        // User found, now check password
        $stmt->fetch();
        if (password_verify($password, $hashed_password)) {
            // Password is correct, start session
            $_SESSION['user_id'] = $id;
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $email; // Store email in session
            $_SESSION['user_role'] = $role;

            // Redirect based on role
            if ($role == 'doctor') {
                header("Location: doctor/dashboard.php");
            } else if ($role == 'patient') {
                header("Location: patient/patient.php");
            }
            exit();
        } else {
            $error = "Invalid credentials!";
        }
    } else {
        $error = "No user found with that email!";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Koshy Med Hosp</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body class="bg-gray-50 flex justify-center items-center min-h-screen" style="background-image: url('cheboin-dispensary-background.jpg'); background-size: cover; background-position: center center; background-attachment: fixed;">
    <div class="w-full max-w-md bg-white p-6 rounded-lg shadow-md opacity-90">
        <!-- Dispensary Icon and Title -->
        
        <h2 class="text-center text-2xl font-semibold text-gray-700 mb-6">Welcome to united Med</h2>

        <!-- Login Form -->
        <form method="POST" action="login.php">
            <?php
            if (isset($error)) {
                echo "<p class='text-red-500 text-center mb-4'>$error</p>";
            }
            ?>
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-600">Email Address</label>
                <input type="email" id="email" name="email" class="w-full mt-2 px-4 py-2 border border-gray-300 rounded-md" required>
            </div>

            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-600">Password</label>
                <input type="password" id="password" name="password" class="w-full mt-2 px-4 py-2 border border-gray-300 rounded-md" required>
            </div>

            <button type="submit" class="w-full bg-teal-600 text-white py-2 rounded-md hover:bg-teal-700">Login</button>
        </form>

        <!-- Additional Information -->
        <div class="mt-6 text-center text-sm text-gray-600">
            <p>Don't have an account? <a href="register.php" class="text-teal-600 hover:text-teal-700">Register</a></p>
        </div>
    </div>
</body>
</html>
