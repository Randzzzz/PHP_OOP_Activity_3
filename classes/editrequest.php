<?php
require_once 'database.php';

class EditRequest extends Database {
    /**
     * Get a single edit request by its ID
     * @param int $request_id
     * @return array|null
     */
    public function getRequestById($request_id) {
        $sql = "SELECT * FROM edit_requests WHERE request_id = ?";
        return $this->executeQuery($sql, [$request_id]);
    }

    /**
     * 
     * @param int $article_id
     * @param int $requester_id
     * @return bool
     */
    public function createRequest($article_id, $requester_id) {
        $sql = "INSERT INTO edit_requests (article_id, requester_id) VALUES (?, ?)";
        return $this->executeNonQuery($sql, [$article_id, $requester_id]);
    }
    
    /**
     * get all pending edit requests
     * @return array
     */
    public function getAllPendingRequests() {
        $sql = "SELECT edit_requests.*, articles.title, articles.article_id, users.first_name, users.last_name FROM edit_requests
                JOIN articles ON edit_requests.article_id = articles.article_id
                JOIN users ON edit_requests.requester_id = users.user_id
                WHERE edit_requests.status = 'pending'";
        return $this->executeQuery($sql);
    }

    /**
     * get all edit requests for an article's author (admin or writer)
     * @param int $author_id
     * @return array
     */
    public function getRequestsForAuthor($author_id) {
        $sql = "SELECT edit_requests.*, articles.title, users.first_name, users.last_name FROM edit_requests
                JOIN articles ON edit_requests.article_id = articles.article_id
                JOIN users ON edit_requests.requester_id = users.user_id
                WHERE articles.author_id = ? AND edit_requests.status = 'pending'";
        return $this->executeQuery($sql, [$author_id]);
    }

    /**
     * Get all edit requests made by a writer
     * @param int $writer_id
     * @return array
     */
    public function getRequestsByWriter($writer_id) {
        $sql = "SELECT edit_requests.*, articles.title FROM edit_requests
                JOIN articles ON edit_requests.article_id = articles.article_id
                WHERE edit_requests.requester_id = ?";
        return $this->executeQuery($sql, [$writer_id]);
    }

    /**
     * accept/reject status update
     * @param int $request_id
     * @param string $status
     * @return bool
     */
    public function updateRequestStatus($request_id, $status) {
        $sql = "UPDATE edit_requests SET status = ? WHERE request_id = ?";
        return $this->executeNonQuery($sql, [$status, $request_id]);
    }

    /**
     * Grant shared access to an article for a writer
     * @param int $article_id
     * @param int $writer_id
     * @param int $granted_by
     * @return bool
     */
    public function grantSharedAccess($article_id, $writer_id, $granted_by) {
        $sql = "INSERT INTO shared_articles (article_id, shared_with, granted_by) VALUES (?, ?, ?)";
        return $this->executeNonQuery($sql, [$article_id, $writer_id, $granted_by]);
    }

    /**
     *
     * @param int $writer_id
     * @return array
     */
    public function getSharedArticles($writer_id) {
        $sql = "SELECT shared_articles.*, articles.*, users.first_name, users.last_name FROM shared_articles
                JOIN articles ON shared_articles.article_id = articles.article_id
                JOIN users ON articles.author_id = users.user_id
                WHERE shared_articles.shared_with = ?
                ORDER BY shared_articles.created_at DESC";
        return $this->executeQuery($sql, [$writer_id]);
    }
}
