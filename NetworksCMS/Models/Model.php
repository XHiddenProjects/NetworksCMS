<?php
namespace NetWorks\Model;

use NetWorks\libs\Database;
include_once dirname(path: __DIR__).'/init.php';
class Model{
    protected Database $conn;
    public function __construct() {
        $this->conn = new Database(file: 'NetworksCMS',flags: Database::OPEN_READWRITE);
    }
    /**
     * Return GET method
     * @param string $table Table
     * @param array|string $selector Selection
     * @param string $conditions Conditions
     * @return array|bool array of data
     */
    public function GET(string $table, array|string $selector, string $conditions): array|bool{
        return $this->conn->selectTable(name: $table)->select(name: null, selector: $selector, conditions: $conditions);
    }
}
?>