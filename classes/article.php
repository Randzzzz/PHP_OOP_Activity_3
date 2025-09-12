<?php  

require_once 'database.php';
require_once 'user.php';
class Article extends Database {
    /**
     * Creates a new article.
     * @param string $title The article title.
     * @param string $content The article content.
     * @param int $author_id The ID of the author.
     * @return int The ID of the newly created article.
     */
    public function createArticle($title, $content, $author_id) {
        $sql = "INSERT INTO articles (title, content, author_id, is_active) VALUES (?, ?, ?, 0)";
        return $this->executeNonQuery($sql, [$title, $content, $author_id]);
    }

    /**
     * Creates a new article with optional image
     * @param string $title
     * @param string $content
     * @param int $author_id
     * @param string|null $image_url
     * @return int
     */
    public function createArticleWithImage($title, $content, $author_id, $image_url = null) {
        if ($image_url) {
            $sql = "INSERT INTO articles (title, content, author_id, image_url, is_active) VALUES (?, ?, ?, ?, 0)";
            return $this->executeNonQuery($sql, [$title, $content, $author_id, $image_url]);
        } else {
            return $this->createArticle($title, $content, $author_id);
        }
    }

    /**
     * Retrieves articles from the database.
     * @param int $id 
     * @return array
     */
    public function getArticles($id = null) {
        if ($id) {
            $sql = "SELECT * FROM articles WHERE article_id = ?";
            return $this->executeQuerySingle($sql, [$id]);
        }
        $sql = "SELECT * FROM articles 
                JOIN users ON 
                articles.author_id = users.user_id 
                ORDER BY articles.created_at DESC";

        return $this->executeQuery($sql);
    }

    public function getActiveArticles($id = null) {
        if ($id) {
            $sql = "SELECT * FROM articles WHERE article_id = ?";
            return $this->executeQuerySingle($sql, [$id]);
        }
        $sql = "SELECT * FROM articles 
                JOIN users ON 
                articles.author_id = users.user_id 
                WHERE is_active = 1 ORDER BY articles.created_at DESC";
                
        return $this->executeQuery($sql);
    }

    public function getArticlesByUserID($user_id) {
        $sql = "SELECT * FROM articles 
                JOIN users ON 
                articles.author_id = users.user_id
                WHERE author_id = ? ORDER BY articles.created_at DESC";
        return $this->executeQuery($sql, [$user_id]);
    }

    /**
     * Toggles the visibility (is_active status) of an article.
     * This operation is restricted to admin users only.
     * @param int $id The article ID to update.
     * @param bool $is_active The new visibility status.
     * @return int The number of affected rows.
     */
    public function updateArticleVisibility($id, $is_active) {
        $sql = "UPDATE articles SET is_active = ? WHERE article_id = ?";
        return $this->executeNonQuery($sql, [$is_active, $id]);
    }

        /**
     * Admin updates an article (title, content, status, and optionally image)
     * @param int $id
     * @param string $title
     * @param string $content
     * @param int $status
     * @param string|null $image_url
     * @return int Number of affected rows
     */
    public function updateArticleAdmin($id, $title, $content, $status, $image_url = null) {
        if ($image_url) {
            $sql = "UPDATE articles SET title = ?, content = ?, image_url = ?, is_active = ? WHERE article_id = ?";
            return $this->executeNonQuery($sql, [$title, $content, $image_url, $status, $id]);
        } else {
            $sql = "UPDATE articles SET title = ?, content = ?, is_active = ? WHERE article_id = ?";
            return $this->executeNonQuery($sql, [$title, $content, $status, $id]);
        }
    }

    /**
     * Deletes an article.
     * @param int $id The article ID to delete.
     * @return int The number of affected rows.
     */
    public function deleteArticle($id) {
        $sql = "DELETE FROM articles WHERE article_id = ?";
        return $this->executeNonQuery($sql, [$id]);
    }

    /**
     * Deletes an article and notifies the author.
     * @param int $article_id
     * @param Notification $notificationObj
     * @return bool
     */
    public function deleteArticleWithNotification($article_id, $notificationObj) {
        $article = $this->getArticles($article_id);
        if ($article) {
            $author_id = $article['author_id'];
            $title = $article['title'];
            $deleted = $this->deleteArticle($article_id);
            if ($deleted) {
                $msg = "Your article '<b>" . htmlspecialchars($title) . "</b>' was deleted by an admin.";
                $notificationObj->send($author_id, $msg);
                return true;
            }
        }
        return false;
    }

    /**
     * Set article as rejected
     * @param int $article_id
     * @return int
     */
    public function rejectArticle($article_id) {
        $sql = "UPDATE articles SET is_active = -1 WHERE article_id = ?";
        return $this->executeNonQuery($sql, [$article_id]);
    }
}
?>