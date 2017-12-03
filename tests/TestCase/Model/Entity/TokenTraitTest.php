<?php
namespace Token\Test\TestCase\Model\Entity;

use Cake\I18n\Time;
use Cake\ORM\Entity;
use Cake\TestSuite\TestCase;
use Token\Model\Entity\TokenTrait;

class Token extends Entity
{
    use TokenTrait;
}

class TokenTraitTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    public function tearDown()
    {
        parent::tearDown();
    }

    /**
     * @expectedException \LogicException
     */
    public function testExceptionTokenGenerate1()
    {
        $token = new Token(['id' => 1]);
        $token->tokenGenerate();
    }

    /**
     * @expectedException \LogicException
     */
    public function testExceptionTokenGenerate2()
    {
        $token = new Token(['id' => 1, 'modified' => null]);
        $token->tokenGenerate();
    }

    public function testTokenGenerate()
    {
        $token = new Token(['id' => 1, 'modified' => Time::now()]);
        $result = $token->tokenGenerate();

        $this->assertInternalType('string', $result);
        $this->assertEquals(1, \Token\Util\Token::getId($result));
    }

    /**
     * @expectedException \LogicException
     */
    public function testExceptionTokenVerify()
    {
        $token = new Token(['id' => 1, 'modified' => null]);
        $token->tokenVerify('');
    }

    public function testTokenVerify()
    {
        $token = new Token(['id' => 1, 'modified' => new Time('2017-01-01')]);
        $tokenString = $token->tokenGenerate();
        $this->assertTrue($token->tokenVerify($tokenString));

        $token->set('modified', new Time('2017-01-02'));
        $this->assertFalse($token->tokenVerify($tokenString));
        $this->assertFalse($token->tokenVerify('test1234567'));
    }

    public function testSetTokenData()
    {
        $token = new Token(['id' => 1, 'modified' => Time::now()]);
        $token
            ->setTokenData('name1', 'value1')
            ->setTokenData('name2', 'value2');
        $result = $token->tokenGenerate();

        $this->assertInternalType('string', $result);
        $this->assertEquals('value1', \Token\Util\Token::getData($result, 'name1'));
        $this->assertEquals('value2', \Token\Util\Token::getData($result, 'name2'));
    }
}
