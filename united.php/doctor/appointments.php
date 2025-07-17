<?php
session_start();

// Check if the user is logged in and is a doctor
if (!isset($_SESSION['user_email']) || $_SESSION['user_role'] !== 'doctor') {
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

// Get the logged-in doctor's ID
$doctor_id = $_SESSION['user_id'];

// Fetch appointments for the doctor
$sql = "SELECT appointments.id, users.name AS patient_name, users.email AS patient_email, appointments.appointment_date, appointments.status
        FROM appointments
        JOIN users ON appointments.patient_id = users.id
        WHERE appointments.doctor_id = ?
        ORDER BY appointments.appointment_date ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$result = $stmt->get_result();

// Process the form submission if AJAX is used
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['appointment_id']) && isset($_POST['status'])) {
    $appointment_id = $_POST['appointment_id'];
    $status = $_POST['status'];

    // Update the appointment status in the database
    $update_sql = "UPDATE appointments SET status = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("si", $status, $appointment_id);
    
    if ($update_stmt->execute()) {
        // Return success response
        echo json_encode(['success' => true, 'message' => 'Appointment status updated successfully.']);
    } else {
        // Return error response
        echo json_encode(['success' => false, 'message' => 'Error updating appointment status.']);
    }

    $update_stmt->close();
    exit();  // Stop further PHP processing since we handled the AJAX request
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>View Appointments</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- jQuery -->
</head>
<body class="bg-gray-100">

  <!-- Main Content -->
  <div class="flex justify-center items-center min-h-screen p-6">
    <div class="bg-white shadow-sm rounded-lg p-6 w-full max-w-4xl">
        <h2 class="text-2xl font-semibold text-gray-800 mb-4">Appointments</h2>

        <!-- Show message if available -->
        <div id="message" class="hidden p-3 mb-4 rounded-md"></div>

        <?php if ($result->num_rows > 0): ?>
            <table class="min-w-full bg-white border border-gray-300 rounded-md shadow-md">
                <thead>
                    <tr>
                        <th class="py-2 px-4 border-b text-left text-gray-700">Patient Name</th>
                        <th class="py-2 px-4 border-b text-left text-gray-700">Patient Email</th>
                        <th class="py-2 px-4 border-b text-left text-gray-700">Appointment Date</th>
                        <th class="py-2 px-4 border-b text-left text-gray-700">Status</th>
                        <th class="py-2 px-4 border-b text-left text-gray-700">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td class="py-2 px-4 border-b text-gray-800"><?php echo htmlspecialchars($row['patient_name']); ?></td>
                            <td class="py-2 px-4 border-b text-gray-800"><?php echo htmlspecialchars($row['patient_email']); ?></td>
                            <td class="py-2 px-4 border-b text-gray-800"><?php echo date('F j, Y, g:i a', strtotime($row['appointment_date'])); ?></td>
                            <td class="py-2 px-4 border-b text-gray-800">
                                <form class="update-form" data-appointment-id="<?php echo $row['id']; ?>" method="POST">
                                    <select name="status" class="bg-gray-50 border border-gray-300 rounded-md px-2 py-1">
                                        <option value="Pending" <?php echo $row['status'] === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                        <option value="Accepted" <?php echo $row['status'] === 'Accepted' ? 'selected' : ''; ?>>Accepted</option>
                                        <option value="Rejected" <?php echo $row['status'] === 'Rejected' ? 'selected' : ''; ?>>Rejected</option>
                                    </select>
                                </form>
                            </td>
                            <td class="py-2 px-4 border-b text-gray-800">
                                <button type="button" class="bg-teal-600 text-white px-4 py-2 rounded-md hover:bg-teal-700 update-status">Update</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-gray-600">You have no appointments scheduled at the moment.</p>
        <?php endif; ?>
    </div>
  </div>

  <script>
    // AJAX to handle the status update without reloading the page
    $(document).on('click', '.update-status', function() {
        var form = $(this).closest('tr').find('.update-form');
        var status = form.find('select[name="status"]').val();
        var appointment_id = form.data('appointment-id');

        // Send AJAX request to update the status
        $.ajax({
            url: 'appointments.php', // PHP script to handle status update (same page)
            method: 'POST',
            data: {
                appointment_id: appointment_id,
                status: status
            },
            success: function(response) {
                // Display the message returned from PHP
                var messageDiv = $('#message');
                var message = JSON.parse(response);

                // Show success or error message
                messageDiv.removeClass('hidden');
                if (message.success) {
                    messageDiv.addClass('bg-green-100 text-green-700');
                    messageDiv.text(message.message);
                } else {
                    messageDiv.addClass('bg-red-100 text-red-700');
                    messageDiv.text(message.message);
                }

                // Optionally, update the status text in the table
                form.closest('tr').find('td').eq(3).text(status);
            }
        });
    });
  </script>

</body>
</html>
