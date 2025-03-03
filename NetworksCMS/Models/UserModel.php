<?php
namespace NetWorks\Model;
include_once dirname(path: __DIR__).'/init.php';
class UserModel extends Model{
    /**
     * Get list of users
     * @param string $conditions Conditions to follow
     * @return array|bool Get user list
     */
    public function getUsers(string $conditions=''): array|bool{
        return $this->GET(table: 'users',selector: ['fname','mname','lname','username','ip','os','browser','joined'],conditions: $conditions);
    }
}
?>