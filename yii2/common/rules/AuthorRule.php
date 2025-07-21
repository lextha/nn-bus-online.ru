<?
namespace common\rules;
 
use yii\rbac\Rule;
 
class AuthorRule extends Rule
{
    public $name = 'isAuthor'; // Имя правила
 
    public function execute($user_id, $item, $params)
    {
      //  return true;
       // var_dump(isset($params['author_id']) AND ($params['author_id']==$user_id));
        if (isset($params['author_id']) AND ($params['author_id']==$user_id)) {
            return true;
        } else {
            return false;
        }
    }
}