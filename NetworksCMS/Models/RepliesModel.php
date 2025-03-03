<?php
namespace NetWorks\Model;
include_once dirname(path: __DIR__).'/init.php';
class RepliesModel extends Model{
    /**
     * Get list of reply
     * @param string $conditions Conditions to follow
     * @return array|bool Get user list
     */
    public function getReplies(string $conditions=''): array|bool{
        return $this->GET(table: 'replies',selector: '*',conditions: $conditions);
    }
}
?>