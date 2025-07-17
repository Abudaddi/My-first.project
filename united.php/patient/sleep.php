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

// Handle form submission for sleep data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // If email form is submitted
    if (isset($_POST['email'])) {
        $email = $_POST['email'];

        // Store email in session
        $_SESSION['email'] = $email;

        // Redirect to refresh the page after storing email
        header("Location: sleep.php");
        exit();
    }

    // If sleep form is submitted
    if (isset($_SESSION['email']) && isset($_POST['hours_slept']) && isset($_POST['sleep_quality']) && isset($_POST['sleep_date'])) {
        $hours_slept = $_POST['hours_slept'];
        $sleep_quality = $_POST['sleep_quality'];
        $sleep_date = $_POST['sleep_date'];
        $email = $_SESSION['email']; // Get the logged-in user's email from the session

        // Function to insert sleep data into the database
        function addSleepData($hours_slept, $sleep_quality, $sleep_date, $email) {
            global $conn;

            $sql = "INSERT INTO sleep_data (hours_slept, sleep_quality, sleep_date, email) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("dsss", $hours_slept, $sleep_quality, $sleep_date, $email);

            if ($stmt->execute()) {
                return "Sleep data has been successfully added!";
            } else {
                return "Error: Could not insert data. " . $conn->error;
            }
        }

        // Add sleep data to the database
        $sleep_confirmation_message = addSleepData($hours_slept, $sleep_quality, $sleep_date, $email);
    }
}

// Fetch the selected user's sleep data
if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];
    $sql = "SELECT id, hours_slept, sleep_quality, sleep_date FROM sleep_data WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $sleep_result = $stmt->get_result();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sleep Tracking</title>
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
            <!-- Sleep Tracking Form -->
            <h2 class="text-2xl font-semibold text-center text-gray-700 mb-6">Sleep Tracking</h2>

            <form method="POST" action="">

                <div class="mb-4">
                    <label for="hours_slept" class="block text-gray-600 font-medium">Hours Slept:</label>
                    <input type="number" step="0.1" name="hours_slept" class="w-full p-3 border border-gray-300 rounded-lg mt-2" required>
                </div>

                <div class="mb-4">
                    <label for="sleep_quality" class="block text-gray-600 font-medium">Sleep Quality:</label>
                    <select name="sleep_quality" class="w-full p-3 border border-gray-300 rounded-lg mt-2" required>
                        <option value="Poor">Poor</option>
                        <option value="Fair">Fair</option>
                        <option value="Good">Good</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label for="sleep_date" class="block text-gray-600 font-medium">Date:</label>
                    <input type="date" name="sleep_date" class="w-full p-3 border border-gray-300 rounded-lg mt-2" required>
                </div>

                <button type="submit" class="w-full bg-green-500 text-white p-3 rounded-lg mt-4 hover:bg-green-600 focus:outline-none">Submit Sleep Data</button>
            </form>

            <?php if (isset($sleep_confirmation_message)): ?>
                <div class="mt-4 text-center text-green-500 font-semibold"><?= htmlspecialchars($sleep_confirmation_message) ?></div>
            <?php endif; ?>

            <!-- Display Previous Sleep Data -->
            <h3 class="text-xl font-semibold text-center text-gray-700 mt-6 mb-4">Your Previous Sleep Data</h3>

            <?php if ($sleep_result && $sleep_result->num_rows > 0): ?>
                <table class="w-full table-auto text-gray-600">
                    <thead>
                        <tr>
                            <th class="border px-4 py-2">Hours Slept</th>
                            <th class="border px-4 py-2">Sleep Quality</th>
                            <th class="border px-4 py-2">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $sleep_result->fetch_assoc()): ?>
                            <tr>
                                <td class="border px-4 py-2"><?= htmlspecialchars($row['hours_slept']) ?></td>
                                <td class="border px-4 py-2"><?= htmlspecialchars($row['sleep_quality']) ?></td>
                                <td class="border px-4 py-2"><?= htmlspecialchars($row['sleep_date']) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="text-center text-gray-500">No sleep data available.</p>
            <?php endif; ?>

        <?php endif; ?>

        <!-- Back Button -->
        <div class="mt-4 text-center">
            <a href="patient.php" class="bg-gray-500 text-white p-3 rounded-lg hover:bg-gray-600">Back to Patient Page</a>
        </div>

    </div>

</body>
</html>
