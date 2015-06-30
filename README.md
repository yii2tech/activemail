ActiveMail Extension for Yii 2
==============================

This extension provides 'active mail message' concept implementation for Yii2.
Active message is a model, which knows all necessary data for self composition and can send itself.

For license information check the [LICENSE](LICENSE.md)-file.

[![Latest Stable Version](https://poser.pugx.org/yii2tech/install/v/stable.png)](https://packagist.org/packages/yii2tech/install)
[![Total Downloads](https://poser.pugx.org/yii2tech/install/downloads.png)](https://packagist.org/packages/yii2tech/install)
[![Build Status](https://travis-ci.org/yii2tech/install.svg?branch=master)](https://travis-ci.org/yii2tech/install)


Requirements
------------

This extension requires any implementation of the Yii2 mailer, such as [yiisoft/yii2-swiftmailer](https://github.com/yiisoft/yii2-swiftmailer).


Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist yii2tech/activemail
```

or add

```json
"yii2tech/activemail": "*"
```

to the require section of your composer.json.

> Note: you should install particular mailer extension such as 'yiisoft/yii2-swiftmailer' separately.


Usage
-----

This extension provides 'active mail message' concept implementation for Yii2.
Active message is a model, which knows all necessary data for self composition and can send itself.

For example:

```php
namespace app\mail\active;

use yii2tech\activemail\ActiveMessage;
use Yii;

class ContactUs extends ActiveMessage
{
    public $name;
    public $email;
    public $message;
    public $subject;

    public function rules()
    {
        return [
            [$this->attributes, 'required'],
            ['email', 'email'],
        ];
    }

    public function defaultFrom()
    {
        return Yii::$app->params['applicationEmail'];
    }

    public function defaultTo()
    {
        return Yii::$app->params->mail['adminEmail'];
    }

    public function defaultSubject()
    {
        return 'Contact: {subject}';
    }

    public function defaultBodyHtml()
    {
        return <<<BODY
Email: <a href="mailto:{email}">{email}</a><br>
Name: {name}<br>
<hr>
{subject}
<hr>
{message}
BODY;
    }
}
```
