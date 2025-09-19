<?php 

require_once 'database.php';

class Category extends Database {
    /**
     *Create a new category
     * @param string $name
     * @return int
     */
    public function createCategory($name) {
        $sql = "INSERT INTO categories (name) VALUES (?)";
        return $this->executeNonQuery($sql, [$name]);
    }

    /**
     * 
     * @return array
     */
    public function getAllCategories() {
        $sql = "SELECT * FROM categories ORDER BY name ASC";
        return $this->executeQuery($sql);
    }
    
    /**
     *
     * @param int $category_id
     * @param string $name
     * @return int
     */
    public function updateCategory($category_id, $name) {
        $sql = "UPDATE categories SET name = ? WHERE category_id = ?";
        return $this->executeNonQuery($sql, [$name, $category_id]);
    }

    /**
     *
     * @param int $category_id
     * @return int
     */
    public function deleteCategory($category_id) {
        $sql = "DELETE FROM categories WHERE category_id = ?";
        return $this->executeNonQuery($sql, [$category_id]);
    }
}
?>
