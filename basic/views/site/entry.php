<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\web\JqueryAsset;

$this->registerJsFile(
    '@web/js/entry.js',
    ['depends' => [JqueryAsset::class]],
);

?>

<?php
    $form = ActiveForm::begin([
        'id' => 'entry-form',
        'action' => '/site/shorten',
        'enableAjaxValidation' => false,
        'options' => ['class' => 'd-flex justify-content-between gap-2'],
    ]);
?>
    <?=
        $form->field($model, 'url', ['options' => ['class' => 'h-100 flex-grow-1']])
            ->textInput(['placeholder' => 'Скопируйте сюда ссылку'])
            ->label(false)
    ?>
    <?= Html::submitButton('ОК', ['class' => 'btn btn-primary h-100']) ?>
<?php ActiveForm::end(); ?>

<div id="result" style="display:none;">
    <div class="mt-4 alert alert-success d-flex flex-wrap justify-content-between gap-2">
        <div>
            <p class="mb-1">Короткая ссылка:</p>
            <a href="#" id="s-link" target="_blank"></a>
        </div>
        <img id="qr-img" class="d-block">
    </div>
</div>

<div id="error" class="mt-4 alert alert-danger" style="display:none;"></div>
