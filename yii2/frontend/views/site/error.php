<?php

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

use yii\helpers\Html;

$this->title = $name;
?>


<header class="header">
    <a href="/" class="logo">
        <img src="/img/logo.svg" alt="logo">
        <h1 class="logo-title">
            <span class="logo__heading">Общественный транспорт</span>
            <span class="logo__text">Нижний Новгород</span>
        </h1>
    </a>
    <div class="header-info">
        <span class="header-info__time">
            <img src="/img/time.svg" alt="time"> <?= date("H"); ?>:<?= date("i"); ?>
        </span>
        <span class="header-info__weather">
            
        </span>
    </div>
</header>
<main class="listing route1">
	
    <div style='position: absolute;top: 50%;left: 50%;transform: translate(-50%, -50%);'>
        <h1> <?= nl2br(Html::encode($message)) ?></h1>

		<div class="text"><?= Html::encode($this->title) ?>
			
		</div>
    </div>

</main>