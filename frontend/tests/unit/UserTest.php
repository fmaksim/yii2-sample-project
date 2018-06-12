<?php

namespace frontend\tests;

use frontend\tests\fixtures\UserFixture;

class UserTest extends \Codeception\Test\Unit
{
    /**
     * @var \frontend\tests\UnitTester
     */
    protected $tester;

    public function _fixtures()
    {
        return ["users" => UserFixture::className()];
    }

    public function testGetNicknameOnEmpty()
    {

        $user = $this->tester->grabFixture('users', 'user1');
        expect($user->getNickname())->equals(1);

    }

    public function testGetNicknameInNotEmpty()
    {
        $user = $this->tester->grabFixture('users', 'user2');
        expect($user->getNickname())->equals('test');
    }

    protected function _before()
    {
    }

    protected function _after()
    {
    }
}