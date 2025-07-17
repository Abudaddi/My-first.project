<?php
session_start();

// Check if the user is logged in, and that the user is a patient
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

// Fetch doctors from the users table
$sql = "SELECT id, name FROM users WHERE role = 'doctor'";
$result = $conn->query($sql);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $doctor_id = $_POST['doctor_id'];
    $appointment_date = $_POST['appointment_date'];
    $patient_id = $_SESSION['user_id'];

    // Insert appointment into the database
    $stmt = $conn->prepare("INSERT INTO appointments (patient_id, doctor_id, appointment_date) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $patient_id, $doctor_id, $appointment_date);

    if ($stmt->execute()) {
        $success_message = "Appointment booked successfully!";
    } else {
        $error_message = "There was an error booking the appointment.";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Book Appointment</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

  <!-- Sidebar (same as previous one) -->
  <div class="flex h-screen">
    <div class="w-64 bg-blue-800 text-white flex flex-col">
      <div class="flex items-center justify-center p-4 text-xl font-semibold">
        <span>Book Appointment</span>
      </div>
      <!-- User Email Display -->
      <div class="bg-blue-700 text-white text-center py-4">
        <p class="text-sm">Logged in as:</p>
        <p class="font-semibold text-lg"><?php echo $_SESSION['user_email']; ?></p>
      </div>
    </div>

    <!-- Main Content -->
    <div class="flex-1 p-6">
      <div class="bg-white shadow-sm rounded-lg p-6">
        <h2 class="text-2xl font-semibold text-gray-800 mb-4">Book an Appointment</h2>

        <?php
        if (isset($success_message)) {
            echo "<div class='bg-green-200 text-green-800 p-4 rounded-md mb-6'>$success_message</div>";
        }

        if (isset($error_message)) {
            echo "<div class='bg-red-200 text-red-800 p-4 rounded-md mb-6'>$error_message</div>";
        }
        ?>

        <!-- Appointment Booking Form -->
        <form method="POST" action="book_appointment.php">
          <div class="mb-4">
            <label for="doctor_id" class="block text-sm font-medium text-gray-600">Select Doctor</label>
            <select name="doctor_id" id="doctor_id" class="w-full mt-2 px-4 py-2 border border-gray-300 rounded-md" required>
              <option value="">Select a Doctor</option>
              <?php
              if ($result->num_rows > 0) {
                  while ($row = $result->fetch_assoc()) {
                      echo "<option value='{$row['id']}'>{$row['name']}</option>";
                  }
              }
              ?>
            </select>
          </div>

          <div class="mb-4">
            <label for="appointment_date" class="block text-sm font-medium text-gray-600">Appointment Date and Time</label>
            <input type="datetime-local" name="appointment_date" id="appointment_date" class="w-full mt-2 px-4 py-2 border border-gray-300 rounded-md" required>
          </div>

          <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700">Book Appointment</button>
        </form>
      </div>
    </div>
  </div>

</body>
</html>
