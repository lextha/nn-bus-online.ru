<? //var_dump($temp); die();
if ($temp) {

$t=(int)(round($temp->main->temp));

?>
<span class="header-info__time">
            <img src="/img/time.svg" alt="time"> <?= date("H"); ?>:<?= date("i"); ?>
        </span>
<span class="header-info__weather">
            <img src="/img/icow/<?=$temp->weather[0]->icon;?>.png" alt="weather"> <?=$t?>°
        </span>
<? } else {
    ?>
<span class="header-info__time">
            <img src="/img/time.svg" alt="time"> <?= date("H"); ?>:<?= date("i"); ?>
        </span>
<span class="header-info__weather">
           
        </span>
<?
}
?>