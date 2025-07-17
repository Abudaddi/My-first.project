<?php
session_start();

// Database connection
$host = 'localhost'; // change to your database host
$username = 'root';  // your database username
$password = '';      // your database password
$dbname = 'amanimed';  // your database name

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the user is logged in, if not redirect to login page
if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit();
}

// Fetch all unique emails from the sleep_data table
$query = "SELECT DISTINCT email FROM sleep_data";
$result = $conn->query($query);
$emails = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $emails[] = $row['email'];
    }
}

// Handle form submission for updating data
$update_message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $id = $_POST['id'];
    $hours_slept = $_POST['hours_slept'];
    $sleep_quality = $_POST['sleep_quality'];
    $sleep_date = $_POST['sleep_date'];

    // Update the record in the sleep_data table
    $update_query = "UPDATE sleep_data SET hours_slept = ?, sleep_quality = ?, sleep_date = ? WHERE id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("dssi", $hours_slept, $sleep_quality, $sleep_date, $id);

    if ($stmt->execute()) {
        $update_message = "Record updated successfully!";
    } else {
        $update_message = "Error updating record.";
    }
}

// Fetch data for the selected email (if set)
$data = [];
if (isset($_GET['email'])) {
    $selected_email = $_GET['email'];
    $query = "SELECT * FROM sleep_data WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $selected_email);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sleep Data - View & Update</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">

    <!-- Back Button -->
    <a href="dashboard.php" class="bg-blue-600 text-white py-2 px-4 rounded mb-4 inline-block">Back to Dashboard</a>

    <!-- Dropdown to select email -->
    <form method="GET" action="">
        <label for="email" class="block text-gray-700">Select Email:</label>
        <select name="email" id="email" class="mt-2 mb-4 p-2 border border-gray-300 rounded">
            <option value="">-- Select Email --</option>
            <?php foreach ($emails as $email) { ?>
                <option value="<?php echo $email; ?>" <?php echo (isset($_GET['email']) && $_GET['email'] == $email) ? 'selected' : ''; ?>><?php echo $email; ?></option>
            <?php } ?>
        </select>
        <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded">View Data</button>
    </form>

    <!-- Update message -->
    <?php if ($update_message) { ?>
        <div class="bg-green-100 text-green-800 p-4 my-4 rounded">
            <?php echo $update_message; ?>
        </div>
    <?php } ?>

    <!-- Table displaying sleep data for selected email -->
    <?php if (isset($_GET['email']) && !empty($data)) { ?>
        <h2 class="text-2xl font-semibold text-gray-800 my-6">Sleep Data for <?php echo htmlspecialchars($selected_email); ?></h2>

        <table class="min-w-full bg-white shadow-md rounded-lg">
            <thead class="bg-gray-200">
                <tr>
                    <th class="p-4">ID</th>
                    <th class="p-4">Hours Slept</th>
                    <th class="p-4">Sleep Quality</th>
                    <th class="p-4">Sleep Date</th>
                    <th class="p-4">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data as $row) { ?>
                    <tr>
                        <form method="POST" action="">
                            <td class="p-4"><?php echo $row['id']; ?></td>
                            <td class="p-4">
                                <input type="number" name="hours_slept" value="<?php echo $row['hours_slept']; ?>" class="p-2 border border-gray-300 rounded" step="0.1" required>
                            </td>
                            <td class="p-4">
                                <input type="text" name="sleep_quality" value="<?php echo $row['sleep_quality']; ?>" class="p-2 border border-gray-300 rounded" required>
                            </td>
                            <td class="p-4">
                                <input type="date" name="sleep_date" value="<?php echo $row['sleep_date']; ?>" class="p-2 border border-gray-300 rounded" required>
                            </td>
                            <td class="p-4">
                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                <button type="submit" name="update" class="bg-blue-600 text-white py-2 px-4 rounded">Update</button>
                            </td>
                        </form>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php } elseif (isset($_GET['email']) && empty($data)) { ?>
        <p class="text-red-500">No sleep data available for this email.</p>
    <?php } ?>

</body>
</html>
