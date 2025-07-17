<?php
include('db.php');

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect form data
    $name = $_POST['name'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $birth_cert = $_POST['birth_cert'];
    $location = $_POST['location'];
    $vaccine_id = $_POST['vaccine_id'];

    // Check if the patient has already been approved for vaccination
    $check_query = "SELECT status FROM vaccination WHERE birth_cert = '$birth_cert'";
    $check_result = $conn->query($check_query);

    if ($check_result && $check_result->num_rows > 0) {
        $row = $check_result->fetch_assoc();
        if ($row['status'] == 'Approved') {
            echo "You have already been approved for vaccination.";
        } else {
            // Insert the patient's booking if not approved
            $insert_query = "INSERT INTO vaccination (name, age, gender, birth_cert, location, vaccine_id) 
                             VALUES ('$name', $age, '$gender', '$birth_cert', '$location', $vaccine_id)";
            if ($conn->query($insert_query) === TRUE) {
                // Check if vaccine is available and reduce stock
                $update_stock_query = "UPDATE vaccines SET stock = stock - 1 WHERE id = $vaccine_id AND stock > 0";
                if ($conn->query($update_stock_query) === TRUE) {
                    // Redirect to the same page after form submission
                    header("Location: book_vaccine.php?success=1");
                    exit();
                } else {
                    echo "Error updating stock: " . $conn->error;
                }
            } else {
                echo "Error: " . $conn->error;
            }
        }
    } else {
        // Insert the patient's booking if no previous entry
        $insert_query = "INSERT INTO vaccination (name, age, gender, birth_cert, location, vaccine_id) 
                         VALUES ('$name', $age, '$gender', '$birth_cert', '$location', $vaccine_id)";
        if ($conn->query($insert_query) === TRUE) {
            // Check if vaccine is available and reduce stock
            $update_stock_query = "UPDATE vaccines SET stock = stock - 1 WHERE id = $vaccine_id AND stock > 0";
            if ($conn->query($update_stock_query) === TRUE) {
                // Redirect to the same page after form submission
                header("Location: book_vaccine.php?success=1");
                exit();
            } else {
                echo "Error updating stock: " . $conn->error;
            }
        } else {
            echo "Error: " . $conn->error;
        }
    }
}

// Fetch available vaccines
$vaccine_query = "SELECT * FROM vaccines WHERE stock > 0";
$vaccine_result = $conn->query($vaccine_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Vaccine</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Book Your Vaccine</h2>

        <!-- Display success message if query parameter "success" is 1 -->
        <?php
        if (isset($_GET['success']) && $_GET['success'] == 1) {
            echo "<div class='alert alert-success'>Your vaccine application has been submitted successfully!</div>";
        }
        ?>

        <form action="book_vaccine.php" method="POST" class="mt-4">
            <div class="mb-3">
                <label for="name" class="form-label">Full Name</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="mb-3">
                <label for="age" class="form-label">Age</label>
                <input type="number" class="form-control" id="age" name="age" required>
            </div>
            <div class="mb-3">
                <label for="gender" class="form-label">Gender</label>
                <select class="form-select" id="gender" name="gender" required>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="birth_cert" class="form-label">Birth Certificate Number</label>
                <input type="text" class="form-control" id="birth_cert" name="birth_cert" required>
            </div>
            <div class="mb-3">
                <label for="location" class="form-label">Location</label>
                <input type="text" class="form-control" id="location" name="location" required>
            </div>
            <div class="mb-3">
                <label for="vaccine_id" class="form-label">Select Vaccine</label>
                <select class="form-select" id="vaccine_id" name="vaccine_id" required>
                    <?php
                    if ($vaccine_result->num_rows > 0) {
                        while ($vaccine = $vaccine_result->fetch_assoc()) {
                            echo "<option value='{$vaccine['id']}'>{$vaccine['name']}</option>";
                        }
                    } else {
                        echo "<option value='' disabled>No vaccines available</option>";
                    }
                    ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>
