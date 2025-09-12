<?php
require_once '../classes/classloader.php';
if (!$userObj->isLoggedIn()) {
  header("Location: login.php");
}

if (!$userObj->isAdmin()) {
  header("Location: ../writer/index.php");
}
$articles = $articleObj->getArticles();
$allEditRequests = $editRequestObj->getAllPendingRequests();
$editRequestsByArticle = [];
foreach ($allEditRequests as $req) {
  $editRequestsByArticle[$req['article_id']][] = $req;
}
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
        <?php include '../includes/navbar_admin.php'; ?>
        <div class="max-w-4xl mx-auto mt-8 pb-8">
          <div class="bg-white rounded-2xl shadow-lg p-8 mb-8 flex items-center gap-4">
            <div>
              <h1 class="text-3xl font-bold text-[#3b4933] flex items-center gap-2">All Submitted Articles</h1>
              <p class="text-lg text-gray-600 mt-1">View and manage all articles.</p>
            </div>
          </div>

          <div class="bg-white rounded-xl shadow p-6">
            <h2 class="text-2xl font-semibold mb-4 flex items-center gap-2">🗂️ Articles List</h2>
            <?php if (empty($articles)) { ?>
              <p class="text-gray-500">No articles found.</p>
            <?php } else { ?>

              <div class="space-y-6">
              <?php foreach ($articles as $article) { ?>
                <div class="border border-gray-200 rounded-lg p-5 shadow-sm bg-gradient-to-r from-[#f4f6f3] to-[#e7e9e2] relative group">
                  <div class="flex items-center gap-2 mb-2">
                    <?php if (isset($article['role']) && $article['role'] === 'admin') { ?>
                      <span class="inline-flex items-center px-2 py-1 bg-[#99a78b] text-[#283123] rounded text-xs font-bold mr-2">🛠 Admin</span>
                    <?php } else { ?>
                      <span class="inline-flex items-center px-2 py-1 bg-[#cdd4c6] text-[#283123] rounded text-xs font-bold mr-2">✒️ Writer</span>
                    <?php } ?>

                    <span class="font-semibold text-lg"><?php echo htmlspecialchars($article['title']); ?></span>

                    <?php if ($article['is_active'] == 1) { ?>
                      <span class="ml-2 px-2 py-1 rounded text-xs font-bold bg-green-200 text-green-800">Active</span>
                    <?php } elseif ($article['is_active'] == 0) { ?>
                      <span class="ml-2 px-2 py-1 rounded text-xs font-bold bg-gray-200 text-gray-700">Pending</span>
                    <?php } elseif ($article['is_active'] == -1) { ?>
                      <span class="ml-2 px-2 py-1 rounded text-xs font-bold bg-red-200 text-red-800">Rejected</span>
                    <?php } ?>
                  </div>

                  <?php if (!empty($article['image_url'])) { ?>
                    <img src="/intro_PHP_OOP_3/<?php echo htmlspecialchars($article['image_url']); ?>" alt="Article image" class="w-fit max-h-60 mx-auto object-contain rounded mb-3 border border-gray-300">
                  <?php } ?>
                  <div class="text-gray-700 mb-2"><?php echo nl2br(htmlspecialchars($article['content'])); ?></div>
                  <div class="flex items-center gap-2 text-sm text-gray-500">
                    <span>By <span class="font-bold text-[#3b4933]"><?php echo htmlspecialchars($article['first_name'] . ' ' . $article['last_name']); ?></span></span>
                    <span>•</span>
                    <span><?php echo htmlspecialchars($article['created_at']); ?></span>
                  </div>

                  <button type="button" class="absolute top-4 right-20 bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 mr-2 rounded shadow flex items-center gap-1 text-sm edit-article-btn">Edit</button>
                  <form action="../core/handleForms.php" method="POST" class="absolute top-4 right-4">
                    <input type="hidden" name="delete_article_id" value="<?php echo $article['article_id']; ?>">
                    <button type="submit" name="deleteArticleBtn" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded shadow flex items-center gap-1 text-sm" onclick="return confirm('Are you sure you want to delete this article?');">Delete</button>
                  </form>

                  <!-- Show edit requests -->
                  <?php if (!empty($editRequestsByArticle[$article['article_id']])) { ?>
                    <div class="mt-4">
                      <?php foreach ($editRequestsByArticle[$article['article_id']] as $req) { ?>
                        <div class="flex items-center justify-between bg-[#e7e9e2] border border-[#cdd4c6] rounded px-3 py-2 mb-2">
                          <span class="text-gray-800 text-sm"><?php echo htmlspecialchars($req['first_name'] . ' ' . $req['last_name']); ?> requests edit access</span>
                          <span class="flex gap-2">
                            <form action="../core/handleForms.php" method="POST" class="inline">
                              <input type="hidden" name="edit_request_id" value="<?php echo $req['request_id']; ?>">
                              <input type="hidden" name="edit_request_action" value="accept">
                              <button type="submit" name="handleEditRequestAdminBtn" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-xs">Accept</button>
                            </form>
                            <form action="../core/handleForms.php" method="POST" class="inline">
                              <input type="hidden" name="edit_request_id" value="<?php echo $req['request_id']; ?>">
                              <input type="hidden" name="edit_request_action" value="reject">
                              <button type="submit" name="handleEditRequestAdminBtn" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-xs">Reject</button>
                            </form>
                          </span>
                        </div>
                      <?php } ?>
                    </div>
                  <?php } ?>

                  <!-- Hidden edit form -->
                  <form action="../core/handleForms.php" method="POST" enctype="multipart/form-data" class="edit-article-form hidden mt-4 bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <input type="hidden" name="edit_article_id" value="<?php echo $article['article_id']; ?>">
                    <div class="mb-2">
                      <label class="block text-sm font-medium text-gray-700">Title:</label>
                      <input type="text" name="edit_title" value="<?php echo htmlspecialchars($article['title']); ?>" class="block w-full border border-gray-300 rounded-md p-2">
                    </div>
                    <div class="mb-2">
                      <label class="block text-sm font-medium text-gray-700">Content:</label>
                      <textarea name="edit_content" class="block w-full border border-gray-300 rounded-md p-2"><?php echo htmlspecialchars($article['content']); ?></textarea>
                    </div>
                    <div class="mb-2">
                      <label class="block text-sm font-medium text-gray-700">Change Image:</label>
                      <input type="file" name="edit_image" accept="image/*" class="inline-block w-full text-sm text-gray-600 border border-gray-300 rounded-md p-2 focus:outline-none focus:ring focus:border-blue-300">
                    </div>
                    <div class="mb-2">
                      <label class="block text-sm font-medium text-gray-700">Status:</label>
                      <select name="edit_status" class="block w-full border border-gray-300 rounded-md p-2">
                        <option value="1" <?php if ($article['is_active']) echo 'selected'; ?>>Active</option>
                        <option value="0" <?php if (!$article['is_active']) echo 'selected'; ?>>Pending</option>
                      </select>
                    </div>
                    <button type="submit" name="editArticleAdminBtn" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">Save Changes</button>
                    <button type="button" class="ml-2 bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded cancel-edit-btn">Cancel</button>
                  </form>
                </div>
              <?php } ?>
              </div>
            <?php } ?>
          </div>
        </div>
    </div>
  </div>
  
  <script>  
    document.querySelectorAll('.edit-article-btn').forEach(function(btn, idx) {
      btn.addEventListener('click', function() {
        var form = btn.parentElement.querySelector('.edit-article-form');
        form.classList.toggle('hidden');
      });
    });
    document.querySelectorAll('.cancel-edit-btn').forEach(function(btn) {
      btn.addEventListener('click', function() {
        var form = btn.closest('.edit-article-form');
        form.classList.add('hidden');
      });
    });
  </script>
</body>
</html>