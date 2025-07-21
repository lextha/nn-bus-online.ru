<?
//use common\helpers\TimeHelper;
//var_dump($stations1); die();
/*$points0=[];
$i=1;
foreach ($stations0 as $s) {
    $points0[$i]['x']=$s['x'];
    $points0[$i]['y']=$s['y'];
    $points0[$i]['name']=$s['name'];
    $points0[$i]['id']=$s['id'];
    $i++;
}

$points1=[];
$i=1;
foreach ($stations1 as $s) {
    $points1[$i]['x']=$s['x'];
    $points1[$i]['y']=$s['y'];
    $points1[$i]['name']=$s['name'];
    $points1[$i]['id']=$s['id'];
    $i++;
}*/
?>
<div class="map_type_direction">
    <? 
    $points=[];
    foreach ($stationsall as $ketd=>$st) {
        $i=1;
        foreach ($st as $s) {
            $points[$ketd][$i]['x']=$s['x'];
            $points[$ketd][$i]['y']=$s['y'];
            $points[$ketd][$i]['name']=$s['name'];
            $points[$ketd][$i]['id']=$s['id'];
            $i++;
        }
        ?>
        <a href="#direct<?=$ketd;?>" idk="<?=$ketd;?>" <?=($ketd==0)?'class="active"':''?>><?=$st[0]['name'];?> - <?=$st[array_key_last($st)]['name'];?></a>
    <? } /*?><a href="#" id="route_B">Маршрут обратно</a> <? */?>
</div>
<div id="map"></div>
<script>
jQuery(document).ready(function(){
    var rrrr='';
    var points = []; points[0]=[];
    var geometrys = []; geometrys[0]=[];
    <? foreach ($route_line as $ketd=>$rl) {?>
        geometrys[<?=$ketd?>] = <?=($rl['line']!='')?$rl['line']:'[]';?>;
    <? } ?>
    <? /* var geometry1 = <?=($route_line[1]['line']!='')?$route_line[1]['line']:'[]';?>; */ ?>
        <? foreach ($points as $key=>$p) { ?>
            points[<?=$key?>] =  <?=json_encode($p);?>;
        <? } /*?>
    var points1 = <?= json_encode($points1);?>; <? */ ?>
    var city='<?=$city->name;?>';
     
   
    ymaps.ready(init);
 
        function init () {
            
            function drawpoint(point) {
                //console.log(point);
                template =  '<div style="margin: 10px;">' +
                                    '<div>' + point.name + '</div>' +
                                    '</div>';
                var stop_placemark = new ymaps.Placemark([point.y, point.x], {
                        id: point.id
                    }, {
                        preset: 'islands#redDotIcon',
                        balloonContentLayout: ymaps.templateLayoutFactory.createClass(
                            template, {})
                });
                myMap.geoObjects.add(stop_placemark);
            }
            
            var myMap = new ymaps.Map("map", {
                center: [55.43613859041051, 37.54702545689286],
                zoom: 10,
                controls: ['smallMapDefaultSet'],
            }, {
                searchControlProvider: 'yandex#search'
            });
 
           

            properties = {
                    hintContent: "Маршрут"
            },
            options = {
                    draggable: true,
                    strokeColor: '#ff0000',
                    strokeWidth: 5

            },
            polyline = new ymaps.Polyline(geometrys[0], properties, options);

            myMap.geoObjects.add(polyline);
            for (let key in points[0]) {
                drawpoint(points[0][key]);
            }
            

    ymaps.geocode(city, {
        results: 1
    }).then(function (res) {
           
            var firstGeoObject = res.geoObjects.get(0),
                coords = firstGeoObject.geometry.getCoordinates(),
                bounds = firstGeoObject.properties.get('boundedBy');
            firstGeoObject.options.set('preset', 'islands#darkBlueDotIconWithCaption');
            firstGeoObject.properties.set('iconCaption', firstGeoObject.getAddressLine());
            myMap.geoObjects.add(firstGeoObject);
            myMap.setBounds(bounds, {
                checkZoomRange: true
            });
            myMap.geoObjects.remove(firstGeoObject);
    });
            
            if (geometrys[0].length>0) { myMap.setBounds(polyline.geometry.getBounds()); }
            
            jQuery(document).on('click','.map_type_direction a', function() {
                var id=jQuery(this).attr('idk');
                myMap.geoObjects.removeAll();
                polyline = new ymaps.Polyline(geometrys[id], properties, options);
                myMap.geoObjects.add(polyline);
                
                for (let key in points[id]) {
                    drawpoint(points[id][key]);
                }
                jQuery('.map_type_direction a').removeClass('active');
                jQuery(this).addClass('active');
                return false;
            });
            <? /*jQuery(document).on('click','#route_B', function() {
                myMap.geoObjects.removeAll();
                polyline = new ymaps.Polyline(geometry1, properties, options);
                myMap.geoObjects.add(polyline);
                for (let key in points1) {
                    drawpoint(points1[key]);
                }
                jQuery(this).addClass('active');
                jQuery('#route_A').removeClass('active');
                return false;
            });*/ ?>
           

        }
});
        //myMap.geoObjects.remove(current_route);
    
</script>