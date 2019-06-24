反垃圾评论
=====
反垃圾评论

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist yiier/yii2-anti-spam "*"
```

or add

```
"yiier/yii2-anti-spam": "*"
```

to the require section of your `composer.json` file.


Migrations
----------
Run the following command

```
php yii migrate --migrationPath=@yiier/antiSpam/migrations/
```

Usage
-----

Create Spam:

```php
<?php
use \yiier\antiSpam\models\Spam;

Spam::create(Spam::TYPE_CONTAINS, '网{2}赌');
Spam::create(Spam::TYPE_CONTAINS, '找小姐');
Spam::create(Spam::TYPE_SIMILAR, '网赌平台冻账号说我违规套利不给出款该怎么办？');

```


Spam Validator :

```php
public function rules()
{
    return [
        // ... 
        ['content', \yiier\antiSpam\SpamValidator::className()],
        // code
    ];
}
```