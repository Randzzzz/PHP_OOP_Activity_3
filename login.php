<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
  <title>Login</title>
</head>
<body class="bg-gradient-to-br from-[#e7e9e2] to-[#cdd4c6] min-h-screen">
  <div class="relative min-h-screen">

    <div class="absolute opacity-80 inset-0 bg-[url('includes/image/bg-dino.png')] brightness-0 invert"></div>

    <div class="relative z-10">
      <?php include 'includes/navbar.php'; ?>
      <div class="flex items-center justify-center m-12">
        <div class="bg-white shadow-lg rounded-lg p-8 w-full max-w-md">
          <form action="core/handleForms.php" method="POST">
            <h2 class="mt-4 mb-1 text-center text-2xl">Login your account</h2>
            <h2 class=" text-center text-sm text-gray-500">One step away.</h2>
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

          <p class="my-2">
            <input type="email" name="email" required placeholder="Email" class="mt-1 block w-full border border-gray-500/50 rounded-md p-2 focus:outline-none focus:ring focus:border-transparent">
          </p>
          <p>
            <input type="password" name="password" required placeholder="Password" class="mt-1 block w-full border border-gray-500/50 rounded-md p-2 focus:outline-none focus:ring focus:border-transparent">
          </p class="my-2">
          <input type="submit" name="loginUserBtn" value="Log In" class="mt-4 w-full bg-[#829374] text-white px-4 py-2 rounded hover:bg-[#4b5b40] cursor-pointer">
          </form>
          <hr class="my-6 border-gray-300">
          <p class="mt-4 text-center text-sm text-gray-600">
              Don't have an account? You may register <a href="register.php" class="text-[#829374] hover:underline hover:text-[#4b5b40]">here.</a>
            </p>
        </div>
      </div>
    </div>
  </div>

  
</body>
</html>