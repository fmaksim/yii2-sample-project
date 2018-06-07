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

    protected $subscriptionService;
    protected $fileStorage;
    protected $redisStorage;

    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->subscriptionService = Yii::createObject(['class' => 'frontend\components\SubscriptionService']);
        $this->fileStorage = Yii::createObject(['class' => 'frontend\components\storage\Storage']);
        $this->redisStorage = Yii::createObject(['class' => 'frontend\components\storage\RedisStorage'])->getStorage();
    }

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
        return ($this->picture) ? $this->fileStorage->getFile($this->picture) : null;
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

    public function getFeed(int $limit): array
    {
        $order = ["post_created_at" => SORT_DESC];
        return $this
            ->hasMany(Feed::className(), ["user_id" => "id"])
            ->orderBy($order)
            ->limit($limit)
            ->all();
    }

    public function getSubscriptions(): array
    {

        $key = "user:{$this->getId()}:subscriptions";
        $ids = $this->redisStorage->smembers($key);

        return User::find()->select("username, nickname, id")
            ->where(["id" => $ids])
            ->orderBy("username")
            ->asArray()
            ->all();
    }

    public function getFollowers(): array
    {

        $key = "user:{$this->getId()}:followers";
        $ids = $this->redisStorage->smembers($key);

        return User::find()
            ->select("username, nickname, id")
            ->where(["id" => $ids])
            ->orderBy("username")
            ->asArray()
            ->all();

    }

    public function getFollowersCount(): int
    {
        return $this->redisStorage->scard("user:{$this->getId()}:followers");
    }

    public function getSubscriptionsCount(): int
    {
        return $this->redisStorage->scard("user:{$this->getId()}:subscriptions");
    }

    public function getMutualSubscriptionsTo(User $user): array
    {

        $ownSubscriptions = "user:{$this->getId()}:subscriptions";
        $userFollowers = "user:{$user->getId()}:followers";

        $ids = $this->redisStorage->sinter($ownSubscriptions, $userFollowers);

        return User::find()
            ->select("id, username, nickname")
            ->where(["id" => $ids])
            ->orderBy("username")
            ->asArray()
            ->all();

    }

    public function isShowFollowBlock($mutualSubscriptions): bool
    {
        return (count($mutualSubscriptions) > 0 and !Yii::$app->user->isGuest) ? true : false;
    }

    public function isIAm(User $user): bool
    {
        return ($this->getId() === $user->getId()) ? true : false;
    }

    public function getPosts()
    {
        $order = ["created_at" => SORT_DESC];
        return $this->hasMany(Post::className(), ["user_id" => "id"])->orderBy($order);
    }

}