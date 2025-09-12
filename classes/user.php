<?php
require_once 'Database.php';

class User extends Database{
    /**
     * Starts a new session if one isn't already active.
     */
    public function startSession() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Checks if the email already exists in the database.
     * @param string $email The email to check.
     * @return bool True if email exists, false otherwise.
     */
    public function emailExists($email) {
        $sql = "SELECT COUNT(*) as email_count FROM users WHERE email = ?";
        $count = $this->executeQuerySingle($sql, [$email]);
        if ($count['email_count'] > 0) {
            return true;
        }
        else {
            return false;
        }
    }

    /**
     * Registers a new user.
     * @param string $email The user's email.
     * @param string $first_name The user's first name.
     * @param string $last_name The user's last name.
     * @param string $password The user's password.
     * @param string $role Whether the user is an admin.
     * @return bool True on success, false on failure.
     */
    public function registerUser($first_name, $last_name, $email, $password, $role) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (first_name, last_name, email, password, role) VALUES (?, ?, ?, ?, ?)";
        try {
            $this->executeNonQuery($sql, [$first_name, $last_name, $email, $hashed_password, $role]);
            return true;
        } catch (\PDOException $e) {
            return false;
        }
    }

    /**
     * Logs in a user by verifying credentials.
     * @param string $email The user's email.
     * @param string $password The user's password.
     * @return bool True on success, false on failure.
     */
    public function loginUser($email, $password) {
        $sql = "SELECT user_id, first_name, last_name, password, role FROM users WHERE email = ?";
        $user = $this->executeQuerySingle($sql, [$email]);

        if ($user && password_verify($password, $user['password'])) {
            $this->startSession();
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['first_name'] = $user['first_name'];
            return true;
        }
        return false;
    }

    /**
     * Checks if a user is currently logged in.
     * @return bool
     */
    public function isLoggedIn() {
        $this->startSession();
        return isset($_SESSION['user_id']);
    }

    /**
     * Checks if the logged-in user is an admin.
     * @return bool
     */
    public function isAdmin() {
        $this->startSession();
        return (isset($_SESSION['role']) && $_SESSION['role'] === 'admin');
    }

    /**
     * Logs out the current user.
     */
    public function logout() {
        $this->startSession();
        session_unset();
        session_destroy();
    }
}

?>