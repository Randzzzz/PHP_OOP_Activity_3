<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
  <title>Register</title>
</head>
<body class="bg-gradient-to-br from-[#e7e9e2] to-[#cdd4c6] min-h-screen">

  <div class="relative min-h-screen">

    <div class="absolute opacity-80 inset-0 bg-[url('includes/image/bg-dino.png')] brightness-0 invert"></div>

    <div class="relative z-10">
      <?php include 'includes/navbar.php'; ?>
        <div class="flex items-center justify-center m-12">
          <div class="bg-white shadow-lg rounded-lg p-8 w-full max-w-md">
            <form action="core/handleForms.php" method="POST">
              <h2 class="mt-4 mb-1 text-center text-2xl">Create a new account</h2>
              <h2 class=" text-center text-sm text-gray-500">Just a few steps.</h2>
              <hr class="my-3 border-gray-300">

            <?php
            if (isset($_SESSION['message']) && isset($_SESSION['status'])) {
              if ($_SESSION['status'] == "200") {
                echo "<h1 style='color: green;'>{$_SESSION['message']}</h1>";
              } else {
                echo "<h1 style='color: red;'>{$_SESSION['message']}</h1>"; 
              }
            }
            unset($_SESSION['message']);
            unset($_SESSION['status']);
            ?>

            <div class="flex space-x-2 my-2">
              <input type="text" name="first_name" required placeholder="First name" class="mt-1 block w-full border border-gray-500/50 rounded-md p-2 focus:outline-none focus:ring focus:border-transparent">
              <input type="text" name="last_name" required placeholder="Last name" class="mt-1 block w-full border border-gray-500/50 rounded-md p-2 focus:outline-none focus:ring focus:border-transparent">
            </div>
            <p class="my-2">
              <label for="role" class="block text-sm font-medium text-gray-700">Role:</label>
              <select id="role" name="role" class="mt-1 block w-full border border-gray-500/50 rounded-md p-2 focus:outline-none focus:ring focus:border-transparent">
                <option value="writer" selected>Writer</option>
                <option value="admin">Admin</option>
              </select>
            </p>
            <p id="admin-verification" class="hidden my-2">
              <input type="password" name="admin_password" id="admin_password" disabled required placeholder="Admin verification password" class="mt-1 block w-full border border-gray-500/50 rounded-md p-2 focus:outline-none focus:ring focus:border-transparent">
            </p>
            <p class="my-2">
              <input type="email" name="email" required placeholder="Email" class="mt-1 block w-full border border-gray-500/50 rounded-md p-2 focus:outline-none focus:ring focus:border-transparent">
            </p>
            <p class="my-2">
              <input type="password" name="password" required placeholder="Password"class="mt-1 block w-full border border-gray-500/50 rounded-md p-2 focus:outline-none focus:ring focus:border-transparent">
            </p>
            <p class="my-2">
              <input type="password" name="confirm_password" required placeholder="Confirm password" class="mt-1 block w-full border border-gray-500/50 rounded-md p-2 focus:outline-none focus:ring focus:border-transparent">
            </p>
            <input type="submit" name="insertNewUserBtn" value="Sign Up" class="mt-4 w-full bg-[#829374] text-white px-4 py-2 rounded hover:bg-[#4b5b40] cursor-pointer">
            </form>
            <hr class="my-6 border-gray-300">
            <p class="mt-4 text-center text-sm text-gray-600">
              Already have an account? Login <a href="login.php" class="text-[#829374] hover:underline hover:text-[#4b5b40]">here.</a>
            </p>
          </div>
        </div>
    </div>
  </div>

  <script>
    const roleSelect = document.getElementById("role");
    const adminVerification = document.getElementById("admin-verification");
    const adminPassword = document.getElementById("admin_password");

    roleSelect.addEventListener("change", function() {
      if (this.value === "admin") {
        adminVerification.classList.remove("hidden");
        adminPassword.disabled = false; 
      } else {
        adminVerification.classList.add("hidden");
        adminPassword.disabled = true;
        adminPassword.value = ""; 
      }
    });
  </script>

</body>
</html>