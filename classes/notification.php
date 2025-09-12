<?php
require_once 'database.php';

class Notification extends Database {
	/**
	 * send a notification
	 * @param int $user_id
	 * @param string $message
	 * @return bool
	 */
	public function send($user_id, $message) {
		$sql = "INSERT INTO notifications (user_id, message) VALUES (?, ?)";
		return $this->executeNonQuery($sql, [$user_id, $message]);
	}

	/**
	 *  get notifications
	 * @param int $user_id
	 * @param int $limit
	 * @return array
	 */
	public function getUserNotifications($user_id, $limit = 10) {
		$sql = "SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT ?";
		return $this->executeQuery($sql, [$user_id, $limit]);
	}
}
  