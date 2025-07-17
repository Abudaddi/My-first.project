<?php
session_start();

// Check if the user is logged in and is a patient
if (!isset($_SESSION['user_email']) || $_SESSION['user_role'] !== 'patient') {
    header("Location: login.php");
    exit();
}

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

// Get the logged-in patient's ID
$patient_id = $_SESSION['user_id'];

// Fetch appointments for the patient
$sql = "SELECT appointments.id, doctors.name AS doctor_name, appointments.appointment_date, appointments.status
        FROM appointments
        JOIN users AS doctors ON appointments.doctor_id = doctors.id
        WHERE appointments.patient_id = ?
        ORDER BY appointments.appointment_date ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Appointments</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

  <!-- Main Content -->
  <div class="flex justify-center items-center min-h-screen p-6">
    <div class="bg-white shadow-sm rounded-lg p-6 w-full max-w-4xl">
        <h2 class="text-2xl font-semibold text-gray-800 mb-4">My Appointments</h2>

        <?php if ($result->num_rows > 0): ?>
            <table class="min-w-full bg-white border border-gray-300 rounded-md shadow-md">
                <thead>
                    <tr>
                        <th class="py-2 px-4 border-b text-left text-gray-700">Appointment ID</th>
                        <th class="py-2 px-4 border-b text-left text-gray-700">Doctor Name</th>
                        <th class="py-2 px-4 border-b text-left text-gray-700">Appointment Date</th>
                        <th class="py-2 px-4 border-b text-left text-gray-700">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td class="py-2 px-4 border-b text-gray-800"><?php echo htmlspecialchars($row['id']); ?></td>
                            <td class="py-2 px-4 border-b text-gray-800"><?php echo htmlspecialchars($row['doctor_name']); ?></td>
                            <td class="py-2 px-4 border-b text-gray-800"><?php echo date('F j, Y, g:i a', strtotime($row['appointment_date'])); ?></td>
                            <td class="py-2 px-4 border-b text-gray-800"><?php echo htmlspecialchars($row['status']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-gray-600">You have no appointments scheduled at the moment.</p>
        <?php endif; ?>
    </div>
  </div>

</body>
</html>
