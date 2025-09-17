<nav class="bg-[#4b5b40] shadow-lg px-16 py-4 mx-48 flex items-center justify-between rounded-full rounded-t-none">

  <a href="index.php" class="text-2xl font-bold text-white tracking-wide flex items-center gap-2">
    <span>Admin Panel</span>
  </a>

  <div class="hidden lg:flex space-x-6 font-medium text-lg" id="menu">
    <a href="index.php"
      class="text-white hover:text-[#cdd4c6] transition-colors duration-200 flex items-center gap-1">
      <span>Home</span>
    </a>
    <a href="set_category.php"
      class="text-white hover:text-[#cdd4c6] transition-colors duration-200 flex items-center gap-1">
      <span>Manage Categories</span>
    </a>
    <a href="articles_from_students.php"
      class="text-white hover:text-[#cdd4c6] transition-colors duration-200 flex items-center gap-1">
      <span>Pending Articles</span>
    </a>
    <a href="articles_submitted.php"
      class="text-white hover:text-[#cdd4c6] transition-colors duration-200 flex items-center gap-1">
      <span>Articles Submitted</span>
    </a>
    <a href="../core/handleForms.php?logoutUserBtn=1"
      class="bg-[#e7e9e2] text-[#283123] px-4 py-2 rounded-full hover:bg-[#cdd4c6] transition-colors duration-200 flex items-center gap-1">
      <span>Logout</span>
    </a>
  </div>
</nav>