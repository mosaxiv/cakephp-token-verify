<?php
namespace Token\Test\TestCase\Util;

use Cake\TestSuite\TestCase;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Token\Util\Token;

class TokenTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    public function tearDown()
    {
        parent::tearDown();
    }

    public function testGetId()
    {
        $this->assertFalse(Token::getId('test123456789'));

        $builder = new Builder();
        $builder->setId(100)->sign(new Sha256(), 'key');
        $token = (string)$builder->getToken();

        $this->assertEquals(100, Token::getId($token));
    }

    public function testGetData()
    {
        $this->assertFalse(Token::getData('test123456789', 'name'));

        $builder = new Builder();
        $builder
            ->set('name1', 'value1')
            ->set('name2', 'value2')
            ->sign(new Sha256(), 'key');
        $token = (string)$builder->getToken();

        $this->assertEquals('value1', Token::getData($token, 'name1'));
        $this->assertEquals('value2', Token::getData($token, 'name2'));
        $this->assertFalse(Token::getData($token, 'name3'));
    }
}
