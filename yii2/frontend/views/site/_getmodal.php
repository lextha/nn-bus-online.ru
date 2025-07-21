<?
if ($rel==1) {
?>
<div class="pagetitle">Нашли ошибку?</div>
        <div class="bodyerrf">
            <p>Вы заметили ошибку на странице? Вы знаете новое расписание или маршрут? Тогда сообщите нам эту информацию, и пользователи нашего сервиса будут Вам благодарны!</p>
           <form action="#" method="post" id="errorfindform">
                <textarea name="errortext" class="styler" placeholder="Ваша информация или ссылка на информацию"></textarea>
               <? /* <div class="ilil">или</div>
                <p>Сфотографируйте расписание или маршрут следования на остановке, в газете или где-то еще.</p>
                <div class="photoload">
                    <div class="form-group">
                        <input type="file" name="photo" id="inphoto" class="input-file">
                      <label for="inphoto" class="btnfinder btn-tertiary js-labelFile">
                        <span class="js-fileName">Сделать фото</span>
                      </label>
                    </div>
                </div>*/ ?>
                <input type="hidden" name="<?=Yii::$app->request->csrfParam?>" value="<?=Yii::$app->request->getCsrfToken()?>">
                <input type="hidden" name="route_id" value="<?=$route_id;?>">
                <input type="button" class="btn" id="formerrsend" value="Отправить">
                        
            </form>
        </div>
        <a href="#" class="close arcticmodal-close"></a>
<script>
jQuery(document).ready(function() {

   <? /* jQuery("#inphoto").change(function() {
       jQuery(".js-labelFile").html("Фото добавлено");
    });*/ ?>
    jQuery("#formerrsend").on("click",function() {
        //jQuery.post('/index.php?option=com_k2&view=errorfind', jQuery('#errorfindform').serializeFiles());
        jQuery.ajax({
            type: "POST",
            url: 'https://goonbus.ru/site/sendinfo',
          <?/*  data: jQuery('#errorfindform').serializeFiles(),*/?>
            data: jQuery('#errorfindform').serializeFiles(),
            processData: false,
            contentType: false
          });
        // jQuery('#errorfindform').ajaxForm(function(result) { });
        jQuery('#errorfindmodal .bodyerrf').html("<p style='color:green'>Спасибо за вашу информацию!</p>");
        return false;
    });
});
</script>
<? } elseif ($rel==2) { ?>
        <div class="pagetitle">Жалоба на водителя?</div>
        <div class="bodyerrf">
            <p>Сервис расписание общественного транспорта GoOnBus.ru не сотрудничает с перевозчиками по данному маршруту. Жалобы можно направлять руководству перевозчика.</p>
        </div>
        <a href="#" class="close arcticmodal-close"></a>
        
<? } elseif ($rel==3) { ?>
           <div class="pagetitle">Забыли вещи?</div>
        <div class="bodyerrf">
            <p>В случае потери вещей в общественном транспорте можно обратиться к организации перевозчика.</p><p>Сервис расписание общественного транспорта GoOnBus.ru не сотрудничает с перевозчиками по данному маршруту.</p>
        </div>
        <a href="#" class="close arcticmodal-close"></a>
<? } elseif ($rel==4) { ?>
           <div class="pagetitle">Предложения по маршруту?</div>
        <div class="bodyerrf">
            <p>Для внесения предложений по организации работы данного маршрута необходимо обращаться в органы местного самоуправления. Такие обращения обычно принимают на официальном сайте администрации.</p><p>Сервис расписание общественного транспорта GoOnBus.ru не сотрудничает с перевозчиками по данному маршруту.</p>
        </div>
        <a href="#" class="close arcticmodal-close"></a>
        
<? } ?>
