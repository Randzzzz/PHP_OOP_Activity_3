<?php
require_once '../classes/classloader.php';
if (!$userObj->isLoggedIn()) {
  header("Location: login.php");
  exit();
}
$user_id = $_SESSION['user_id'];
$allArticles = $articleObj->getArticles();
$myEditRequests = $editRequestObj->getRequestsForAuthor($user_id);
$userRequests = $editRequestObj->getRequestsByWriter($user_id); // Get all requests made by this user

$editRequestsByArticle = [];
foreach ($myEditRequests as $req) {
    $editRequestsByArticle[$req['article_id']][] = $req;
}

$userRequestedArticleIds = array_column($userRequests, 'article_id');

$categories = $categoryObj->getAllCategories();
$selectedCategory = isset($_GET['category']) ? intval($_GET['category']) : 0;
$articles = array_filter($allArticles, function($a) {
  return isset($a['is_active']) && $a['is_active'] != -1;
});
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
  <title>All Submitted Articles</title>
</head>
<body class="bg-[#cdd4c6] min-h-screen">
  <div class="relative min-h-screen">

    <div class="absolute opacity-70 inset-0 bg-[url('../includes/image/bg-hero.png')] brightness-0 invert"></div>

    <div class="relative z-10">
      <?php include '../includes/navbar_writer.php'; ?>

      <div class="max-w-4xl mx-auto mt-8">

        <div class="bg-white rounded-2xl shadow-lg p-8 mb-8 flex items-center gap-4">
          <div>
            <h1 class="text-3xl font-bold text-[#3b4933] flex items-center gap-2"> Submitted Articles</h1>
            <p class="text-lg text-gray-600 mt-1">View all submitted articles and request edit access to other authors.</p>
          </div>
        </div>

        <div class="bg-white rounded-xl shadow p-6">
          <div class="flex items-center justify-between mb-4">
            <h2 class="text-2xl font-semibold mb-4 flex items-center gap-2">🗂️ All Submitted Articles</h2>
            <form method="get" class="flex items-center gap-2">
              <label for="category" class="text-sm font-medium text-gray-700">Category:</label>
              <select name="category" id="category" class="border border-gray-300 rounded-md p-2" onchange="this.form.submit()">
                <option value="0">All</option>
                <?php foreach ($categories as $category) { ?>
                  <option value="<?php echo $category['category_id']; ?>" <?php if ($selectedCategory == $category['category_id']) echo 'selected'; ?>><?php echo htmlspecialchars($category['name']); ?></option>
                <?php } ?>
              </select>
            </form>
          </div>
          <?php
            // Filter articles by selected category if not 'All'
            $filteredArticles = $articles;
            if ($selectedCategory > 0) {
              $filteredArticles = array_filter($articles, function($a) use ($selectedCategory) {
                return $a['category_id'] == $selectedCategory;
              });
            }
          ?>
          <?php if (empty($filteredArticles)) { ?>
            <p class="text-gray-500">No articles found.</p>
          <?php } else { ?>

            <div class="space-y-6">
            <?php foreach ($filteredArticles as $article) { ?>
              <div class="border border-gray-200 rounded-lg p-5 shadow-sm bg-gradient-to-r from-[#f4f6f3] to-[#e7e9e2] relative group">
                <div class="flex items-center gap-2 mb-2">
                  <?php if (isset($article['role']) && $article['role'] === 'admin') { ?>
                    <span class="inline-flex items-center px-2 py-1 bg-[#99a78b] text-[#283123] rounded text-xs font-bold mr-2">🛠 Admin</span>
                  <?php } else { ?>
                    <span class="inline-flex items-center px-2 py-1 bg-[#cdd4c6] text-[#283123] rounded text-xs font-bold mr-2">✒️ Writer</span>
                  <?php } ?>

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
                  <span class="font-semibold text-lg text-[#4b5b40]">| <?php echo htmlspecialchars($categoryName); ?></span>
                  <span class="ml-2 px-2 py-1 rounded text-xs font-bold <?php echo ($article['is_active'] ? 'bg-green-200 text-green-800' : 'bg-gray-200 text-gray-700'); ?>">
                    <?php echo ($article['is_active'] ? 'Active' : 'Pending'); ?>
                  </span>
                </div>

                <?php if (!empty($article['image_url'])) { ?>
                  <img src="/intro_PHP_OOP_3/<?php echo htmlspecialchars($article['image_url']); ?>" alt="Article image" class="w-fit max-h-60 mx-auto object-contain rounded mb-3 border border-gray-300">
                <?php } ?>
                <div class="text-gray-700 mb-2"><?php echo nl2br(htmlspecialchars($article['content'])); ?></div>
                <div class="flex items-center gap-2 text-sm text-gray-500 mb-2">
                  <span>By <span class="font-bold text-[#3b4933]"><?php echo htmlspecialchars($article['first_name'] . ' ' . $article['last_name']); ?></span></span>
                  <span>•</span>
                  <span><?php echo htmlspecialchars($article['created_at']); ?></span>
                </div>

                <!-- Edit request -->
                <?php if ($article['author_id'] == $user_id && !empty($editRequestsByArticle[$article['article_id']])) { ?>
                  <?php foreach ($editRequestsByArticle[$article['article_id']] as $req) { ?>
                    <div class="flex items-center justify-between bg-[#e7e9e2] border border-[#cdd4c6] rounded px-3 py-2 mt-2">
                      <span class="text-gray-800 text-sm"><?php echo htmlspecialchars($req['first_name'] . ' ' . $req['last_name']); ?> request edit access</span>
                      <span class="flex gap-2">
                        <form action="../core/handleForms.php" method="POST" class="inline">
                          <input type="hidden" name="edit_request_id" value="<?php echo $req['request_id']; ?>">
                          <input type="hidden" name="edit_request_action" value="accept">
                          <button type="submit" name="handleEditRequestBtn" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-xs">Accept</button>
                        </form>
                        <form action="../core/handleForms.php" method="POST" class="inline">
                          <input type="hidden" name="edit_request_id" value="<?php echo $req['request_id']; ?>">
                          <input type="hidden" name="edit_request_action" value="reject">
                          <button type="submit" name="handleEditRequestBtn" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-xs">Reject</button>
                        </form>
                      </span>
                    </div>
                  <?php } ?>
                <?php } elseif ($article['author_id'] != $user_id) { ?>

                  <?php 
                    // Find the user's request for this article, if any
                    $userRequest = null;
                    foreach ($userRequests as $ur) {
                      if ($ur['article_id'] == $article['article_id']) {
                        $userRequest = $ur;
                        break;
                      }
                    }
                  ?>
                  <?php if (!$userRequest) { ?>
                    <form action="../core/handleForms.php" method="POST" class="mt-2">
                      <input type="hidden" name="edit_request_article_id" value="<?php echo $article['article_id']; ?>">
                      <button type="submit" name="requestEditBtn" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-1 rounded text-xs">Request Edit Access</button>
                    </form>
                  <?php } else { ?>
                    <?php if ($userRequest['status'] === 'accepted') { ?>
                      <span class="inline-block mt-2 px-3 py-1 rounded text-xs font-semibold bg-green-200 text-green-800">Edit Request Granted</span>
                    <?php } elseif ($userRequest['status'] === 'rejected') { ?>
                      <span class="inline-block mt-2 px-3 py-1 rounded text-xs font-semibold bg-red-200 text-red-800">Edit Request Denied</span>
                    <?php } else { ?>
                      <span class="inline-block mt-2 px-3 py-1 rounded text-xs font-semibold bg-yellow-200 text-yellow-800">Edit Request Pending</span>
                    <?php } ?>
                  <?php } ?>
                <?php } ?>
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