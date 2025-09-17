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
$articles = $articleObj->getArticles();
$studentArticles = array_filter($articles, function($a) {
  return isset($a['role']) && $a['role'] !== 'admin' && isset($a['is_active']) && $a['is_active'] == 0; // Only show articles that are pending (is_active = 0)
});
$categories = $categoryObj->getAllCategories();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
  <title>Articles from Students</title>
</head>
<body class="bg-[#cdd4c6] min-h-screen">
  <div class="relative min-h-screen">

    <div class="absolute opacity-70 inset-0 bg-[url('../includes/image/bg-hero.png')] brightness-0 invert"></div>

    <div class="relative z-10">
      <?php include '../includes/navbar_admin.php'; ?>

      <div class="max-w-4xl mx-auto mt-8 pb-8">

        <div class="bg-white rounded-2xl shadow-lg p-8 mb-8 flex items-center gap-4">
          <div>
            <h1 class="text-3xl font-bold text-[#3b4933] flex items-center gap-2">Review Writer Articles</h1>
            <p class="text-lg text-gray-600 mt-1">Approve or reject articles submitted by writers.</p>
          </div>
        </div>

        <div class="bg-white rounded-xl shadow p-6">

          <h2 class="text-2xl font-semibold mb-4 flex items-center gap-2">🗂️ Submitted Articles</h2>

          <?php if (empty($studentArticles)) { ?>
            <p class="text-gray-500">No articles from writers found.</p>
          <?php } else { ?>
            <div class="space-y-6">
            <?php foreach ($studentArticles as $article) { ?>
              <div class="border border-gray-200 rounded-lg p-5 shadow-sm bg-gradient-to-r from-[#f4f6f3] to-[#e7e9e2] relative">
                <div class="flex items-center gap-2 mb-2">
                  <span class="inline-flex items-center px-2 py-1 bg-[#cdd4c6] text-[#283123] rounded text-xs font-bold mr-2">✒️ Writer</span>
                  <span class="font-semibold text-lg"><?php echo htmlspecialchars($article['title']); ?></span>
                  <?php
                      
                      $categoryName = '';
                      foreach ($categories as $category) {
                        if ($category['category_id'] == $article['category_id']) {
                          $categoryName = $category['name'];
                          break;
                        }
                      }
                    ?>
                    <span class="font-semibold text-lg text-[#4b5b40]">| 
                      <?php echo htmlspecialchars($categoryName); ?>
                    </span>
                </div>
                <?php if (!empty($article['image_url'])) { ?>
                  <img src="/intro_PHP_OOP_3/<?php echo htmlspecialchars($article['image_url']); ?>" alt="Article image" class="w-fit max-h-60 mx-auto object-cover rounded mb-3 border border-gray-300">
                <?php } ?>
                <div class="text-gray-700 mb-2"><?php echo nl2br(htmlspecialchars($article['content'])); ?></div>
                <div class="flex items-center gap-2 text-sm text-gray-500">
                  <span>By <span class="font-bold text-[#3b4933]"><?php echo htmlspecialchars($article['first_name'] . ' ' . $article['last_name']); ?></span></span>
                  <span>•</span>
                  <span><?php echo htmlspecialchars($article['created_at']); ?></span>
                </div>

                <form action="../core/handleForms.php" method="POST" class="flex gap-2 mt-3">
                  <input type="hidden" name="article_id" value="<?php echo $article['article_id']; ?>">
                  <button type="submit" name="approveArticleBtn" class="bg-green-500 hover:bg-green-600 text-white px-4 py-1 rounded shadow flex items-center gap-1 text-sm">Approve</button>
                  <button type="submit" name="rejectArticleBtn" class="bg-red-500 hover:bg-red-600 text-white px-4 py-1 rounded shadow flex items-center gap-1 text-sm" onclick="return confirm('Reject this article?');">Reject</button>
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
