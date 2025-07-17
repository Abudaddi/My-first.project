<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "amanimed");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch last 4 digits from form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $last_4_digits = $_POST['last_4_digits'];

    // Query to search by last 4 digits
    $sql = "SELECT * FROM vaccination WHERE RIGHT(birth_cert, 4) = '$last_4_digits'";
    $result = $conn->query($sql);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Status</title>

    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <h2 class="text-center">Vaccination Application Status</h2>

    <?php if ($result->num_rows > 0) { ?>
        <table class="table table-bordered mt-4">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Age</th>
                    <th>Vaccine</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo $row['age']; ?></td>
                        <td><?php echo htmlspecialchars($row['vaccine']); ?></td>
                        <td>
                            <span class="badge badge-<?php echo ($row['status'] == 'Approved') ? 'success' : (($row['status'] == 'Denied') ? 'danger' : 'warning'); ?>">
                                <?php echo $row['status']; ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($row['status'] == 'Approved' || $row['status'] == 'Denied') { ?>
                                <p>Please visit our nearest institution to receive your vaccination.</p>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php } else { echo "<p class='text-center text-danger'>No application found!</p>"; } ?>

</div>

</body>
</html>

<?php $conn->close(); ?>
