<?php require_once 'classes/classloader.php'; ?>
<!doctype html>
<html lang="en">
<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
  <body class="bg-gradient-to-br from-[#e7e9e2] to-[#cdd4c6] min-h-screen">
  
  <div class="relative min-h-screen">

    <div class="absolute inset-0 bg-[url('includes/image/bg-books.png')] brightness-0 invert"></div>
    
    <div class="relative z-10">
      <?php include 'includes/navbar.php'; ?>

      <div class="max-w-5xl mx-auto px-4 py-8">
        <div class="text-4xl font-bold text-center text-[#283123] mb-8">
          Welcome to School Publication Homepage
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-10">
          <div class="bg-white rounded-2xl shadow-lg p-6 flex flex-col items-center">
            <h1 class="text-2xl font-bold text-[#283123] mb-2">Writer</h1>
            <img src="https://images.unsplash.com/photo-1577900258307-26411733b430?q=80&w=1170&auto=format&fit=crop"
                class="rounded-md w-full h-48 object-cover mb-4 shadow">
            <p class="text-gray-700 text-center">
              Content writers create clear, engaging, and informative content by researching topics, writing across different formats, and optimizing material for readability.
            </p>
          </div>

          <div class="bg-white rounded-2xl shadow-lg p-6 flex flex-col items-center">
            <h1 class="text-2xl font-bold text-[#283123] mb-2">Admin</h1>
            <img src="https://plus.unsplash.com/premium_photo-1661582394864-ebf82b779eb0?q=80&w=1170&auto=format&fit=crop"
                class="rounded-md w-full h-48 object-cover mb-4 shadow">
            <p class="text-gray-700 text-center">
              Admin writers play a key role in content team development by managing, reviewing drafts, and ensuring consistency across all deliverables.
            </p>
          </div>
        </div>

        <div class="text-5xl font-bold text-center text-[#283123] mt-8 mb-4 border border-gray-300 rounded-lg bg-gradient-to-r from-[#e7e9e2] to-[#fff] px-6 py-4">
          All Articles
        </div>

        <div class="flex flex-col items-center">
          <div class="w-full max-w-4xl">
            <?php $articles = $articleObj->getActiveArticles(); ?>
            <?php foreach ($articles as $article) { ?>
              <div class="border border-gray-200 rounded-lg shadow-sm bg-gradient-to-r from-[#fff] to-[#e7e9e2] mt-6 p-6">
                <h1 class="text-xl font-bold mb-2"><?php echo $article['title']; ?></h1>
                <?php if (!empty($article['image_url'])) { ?>
                  <img src="/intro_PHP_OOP_3/<?php echo htmlspecialchars($article['image_url']); ?>"
                      alt="Article image"
                      class="w-fit max-h-60 mx-auto object-contain rounded mb-3 border border-gray-300">
                <?php } ?>
                <div class="text-gray-800 mb-2"><?php echo nl2br(htmlspecialchars($article['content'])); ?></div>
                <div class="text-sm text-gray-500 mb-2 text-right">
                  <span class="font-bold">
                    [<?php echo (isset($article['role']) && $article['role'] === 'admin') ? '🛠 Admin' : '✒️ Writer'; ?>]
                    <?php echo htmlspecialchars($article['first_name'] . ' ' . $article['last_name']); ?>
                  </span>
                  - <?php echo $article['created_at']; ?>
                </div>
              </div>
            <?php } ?>   
          </div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>