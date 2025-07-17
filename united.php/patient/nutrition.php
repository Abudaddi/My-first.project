<?php
// Start the session
session_start();

// Database connection settings
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "amanimed";  // Database name: 'brenda'

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle email validation and form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // If the email is being submitted
    if (isset($_POST['email'])) {
        $email = $_POST['email'];

        // Check if the email exists in the 'users' table
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Email exists, store it in the session
            $_SESSION['email'] = $email;
            // Redirect to refresh the page after storing email
            header("Location: nutrition.php");
            exit();
        } else {
            // Email not found
            $error_message = "Email not found in our records. Access Denied!";
        }
    }
    
    // If the nutrition form is submitted
    if (isset($_SESSION['email']) && isset($_POST['name'])) {
        $name = $_POST['name'];
        $date = $_POST['date'];
        $protein_food = $_POST['protein_food'];
        $vitamin_food = $_POST['vitamin_food'];
        $carb_food = $_POST['carb_food'];
        $email = $_SESSION['email']; // Get the logged-in user's email from the session

        // Function to insert nutrition data into the database
        function addNutritionData($name, $date, $protein_food, $vitamin_food, $carb_food, $email) {
            global $conn;

            $sql = "INSERT INTO nutrition_data (food_name, date, protein_food, vitamin_food, carb_food, email)
                    VALUES (?, ?, ?, ?, ?, ?)";

            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssss", $name, $date, $protein_food, $vitamin_food, $carb_food, $email);

            if ($stmt->execute()) {
                return "Nutrition data has been successfully added!";
            } else {
                return "Error: Could not insert data. " . $conn->error;
            }
        }

        // Add nutrition data to the database
        $confirmation_message = addNutritionData($name, $date, $protein_food, $vitamin_food, $carb_food, $email);
    }
}

// Fetch the logged-in user's nutrition data
if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];
    $sql = "SELECT food_name, date, protein_food, vitamin_food, carb_food, status FROM nutrition_data WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Health Tracking System</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">

    <div class="max-w-lg mx-auto bg-white p-6 rounded-lg shadow-lg">
        <?php if (!isset($_SESSION['email'])): ?>
            <!-- Email Prompt Form -->
            <h2 class="text-2xl font-semibold text-center text-gray-700 mb-6">Enter Your Email to Proceed</h2>

            <form method="POST" action="">
                <div class="mb-4">
                    <label for="email" class="block text-gray-600 font-medium">Email Address:</label>
                    <input type="email" name="email" class="w-full p-3 border border-gray-300 rounded-lg mt-2" required>
                </div>

                <button type="submit" class="w-full bg-blue-500 text-white p-3 rounded-lg mt-4 hover:bg-blue-600 focus:outline-none">Submit</button>
            </form>

            <?php if (isset($error_message)): ?>
                <div class="mt-4 text-center text-red-500 font-semibold"><?= htmlspecialchars($error_message) ?></div>
            <?php endif; ?>
        <?php else: ?>
            <!-- Nutrition Data Form -->
            <h2 class="text-2xl font-semibold text-center text-gray-700 mb-6">Health Tracking - Add Nutrition Data</h2>

            <form method="POST" action="">
                <div class="mb-4">
                    <label for="name" class="block text-gray-600 font-medium">Meal Name:</label>
                    <input type="text" name="name" class="w-full p-3 border border-gray-300 rounded-lg mt-2" required>
                </div>

                <div class="mb-4">
                    <label for="date" class="block text-gray-600 font-medium">Date:</label>
                    <input type="date" name="date" class="w-full p-3 border border-gray-300 rounded-lg mt-2" required>
                </div>

                <div class="mb-4">
                    <label for="protein_food" class="block text-gray-600 font-medium">Protein Food (e.g. Eggs):</label>
                    <input type="text" name="protein_food" class="w-full p-3 border border-gray-300 rounded-lg mt-2" required>
                </div>

                <div class="mb-4">
                    <label for="vitamin_food" class="block text-gray-600 font-medium">Vitamin Food (e.g. Carrots):</label>
                    <input type="text" name="vitamin_food" class="w-full p-3 border border-gray-300 rounded-lg mt-2" required>
                </div>

                <div class="mb-4">
                    <label for="carb_food" class="block text-gray-600 font-medium">Carb Food (e.g. Rice):</label>
                    <input type="text" name="carb_food" class="w-full p-3 border border-gray-300 rounded-lg mt-2" required>
                </div>

                <button type="submit" class="w-full bg-blue-500 text-white p-3 rounded-lg mt-4 hover:bg-blue-600 focus:outline-none">Submit Nutrition Data</button>
            </form>

            <?php if (isset($confirmation_message)): ?>
                <div class="mt-4 text-center text-green-500 font-semibold"><?= htmlspecialchars($confirmation_message) ?></div>
            <?php endif; ?>

            <!-- Display Previous Nutrition Data -->
            <h3 class="text-xl font-semibold text-center text-gray-700 mt-6 mb-4">Your Previous Nutrition Data</h3>

            <?php if ($result && $result->num_rows > 0): ?>
                <table class="w-full table-auto text-gray-600">
                    <thead>
                        <tr>
                            <th class="border px-4 py-2">Meal Name</th>
                            <th class="border px-4 py-2">Date</th>
                            <th class="border px-4 py-2">Protein Food</th>
                            <th class="border px-4 py-2">Vitamin Food</th>
                            <th class="border px-4 py-2">Carb Food</th>
                            <th class="border px-4 py-2">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td class="border px-4 py-2"><?= htmlspecialchars($row['food_name']) ?></td>
                                <td class="border px-4 py-2"><?= htmlspecialchars($row['date']) ?></td>
                                <td class="border px-4 py-2"><?= htmlspecialchars($row['protein_food']) ?></td>
                                <td class="border px-4 py-2"><?= htmlspecialchars($row['vitamin_food']) ?></td>
                                <td class="border px-4 py-2"><?= htmlspecialchars($row['carb_food']) ?></td>
                                <td class="border px-4 py-2"><?= htmlspecialchars($row['status']) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="text-center text-gray-500">No nutrition data available.</p>
            <?php endif; ?>
        <?php endif; ?>
        
        <!-- Back Button -->
        <div class="mt-4 text-center">
            <a href="patient.php" class="bg-gray-500 text-white p-3 rounded-lg hover:bg-gray-600">Back to Patient Page</a>
        </div>
    </div>

</body>
</html>
