<?php
require_once '../classes/classloader.php';
if (!$userObj->isLoggedIn()) {
  header("Location: login.php");
}

if (!$userObj->isAdmin()) {
  header("Location: ../writer/index.php");
  exit();
}
$adminName = isset($_SESSION['first_name']) ? $_SESSION['first_name'] : 'Admin';
$articles = $articleObj->getActiveArticles();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
  <title>Admin Page</title>
</head>
<body class="bg-[#cdd4c6] min-h-screen">
  <div class="relative min-h-screen">

    <div class="absolute opacity-70 inset-0 bg-[url('../includes/image/bg-hero.png')] brightness-0 invert"></div>

    <div class="relative z-10">
        <?php include '../includes/navbar_admin.php'; ?>
        <div class="max-w-4xl mx-auto mt-8 pb-8">
        <div class="bg-white rounded-2xl shadow-lg p-8 mb-8 flex items-center gap-4">
          <div>
            <h1 class="text-3xl font-bold text-[#3b4933] flex items-center gap-2">Welcome, <span class="text-[#99a78b]"><?php echo htmlspecialchars($adminName); ?></span>!</h1>
            <p class="text-lg text-gray-600 mt-1">Manage all articles and messages for the school newspaper.</p>
          </div>
        </div>

        <div class="bg-white rounded-xl shadow p-6 mb-10">
          <h2 class="text-2xl font-semibold mb-4 flex items-center gap-2">📝 Post a New Article</h2>
          <form action="../core/handleForms.php" method="POST" enctype="multipart/form-data" class="space-y-4">
            <input type="text" name="title" required placeholder="Input title here" class="block w-full border border-gray-300 rounded-md p-2 focus:outline-none focus:ring focus:border-blue-300">
            <textarea name="description" required placeholder="Message as admin" class="block w-full border border-gray-300 rounded-md p-2 focus:outline-none focus:ring focus:border-blue-300"></textarea>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Add an image:</label>
              <input type="file" name="article_image" accept="image/*" class="inline-block w-full text-sm text-gray-600 border border-gray-300 rounded-md p-2 focus:outline-none focus:ring focus:border-blue-300">
            </div>
            <input type="submit" name="insertAdminArticleBtn" value="Post Article" class="bg-[#4b5b40] text-white px-6 py-2 rounded-lg hover:bg-[#3b4933] cursor-pointer">
          </form>
        </div>

        <!-- Articles List -->
        <div class="bg-white rounded-xl shadow p-6">
          <h2 class="text-2xl font-semibold mb-4 flex items-center gap-2">📚 Active Articles</h2>
          <?php if (empty($articles)) { ?>
            <p class="text-gray-500">No articles found.</p>
          <?php } else { ?>
            <div class="space-y-6">
            <?php foreach ($articles as $article) { ?>
              <div class="border border-gray-200 rounded-lg p-5 shadow-sm bg-gradient-to-r from-[#f4f6f3] to-[#e7e9e2] relative">
                <div class="flex items-center gap-2 mb-2">
                  <?php if (isset($article['role']) && $article['role'] === 'admin') { ?>
                    <span class="inline-flex items-center px-2 py-1 bg-[#99a78b] text-[#283123] rounded text-xs font-bold mr-2">🛠 Admin</span>
                  <?php } else { ?>
                    <span class="inline-flex items-center px-2 py-1 bg-[#cdd4c6] text-[#283123] rounded text-xs font-bold mr-2">✒️ Writer</span>
                  <?php } ?>
                  <span class="font-semibold text-lg"><?php echo htmlspecialchars($article['title']); ?></span>
                </div>
                
                <?php if (!empty($article['image_url'])) { ?>
                  <img src="/intro_PHP_OOP_3/<?php echo htmlspecialchars($article['image_url']); ?>" alt="Article image" class="w-fit max-h-60 mx-auto object-contain rounded mb-3 border border-gray-300 ">
                <?php } ?>
                <div class="text-gray-700 mb-2"><?php echo nl2br(htmlspecialchars($article['content'])); ?></div>
                <div class="flex items-center gap-2 text-sm text-gray-500">
                  <span>By <span class="font-bold text-[#3b4933]"><?php echo htmlspecialchars($article['first_name'] . ' ' . $article['last_name']); ?></span></span>
                  <span>•</span>
                  <span><?php echo htmlspecialchars($article['created_at']); ?></span>
                </div>

                <form action="../core/handleForms.php" method="POST" class="absolute top-4 right-4">
                  <input type="hidden" name="delete_article_id" value="<?php echo $article['article_id']; ?>">
                  <button type="submit" name="deleteArticleBtn" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded shadow flex items-center gap-1 text-sm" onclick="return confirm('Are you sure you want to delete this article?');">Delete</button>
                </form>
              </div>
            <?php } ?>
            </div>
          <?php } ?>
        </div>
      </div>
    </div>
  </div>
</body>
</html>