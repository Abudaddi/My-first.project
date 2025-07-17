<?php
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "amanimed";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check for connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch available appointments along with patient emails
function getAppointments() {
    global $conn;
    $appointments = [];

    $sql = "SELECT a.id, u.email 
            FROM appointments a
            JOIN users u ON a.patient_id = u.id"; // Joining users table to fetch patient email
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $appointments[] = [
                'id' => $row['id'],
                'email' => $row['email']
            ];
        }
    }

    return $appointments;
}

// Function to fetch symptoms
function fetchSymptomsAndAddDiagnosis($appointment_id) {
    global $conn;

    $sql = "SELECT symptoms FROM diagnoses WHERE appointment_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $appointment_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Display symptoms
        echo "<div class='mb-4'><strong>Symptoms:</strong><p class='text-gray-700'>" . htmlspecialchars($row['symptoms']) . "</p></div>";

        // Diagnosis input form
        echo "<div class='mb-4'>
                <label for='diagnosis' class='block text-gray-600 font-medium'>Enter Diagnosis:</label>
                <input type='text' name='diagnosis' class='w-full p-3 border border-gray-300 rounded-lg mt-2' required>
              </div>";
        echo "<button type='submit' class='w-full bg-blue-500 text-white p-3 rounded-lg mt-4 hover:bg-blue-600 focus:outline-none'>Submit Diagnosis</button>";
    } else {
        echo "<div class='text-red-500 font-semibold mt-4'>No symptoms found for this appointment ID.</div>";
    }
}

// Function to insert or update diagnosis
function addDiagnosis($appointment_id, $diagnosis) {
    global $conn;

    $check_sql = "SELECT id FROM dios WHERE appointment_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("i", $appointment_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        // Update diagnosis
        $update_sql = "UPDATE dios SET diagnosis_text = ? WHERE appointment_id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("si", $diagnosis, $appointment_id);
        if ($update_stmt->execute()) {
            return "<div class='text-green-500 font-semibold mt-4'>Diagnosis has been successfully updated!</div>";
        } else {
            return "<div class='text-red-500 font-semibold mt-4'>Error: Could not update diagnosis. " . $conn->error . "</div>";
        }
    } else {
        // Insert new diagnosis
        $insert_sql = "INSERT INTO dios (appointment_id, diagnosis_text) VALUES (?, ?)";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("is", $appointment_id, $diagnosis);
        if ($insert_stmt->execute()) {
            return "<div class='text-green-500 font-semibold mt-4'>Diagnosis has been successfully added!</div>";
        } else {
            return "<div class='text-red-500 font-semibold mt-4'>Error: Could not insert diagnosis. " . $conn->error . "</div>";
        }
    }
}

// Handle form submission
$confirmation_message = '';
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['appointment_id']) && isset($_POST['diagnosis'])) {
    $appointment_id = intval($_POST['appointment_id']);  
    $diagnosis = trim($_POST['diagnosis']);

    // Prevent empty input
    if (!empty($appointment_id) && !empty($diagnosis)) {
        $confirmation_message = addDiagnosis($appointment_id, $diagnosis);
    } else {
        $confirmation_message = "<div class='text-red-500 font-semibold mt-4'>Please enter a valid diagnosis.</div>";
    }
}

// Get selected appointment ID
$appointment_id = isset($_GET['appointment_id']) ? intval($_GET['appointment_id']) : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Symptoms & Add Diagnosis</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">

    <div class="max-w-lg mx-auto bg-white p-6 rounded-lg shadow-lg">
        <h2 class="text-2xl font-semibold text-center text-gray-700 mb-6">View Symptoms & Add Diagnosis</h2>

        <!-- Form to get symptoms -->
        <form method="GET" action="">
            <div class="mb-4">
                <label for="appointment_id" class="block text-gray-600 font-medium">Select Appointment:</label>
                <select name="appointment_id" class="w-full p-3 border border-gray-300 rounded-lg mt-2" required>
                    <option value="">-- Select Appointment --</option>
                    <?php
                    $appointments = getAppointments();
                    foreach ($appointments as $appointment) {
                        echo "<option value='{$appointment['id']}' " . ($appointment_id == $appointment['id'] ? "selected" : "") . ">
                                ID: {$appointment['id']} - Email: {$appointment['email']}
                              </option>";
                    }
                    ?>
                </select>
            </div>
            <button type="submit" class="w-full bg-blue-500 text-white p-3 rounded-lg mt-4 hover:bg-blue-600 focus:outline-none">View Symptoms</button>
        </form>

        <!-- Show symptoms and diagnosis form -->
        <?php
        if (!empty($appointment_id)) {
            echo "<form method='POST' action=''>";
            fetchSymptomsAndAddDiagnosis($appointment_id);
            echo "<input type='hidden' name='appointment_id' value='$appointment_id'>";
            echo "</form>";
        }

        // Display confirmation message
        if (!empty($confirmation_message)) {
            echo $confirmation_message;
        }
        ?>

        <!-- Back to Dashboard -->
        <div class="mt-4 text-center">
            <a href="dashboard.php" class="bg-gray-500 text-white p-3 rounded-lg hover:bg-gray-600">Back to Dashboard</a>
        </div>
    </div>

</body>
</html>
