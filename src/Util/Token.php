<?php
namespace Token\Util;

use Lcobucci\JWT\Parser;

class Token
{
    /**
     * get id
     *
     * @param string $token jwt
     * @return string|bool
     */
    public static function getId(string $token)
    {
        try {
            $token = (new Parser())->parse($token);

            return $token->getClaim('jti');
        } catch (\Exception $e) {
            return false;
        }
    }
}
