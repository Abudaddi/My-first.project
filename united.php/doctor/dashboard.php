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
  <!-- Add Heroicons for icons -->
  <script src="https://cdn.jsdelivr.net/npm/heroicons@1.0.6/umd/heroicons.min.js"></script>
  <!-- Add Font Awesome for icons -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100" style="background-image: url('istockphoto-1629901654-612x612.webp'); background-size: cover; background-position: center; background-attachment: fixed;"> 

  <!-- Sidebar -->
  <div class="flex h-screen">
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
        <a href="appointments.php" class="flex items-center text-white px-4 py-2 hover:bg-blue-700 rounded-md">
          <!-- Heroicon for appointments -->
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M16 2v4M8 2v4M4 6h16M4 10h16M4 14h16M4 18h16M4 22h16"></path>
          </svg>
          View Appointments
        </a>
        <a href="patients.php" class="flex items-center text-white px-4 py-2 hover:bg-blue-700 rounded-md">
          <!-- Heroicon for patients -->
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 2a6 6 0 00-6 6v10a6 6 0 006 6h6a6 6 0 006-6V8a6 6 0 00-6-6H12z"></path>
          </svg>
          Manage Patients
        </a>
       
        <a href="prescribe.php" class="flex items-center text-white px-4 py-2 hover:bg-blue-700 rounded-md">
          <!-- Heroicon for prescribing -->
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 10h-6.586l1.293-1.293a1 1 0 10-1.414-1.414L12 9.586 9.707 7.293a1 1 0 10-1.414 1.414L10.586 10H4a1 1 0 000 2h6.586l-1.293 1.293a1 1 0 101.414 1.414L12 14.414l2.293 2.293a1 1 0 101.414-1.414L13.414 12H20a1 1 0 000-2z"></path>
          </svg>
          Prescribe
        </a>
        <!-- Logout Link -->
        <a href="logout.php" class="flex items-center text-white px-4 py-2 hover:bg-blue-700 rounded-md mt-auto">
          <!-- Heroicon for logout -->
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M17 7l5 5-5 5M3 12h14m4 0a9 9 0 11-9-9 9 9 0 019 9z"></path>
          </svg>
          Logout
        </a>
      </nav>
    </div>

    <!-- Main Content -->
    <div class="flex-1 p-6">
      <div class="bg-white shadow-sm rounded-lg p-6">
        <h2 class="text-2xl font-semibold text-gray-800 mb-4">Welcome to the Dashboard</h2>

        <!-- Section for Clickable Tailwind Cards -->
        <div id="cards" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6 mb-6">

          <!-- View Patients Card -->
          <a href="patients.php" class="block p-6 bg-blue-100 rounded-lg shadow-lg hover:bg-blue-200 transition-all">
            <div class="flex items-center">
              <i class="fas fa-user-injured text-blue-600 h-8 w-8 mr-4"></i>
              <div>
                <h3 class="text-xl font-semibold text-gray-800">View Patients</h3>
                <p class="text-gray-600">View and manage patient information.</p>
                <span class="text-blue-600">View</span>
              </div>
            </div>
          </a>

          <!-- View Nutrition Card -->
          <a href="nutrition.php" class="block p-6 bg-green-100 rounded-lg shadow-lg hover:bg-green-200 transition-all">
            <div class="flex items-center">
              <i class="fas fa-apple-alt text-green-600 h-8 w-8 mr-4"></i>
              <div>
                <h3 class="text-xl font-semibold text-gray-800">View Nutrition</h3>
                <p class="text-gray-600">Track and manage patient nutrition.</p>
                <span class="text-green-600">View</span>
              </div>
            </div>
          </a>

          <!-- View Water Intake Card -->
          <a href="water.php" class="block p-6 bg-teal-100 rounded-lg shadow-lg hover:bg-teal-200 transition-all">
            <div class="flex items-center">
              <i class="fas fa-tint text-teal-600 h-8 w-8 mr-4"></i>
              <div>
                <h3 class="text-xl font-semibold text-gray-800">View Water Intake</h3>
                <p class="text-gray-600">Monitor daily water consumption.</p>
                <span class="text-teal-600">View</span>
              </div>
            </div>
          </a>

          <!-- View Exercise Card -->
          <a href="exercise.php" class="block p-6 bg-yellow-100 rounded-lg shadow-lg hover:bg-yellow-200 transition-all">
            <div class="flex items-center">
              <i class="fas fa-dumbbell text-yellow-600 h-8 w-8 mr-4"></i>
              <div>
                <h3 class="text-xl font-semibold text-gray-800">View Exercise</h3>
                <p class="text-gray-600">Track physical activity for patients.</p>
                <span class="text-yellow-600">View</span>
              </div>
            </div>
          </a>
          <!-- Add View Vaccination Applications Card -->
<a href="d.php" class="block p-6 bg-indigo-100 rounded-lg shadow-lg hover:bg-indigo-200 transition-all">
  <div class="flex items-center">
    <i class="fas fa-syringe text-indigo-600 h-8 w-8 mr-4"></i>
    <div>
      <h3 class="text-xl font-semibold text-gray-800">View Vaccination Applications</h3>
      <p class="text-gray-600">Review and manage vaccination applications.</p>
      <span class="text-indigo-600">View</span>
    </div>
  </div>
</a>


          <!-- View Sleep Card -->
          <a href="sleep.php" class="block p-6 bg-purple-100 rounded-lg shadow-lg hover:bg-purple-200 transition-all">
            <div class="flex items-center">
              <i class="fas fa-bed text-purple-600 h-8 w-8 mr-4"></i>
              <div>
                <h3 class="text-xl font-semibold text-gray-800">View Sleep</h3>
                <p class="text-gray-600">Monitor and manage sleep patterns.</p>
                <span class="text-purple-600">View</span>
              </div>
            </div>
          </a>

        </div>
      </div>
    </div>
  </div>

</body>
</html>
