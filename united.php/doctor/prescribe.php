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

// Function to add a prescription
function addPrescription($appointment_id, $medication, $dosage, $instructions, $diagnosis) {
    global $conn;

    // Prepare SQL query to insert prescription data
    $sql = "INSERT INTO prescriptions (appointment_id, medication, dosage, instructions, diagnosis) 
            VALUES (?, ?, ?, ?, ?)";

    // Prepare statement
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issss", $appointment_id, $medication, $dosage, $instructions, $diagnosis);

    // Execute the query and check if insertion is successful
    if ($stmt->execute()) {
        return "<div class='text-green-500 font-semibold mt-4'>Prescription has been successfully added!</div>";
    } else {
        return "<div class='text-red-500 font-semibold mt-4'>Error: Could not add prescription. " . $conn->error . "</div>";
    }
}

// Handle form submission for prescription
$confirmation_message = '';
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['appointment_id']) && isset($_POST['medication']) && isset($_POST['dosage'])) {
    $appointment_id = $_POST['appointment_id'];  // Appointment ID from form input
    $medication = $_POST['medication'];          // Medication from form input
    $dosage = $_POST['dosage'];                  // Dosage from form input
    $instructions = isset($_POST['instructions']) ? $_POST['instructions'] : ''; // Optional instructions
    $diagnosis = isset($_POST['diagnosis']) ? $_POST['diagnosis'] : ''; // Optional diagnosis

    // Call function to add the prescription
    $confirmation_message = addPrescription($appointment_id, $medication, $dosage, $instructions, $diagnosis);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Prescription</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">

    <div class="max-w-lg mx-auto bg-white p-6 rounded-lg shadow-lg">
        <h2 class="text-2xl font-semibold text-center text-gray-700 mb-6">Add Prescription</h2>

        <!-- Form to add a prescription -->
        <form method="POST" action="">
            <div class="mb-4">
                <label for="appointment_id" class="block text-gray-600 font-medium">Appointment ID:</label>
                <input type="number" name="appointment_id" class="w-full p-3 border border-gray-300 rounded-lg mt-2" required>
            </div>
            <div class="mb-4">
                <label for="medication" class="block text-gray-600 font-medium">Medication:</label>
                <input type="text" name="medication" class="w-full p-3 border border-gray-300 rounded-lg mt-2" required>
            </div>
            <div class="mb-4">
                <label for="dosage" class="block text-gray-600 font-medium">Dosage:</label>
                <input type="text" name="dosage" class="w-full p-3 border border-gray-300 rounded-lg mt-2" required>
            </div>
            <div class="mb-4">
                <label for="instructions" class="block text-gray-600 font-medium">Instructions (Optional):</label>
                <textarea name="instructions" class="w-full p-3 border border-gray-300 rounded-lg mt-2" rows="4"></textarea>
            </div>
            <div class="mb-4">
                <label for="diagnosis" class="block text-gray-600 font-medium">Diagnosis (Optional):</label>
                <textarea name="diagnosis" class="w-full p-3 border border-gray-300 rounded-lg mt-2" rows="4"></textarea>
            </div>
            <button type="submit" class="w-full bg-blue-500 text-white p-3 rounded-lg mt-4 hover:bg-blue-600 focus:outline-none">Add Prescription</button>
        </form>

        <?php
        // Show the confirmation message if any
        if ($confirmation_message != '') {
            echo $confirmation_message;
        }
        ?>

    </div>

</body>
</html>
