<?php
use yii\helpers\Url;
?>
<div class="verify-email">
    <p>Hello,</p>

    <p>Link: <a href='<?=Url::toRoute(['site/route', 'id' => $route_id]);?>'>Ссылка</a></p>

    <p>Текст: <?=$errortext?></p>
</div>
