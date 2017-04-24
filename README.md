ActiveMail Extension for Yii 2
==============================

This extension provides 'active mail message' concept implementation for Yii2.
Active message is a model, which knows all necessary data for self composition and can send itself.

For license information check the [LICENSE](LICENSE.md)-file.

[![Latest Stable Version](https://poser.pugx.org/yii2tech/activemail/v/stable.png)](https://packagist.org/packages/yii2tech/activemail)
[![Total Downloads](https://poser.pugx.org/yii2tech/activemail/downloads.png)](https://packagist.org/packages/yii2tech/activemail)
[![Build Status](https://travis-ci.org/yii2tech/activemail.svg?branch=master)](https://travis-ci.org/yii2tech/activemail)


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
ActiveMessage is a model, which knows all necessary data for self composition and can send itself.
It allows email message composition based on templates stored inside PHP files or database.

In order to use this extension you need to add mail template storage component to your application:

```php
return [
    'components' => [
        'mailTemplateStorage' => [
            'class' => 'yii2tech\activemail\TemplateStoragePhp',
            'templatePath' => '@app/mail/templates',
        ],
        // ...
    ],
    // ...
];
```


## ActiveMessage <span id="active-message"></span>

Each particular active message should extend [[\yii2tech\activemail\ActiveMessage]] class, implementing at least
all abstract methods, which guarantees particular active message has default values for each necessary part.
As a regular model it can contain attributes, which are defined via public fields. Validation rules can be setup
for those attributes.
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

Once declared active message can be used as regular model inside controller:

```php
use app\mail\active\ContactUs;

// ...

public function actionContact()
{
    $model = new ContactUs();
    if ($model->load(Yii::$app->request->post()) && $model->send()) {
        Yii::$app->session->setFlash('contactFormSubmitted');
        return $this->refresh();
    }
    return $this->render('contact', [
        'model' => $model,
    ]);
}
```

[[\yii2tech\activemail\ActiveMessage]] uses regular Yii2 mail composition mechanism based on view files.
By default it uses internal view provided by this extension. However in order to work properly it obviously
requires layout view to exist.
Each particular active message may specify its own view via `viewName()` method declaration.
The most basic content for such view would be following:

```php
<?php
/* @var $this yii\web\View */
/* @var $activeMessage yii2tech\activemail\ActiveMessage */

echo $activeMessage->getBodyHtml();
?>
```


## Working with placeholders <span id="working-with-placeholders"></span>

Each part of active message such as subject or body may contain placeholders in format: `{placeholderName}`.
While message composition these placeholders will be replaced by thier actual values. The actual placeholders
are defined via `templatePlaceholders()` method. By default it it uses current active message attribute values,
but you may override it in order to add extra placeholders:

```php
public function templatePlaceholders()
{
    return array_merge(
        parent::templatePlaceholders(),
        [
            'nowDate' => date('Y-m-d')
        ]
    );
}
```

[[\yii2tech\activemail\ActiveMessage]] also declares `templatePlaceholderHints()` method, which can be used
to specify hints for each used placeholder. You may use it, while composing edit form for the mail template.


## Template usage <span id="template-usage"></span>

The main benefit of [[\yii2tech\activemail\ActiveMessage]] usage is mail template feature.
Each active message can have a named template, which overrides its default values for subject, body etc.
The template name is defined via `templateName()` method. By default the active message class base name is used.

Actual template source is defined via 'mail template storage' component, which has been already mentioned above.

Following template storages are available:
 - [[\yii2tech\activemail\TemplateStoragePhp]] - stores templates inside PHP files
 - [[\yii2tech\activemail\TemplateStorageDb]] - stores templates inside relational database
 - [[\yii2tech\activemail\TemplateStorageMongoDb]] - stores templates inside MongoDB
 - [[\yii2tech\activemail\TemplateStorageActiveRecord]] - finds templates using ActiveRecord

Please refer to the particular storage class for more details.

For example: assume we use [[\yii2tech\activemail\TemplateStoragePhp]] as template storage. In order to define
a template for our `app\mail\active\ContactUs` active message, we should create a file under '@app/mail/templates'
named 'ContactUs.php' with following content:

```php
<?php

return [
    'subject' => 'Override',
    'htmlBody' => 'Override:<br>{message}',
];
```

After this is done, values from this file for 'subject' and 'htmlBody' will override default ones
declared by `app\mail\active\ContactUs`.

This feature may prove itself very useful, while creating multi-lingual sites. In this case you can declare
`templateName()` method for active message as following:

```php
class ContactUs extends ActiveMessage
{
    // ...

    public function templateName()
    {
        return Yii::$app->language . DIRECTORY_SEPARATOR . 'ContactUs';
    }
}
```

Then you may create multiple templates named 'ContactUs' under sub-directories, which names matching particular
language code like 'en-US', 'de' and so on.

Using database template storages allows application administrator override mail messages content if necessary,
by inserting corresponding row into a table and restore default value by deleting it.

> Note: templates are meant to override default active message values, thus if particular template is missing
  in the storage, the program will NOT trigger any error or throw any exception.


## Template management <span id="template-management"></span>

The most common reason of using special mail template system is allowing application administrator to edit them
via web interface. In order to simplify such feature creation, this extension provides [[\yii2tech\activemail\TemplateModelFinder]]
class, which allows listing all available active messages and created templates.
The search model for the active messages can look like following:

```php
use yii\base\Model;
use yii\data\ArrayDataProvider;
use yii2tech\activemail\TemplateModelFinder;
use app\models\MailTemplate;

class MailTemplateSearch extends Model
{
    public $name;
    public $subject;

    public function search()
    {
        // get raw data
        $finder = new TemplateModelFinder([
            'activeRecordClass' => MailTemplate::className();
        ]);
        $models = $finder->findAllTemplateModels();

        // filter list :
        $filterModel = $this;
        $models = array_filter($models, function ($model) use ($filterModel) {
            /* @var $model MailTemplate */
            if (!empty($filterModel->name)) {
                if ($filterModel->name != $model->name) {
                    return false;
                }
            }
            if (!empty($filterModel->subject)) {
                if (strpos($model->subject, $filterModel->subject) === false) {
                    return false;
                }
            }
            return true;
        });

        // compose data provider
        return new ArrayDataProvider([
            'allModels' => $models,
            'sort' => [
                'attributes' => ['name', 'subject'],
            ],
        ]);
    }
}
```

The web controller for email templates can look like following:

```php
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use Yii;
use app\models\MailTemplate;
use app\models\MailTemplateSearch;

class MailTemplateController extends Controller
{
    public function actionIndex()
    {
        $searchModel = new MailTemplateSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionUpdate($name)
    {
        $finder = new TemplateModelFinder([
            'activeRecordClass' => MailTemplate::className();
        ]);

        $model = $finder->findTemplateModel($name);
        if ($model === null) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }
}
```
