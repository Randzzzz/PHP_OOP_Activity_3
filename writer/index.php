<?php
require_once '../classes/classloader.php';
if (!$userObj->isLoggedIn()) {
  header("Location: login.php");
  exit();
}

$user_id = $_SESSION['user_id'];

$userArticles = $articleObj->getArticlesByUserID($user_id);
$notifications = $notificationObj->getUserNotifications($user_id, 10);
$editRequests = $editRequestObj->getRequestsByWriter($user_id);

$writerName = isset($_SESSION['first_name']) ? $_SESSION['first_name'] : 'Writer';
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
      <?php include '../includes/navbar_writer.php'; ?>
      <div class="max-w-4xl mx-auto mt-8">
        <div class="bg-white rounded-2xl shadow-lg p-8 mb-8 flex items-center gap-4">
          <div>
            <h1 class="text-3xl font-bold text-[#3b4933] flex items-center gap-2">Welcome, <span class="text-[#99a78b]"><?php echo htmlspecialchars($writerName); ?></span>!</h1>
            <p class="text-lg text-gray-600 mt-1">Submit your articles and track their status.</p>
          </div>
        </div>

        <?php if (!empty($notifications)) { ?>
        <div class="bg-[#f4f6f3] border-l-4 border-[#829374] p-4 mb-6 rounded-xl">
          <div class="font-semibold text-[#313b2a] mb-1">Notifications:</div>
          <ul class="list-disc pl-5 text-[#283123]">
            <?php foreach ($notifications as $notif) { ?>
              <li><?php echo $notif['message']; ?></li>
            <?php } ?>
          </ul>
        </div>
        <?php } ?>

        <div class="bg-white rounded-xl shadow p-6 mb-10">
          <h2 class="text-2xl font-semibold mb-4 flex items-center gap-2">📝 Submit a New Article</h2>
          <form action="../core/handleForms.php" method="POST" enctype="multipart/form-data" class="space-y-4">
            <input type="text" name="title" required placeholder="Input title here" class="block w-full border border-gray-300 rounded-md p-2 focus:outline-none focus:ring focus:border-blue-300">
            <textarea name="description" required placeholder="Write your article here..." class="block w-full border border-gray-300 rounded-md p-2 focus:outline-none focus:ring focus:border-blue-300"></textarea>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Add an image:</label>
              <input type="file" name="article_image" accept="image/*" class="inline-block w-full text-sm text-gray-600 border border-gray-300 rounded-md p-2 focus:outline-none focus:ring focus:border-blue-300">
            </div>
            <input type="submit" name="insertWriterArticleBtn" value="Submit Article" class="bg-[#4b5b40] text-white px-6 py-2 rounded-lg hover:bg-[#3b4933] cursor-pointer">
          </form>
        </div>

        <!-- Writer Article List -->
        <div class="bg-white rounded-xl shadow p-6">
          <h2 class="text-2xl font-semibold mb-4 flex items-center gap-2">📚 My Articles</h2>
          <?php if (empty($userArticles)) { ?>
            <p class="text-gray-500">You haven't submitted any articles yet.</p>
          <?php } else { ?>

            <div class="space-y-6">
            <?php foreach ($userArticles as $idx => $article) { ?>
              <div class="border border-gray-200 rounded-lg p-5 shadow-sm bg-gradient-to-r from-[#f4f6f3] to-[#e7e9e2] relative">
                <div class="absolute top-4 right-4 flex gap-2">
                  <button type="button" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded shadow flex items-center gap-1 text-sm edit-article-btn" data-edit-idx="<?php echo $idx; ?>">Edit</button>
                  <form action="../core/handleForms.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this article?');">
                    <input type="hidden" name="delete_article_id" value="<?php echo $article['article_id']; ?>">
                    <button type="submit" name="deleteArticleBtn" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded shadow flex items-center gap-1 text-sm">Delete</button>
                  </form>
                </div>

                <div class="flex items-center gap-2 mb-2">
                  <span class="inline-flex items-center px-2 py-1 bg-[#cdd4c6] text-[#283123] rounded text-xs font-bold mr-2">✒️ Writer</span>
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
                  <img src="/intro_PHP_OOP_3/<?php echo htmlspecialchars($article['image_url']); ?>" alt="Article image" class="w-fit max-h-60 mx-auto object-contain rounded mb-3 border border-gray-300 ">
                <?php } ?>
                <div class="text-gray-700 mb-2"><?php echo nl2br(htmlspecialchars($article['content'])); ?></div>
                <div class="flex items-center gap-2 text-sm text-gray-500 mb-2">
                  <span>Submitted on <span class="font-bold"><?php echo htmlspecialchars($article['created_at']); ?></span></span>
                </div>

                <form action="../core/handleForms.php" method="POST" enctype="multipart/form-data" class="edit-article-form hidden mt-4 bg-gray-50 p-4 rounded-lg border border-gray-200" data-edit-idx="<?php echo $idx; ?>">
                  <input type="hidden" name="edit_own_article_id" value="<?php echo $article['article_id']; ?>">
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
                    <input type="file" name="edit_image" accept="image/*" class=" inline-block w-full text-sm text-gray-600 border border-gray-300 rounded-md p-2 focus:outline-none focus:ring focus:border-blue-300">
                  </div>
                  <button type="submit" name="editOwnArticleBtn" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">Save Changes</button>
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
    document.querySelectorAll('.edit-article-btn').forEach(function(btn) {
      btn.addEventListener('click', function() {
        var idx = btn.getAttribute('data-edit-idx');
        var form = document.querySelector('.edit-article-form[data-edit-idx="' + idx + '"]');
        if (form) form.classList.toggle('hidden');
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