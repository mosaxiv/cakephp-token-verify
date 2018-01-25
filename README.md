# Token Verify plugin for CakePHP3

[![MIT License](http://img.shields.io/badge/license-MIT-blue.svg?style=flat)](LICENSE)
[![Build Status](https://travis-ci.org/mosaxiv/cakephp-token-verify.svg?branch=master)](https://travis-ci.org/mosaxiv/cakephp-token-verify)

JWT for mail authentication.  

Easily issue tokens(JWT) that can be used for mail authentication.  
No need for token field in table.  
one-time/url-safe/safety :+1:

# Requirements

- PHP 7.0+
- CakePHP 3.0.0+

# Installation

```
composer require mosaxiv/cakephp-token-verify
```

# Example

## reset password

```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY, # Required
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    created DATETIME,
    modified DATETIME # Required
);
```

```php
// app/src/Model/Entity/User.php

use Token\Model\Entity\TokenTrait;

class User extends Entity
{
    use TokenTrait;
}

```

```php
// app/src/Controller/UsersController.php

use Cake\Routing\Router;
use Token\Util\Token;

class UsersController extends AppController
{

    public function forgotPassword()
    {
        if ($this->request->is('post')) {
            $email = $this->request->getData('email');
            $user = $this->Users->findByEmail($email)->first();
            if ($user) {
                $token = $user->tokenGenerate();
                $url = Router::url(['controller' => 'User', 'action' => 'resetPassword', $token], true);
                // send email
            }
        }
    }

    public function resetPassword($token)
    {
        $user = $this->Users->get(Token::getId($token));
        if (!$user->tokenVerify($token)) {
            throw new \Cake\Network\Exception\NotFoundException();
        }

        if ($this->request->is('post')) {
            $user = $this->Users->patchEntity($user, $this->request->getData());
            if ($this->Users->save($user)) {
                // success
            } else {
                // error
            }
        }
    }
}
```


# Usage

## Required database field

* `id` field
* `modified` field

By using modified field, JWT can be used as one-time tokens.  
JWT should be discarded when the table is updated.

## Token\Model\Entity\TokenTrait

Used in entity.

### tokenGenerate($minits = 10)

```php
// token generate(default token expiration in 10 minits)
$token = $entity->tokenGenerate();

// token generate(token expiration in 60 minits)
$token = $entity->tokenGenerate(60);
```

### tokenVerify($token)

```php
$user->tokenVerify($token) // true or false
```

### setTokenData($name, $value)

※ It does not encrypt the set data

```php
$user->setTokenData('test', 'testdata')
```

## Token\Util\Token

### Token::getId($token)

```php
Token::getId($token) // id or false
```

### Token::getData($token, $name)

```php
Token::getData($token, 'test') // data or false
```
