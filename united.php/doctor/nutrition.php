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

// Fetch all unique emails from the nutrition_data table
$query = "SELECT DISTINCT email FROM nutrition_data";
$result = $conn->query($query);
$emails = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $emails[] = $row['email'];
    }
}

// Handle form submission for updating data
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $id = $_POST['id'];
    $food_name = $_POST['food_name'];
    $date = $_POST['date'];
    $protein_food = $_POST['protein_food'];
    $vitamin_food = $_POST['vitamin_food'];
    $carb_food = $_POST['carb_food'];
    $status = $_POST['status'];

    // Update the record in the nutrition_data table
    $update_query = "UPDATE nutrition_data SET food_name = ?, date = ?, protein_food = ?, vitamin_food = ?, carb_food = ?, status = ? WHERE id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("ssssssi", $food_name, $date, $protein_food, $vitamin_food, $carb_food, $status, $id);

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
    $query = "SELECT * FROM nutrition_data WHERE email = ?";
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
    <title>Nutrition Data - View & Update</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">

    <!-- Dropdown to select email -->
    <form method="GET" action="nutrition.php">
        <label for="email" class="block text-gray-700">Select Email:</label>
        <select name="email" id="email" class="mt-2 mb-4 p-2 border border-gray-300 rounded">
            <option value="">-- Select Email --</option>
            <?php foreach ($emails as $email) { ?>
                <option value="<?php echo $email; ?>" <?php echo (isset($_GET['email']) && $_GET['email'] == $email) ? 'selected' : ''; ?>><?php echo $email; ?></option>
            <?php } ?>
        </select>
        <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded">View Data</button>
    </form>

    <?php if (isset($update_message)) { ?>
        <div class="bg-green-100 text-green-800 p-4 my-4 rounded">
            <?php echo $update_message; ?>
        </div>
    <?php } ?>

    <?php if (isset($_GET['email'])) { ?>
        <!-- Table displaying nutrition data for selected email -->
        <h2 class="text-2xl font-semibold text-gray-800 my-6">Nutrition Data for <?php echo htmlspecialchars($selected_email); ?></h2>

        <table class="min-w-full bg-white shadow-md rounded-lg">
            <thead class="bg-gray-200">
                <tr>
                    <th class="p-4">ID</th>
                    <th class="p-4">Food Name</th>
                    <th class="p-4">Date</th>
                    <th class="p-4">Protein Food</th>
                    <th class="p-4">Vitamin Food</th>
                    <th class="p-4">Carb Food</th>
                    <th class="p-4">Status</th>
                    <th class="p-4">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data as $row) { ?>
                    <tr>
                        <form method="POST" action="nutrition.php">
                            <td class="p-4"><?php echo $row['id']; ?></td>
                            <td class="p-4">
                                <input type="text" name="food_name" value="<?php echo $row['food_name']; ?>" class="p-2 border border-gray-300 rounded" required>
                            </td>
                            <td class="p-4">
                                <input type="date" name="date" value="<?php echo $row['date']; ?>" class="p-2 border border-gray-300 rounded" required>
                            </td>
                            <td class="p-4">
                                <input type="text" name="protein_food" value="<?php echo $row['protein_food']; ?>" class="p-2 border border-gray-300 rounded">
                            </td>
                            <td class="p-4">
                                <input type="text" name="vitamin_food" value="<?php echo $row['vitamin_food']; ?>" class="p-2 border border-gray-300 rounded">
                            </td>
                            <td class="p-4">
                                <input type="text" name="carb_food" value="<?php echo $row['carb_food']; ?>" class="p-2 border border-gray-300 rounded">
                            </td>
                            <td class="p-4">
                                <select name="status" class="p-2 border border-gray-300 rounded">
                                    <option value="Approved" <?php echo ($row['status'] == 'Approved') ? 'selected' : ''; ?>>Approved</option>
                                    <option value="Fair" <?php echo ($row['status'] == 'Fair') ? 'selected' : ''; ?>>Fair</option>
                                    <option value="Not Approved" <?php echo ($row['status'] == 'Not Approved') ? 'selected' : ''; ?>>Not Approved</option>
                                </select>
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
    <?php } ?>

    <!-- Back Button to Dashboard -->
    <div class="mt-4 text-center">
        <a href="dashboard.php" class="bg-gray-500 text-white p-3 rounded-lg hover:bg-gray-600">Back to Dashboard</a>
    </div>

</body>
</html>
