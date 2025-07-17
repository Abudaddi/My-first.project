<?php
// Assuming you already have a database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "amanimed";

// Create a connection to the database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check if connection is successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to fetch prescriptions for a specific appointment
function fetchPrescription($appointment_id) {
    global $conn;

    // Query to get prescriptions for the given appointment_id
    $sql = "SELECT * FROM prescriptions WHERE appointment_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $appointment_id); // Bind appointment_id as an integer
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Display prescription details
        while ($row = $result->fetch_assoc()) {
            echo "<div class='mb-6 p-6 bg-white rounded-lg shadow-lg border border-gray-200'>";
            echo "<h3 class='text-2xl font-semibold text-gray-800 mb-4'>Prescription Details</h3>";
            
            echo "<div class='grid grid-cols-1 sm:grid-cols-2 gap-6 mb-4'>";
            echo "<div><strong class='text-gray-700'>Medication:</strong> " . htmlspecialchars($row['medication']) . "</div>";
            echo "<div><strong class='text-gray-700'>Dosage:</strong> " . htmlspecialchars($row['dosage']) . "</div>";
            echo "<div><strong class='text-gray-700'>Instructions:</strong> " . nl2br(htmlspecialchars($row['instructions'])) . "</div>";
            echo "<div><strong class='text-gray-700'>Diagnosis:</strong> " . nl2br(htmlspecialchars($row['diagnosis'])) . "</div>"; // Display diagnosis
            echo "<div><strong class='text-gray-700'>Prescribed On:</strong> " . $row['created_at'] . "</div>";
            echo "</div>";
            echo "</div>";
        }
    } else {
        echo "<div class='text-red-500 font-semibold mt-4'>No prescriptions found for this appointment ID.</div>";
    }
}

// Handle form submission for appointment_id
$prescription_message = '';
$appointment_id = '';
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['appointment_id'])) {
    $appointment_id = $_GET['appointment_id'];  // Appointment ID from form input
    if (!empty($appointment_id)) {
        // Fetch and display prescription for the given appointment_id
        $prescription_message = fetchPrescription($appointment_id);
    } else {
        $prescription_message = "<div class='text-red-500 font-semibold mt-4'>Please provide a valid appointment ID.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Prescription</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">

    <div class="max-w-lg mx-auto bg-white p-6 rounded-lg shadow-lg">
        <h2 class="text-2xl font-semibold text-center text-gray-700 mb-6">View Prescription</h2>

        <!-- Form to view prescription by appointment_id -->
        <form method="GET" action="">
            <div class="mb-4">
                <label for="appointment_id" class="block text-gray-600 font-medium">Enter Appointment ID:</label>
                <input type="number" name="appointment_id" value="<?= htmlspecialchars($appointment_id) ?>" class="w-full p-3 border border-gray-300 rounded-lg mt-2" required>
            </div>
            <button type="submit" class="w-full bg-blue-500 text-white p-3 rounded-lg mt-4 hover:bg-blue-600 focus:outline-none">View Prescription</button>
        </form>

        <?php
        // Show the prescription details or error message if any
        if ($prescription_message != '') {
            echo $prescription_message;
        }
        ?>

    </div>

</body>
</html>
