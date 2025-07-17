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

// Handle form submission for exercise data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // If email form is submitted
    if (isset($_POST['email'])) {
        $email = $_POST['email'];

        // Store email in session
        $_SESSION['email'] = $email;

        // Redirect to refresh the page after storing email
        header("Location: exercise.php");
        exit();
    }

    // If exercise form is submitted
    if (isset($_SESSION['email']) && isset($_POST['exercise_type']) && isset($_POST['duration']) && isset($_POST['exercise_date'])) {
        $exercise_type = $_POST['exercise_type'];
        $duration = $_POST['duration'];
        $exercise_date = $_POST['exercise_date'];
        $email = $_SESSION['email']; // Get the logged-in user's email from the session

        // Function to insert exercise data into the database
        function addExerciseData($exercise_type, $duration, $exercise_date, $email) {
            global $conn;

            $sql = "INSERT INTO exercise_data (exercise_type, duration, date, email, status) VALUES (?, ?, ?, ?, 'not approved')";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("siss", $exercise_type, $duration, $exercise_date, $email);

            if ($stmt->execute()) {
                return "Exercise data has been successfully added!";
            } else {
                return "Error: Could not insert data. " . $conn->error;
            }
        }

        // Add exercise data to the database
        $exercise_confirmation_message = addExerciseData($exercise_type, $duration, $exercise_date, $email);
    }
}

// Fetch the selected user's exercise data
if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];
    $sql = "SELECT id, exercise_type, duration, date, status FROM exercise_data WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $exercise_result = $stmt->get_result();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exercise Tracking</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">

    <div class="max-w-lg mx-auto bg-white p-6 rounded-lg shadow-lg">
        <?php if (!isset($_SESSION['email'])): ?>
            <!-- Email Entry Form -->
            <h2 class="text-2xl font-semibold text-center text-gray-700 mb-6">Enter Your Email to Proceed</h2>

            <form method="POST" action="">
                <div class="mb-4">
                    <label for="email" class="block text-gray-600 font-medium">Email Address:</label>
                    <input type="email" name="email" class="w-full p-3 border border-gray-300 rounded-lg mt-2" required>
                </div>

                <button type="submit" class="w-full bg-blue-500 text-white p-3 rounded-lg mt-4 hover:bg-blue-600 focus:outline-none">Submit</button>
            </form>

        <?php else: ?>
            <!-- Exercise Form -->
            <h2 class="text-2xl font-semibold text-center text-gray-700 mb-6">Exercise Tracking</h2>

            <form method="POST" action="">

                <div class="mb-4">
                    <label for="exercise_type" class="block text-gray-600 font-medium">Exercise Type:</label>
                    <input type="text" name="exercise_type" class="w-full p-3 border border-gray-300 rounded-lg mt-2" required>
                </div>

                <div class="mb-4">
                    <label for="duration" class="block text-gray-600 font-medium">Duration (in minutes):</label>
                    <input type="number" name="duration" class="w-full p-3 border border-gray-300 rounded-lg mt-2" required>
                </div>

                <div class="mb-4">
                    <label for="exercise_date" class="block text-gray-600 font-medium">Date:</label>
                    <input type="date" name="exercise_date" class="w-full p-3 border border-gray-300 rounded-lg mt-2" required>
                </div>

                <button type="submit" class="w-full bg-green-500 text-white p-3 rounded-lg mt-4 hover:bg-green-600 focus:outline-none">Submit Exercise</button>
            </form>

            <?php if (isset($exercise_confirmation_message)): ?>
                <div class="mt-4 text-center text-green-500 font-semibold"><?= htmlspecialchars($exercise_confirmation_message) ?></div>
            <?php endif; ?>

            <!-- Display Previous Exercise Data -->
            <h3 class="text-xl font-semibold text-center text-gray-700 mt-6 mb-4">Your Previous Exercise</h3>

            <?php if ($exercise_result && $exercise_result->num_rows > 0): ?>
                <table class="w-full table-auto text-gray-600">
                    <thead>
                        <tr>
                            <th class="border px-4 py-2">Exercise Type</th>
                            <th class="border px-4 py-2">Duration (minutes)</th>
                            <th class="border px-4 py-2">Date</th>
                            <th class="border px-4 py-2">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $exercise_result->fetch_assoc()): ?>
                            <tr>
                                <td class="border px-4 py-2"><?= htmlspecialchars($row['exercise_type']) ?></td>
                                <td class="border px-4 py-2"><?= htmlspecialchars($row['duration']) ?></td>
                                <td class="border px-4 py-2"><?= htmlspecialchars($row['date']) ?></td>
                                <td class="border px-4 py-2"><?= htmlspecialchars($row['status']) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="text-center text-gray-500">No exercise data available.</p>
            <?php endif; ?>

        <?php endif; ?>

        <!-- Back Button -->
        <div class="mt-4 text-center">
            <a href="patient.php" class="bg-gray-500 text-white p-3 rounded-lg hover:bg-gray-600">Back to Patient Page</a>
        </div>

    </div>

</body>
</html>
