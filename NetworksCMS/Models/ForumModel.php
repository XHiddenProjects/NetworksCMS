<?php
namespace NetWorks\Model;
include_once dirname(path: __DIR__).'/init.php';
class ForumModel extends Model{
    /**
     * Get list of forums
     * @param string $conditions Conditions to follow
     * @return array|bool Get user list
     */
    public function getForums(string $conditions=''): array|bool{
        return $this->GET(table: 'forums',selector: '*',conditions: $conditions);
    }
}
?>