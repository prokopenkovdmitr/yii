<?php
use hail812\adminlte3\assets\AdminLteAsset;

AdminLteAsset::register($this);
$this->beginPage();
?>
<!DOCTYPE html>
<html>
<head>
    <title><?= $this->title ?></title>
    <?php $this->head() ?>
</head>
<body class="hold-transition sidebar-mini">
<?php $this->beginBody() ?>
<div class="wrapper">
    <?= $this->render('//layouts/_header') ?>
    <?= $this->render('//layouts/_sidebar') ?>
    <div class="content-wrapper">
        <?= $content ?>
    </div>
    <?= $this->render('//layouts/_footer') ?>
</div>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>