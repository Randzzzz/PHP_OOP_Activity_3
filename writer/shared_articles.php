<?php
require_once '../classes/classloader.php';
if (!$userObj->isLoggedIn()) {
	header("Location: login.php");
	exit();
}
$user_id = $_SESSION['user_id'];
$allSharedArticles = $editRequestObj->getSharedArticles($user_id);

$sharedArticles = array_filter($allSharedArticles, function($a) {
	return isset($a['is_active']) && $a['is_active'] != -1;
});
$categories = $categoryObj->getAllCategories();
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
	<title>Shared Articles</title>
</head>
<body class="bg-[#cdd4c6] min-h-screen">
  <div class="relative min-h-screen">

		<div class="absolute opacity-70 inset-0 bg-[url('../includes/image/bg-hero.png')] brightness-0 invert"></div>

		<div class="relative z-10">
			<?php include '../includes/navbar_writer.php'; ?>

				<div class="max-w-4xl mx-auto mt-8">
					<div class="bg-white rounded-2xl shadow-lg p-8 mb-8 flex items-center gap-4">
						<div>
							<h1 class="text-3xl font-bold text-[#3b4933] flex items-center gap-2">Shared Articles</h1>
							<p class="text-lg text-gray-600 mt-1">These articles have been shared with you for editing.</p>
						</div>
					</div>

					<div class="bg-white rounded-xl shadow p-6">
						<h2 class="text-2xl font-semibold mb-4 flex items-center gap-2">🔗 Articles Shared With Me</h2>
						<?php if (empty($sharedArticles)) { ?>
							<p class="text-gray-500">No articles have been shared with you yet.</p>
						<?php } else { ?>

							<div class="space-y-6">
							<?php foreach ($sharedArticles as $article) { ?>
								<div class="border border-gray-200 rounded-lg p-5 shadow-sm bg-gradient-to-r from-[#f4f6f3] to-[#e7e9e2] relative">
									<div class="flex items-center gap-2 mb-2">
																	<?php if (isset($article['role']) && $article['role'] === 'admin') { ?>
																		<span class="inline-flex items-center px-2 py-1 bg-[#99a78b] text-[#283123] rounded text-xs font-bold mr-2">🛠 Admin</span>
																	<?php } else { ?>
																		<span class="inline-flex items-center px-2 py-1 bg-[#cdd4c6] text-[#283123] rounded text-xs font-bold mr-2">✒️ Writer</span>
																	<?php } ?>
										<span class="font-semibold text-lg"><?php echo htmlspecialchars($article['title']); ?></span>
										<?php
										$categoryName = '';
										if (isset($categories) && is_array($categories)) {
											foreach ($categories as $category) {
												if ($category['category_id'] == $article['category_id']) {
													$categoryName = $category['name'];
													break;
												}
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
										<span>Author: <span class="font-bold text-[#3b4933]"><?php echo htmlspecialchars($article['first_name'] . ' ' . $article['last_name']); ?></span></span>
										<span>•</span>
										<span>Shared on <span class="font-bold text-[#3b4933]"><?php echo htmlspecialchars($article['created_at']); ?></span></span>
									</div>

									<!-- Edit button form -->
									<button type="button" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded shadow flex items-center gap-1 text-sm edit-shared-article-btn">Edit</button>
									<form action="../core/handleForms.php" method="POST" enctype="multipart/form-data" class="edit-shared-article-form hidden mt-4 bg-gray-50 p-4 rounded-lg border border-gray-200">
										<input type="hidden" name="edit_shared_article_id" value="<?php echo $article['article_id']; ?>">
										<label class="block text-sm font-medium text-gray-700">Title:</label>
										<div class="flex gap-2">
											<input type="text" name="edit_title" value="<?php echo htmlspecialchars($article['title']); ?>" class="block w-full border border-gray-300 rounded-md p-2">
											<select name="edit_category_id" required class="border border-gray-300 rounded-md p-2">
                      <option value="">Select Category</option>
                      <?php foreach ($categories as $category) { ?>
                        <option value="<?php echo $category['category_id']; ?>" <?php if ($category['category_id'] == $article['category_id']) echo 'selected'; ?>><?php echo htmlspecialchars($category['name']); ?></option>
                      <?php } ?>
                    </select>
										</div>
										<div class="mb-2">
											<label class="block text-sm font-medium text-gray-700">Content:</label>
											<textarea name="edit_content" class="block w-full border border-gray-300 rounded-md p-2"><?php echo htmlspecialchars($article['content']); ?></textarea>
										</div>
										<div class="mb-2">
											<label class="block text-sm font-medium text-gray-700">Change Image:</label>
											<input type="file" name="edit_image" accept="image/*" class="inline-block w-full text-sm text-gray-600 border border-gray-300 rounded-md p-2 focus:outline-none focus:ring focus:border-blue-300">
										</div>
										<button type="submit" name="editSharedArticleBtn" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">Save Changes</button>
										<button type="button" class="ml-2 bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded cancel-edit-shared-btn">Cancel</button>
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
		document.querySelectorAll('.edit-shared-article-btn').forEach(function(btn) {
			btn.addEventListener('click', function() {
				var form = btn.parentElement.querySelector('.edit-shared-article-form');
				form.classList.toggle('hidden');
			});
		});
		document.querySelectorAll('.cancel-edit-shared-btn').forEach(function(btn) {
			btn.addEventListener('click', function() {
				var form = btn.closest('.edit-shared-article-form');
				form.classList.add('hidden');
			});
		});
	</script>
</body>
</html>
