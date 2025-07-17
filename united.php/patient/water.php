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

// Fetch all unique emails for dropdown (not needed since user will enter the email)
$emails = [];
$query = "SELECT DISTINCT email FROM water_intake_data";
$result = $conn->query($query);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $emails[] = $row['email'];
    }
}

// Handle form submission for water intake data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // If email form is submitted
    if (isset($_POST['email'])) {
        $email = $_POST['email'];

        // Store email in session
        $_SESSION['email'] = $email;

        // Redirect to refresh the page after storing email
        header("Location: water.php");
        exit();
    }

    // If water intake form is submitted
    if (isset($_SESSION['email']) && isset($_POST['date']) && isset($_POST['amount'])) {
        $date = $_POST['date'];
        $amount = $_POST['amount'];
        $email = $_SESSION['email']; // Get the logged-in user's email from the session

        // Function to insert water intake data into the database
        function addWaterIntakeData($date, $amount, $email) {
            global $conn;

            $sql = "INSERT INTO water_intake_data (date, amount, email, status) VALUES (?, ?, ?, 'fair')";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $date, $amount, $email);

            if ($stmt->execute()) {
                return "Water intake data has been successfully added!";
            } else {
                return "Error: Could not insert data. " . $conn->error;
            }
        }

        // Add water intake data to the database
        $confirmation_message = addWaterIntakeData($date, $amount, $email);
    }
}

// Fetch the selected user's water intake data
if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];
    $sql = "SELECT id, date, amount, status FROM water_intake_data WHERE email = ?";
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
    <title>Water Intake Tracking</title>
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
            <!-- Water Intake Form -->
            <h2 class="text-2xl font-semibold text-center text-gray-700 mb-6">Water Intake Tracking</h2>

            <form method="POST" action="">
                <div class="mb-4">
                    <label for="date" class="block text-gray-600 font-medium">Date:</label>
                    <input type="date" name="date" class="w-full p-3 border border-gray-300 rounded-lg mt-2" required>
                </div>

                <div class="mb-4">
                    <label for="amount" class="block text-gray-600 font-medium">Amount of Water (in liters):</label>
                    <input type="number" step="0.01" name="amount" class="w-full p-3 border border-gray-300 rounded-lg mt-2" required>
                </div>

                <button type="submit" class="w-full bg-blue-500 text-white p-3 rounded-lg mt-4 hover:bg-blue-600 focus:outline-none">Submit Water Intake</button>
            </form>

            <?php if (isset($confirmation_message)): ?>
                <div class="mt-4 text-center text-green-500 font-semibold"><?= htmlspecialchars($confirmation_message) ?></div>
            <?php endif; ?>

            <!-- Display Previous Water Intake Data -->
            <h3 class="text-xl font-semibold text-center text-gray-700 mt-6 mb-4">Your Previous Water Intake</h3>

            <?php if ($result && $result->num_rows > 0): ?>
                <table class="w-full table-auto text-gray-600">
                    <thead>
                        <tr>
                            <th class="border px-4 py-2">Date</th>
                            <th class="border px-4 py-2">Amount (liters)</th>
                            <th class="border px-4 py-2">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td class="border px-4 py-2"><?= htmlspecialchars($row['date']) ?></td>
                                <td class="border px-4 py-2"><?= htmlspecialchars($row['amount']) ?></td>
                                <td class="border px-4 py-2"><?= htmlspecialchars($row['status']) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="text-center text-gray-500">No water intake data available.</p>
            <?php endif; ?>
        <?php endif; ?>

        <!-- Back Button -->
        <div class="mt-4 text-center">
            <a href="patient.php" class="bg-gray-500 text-white p-3 rounded-lg hover:bg-gray-600">Back to Patient Page</a>
        </div>
    </div>

</body>
</html>
