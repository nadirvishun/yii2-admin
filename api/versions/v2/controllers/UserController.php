<?php

namespace api\versions\v2\controllers;

use yii\rest\ActiveController;

class UserController extends ActiveController
{
    public $modelClass= 'api\versions\v2\models\User';
}