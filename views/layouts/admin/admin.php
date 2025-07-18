<?php
/** @var yii\web\View $this */
/** @var string $content */

use hail812\adminlte3\assets\AdminLteAsset;
use yii\bootstrap5\Html;
use yii\bootstrap5\Alert;

AdminLteAsset::register($this);

// Регистрация мета-тегов и CSRF
$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);
$this->registerMetaTag(['name' => 'description', 'content' => $this->params['meta_description'] ?? '']);
$this->registerMetaTag(['name' => 'keywords', 'content' => $this->params['meta_keywords'] ?? '']);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/x-icon', 'href' => Yii::getAlias('@web/favicon.ico')]);

$this->beginPage();
?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
    <!-- Критический CSS для ускорения рендеринга -->
    <style>
        body {
            font-family: 'Source Sans Pro', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f6f9;
        }
        .wrapper {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .content-wrapper {
            flex: 1;
            padding: 20px;
        }
        @media (max-width: 768px) {
            .content-wrapper {
                padding: 10px;
            }
        }
    </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<?php $this->beginBody() ?>
<div class="wrapper">
    <?= $this->render('//layouts/_header') ?>
    <?= $this->render('//layouts/_sidebar') ?>
    <div class="content-wrapper">
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
    <?= $this->render('//layouts/_footer') ?>
</div>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>