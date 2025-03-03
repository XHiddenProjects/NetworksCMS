<?php
namespace NetWorks\Model;
include_once dirname(path: __DIR__).'/init.php';
class TopicsModel extends Model{
    /**
     * Get list of topics
     * @param string $conditions Conditions to follow
     * @return array|bool Get user list
     */
    public function getTopics(string $conditions=''): array|bool{
        return $this->GET(table: 'topics',selector: '*',conditions: $conditions);
    }
}
?>