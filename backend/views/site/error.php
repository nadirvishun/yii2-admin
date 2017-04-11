<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

$this->title = $name;
?>
<section class="content">

    <div class="error-page">
        <h2 class="headline text-info"><i class="fa fa-warning text-yellow"></i></h2>

        <div class="error-content">
            <h3><?= $name ?></h3>

            <p>
                <?= nl2br(Html::encode($message)) ?>
            </p>

            <p>
                <?= Yii::t('common','The above error occurred while the Web server was processing your request.')?>
                <br>
                <?= Yii::t('common','Please contact us if you think this is a server error. Thank you.')?>
                <br>
                <?= Yii::t('common','Meanwhile, you may click {homePage} return to home page or click {previousPage} return to previous page.',[
                    'homePage'=>Html::a(Yii::t('common','homePage'),Yii::$app->homeUrl),
                    'previousPage'=>Html::a(Yii::t('common','previousPage'),Yii::$app->request->referrer)
                ])?>
            </p>

            <!--<form class='search-form'>
                <div class='input-group'>
                    <input type="text" name="search" class='form-control' placeholder="Search"/>

                    <div class="input-group-btn">
                        <button type="submit" name="submit" class="btn btn-primary"><i class="fa fa-search"></i>
                        </button>
                    </div>
                </div>
            </form>-->
        </div>
    </div>

</section>
