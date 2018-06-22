<?php

namespace frontend\components;


use yii\base\BootstrapInterface;
use yii\web\Cookie;
use Yii;

class LanguageSelector implements BootstrapInterface
{

    const SUPPORTED_LANGUAGES = ['en-US', 'ru-RU'];
    const MONTH_EXPIRE_TIMESTAMP = 3600 * 24 * 30;

    public function change(string $language): bool
    {
        if (!$this->isSupported($language)) {
            throw new \Exception('Selected language is not supported!');
        }

        Yii::$app->language = $language;
        $languageCookie = new Cookie([
            'name' => 'language',
            'value' => $language,
            'expire' => time() + self::MONTH_EXPIRE_TIMESTAMP, // 30 days
        ]);

        Yii::$app->response->cookies->add($languageCookie);

        return true;
    }

    private function isSupported(string $language): bool
    {
        return in_array($language, self::SUPPORTED_LANGUAGES);
    }

    public function bootstrap($app)
    {
        $cookieLanguage = $app->request->cookies['language'];
        if ($this->isSupported($cookieLanguage)) {
            $app->language = $cookieLanguage;
        }
    }

}