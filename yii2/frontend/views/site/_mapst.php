<?
//use common\helpers\TimeHelper;
//var_dump($stations1); die();
$point=[];
$i=0;
foreach ($stations as $station) {
    $point[$i]['x']=$station->x;
    $point[$i]['y']=$station->y;
    $point[$i]['name']=$station->name;
    $point[$i]['id']=$station->id;
    $i++;
}
    //var_dump(json_encode($point));
?>
<div id="map"></div>
<script>
jQuery(document).ready(function(){
    var rrrr='';
    var points = <?=json_encode($point);?>;
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
                iconLayout: 'default#image',
                iconImageHref: '/i/busonl.png',
                iconImageSize: [10, 10],
                iconImageOffset: [-5, -5],
                balloonContentLayout: ymaps.templateLayoutFactory.createClass(
                    template, {})
            });
                myMap.geoObjects.add(stop_placemark);
                 myMap.setBounds(stop_placemark.geometry.getBounds(),{checkZoomRange:true}).then(function(){ if(myMap.getZoom() > 15) myMap.setZoom(15);});
                 // myMap.setBounds(stop_placemark.geometry.getBounds(),{checkZoomRange:true}).then(function(){ if(myMap.getZoom() > 15) myMap.setZoom(15);});
            }
            
            var myMap = new ymaps.Map("map", {
                center: [55.43613859041051, 37.54702545689286],
                zoom: 10,
                controls: ['smallMapDefaultSet'],
            }, {
                searchControlProvider: 'yandex#search'
            });
 
           

           <? /*  properties = {
                    hintContent: "Остановка"
            },
            options = {
                    draggable: true,
                    strokeColor: '#ff0000',
                    strokeWidth: 5

            }, */ ?>
            for (let key in points) {
                drawpoint(points[key]);
            }
           
             
            <? /* drawpoint(point); */ ?>
            

    ymaps.geocode(city, {
        results: 1
    }).then(function (res) {
           
            var firstGeoObject = res.geoObjects.get(0),
                coords = firstGeoObject.geometry.getCoordinates(),
                bounds = firstGeoObject.properties.get('boundedBy');
            firstGeoObject.options.set('preset', 'islands#darkBlueDotIconWithCaption');
            firstGeoObject.properties.set('iconCaption', firstGeoObject.getAddressLine());
            myMap.geoObjects.add(firstGeoObject);
           /* myMap.setBounds(bounds, {
                checkZoomRange: true
            });*/
            myMap.geoObjects.remove(firstGeoObject);
    });
            
           
           
           

        }
});
        //myMap.geoObjects.remove(current_route);
    
</script>