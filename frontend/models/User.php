<?php
namespace frontend\models;

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
 * @property string $email
 * @property string $about
 * @property string $nickname
 * @property string $picture
 * @property integer $type
 * @property string $auth_key
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $password write-only password
 */
class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;

    const DEFAULT_IMAGE = '/img/profile_default_image.jpg';


    public static function tableName()
    {
        return '{{%user}}';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    public function rules()
    {
        return [
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]],
        ];
    }

    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

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
    public static function findByEmail($email)
    {
        return static::findOne(['email' => $email, 'status' => self::STATUS_ACTIVE]);
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
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }


    /**
     * @return int|mixed|string
     */
    public function getNickname()
    {
        return ($this->nickname) ? $this->nickname : $this->getId();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    public function getPicture()
    {
        return ($this->picture) ? Yii::$app->fileStorage->getFile($this->picture) : self::DEFAULT_IMAGE;
    }

    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @inheritdoc
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
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
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
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    public function followUser(User $user)
    {
        if ($this->isIAm($user))
            return false;

        $redis = Yii::$app->redis;
        $redis->sadd("user:" . $user->getId() . ":followers", $this->getId());
        $redis->sadd("user:" . $this->getId() . ":subscriptions", $user->getId());

    }

    public function unFollowUser(User $user)
    {
        if ($this->isIAm($user))
            return false;

        $redis = Yii::$app->redis;
        $redis->srem("user:{$user->getId()}:followers", $this->getId());
        $redis->srem("user:{$this->getId()}:subscriptions", $user->getId());

    }

    public function getSubscriptions()
    {

        $redis = Yii::$app->redis;
        $key = "user:{$this->getId()}:subscriptions";
        $idS = $redis->smembers($key);

        return User::find()->select("username, nickname, id")->where(["id" => $idS])->orderBy("username")->asArray()->all();

    }

    public function getFollowers()
    {
        $redis = Yii::$app->redis;
        $key = "user:{$this->getId()}:followers";
        $idS = $redis->smembers($key);

        return User::find()->select("username, nickname, id")->where(["id" => $idS])->orderBy("username")->asArray()->all();

    }

    public function getFollowersCount()
    {
        $redis = Yii::$app->redis;
        return $redis->scard("user:{$this->getId()}:followers");
    }

    public function getSubscriptionsCount()
    {
        $redis = Yii::$app->redis;
        return $redis->scard("user:{$this->getId()}:subscriptions");
    }

    public function getMutualSubscriptionsTo(User $user)
    {
        $redis = Yii::$app->redis;

        $ownSubscriptions = "user:{$this->getId()}:subscriptions";
        $userFollowers = "user:{$user->getId()}:followers";

        $ids = $redis->sinter($ownSubscriptions, $userFollowers);

        return User::find()->select("id, username, nickname")->where(["id" => $ids])->orderBy("username")->asArray()->all();

    }

    public function isShowFollowBlock($mutualSubscriptions)
    {
        return (count($mutualSubscriptions) > 0 and !Yii::$app->user->isGuest) ? true : false;
    }

    public function isIAm(User $user)
    {
        return ($this->getId() === $user->getId()) ? true : false;
    }

    public function isCanSubscribe(User $user)
    {
        $redis = Yii::$app->redis;
        $ids = $redis->smembers("user:{$user->getId()}:followers");
        return in_array($this->getId(), $ids) ? false : true;
    }

    public function isCanUnSubscribe(User $user)
    {
        $redis = Yii::$app->redis;
        $ids = $redis->smembers("user:{$user->getId()}:followers");
        return in_array($this->getId(), $ids) ? true : false;
    }

}