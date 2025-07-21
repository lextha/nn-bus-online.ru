<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
use yii\helpers\Url;
//var_dump($city_ip); die();
?>
<noindex><div class="forcity"><a href='<?=Url::toRoute(['site/city', 'id' => $city->id]);?>' rel="nofollow"><?=$city->name?></a></div></noindex>