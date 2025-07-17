<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>United </title>

    <!-- Include Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    <!-- Include FontAwesome for healthcare icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">

    <!-- Optional: Custom CSS -->
    <style>
        body {
            padding-top: 100px; /* Ensure space for fixed navbar */
            background-image: url('istockphoto-1766603296-1024x1024.jpg'); /* Update this with your background image */
            background-size: cover; /* Ensure the background image covers the entire screen */
            background-position: center center; /* Center the image */
            background-attachment: fixed; /* Keep the background fixed during scroll */
            background-color: transparent; /* Fallback color */
        }

        /* Adjust the Navbar: Set it to the topmost part and same color as hero */
        .navbar {
            background-color: rgba(0, 123, 255, 0.7) !important; /* Semi-transparent blue background */
            padding: 0px 0; /* Adjust padding to make navbar thinner */
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1000; /* Ensure it stays on top of other elements */
        }

        .navbar .navbar-brand {
            font-size: 1.5rem;
        }

        .navbar .nav-link {
            font-size: 1rem;
        }

        .container {
            margin-top: 50px;
        }

        .hero-section {
            background-color: rgba(0, 123, 255, 0.7); /* Semi-transparent blue background */
            color: white;
            padding: 50px 0;
            text-align: center;
            border-radius: 8px;
        }

        .hero-section h1 {
            font-size: 3rem;
            margin-bottom: 30px;
        }

        .hero-section p {
            font-size: 1.2rem;
        }

        .card {
            margin-top: 30px;
        }

        .icon {
            font-size: 3rem;
            color: #007bff;
            margin-bottom: 20px;
        }

        .btn-custom {
            background-color: #007bff;
            color: white;
            font-size: 1.1rem;
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">United Med Hospital</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="register.php">Register</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">Login</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <h1>Welcome to United Med Hospital</h1>
            <p>Your health is our priority. Access medical services and medications with ease at AmaniMed</p>
        </div>
    </section>

    <!-- Main Content -->
    <section class="container text-center">
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <i class="fas fa-user-plus icon"></i>
                        <h5 class="card-title">Register</h5>
                        <p class="card-text">Create an account to get access to exclusive health services and medication records.</p>
                        <a href="register.php" class="btn btn-custom">Register Now</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <i class="fas fa-sign-in-alt icon"></i>
                        <h5 class="card-title">Login</h5>
                        <p class="card-text">Already a member? Log in to access your personalized health services and records.</p>
                        <a href="login.php" class="btn btn-custom">Login</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <i class="fas fa-syringe icon"></i>
                        <h5 class="card-title">Register for a Vaccination</h5>
                        <p class="card-text">Protect yourself and your loved ones. Register for a vaccination today.</p>
                        <a href="book_vaccine.php" class="btn btn-custom">Register Now</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-4 mt-5">
        <p>&copy; <?php echo date("Y"); ?> united Med Hospital. All Rights Reserved.</p>
    </footer>

    <!-- Include Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
