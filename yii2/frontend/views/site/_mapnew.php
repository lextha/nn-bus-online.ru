    <div class="dropdown dropdown2">
        <button class="dropdown__btn dropdown__btn2" style="margin: 7px;">Направления маршрута</button>
        <div class="dropdown-content">  
            <?
    $points = [];
    foreach ($stationsall as $ketd => $st) {
        $i = 1;
        foreach ($st as $s) {
            $points[$ketd][$i]['x'] = $s['x'];
            $points[$ketd][$i]['y'] = $s['y'];
            $points[$ketd][$i]['name'] = $s['name'];
            $points[$ketd][$i]['id'] = $s['id'];
            $i++;
        }
        ?>
                <button class="dropdown__item is_active" val="<?=$ketd;?>"><?= $st[0]['name']; ?> - <?= $st[array_key_last($st)]['name']; ?></button>

            <? }  ?>
        </div>
    </div>

<?/*
<div class="map_type_direction">
    <?
    $points = [];
    foreach ($stationsall as $ketd => $st) {
        $i = 1;
        foreach ($st as $s) {
            $points[$ketd][$i]['x'] = $s['x'];
            $points[$ketd][$i]['y'] = $s['y'];
            $points[$ketd][$i]['name'] = $s['name'];
            $points[$ketd][$i]['id'] = $s['id'];
            $i++;
        }
        ?>


        <i></i><a href="#direct<?= $ketd; ?>" idk="<?= $ketd; ?>" <?= ($ketd == 0) ? 'class="active"' : '' ?>><?= $st[0]['name']; ?> - <?= $st[array_key_last($st)]['name']; ?></a>
    <? } ?>
</div> */ ?>
<div id="map_yamps" style="position: fixed;
    top: 180px;
    left: 0;
    width: 100%;
    z-index: 99;
    padding-bottom: 50px;
    background-color: var(--white);
    height: 100vh;"></div>
<?
if ($_SERVER['REMOTE_ADDR'] == '5.187.70.294') { /* ?>

  <script>
  jQuery(document).ready(function(){
  function drawline(myMap,geometrys) {
  properties = {
  hintContent: "Маршрут"
  },
  options = {
  draggable: false,
  strokeColor: '#1b3ca8',
  strokeWidth: 2

  },
  polyline = new ymaps.Polyline(geometrys[0], properties, options);
  myMap.geoObjects.add(polyline);
  }
  function drawpoint(myMap,point) {
  template =  '<div style="margin: 10px;">' +
  '<div>' + point.name + '</div>' +
  '</div>';
  if ( point.id!='0') {
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
  } else {
  var stop_placemark = new ymaps.Placemark([point.y, point.x], {
  id: point.id,
  mid: point.mid
  }, {
  iconLayout: 'default#image',
  iconImageHref: '/i/logo-m-old.png',
  iconImageSize: [26, 30],
  iconImageOffset: [-13, -15],
  //  balloonContentLayout: ymaps.templateLayoutFactory.createClass()
  });

  }

  myMap.geoObjects.add(stop_placemark);
  }
  var points = []; points[0]=[];
  var geometrys = []; geometrys[0]=[];
  <? foreach ($route_line as $ketd=>$rl) {?>
  geometrys[<?=$ketd?>] = <?=($rl['line']!='')?$rl['line']:'[]';?>;
  <? } ?>
  <? foreach ($points as $key=>$p) { ?>
  points[<?=$key?>] =  <?=json_encode($p);?>;
  <? } ?>

  ymaps.ready(init);

  function init () {
  const myMap = new ymaps.Map("map", {
  center: [55.43613859041051, 37.54702545689286],
  zoom: 9,
  controls: ['smallMapDefaultSet'],
  }, {
  searchControlProvider: 'yandex#search'
  });
  for (let key in points[0]) {
  drawpoint(myMap,points[0][key]);
  }
  drawline(myMap,geometrys);
  }
  });
  </script>
  <? */
} else {
    ?> 
    <script>
            window.transportonline=[];
            window.intervalarr=[];
            
            if (typeof intervalIdI === 'undefined' || intervalIdI === null) {
                var intervalIdI;
            }
           
            jQuery(document).ready(function () {
                var rrrr = '';
                var points = [];
                points[0] = [];
                var geometrys = [];
                geometrys[0] = [];
        <? foreach ($route_line as $ketd => $rl) { ?>
                        geometrys[<?= $ketd ?>] = <?= ($rl['line'] != '') ? $rl['line'] : '[]'; ?>;
        <? } ?>
        <? /* var geometry1 = <?=($route_line[1]['line']!='')?$route_line[1]['line']:'[]';?>; */ ?>
        <? foreach ($points as $key => $p) { ?>
                        points[<?= $key ?>] = <?= json_encode($p); ?>;
        <? } /* ?>
          var points1 = <?= json_encode($points1);?>; <? */ ?>
                var city = '<?= $city->name; ?>';


            ymaps.ready(init);

            function init() {

                function drawpoint(point, key) {
                    let r = '';
                    //console.log(key);
                    if (key != '0') {
                        r = '1';
                    }
                    template = '<div style="margin: 10px;">' +
                            '<div>' + point.name + '</div>' +
                            '</div>';
                    if (point.id != '0') {
                        var stop_placemark = new ymaps.Placemark([point.y, point.x], {
                            id: point.id
                        }, {
                            iconLayout: 'default#image',
                            iconImageHref: '/img/bus/busstop' + r + '.png',
                            iconImageSize: [10, 10],
                            iconImageOffset: [-5, -5],
                            balloonContentLayout: ymaps.templateLayoutFactory.createClass(
                                    template, {})
                        });
                    } else {
                        var stop_placemark = new ymaps.Placemark([point.y, point.x], {
                            id: point.id,
                            mid: point.mid
                        }, {
                            iconLayout: 'default#image',
                            iconImageHref: '/img/bus/b.png',
                            iconImageSize: [21, 34],
                            iconImageOffset: [-10, -10],
                            //  balloonContentLayout: ymaps.templateLayoutFactory.createClass()
                        });

                    }

                    myMap.geoObjects.add(stop_placemark);
                }
                function isJsonString(str) {
                    try {
                        JSON.parse(str);
                    } catch (e) {
                        return false;
                    }
                    return true;
                }
                function drawbus(myMap) {
                   /* const interval_id = window.setInterval(function(){}, Number.MAX_SAFE_INTEGER);
                    for (let i = 0; i < window.intervalarr.length; i++) {
                        window.clearInterval(window.intervalarr[i]);
                        window.intervalarr.splice(i, 1);
                    }*/
                    //console.log('drawbus');
                    let cawe=$("#map").attr("cawe");
                    if (isJsonString(cawe)) {
                        let cawej=JSON.parse(cawe);
                       <? /* template = '<div style="margin: 10px;">' +
                            '<div>XXX</div>' +
                            '</div>';*/ ?>
                        let trans=[]; <? // в массив записываем автобусы, которые были в выгрузке и мы их отрисовали/передвинули ?>
                        if (cawej.length>0) {
                            for (var j = 0; j < cawej.length; j++) {
                                    if (window.transportonline[cawej[j][2]] !==undefined) <? // меняем координаты автобуса, который уже отрисован на карте ?>
                                    {   let plm=window.transportonline[cawej[j][2]];
                                            let coords1 = plm.geometry.getCoordinates();
                                           
                                            let x1 = parseFloat(coords1[0]), y1 = parseFloat(coords1[1]);
                                            let x2 = parseFloat(cawej[j][0]), y2 = parseFloat(cawej[j][1]); 
                                            if (Math.abs((x1-x2))>0.00005 || Math.abs((y1-y2))>0.00005) <? // чтобы не прыгал на месте автобус, при не значительном изменении координат?>
                                            {
                                                let n = 50;
                                                let px = (x2 - x1) / n;
                                                let py = (y2 - y1) / n;
                                                let interval, i = 1;
                                                x1 = x1+px;
                                                y1 = y1+py;
                                               
                                                plm.geometry.setCoordinates([x1, y1]);
                                                interval = setInterval(function () {
                                                    x1 = x1+px;
                                                    y1 = y1+py;
                                                    plm.geometry.setCoordinates([x1, y1]);
                                                    i++;
                                                    if (i >= n) { //console.log(n);
                                                        clearInterval(interval);
                                                    }
                                                }, 100);
                                                window.intervalarr=interval;
                                                if (plm.options.get('iconRotate') != cawej[j][3]) {
                                                        plm.options.set('iconRotate', cawej[j][3]);
                                                }
                                        }
                                            trans[cawej[j][2]]=plm;
                                    } else { <? // добавляем автобус на карту, т.к. его нет на карте ?>
                                        var stop_placemark = new ymaps.Placemark([cawej[j][0], cawej[j][1]], {
                                            id: cawej[j][2],
                                            mid: cawej[j][3]
                                        }, {
                                            iconLayout:
                                            ymaps.templateLayoutFactory.createClass(
                                                '<div style="transform:rotate({{options.rotate}}deg);">' +
                                                '{% include "default#image" %}' +
                                                '</div>'
                                            ),
                                            iconImageHref: '/img/bus/bus.svg',
                                            iconRotate: cawej[j][3],
                                            iconImageSize: [21, 34],
                                            iconImageOffset: [-10, -10],
                                            //  balloonContentLayout: ymaps.templateLayoutFactory.createClass()
                                        });                    
                                        myMap.geoObjects.add(stop_placemark);
                                        trans[cawej[j][2]]=stop_placemark;
                                    }
                                }
                            window.transportonline.forEach(function(element) { <? // удаляем автобус с карты, т.к. его нет в новой выгрузке?>
                                if (!trans.includes(element)) {
                                    myMap.geoObjects.remove(element);
                                }
                            });
                        }
                        window.transportonline=trans; <? // записываем в глобальный массив отрисованые автобусы на карте ?>
                    }
                   
                }
                

                let colors = ['#1b3ca8', '#E44E6D', '#ED7D2B', '#00F845', '#FF0000', '#14ACAF', '#D9D9D9', '#0073d7', '#a1ad03', '#00cf12'];
               
                
                var myMap = new ymaps.Map("map_yamps", {
                    center: [56.328, 44.002],
                    zoom: 9,
                    controls: ['smallMapDefaultSet'],
                }, {
                    searchControlProvider: 'yandex#search'
                });

                let i = 0;
                geometrys.forEach((geometry) => {
                    properties = {
                        hintContent: "Маршрут"
                    };
                    options = {
                        draggable: false,
                        strokeColor: colors[i],
                        strokeWidth: 3

                    };
                    polyline = new ymaps.Polyline(geometry, properties, options);

                    myMap.geoObjects.add(polyline);
                    i++;
                });
              

                i = 0;
                points.forEach((point) => {
                    for (let key in point) {
                        //  alert(points[0][key].name);
                        drawpoint(point[key], i);
                    }
                    i++;
                });

                if (geometrys[0].length > 0) {
                    myMap.setBounds(polyline.geometry.getBounds(), {checkZoomRange: true}).then(function () {
                        if (myMap.getZoom() > 14)
                            myMap.setZoom(14);
                    });
                }

    <? //if ($_SERVER['REMOTE_ADDR']=='5.187.69.14') {  // МОЯ ГЕОПОЗИЦИЯ. НУЖНО ИЛИ НЕТ?
    /*     ?>
                let func = positionmy();
                function positionmy() {
                    if (navigator.geolocation) {

                        navigator.geolocation.getCurrentPosition(position => {
                            if (position.coords.accuracy > 100000) {
                                console.log('bad position');
                                return 0;
                            } else {
                                var x = position.coords.latitude;
                                var y = position.coords.longitude;
                                var my_placemark = new ymaps.Placemark([x, y], {
                                }, {
                                    iconLayout: 'default#image',
                                    iconImageHref: '/img/bus/b.png',
                                    iconImageSize: [16, 15],
                                    iconImageOffset: [-8, -7],
                                    //  balloonContentLayout: ymaps.templateLayoutFactory.createClass()
                                });
                                myMap.geoObjects.add(my_placemark);
                               // myMap.setCenter([x, y], 13);
                                console.log('good position');
                                return 1;
                            }
                        });
                    } else {
                        return 0;
                        // браузер не поддерживает геолокацию
                    }
                }
<? */ ?>
                $('.dropdown2 .dropdown__item').on('click', function () {
                    var val=[];
                    $(this).toggleClass("is_active");
                    myMap.geoObjects.removeAll();
                    
                    $(".dropdown2 .dropdown__item.is_active" ).each(function(index, element) {
                        val[index]=jQuery(element).attr('val');
                    });
                    val.forEach(function(id, i) {
                        properties = {
                            hintContent: "Маршрут"
                        };
                        options = {
                            draggable: false,
                            strokeColor: colors[id],
                            strokeWidth: 3

                        };
                        polyline = new ymaps.Polyline(geometrys[id], properties, options);
                        myMap.geoObjects.add(polyline);

                        for (let key in points[id]) {
                            drawpoint(points[id][key], id);
                        }
                    });
                    window.transportonline=[];
                    drawbus(myMap);
                    return false;
                });
    <? /* jQuery(document).on('click','#route_B', function() {
      myMap.geoObjects.removeAll();
      polyline = new ymaps.Polyline(geometry1, properties, options);
      myMap.geoObjects.add(polyline);
      for (let key in points1) {
      drawpoint(points1[key]);
      }
      jQuery(this).addClass('active');
      jQuery('#route_A').removeClass('active');
      return false;
      }); */ ?>

                setTimeout(drawbus,3000,myMap);
                startTimerI(myMap);
                
               

                function startTimerI(myMap) {
                    if (!intervalIdI) {
                        setTimeout(drawbus,3000,myMap);
                        intervalIdI = setInterval(drawbus,7000,myMap);
                    }
                }

                function stopTimerI() {
                    clearInterval(intervalIdI);
                    intervalIdI = null;
                }
                
                let timeoutStopI ;
                $(window).on('focus', function () {
                    clearTimeout(timeoutStopI);
                    var object = $('#map');
                    if (object.is(':visible')) {
                        startTimerI();
                    }
                });
                $(window).on('blur', function () {
                    timeoutStopI = setTimeout(function() {
                        stopTimerI();
                    }, 5000);
                });

                $(window).on('click', function (e) {
                    var object = $('#map');
                    setTimeout(function () {
                        if (object.is(':hidden')) {
                            stopTimerI();
                        }
                    }, 3000);

                });
                
            }
            
            $('.dropdown__btn2').on('click', function (e) {
                console.log('dropdown__btn2');
                if ($(this).hasClass('is_active')) {
                    $('.dropdown__btn2').removeClass('is_active').next().slideUp(200);
                    $('.dropdown').removeClass('is_active')
                } else {
                    $(this).parent('.dropdown').addClass('is_active')
                    $('.dropdown__btn2').removeClass('is_active').next().slideUp(200);
                    $(this).toggleClass('is_active').next().slideToggle(200);
                }
            });
            
        });
    </script>
<? } ?>