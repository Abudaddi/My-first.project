<?php
session_start();
$conn = new mysqli("localhost", "root", "", "amanimed");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Process Vaccine Addition
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_vaccine'])) {
    $vaccine_name = $_POST['vaccine_name'];
    $stock = intval($_POST['stock']);

    $insert_vaccine_sql = "INSERT INTO vaccines (name, stock) VALUES (?, ?)";
    $stmt = $conn->prepare($insert_vaccine_sql);
    $stmt->bind_param("si", $vaccine_name, $stock);
    if ($stmt->execute()) {
        echo "<script>alert('Vaccine added successfully!'); window.location.href='dashboard.php';</script>";
    } else {
        echo "Error: " . $conn->error;
    }
}

// Process Status Update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_status'])) {
    $id = $_POST['id'];
    $new_status = $_POST['status'];

    $update_sql = "UPDATE vaccination SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("si", $new_status, $id);
    if ($stmt->execute()) {
        echo "<script>alert('Status updated successfully!'); window.location.href='dashboard.php';</script>";
    } else {
        echo "Error updating status: " . $conn->error;
    }
}

// Fetch Vaccination Applications
$appointments_sql = "SELECT v.id, v.name, v.age, v.gender, v.birth_cert, v.location, 
                    vac.name AS vaccine, v.status 
                    FROM vaccination v 
                    LEFT JOIN vaccines vac ON v.vaccine_id = vac.id 
                    ORDER BY v.applied_at DESC";
$appointments_result = $conn->query($appointments_sql);

// Fetch Vaccine Inventory
$vaccine_sql = "SELECT * FROM vaccines ORDER BY name ASC";
$vaccine_result = $conn->query($vaccine_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Dashboard - Vaccination Applications</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-4">
    <h2 class="text-center mb-4">Doctor Dashboard - Vaccination Applications</h2>

    <!-- Vaccination Applications Table -->
    <table class="table table-bordered">
        <thead class="thead-dark">
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Age</th>
                <th>Gender</th>
                <th>Birth Cert</th>
                <th>Location</th>
                <th>Vaccine</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $appointments_result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo $row['age']; ?></td>
                    <td><?php echo $row['gender']; ?></td>
                    <td><?php echo htmlspecialchars($row['birth_cert']); ?></td>
                    <td><?php echo htmlspecialchars($row['location']); ?></td>
                    <td><?php echo htmlspecialchars($row['vaccine'] ?? "Not Assigned"); ?></td>
                    <td>
                        <span class="badge badge-<?php echo ($row['status'] == 'Approved') ? 'success' : (($row['status'] == 'Denied') ? 'danger' : 'warning'); ?>">
                            <?php echo $row['status']; ?>
                        </span>
                    </td>
                    <td>
                        <form method="POST" class="form-inline">
                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                            <select name="status" class="form-control form-control-sm mr-2">
                                <option value="Pending" <?php if ($row['status'] == 'Pending') echo 'selected'; ?>>Pending</option>
                                <option value="Approved" <?php if ($row['status'] == 'Approved') echo 'selected'; ?>>Approved</option>
                                <option value="Denied" <?php if ($row['status'] == 'Denied') echo 'selected'; ?>>Denied</option>
                            </select>
                            <button type="submit" name="update_status" class="btn btn-primary btn-sm">Update</button>
                        </form>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <!-- Vaccine Inventory Management -->
    <h3 class="mt-5">Vaccine Inventory</h3>
    <table class="table table-bordered">
        <thead class="thead-dark">
            <tr>
                <th>ID</th>
                <th>Vaccine Name</th>
                <th>Stock</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $vaccine_result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo $row['stock']; ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <!-- Add New Vaccine Form -->
    <h4 class="mt-4">Add a New Vaccine</h4>
    <form method="POST">
        <div class="form-group">
            <label>Vaccine Name:</label>
            <input type="text" name="vaccine_name" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Stock:</label>
            <input type="number" name="stock" class="form-control" required>
        </div>
        <button type="submit" name="add_vaccine" class="btn btn-success">Add Vaccine</button>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php $conn->close(); ?>
