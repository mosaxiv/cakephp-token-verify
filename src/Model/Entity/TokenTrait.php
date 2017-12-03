<?php
namespace Token\Model\Entity;

use Cake\I18n\Time;
use Cake\Utility\Security;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\ValidationData;

trait TokenTrait
{
    /**
     * @var string
     */
    protected $_idField = 'id';

    /**
     * @var string
     */
    protected $_modifiedField = 'modified';

    /**
     * @var array
     */
    protected $_tokenData = [];

    /**
     * set token data
     *
     * @param string $name claim name
     * @param mixed $value claim value
     * @return $this
     */
    public function setTokenData(string $name, $value)
    {
        $this->_tokenData[] = ['name' => $name, 'value' => $value];

        return $this;
    }

    /**
     * token generate
     *
     * @param int $minute token expiration
     * @throws \LogicException
     * @return string
     */
    public function tokenGenerate(int $minute = 10)
    {
        if (!$this->has([$this->_idField, $this->_modifiedField])) {
            throw new \LogicException();
        }

        $time = new Time();
        $time->addMinute($minute);

        $builder = new Builder();
        $builder
            ->setId($this->get($this->_idField))
            ->setExpiration($time->toUnixString());

        foreach ($this->_tokenData as $data) {
            $builder->set($data['name'], $data['value']);
        }

        $builder->sign(new Sha256(), $this->_createKey());

        return (string)$builder->getToken();
    }

    /**
     * verify & validation
     *
     * @param string $token jwt
     * @return bool
     */
    public function tokenVerify(string $token)
    {
        if (!$this->has($this->_modifiedField)) {
            throw new \LogicException();
        }

        try {
            $token = (new Parser())->parse($token);
        } catch (\Exception $e) {
            return false;
        }

        if (!$token->verify(new Sha256(), $this->_createKey())) {
            return false;
        }

        $data = new ValidationData();
        $data->setCurrentTime(Time::now()->toUnixString());
        if (!$token->validate($data)) {
            return false;
        }

        return true;
    }

    /**
     * create key
     *
     * @return string
     */
    protected function _createKey()
    {
        return Security::hash((string)$this->get($this->_modifiedField), 'sha256', true);
    }
}
