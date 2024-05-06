<?php 
require_once("class.db.php");

class ArticleManager {
    private $db;

    
    function __construct() {
        $this->db = new DB();
    }

    function updateArticle($data, $where) {
        return $this->db->updateQuery("articles", $data, $where);
    }
}
?>
