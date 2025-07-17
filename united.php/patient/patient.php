<?php
session_start();

// Check if the user is logged in, if not redirect to the login page
if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- Add Font Awesome for icons -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
  <style>
    /* Background Image and Overlay */
    body {
        background-image: url('istockphoto-1629901654-612x612.webp'); /* Add your image path here */
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        height: 100vh;
        color: #333;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    
    /* Overlay for better visibility */
    .overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0, 0, 0, 0.3); /* Light overlay */
    }
    
    /* To ensure content is above overlay */
    .content {
        position: relative;
        z-index: 10;
    }
  </style>
</head>
<body class="bg-gray-100">

  <div class="overlay"></div> <!-- Overlay for the background image -->
  
  <div class="flex h-screen content">

    <!-- Sidebar -->
    <div class="w-64 bg-blue-800 text-white flex flex-col">
      <div class="flex items-center justify-center p-4 text-xl font-semibold">
        <span>Dashboard</span>
      </div>

      <!-- Display User Email -->
      <div class="bg-blue-700 text-white text-center py-4">
        <p class="text-sm">Logged in as:</p>
        <p class="font-semibold text-lg"><?php echo $_SESSION['user_email']; ?></p>
      </div>

      <nav class="flex flex-col py-4 space-y-2">
        <!-- Book Appointment -->
        <a href="book_appointment.php" class="flex items-center text-white px-4 py-2 hover:bg-blue-700 rounded-md transition-colors">
          <i class="fas fa-calendar-plus h-5 w-5 mr-3"></i> 
          <span>Book Appointment</span>
        </a>

        <!-- View Appointments -->
        <a href="view_appointments.php" class="flex items-center text-white px-4 py-2 hover:bg-blue-700 rounded-md transition-colors">
          <i class="fas fa-calendar-check h-5 w-5 mr-3"></i> 
          <span>View Appointments</span>
        </a>

        <!-- Diagnosis -->
        <a href="diagnosis.php" class="flex items-center text-white px-4 py-2 hover:bg-blue-700 rounded-md transition-colors">
          <i class="fas fa-stethoscope h-5 w-5 mr-3"></i> 
          <span>Diagnosis</span>
        </a>

        <!-- Prescriptions -->
        <a href="medical_records.php" class="flex items-center text-white px-4 py-2 hover:bg-blue-700 rounded-md transition-colors">
          <i class="fas fa-folder-medical h-5 w-5 mr-3"></i> 
          <span>Prescriptions</span>
        </a>

        <!-- Logout Link -->
        <a href="logout.php" class="flex items-center text-white px-4 py-2 hover:bg-blue-700 rounded-md mt-auto transition-colors">
          <i class="fas fa-sign-out-alt h-5 w-5 mr-3"></i> 
          <span>Logout</span>
        </a>
      </nav>
    </div>

    <!-- Main Content -->
    <div class="flex-1 p-6">
      <div class="bg-white shadow-sm rounded-lg p-6">
        <h2 class="text-2xl font-semibold text-gray-800 mb-4">Welcome to the Dashboard</h2>
        
        <!-- Section for Nutrition -->
        <div id="nutrition" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6 mb-6">
          <!-- Nutrition Card -->
          <a href="nutrition.php" class="block p-6 bg-blue-100 rounded-lg shadow-lg hover:bg-blue-200 transition-all">
            <div class="flex items-center">
              <i class="fas fa-apple-alt text-blue-600 h-8 w-8 mr-4"></i>
              <div>
                <h3 class="text-xl font-semibold text-gray-800">Nutrition</h3>
                <p class="text-gray-600">Track your nutrition intake.</p>
              </div>
            </div>
          </a>

          <!-- Water Intake Card -->
          <a href="water.php" class="block p-6 bg-green-100 rounded-lg shadow-lg hover:bg-green-200 transition-all">
            <div class="flex items-center">
              <i class="fas fa-tint text-green-600 h-8 w-8 mr-4"></i>
              <div>
                <h3 class="text-xl font-semibold text-gray-800">Water Intake</h3>
                <p class="text-gray-600">Monitor your daily water consumption.</p>
              </div>
            </div>
          </a>

          <!-- Exercise Card -->
          <a href="exercise.php" class="block p-6 bg-yellow-100 rounded-lg shadow-lg hover:bg-yellow-200 transition-all">
            <div class="flex items-center">
              <i class="fas fa-dumbbell text-yellow-600 h-8 w-8 mr-4"></i>
              <div>
                <h3 class="text-xl font-semibold text-gray-800">Exercise</h3>
                <p class="text-gray-600">Track your daily physical activities.</p>
              </div>
            </div>
          </a>

          <!-- Sleep Card -->
          <a href="sleep.php" class="block p-6 bg-purple-100 rounded-lg shadow-lg hover:bg-purple-200 transition-all">
            <div class="flex items-center">
              <i class="fas fa-bed text-purple-600 h-8 w-8 mr-4"></i>
              <div>
                <h3 class="text-xl font-semibold text-gray-800">Sleep</h3>
                <p class="text-gray-600">Monitor your sleep patterns.</p>
              </div>
            </div>
          </a>
        </div>

      </div>
    </div>
  </div>

</body>
</html>
