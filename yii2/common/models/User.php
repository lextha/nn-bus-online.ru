<?php
namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $verification_token
 * @property string $email
 * @property string $auth_key
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $password write-only password
 */
class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_INACTIVE = 9;
    const STATUS_ACTIVE = 10;
    public $password2;


    //   public $newcitis;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['status', 'default', 'value' => self::STATUS_INACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_INACTIVE, self::STATUS_DELETED]],
            [['name','username','password_hash', 'password_reset_token', 'email'], 'string', 'max' => 255],
           // [['citis']],
        ];
    }
    
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Имя',
            'status' => 'Статус',
            'username' => 'Логин',
        //    'password' => 'Пароль',
        ];
    }
    
   /* public function afterSave($insert, $changedAttributes)
    {   
        parent::afterSave($insert, $changedAttributes);
        if (is_array($this->citis)) {
            $old_citis= \yii\helpers\ArrayHelper::map($this->citis, 'id','name');
            foreach ($this->)
        } else {
            
        }        
    }*/
     public function afterFind()
    {   
       //  $this->newcitis=\yii\helpers\ArrayHelper::map($this->citis, 'id','name');
    }
     public function beforeDelete() {
         $rrrr="DELETE FROM `user_acc` WHERE user_id='".$this->id."'";
            Yii::$app->db->createCommand($rrrr)->query(); 
            
             $rrrr="DELETE FROM `auth_assignment` WHERE user_id='".$this->id."'";
            Yii::$app->db->createCommand($rrrr)->query(); 
         parent::beforeDelete();
         return true;
     }
    
    public function beforeSave($insert)
    {
        if (Yii::$app->request->post()['User']['password2']!='') {
            $this->setPassword(Yii::$app->request->post()['User']['password2']);
        }
        $this->generateAuthKey();
        $this->generateEmailVerificationToken();
        
        if (Yii::$app->request->post()['User']['citis']!='') {
            if ($this->id) {
                $rrrr="DELETE FROM `user_acc` WHERE user_id='".$this->id."'";
                Yii::$app->db->createCommand($rrrr)->query(); 

                foreach (Yii::$app->request->post()['User']['citis'] as $c) {
                     $rrrr="INSERT INTO `user_acc`(`id`, `user_id`, `city_id`) "
                                    . "VALUES (NULL,'".$this->id."','".$c."')";
                    Yii::$app->db->createCommand($rrrr)->query();  

                    $rrrr="INSERT INTO `auth_assignment`(`item_name`, `user_id`, `created_at`) "
                                    . "VALUES ('editor','".$this->id."','".time()."')";
                    Yii::$app->db->createCommand($rrrr)->query();  
                }
            }
           
        }
        
        
        parent::beforeSave($insert);
        return true;
        //  var_dump($expression)
        // var_dump($this,$this->password2,$this->citis); die();
    }
    
    public function getCitis()
    {
        return $this->hasMany(City::className(), ['id' => 'city_id'])
            ->viaTable('user_acc', ['user_id' => 'id']);
    }
    

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds user by verification email token
     *
     * @param string $token verify email token
     * @return static|null
     */
    public static function findByVerificationToken($token) {
        return static::findOne([
            'verification_token' => $token,
            'status' => self::STATUS_INACTIVE
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return bool
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
      //  var_dump($password);
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
     //   var_dump( $this->password_hash);
      //  die();
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Generates new token for email verification
     */
    public function generateEmailVerificationToken()
    {
        $this->verification_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }
}
