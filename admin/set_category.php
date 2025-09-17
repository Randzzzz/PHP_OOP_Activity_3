<?php
require_once '../classes/classloader.php';
if (!$userObj->isLoggedIn()) {
  header("Location: login.php");
  exit();
}

if (!$userObj->isAdmin()) {
  header("Location: ../writer/index.php");
  exit();
}

$categories = $categoryObj->getAllCategories();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
  <title>categories</title>
</head>
<body class="bg-[#cdd4c6] min-h-screen">
  <div class="relative min-h-screen">

    <div class="absolute opacity-70 inset-0 bg-[url('../includes/image/bg-hero.png')] brightness-0 invert"></div>

    <div class="relative z-10">
      <?php include '../includes/navbar_admin.php'; ?>

      <div class="max-w-4xl mx-auto mt-8 pb-8">
        <div class="bg-white rounded-2xl shadow-lg p-8 mb-8 flex items-center gap-4">
          <div>
            <h1 class="text-3xl font-bold text-[#3b4933] flex items-center gap-2">Manage Categories</h1>
            <p class="text-lg text-gray-600 mt-1">Add, edit, or delete article categories.</p>
          </div>
        </div>

        <div class="bg-white rounded-xl shadow p-6">
          <h2 class="text-2xl font-semibold mb-4 flex items-center gap-2">📋 Category Management</h2>

          <form action="../core/handleForms.php" method="POST" class="mb-6 flex gap-2 items-end">
            <div>
              <input type="text" name="category_name" class="border border-gray-300 rounded-md p-2" required placeholder="New Category Name">
            </div>
            <button type="submit" name="addCategoryBtn" class="bg-[#4b5b40] hover:bg-[#3b4933] text-white font-medium px-4 py-2 rounded-lg">Add Category</button>
          </form>

          <!-- List Categories -->
          <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-200 rounded-md">
              <thead>
                <tr>
                  <!-- Make category name take most of the width -->
                  <th class="py-2 px-4 border-b border-gray-200 w-3/4 text-left">Category Name</th>
                  <!-- Keep actions narrow -->
                  <th class="py-2 px-4 border-b border-gray-200 w-1/4 text-left">Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($categories as $row) { ?>
                  <tr>
                    <?php if (isset($_GET['edit_id']) && $_GET['edit_id'] == $row['category_id']) { ?>
                      <form action="../core/handleForms.php" method="POST" class="flex w-full">
                        <td class="py-2 px-4  border-gray-200 w-3/4">
                          <input type="hidden" name="edit_category_id" value="<?php echo $row['category_id']; ?>">
                          <input type="text" name="edit_category_name" value="<?php echo htmlspecialchars($row['name']); ?>" class="border border-gray-300 rounded p-1 w-full">
                        </td>
                        <td class="py-2 px-4 w-1/4 flex gap-2">
                          <button type="submit" name="updateCategoryBtn" class="bg-green-600 hover:bg-green-700 text-white px-2 py-1 rounded text-xs">Save</button>
                          <a href="set_category.php" class="bg-gray-400 hover:bg-gray-500 text-white px-2 py-1 rounded text-xs">Cancel</a>
                        </td>
                      </form>
                    <?php } else { ?>
                      <td class="py-2 px-4 w-3/4">
                        <?php echo htmlspecialchars($row['name']); ?>
                      </td>
                      <td class="py-2 px-4 w-1/4 flex gap-2">
                        <a href="?edit_id=<?php echo $row['category_id']; ?>" class="bg-yellow-500 hover:bg-yellow-600 text-white px-2 py-1 rounded text-xs">Edit</a>
                        <form action="../core/handleForms.php" method="POST" onsubmit="return confirm('Delete this category?');" class="inline">
                          <input type="hidden" name="delete_category_id" value="<?php echo $row['category_id']; ?>">
                          <button type="submit" name="deleteCategoryBtn" class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded text-xs">Delete</button>
                        </form>
                      </td>
                    <?php } ?>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </div>
    </div>
  </div>
</body>
</html>