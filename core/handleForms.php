<?php
require_once '../classes/classloader.php';

if (isset($_POST['insertNewUserBtn'])) {
	$email = htmlspecialchars(trim($_POST['email']));
	$first_name = trim($_POST['first_name']);
	$last_name = trim($_POST['last_name']);
	$role = trim($_POST['role']);
	$password = trim($_POST['password']);
	$confirm_password = trim($_POST['confirm_password']);
	$admin_verification = "admin123";
	if($role === "admin"){
		if(!isset($_POST['admin_password']) || $_POST['admin_password'] !== $admin_verification){
			$_SESSION['message'] = "Invalid admin verification password.";
			$_SESSION['status'] = '400';
			header("Location: ../register.php");
		}
	}

	if (!empty($email) && !empty($password) && !empty($first_name) && !empty($last_name)) {
		if ($password == $confirm_password) {
			if (!$userObj->emailExists($email)) {
				if ($userObj->registerUser($first_name, $last_name, $email, $password, $role)) {
					$_SESSION['message'] = "Registration successful! Please login.";
					$_SESSION['status'] = '200';
					header("Location: ../login.php");
				} else {
					$_SESSION['message'] = "An error occurred with the query!";
					$_SESSION['status'] = '400';
					header("Location: ../register.php");
				}
			} else {
				$_SESSION['message'] = $email . " is already taken";
				$_SESSION['status'] = '400';
				header("Location: ../register.php");
			}
		} else {
			$_SESSION['message'] = "Please make sure both passwords are equal";
			$_SESSION['status'] = '400';
			header("Location: ../register.php");
		}
	} else {
		$_SESSION['message'] = "Please make sure there are no empty input fields";
		$_SESSION['status'] = '400';
		header("Location: ../register.php");
	}
}

if (isset($_POST['loginUserBtn'])) {
	$email = trim($_POST['email']);
	$password = trim($_POST['password']);

	if (!empty($email) && !empty($password)) {
		if ($userObj->loginUser($email, $password)) {
			$role = isset($_SESSION['role']) ? $_SESSION['role'] : '';
			if ($role === 'admin') {
				header("Location: ../admin/index.php");
			} else {
				header("Location: ../writer/index.php");
			}
		} else {
			$_SESSION['message'] = "Username/password invalid";
			$_SESSION['status'] = "400";
			header("Location: ../login.php");
		}
	} else {
		$_SESSION['message'] = "Please make sure there are no empty input fields";
		$_SESSION['status'] = '400';
		header("Location: ../login.php");
	}
}


if (isset($_GET['logoutUserBtn'])) {
	$userObj->logout();
	header("Location: ../index.php");
}

// image upload helper
function uploadArticleImage($fileInputName = 'article_image') {
	if (!isset($_FILES[$fileInputName]) || $_FILES[$fileInputName]['error'] !== UPLOAD_ERR_OK) {
		return null;
	}
	$uploadDir = __DIR__ . '/../uploads/';
	$dbUploadDir = 'uploads/';
	if (!is_dir($uploadDir)) {
		mkdir($uploadDir, 0777, true);
	}
	$fileTmp = $_FILES[$fileInputName]['tmp_name'];
	$fileName = basename($_FILES[$fileInputName]['name']);
	$fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
	$allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
	if (in_array($fileExt, $allowed)) {
		$newFileName = uniqid('img_', true) . '.' . $fileExt;
		$destPath = $uploadDir . $newFileName;
		if (move_uploaded_file($fileTmp, $destPath)) {
			return $dbUploadDir . $newFileName;
		}
	}
	return null;
}

// Article submission (admin/writer)
if ((isset($_POST['insertAdminArticleBtn']) && $userObj->isAdmin()) || (isset($_POST['insertWriterArticleBtn']) && !$userObj->isAdmin())) {
	$title = trim($_POST['title']);
	$content = trim($_POST['description']);
	$category_id = isset($_POST['category_id']) ? intval($_POST['category_id']) : null;
	$author_id = $_SESSION['user_id'];
	$image_url = null;

	$image_url = uploadArticleImage('article_image');
	$success = $articleObj->createArticleWithImage($title, $content, $category_id, $author_id, $image_url);
	if ($userObj->isAdmin()) {
		$_SESSION['message'] = $success ? "Article posted successfully!" : "Failed to post article.";
		$_SESSION['status'] = $success ? '200' : '400';
		header("Location: ../admin/index.php");
		exit();
	} else {
		$_SESSION['message'] = $success ? "Article submitted successfully!" : "Failed to submit article.";
		$_SESSION['status'] = $success ? '200' : '400';
		header("Location: ../writer/index.php");
		exit();
	}
}

// Article delete (admin/writer)
if (isset($_POST['deleteArticleBtn'])) {
	$article_id = isset($_POST['delete_article_id']) ? intval($_POST['delete_article_id']) : 0;
	if ($userObj->isAdmin()) {
		if ($article_id && $articleObj->deleteArticleWithNotification($article_id, $notificationObj)) {
			$_SESSION['message'] = "Article deleted and author notified.";
			$_SESSION['status'] = '200';
		} else {
			$_SESSION['message'] = "Failed to delete article.";
			$_SESSION['status'] = '400';
		}
		header("Location: ../admin/index.php");
		exit();
	} else {
		// Writer can only delete their own article
		$article = $articleObj->getArticles($article_id);
		if ($article && $article['author_id'] == $_SESSION['user_id']) {
			if ($articleObj->deleteArticle($article_id)) {
				$_SESSION['message'] = "Article deleted successfully.";
				$_SESSION['status'] = '200';
			} else {
				$_SESSION['message'] = "Failed to delete article.";
				$_SESSION['status'] = '400';
			}
		} else {
			$_SESSION['message'] = "Unauthorized delete attempt.";
			$_SESSION['status'] = '400';
		}
		header("Location: ../writer/index.php");
		exit();
	}
}

// Article edit (admin)
if (isset($_POST['editArticleAdminBtn']) && $userObj->isAdmin()) {
	$article_id = intval($_POST['edit_article_id']);
	$title = trim($_POST['edit_title']);
	$content = trim($_POST['edit_content']);
	$status = isset($_POST['edit_status']) ? intval($_POST['edit_status']) : 0;
	$category_id = isset($_POST['edit_category_id']) ? intval($_POST['edit_category_id']) : null;
	$image_url = uploadArticleImage('edit_image');

	$result = $articleObj->updateArticle($article_id, $title, $content, $status, $image_url, $category_id);

	if ($result) {
		$_SESSION['message'] = "Article updated successfully!";
		$_SESSION['status'] = '200';
	} else {
		$_SESSION['message'] = "Failed to update article.";
		$_SESSION['status'] = '400';
	}
	header("Location: ../admin/articles_submitted.php");
	exit();
}

// Article approve/reject
if (isset($_POST['approveArticleBtn']) && $userObj->isAdmin()) {
	$article_id = intval($_POST['article_id']);
	$article = $articleObj->getArticles($article_id);
	if ($article) {
		$author_id = $article['author_id'];
		$title = $article['title'];
		if ($articleObj->updateArticleVisibility($article_id, 1)) {
			$msg = "Your article '<b>" . htmlspecialchars($title) . "</b>' was approved and published!";
			$notificationObj->send($author_id, $msg);
			$_SESSION['message'] = "Article approved and author notified.";
			$_SESSION['status'] = '200';
		} else {
			$_SESSION['message'] = "Failed to approve article.";
			$_SESSION['status'] = '400';
		}
	}
	header("Location: ../admin/articles_from_students.php");
	exit();
}

if (isset($_POST['rejectArticleBtn']) && $userObj->isAdmin()) {
	$article_id = intval($_POST['article_id']);
	$article = $articleObj->getArticles($article_id);
	if ($article) {
		$author_id = $article['author_id'];
		$title = $article['title'];
		// Set is_active to -1 for rejected
	$articleObj->rejectArticle($article_id);
		$msg = "Your article '<b>" . htmlspecialchars($title) . "</b>' was rejected by the admin.";
		$notificationObj->send($author_id, $msg);
		$_SESSION['message'] = "Article rejected and author notified.";
		$_SESSION['status'] = '200';
	}
	header("Location: ../admin/articles_from_students.php");
	exit();
}


// Writer edits own article
if (isset($_POST['editOwnArticleBtn']) && !$userObj->isAdmin()) {
	$article_id = intval($_POST['edit_own_article_id']);
	$title = trim($_POST['edit_title']);
	$content = trim($_POST['edit_content']);
	$category_id = isset($_POST['edit_category_id']) ? intval($_POST['edit_category_id']) : null;
	$image_url = uploadArticleImage('edit_image');

	// allow update if the logged-in user is the author
	$article = $articleObj->getArticles($article_id);
	if ($article && $article['author_id'] == $_SESSION['user_id']) {
		// Always set status to pending 
		$result = $articleObj->updateArticle($article_id, $title, $content, 0, $image_url, $category_id); 
		if ($result) {
			$_SESSION['message'] = "Your article was updated and is pending admin approval.";
			$_SESSION['status'] = '200';
		} else {
			$_SESSION['message'] = "Failed to update your article.";
			$_SESSION['status'] = '400';
		}
	} else {
		$_SESSION['message'] = "You do not have permission to edit this article.";
		$_SESSION['status'] = '400';
	}
	header("Location: ../writer/index.php");
	exit();
}

// Writer requests edit access for an article
if (isset($_POST['requestEditBtn']) && !$userObj->isAdmin()) {
	$article_id = intval($_POST['edit_request_article_id']);
	$requester_id = $_SESSION['user_id'];
	$article = $articleObj->getArticles($article_id);
	if ($article) {
		$author_id = $article['author_id'];
		if ($editRequestObj->createRequest($article_id, $requester_id)) {
			$msg = "Edit request submitted for your article '<b>" . htmlspecialchars($article['title']) . "</b>'.";
			$notificationObj->send($author_id, $msg);
			$_SESSION['message'] = "Edit request submitted.";
			$_SESSION['status'] = '200';
		} else {
			$_SESSION['message'] = "Failed to submit edit request.";
			$_SESSION['status'] = '400';
		}
	}
	header("Location: ../writer/articles_submitted.php");
	exit();
}

// Writer handles edit request (accept/reject)
if (isset($_POST['handleEditRequestBtn']) && !$userObj->isAdmin()) {
	$request_id = intval($_POST['edit_request_id']);
	$action = $_POST['edit_request_action'];
	$status = ($action === 'accept') ? 'accepted' : 'rejected';
	if ($editRequestObj->updateRequestStatus($request_id, $status)) {

		if ($status === 'accepted') {
			// Get request
			$request = null;
			$requests = $editRequestObj->getRequestsForAuthor($_SESSION['user_id']);
			foreach ($requests as $r) {
				if ($r['request_id'] == $request_id) {
					$request = $r;
					break;
				}
			}
			if ($request) {
				$editRequestObj->grantSharedAccess($request['article_id'], $request['requester_id'], $_SESSION['user_id']);
			}
		}
		$_SESSION['message'] = "Edit request $status.";
		$_SESSION['status'] = '200';
	} else {
		$_SESSION['message'] = "Failed to update request.";
		$_SESSION['status'] = '400';
	}
	header("Location: ../writer/articles_submitted.php");
	exit();
}

// Admin handles edit request 
if (isset($_POST['handleEditRequestAdminBtn']) && $userObj->isAdmin()) {
	$request_id = intval($_POST['edit_request_id']);
	$action = $_POST['edit_request_action'];
	$status = ($action === 'accept') ? 'accepted' : 'rejected';
	// Get request 
	$requestArr = $editRequestObj->getRequestById($request_id);
	$request = is_array($requestArr) && count($requestArr) > 0 ? $requestArr[0] : null;
	if ($editRequestObj->updateRequestStatus($request_id, $status)) {
		if ($status === 'accepted' && $request && isset($request['article_id'], $request['requester_id'])) {
			$editRequestObj->grantSharedAccess($request['article_id'], $request['requester_id'], $_SESSION['user_id']);
		}
		if ($request && isset($request['article_id'], $request['requester_id'])) {
			// Get article title for notification
			$article = $articleObj->getArticles($request['article_id']);
			$articleTitle = $article && isset($article['title']) ? $article['title'] : $request['article_id'];
			$msg = ($status === 'accepted')
				? "Your edit request for article '<b>" . htmlspecialchars($articleTitle) . "</b>' was accepted by the admin."
				: "Your edit request for article '<b>" . htmlspecialchars($articleTitle) . "</b>' was rejected by the admin.";
			$notificationObj->send($request['requester_id'], $msg);
		}
		$_SESSION['message'] = "Edit request $status.";
		$_SESSION['status'] = '200';
	} else {
		$_SESSION['message'] = "Failed to update request.";
		$_SESSION['status'] = '400';
	}
	header("Location: ../admin/articles_submitted.php");
	exit();
}
// Writer edits a shared article
if (isset($_POST['editSharedArticleBtn']) && !$userObj->isAdmin()) {
	$article_id = intval($_POST['edit_shared_article_id']);
	$title = trim($_POST['edit_title']);
	$content = trim($_POST['edit_content']);
	$category_id = isset($_POST['edit_category_id']) ? intval($_POST['edit_category_id']) : null;
	$image_url = uploadArticleImage('edit_image');

	// Only allow update if the article is shared
	$sharedArticles = $editRequestObj->getSharedArticles($_SESSION['user_id']);
	$canEdit = false;
	foreach ($sharedArticles as $a) {
		if ($a['article_id'] == $article_id) {
			$canEdit = true;
			break;
		}
	}
	if ($canEdit) {
		$result = $articleObj->updateArticle($article_id, $title, $content, 1, $image_url, $category_id);
		if ($result) {
			$_SESSION['message'] = "Shared article updated successfully!";
			$_SESSION['status'] = '200';
		} else {
			$_SESSION['message'] = "Failed to update shared article.";
			$_SESSION['status'] = '400';
		}
	} else {
		$_SESSION['message'] = "You do not have access to edit this article.";
		$_SESSION['status'] = '400';
	}
	header("Location: ../writer/shared_articles.php");
	exit();
}

	// admin category management
	if (isset($_POST['addCategoryBtn'])) {
  $categoryName = trim($_POST['category_name']);
  if ($categoryName !== '') {
    if ($categoryObj->createCategory($categoryName)) {
      $_SESSION['message'] = "Category added!";
			$_SESSION['status'] = '200';
    } else {
      $_SESSION['message'] = "Failed to add category.";
			$_SESSION['status'] = '400';
    }
  }
	header("Location: ../admin/set_category.php");
	exit();
}
if (isset($_POST['updateCategoryBtn'])) {
  $categoryId = intval($_POST['edit_category_id']);
  $categoryName = trim($_POST['edit_category_name']);
  if ($categoryId && $categoryName !== '') {
    if ($categoryObj->updateCategory($categoryId, $categoryName)) {
      $_SESSION['message'] = "Category updated!";
			$_SESSION['status'] = '200';
    } else {
      $_SESSION['message'] = "Failed to update category.";
			$_SESSION['status'] = '400';
    }
  }
	header("Location: ../admin/set_category.php");
	exit();
}
if (isset($_POST['deleteCategoryBtn'])) {
  $categoryId = intval($_POST['delete_category_id']);
  if ($categoryId) {
    if ($categoryObj->deleteCategory($categoryId)) {
      $_SESSION['message'] = "Category deleted!";
			$_SESSION['status'] = '200';
    } else {
      $_SESSION['message'] = "Failed to delete category.";
			$_SESSION['status'] = '400';
    }
  }
	header("Location: ../admin/set_category.php");
	exit();
}
?>