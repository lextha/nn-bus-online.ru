$(document).ready(function () {

    let intervalId;
    let intervalIdX
    let inactivityTime = 10 * 60 * 1000; // 10 минут * 60 секунд * 1000 миллисекунд
    let lastActivityTime = Date.now();


    function startTimer() {
        if (!intervalId) {
            loadonline($("#route").attr("boreins"));
            intervalId = setInterval(function () {
                loadonline($("#route").attr("boreins"));
            }, 15000);
        }
        
    }
    
    function startTimerS() {
        if (!intervalIdX) {
            loadsxem($("#route").attr("boreins"));
            intervalIdX = setInterval(function () {
                loadsxem($("#route").attr("boreins"));
            }, 17000);
        }
    }

    function stopTimer() {
        clearInterval(intervalId);
        intervalId = null;
        $('#map div.loaderinfo').removeClass('modal-online');
        $('#map div.loaderinfo').addClass('modal-update');
        $('#map div.loaderinfo').html('Обновление приостановлено.');
    }

   function stopTimerS() {
       clearInterval(intervalIdX);
        intervalIdX = null;
        $('#scheme div.loaderinfo').removeClass('modal-online');
        $('#scheme div.loaderinfo').addClass('modal-update');
        $('#scheme div.loaderinfo').html('Обновление приостановлено.');
   }

    function hideModals() {
        $('.modal').removeClass('is_active').fadeOut(200);
        $('body, .modal').removeClass('is_active');
    }

    function loadsh(stationid) {
        event.preventDefault();
        $('<div class="modal-center"><span class="loader"></span></div>').insertBefore("#schedule");
        var day = jQuery(".day_week").attr('day');
        jQuery.ajax({
            url: '/site/gettime',
            type: 'get',
            data: {
                station_rout_id: stationid,
                day: day,
            },
            success: function (data) {
                $("#schedule").html(data);
                $(".modal-center").remove();
            }
        });
    }
    function isEmpty(el) {
        return !$.trim(el.html())
    }

    function loadsxem(ri) {
        if (isEmpty($(".scheme-section"))) {
            $('<div class="modal-center"><span class="loader"></span></div>').insertBefore(".scheme-section");
        }
        $('#scheme div.loaderinfo').removeClass('modal-update');
        $('#scheme div.loaderinfo').addClass('modal-online');
        $('#scheme div.loaderinfo').html('Обновление информации <span class="loader"></span>');
        jQuery.ajax({
            url: '/site/getsxem',
            type: 'get',
            data: {
                ri: ri
            },
            success: function (data) {
                $(".scheme-section").html(data);
                $(".modal-center").remove();
                $('#scheme div.loaderinfo').html('Транспортные средства загружены');
            }
        });
    }

    function loadmap(ri) {
        event.preventDefault();
         $('#map div.loaderinfo').removeClass('modal-update');
        $('#map div.loaderinfo').addClass('modal-online');
        $('#map div.loaderinfo').html('Обновление информации <span class="loader"></span>');
        $('<div class="modal-center"><span class="loader"></span></div>').insertBefore(".maponline");
        jQuery.ajax({
            url: '/site/getmaponline',
            type: 'get',
            data: {
                ri: ri
            },
            success: function (data) {
                $(".maponline").html(data);
                $(".modal-center").remove();
                setTimeout(loadonline($("#route").attr("boreins"), 2000));
                startTimer();
            }
        });
    }

    function loadonline(ri) {
        $('#map div.loaderinfo').removeClass('modal-update');
        $('#map div.loaderinfo').addClass('modal-online');
        $('#map div.loaderinfo').html('Обновление информации <span class="loader"></span>');
        jQuery.ajax({
            url: '/site/getbusonline',
            type: 'get',
            data: {
                ri: ri
            },
            success: function (data) {
                $("#map").attr('cawe', data);
                let count = JSON.parse(data);
                $('#map div.loaderinfo').html('Доступно ' + count.length + ' транспортных средства');
            }, error: function (jqXHR, exception) {
                $('#map div.loaderinfo').removeClass('modal-online');
                $('#map div.loaderinfo').addClass('modal-update');
                $('#map div.loaderinfo').html('Ошибка загрузки. Попробуйте позже.');
            }

        });
    }

    function loadtemp() {
        jQuery.ajax({
            url: '/site/gettemp',
            type: 'get',
            data: {},
            success: function (data) {
               
                $(".header-info").html(data);
            }
        });
    }

    function showModal(id) {
        hideModals();
        $(id).addClass('is_active').fadeIn(100);
        $('body, .modal').addClass('is_active');
    }


    loadtemp();

    $('.tab a').on('click', function (e) {
        if (!$(this).hasClass("is_active")) {
            var d = $(this).attr('d');
            $('.tab.day_week a').removeClass('is_active');
            $(this).addClass('is_active');
            jQuery(".day_week").attr('day', d);
            loadsh($(".modal#route").attr('stationid'));
        }
        return false;
    });

    $('[data-modal]').on('click', function (e) {
        e.preventDefault();
        lastActivityTime = Date.now();
        clearInterval(intervalIdX);
        var id = $(this).attr("data-modal");
        if (id == "route") {
            stopTimer();
            $(".modal#route .modal__heading").text($(this).find(".stops__text").text());
            $(".modal#route .modal__addres i").text($(this).parent().find(".stops-item:last .stops__text").text());
            loadsh($(this).attr("stationid"));
            $(".modal#route").attr('stationid', $(this).attr("stationid"));
        } else if (id == "scheme") {
            stopTimer();
            startTimerS();
            
        } else if (id == "map") {
            $("ymaps").remove();
            loadmap($("#route").attr("boreins"));
        }
        showModal('#' + id);
        return false;
    });

    $('.modal__back, .modal_close').on('click', () => {
        hideModals();
    });


    $('.stops__show').on('click', function (e) {
        $(this).parents('ul').find('.--hidden').removeClass('--hidden')
        $(this).parent().hide()
    });

    $('.dropdown__btn').on('click', function (e) {
        if ($(this).hasClass('is_active')) {
            $('.dropdown__btn').removeClass('is_active').next().slideUp(200);
            $('.dropdown').removeClass('is_active')
        } else {
            $(this).parent('.dropdown').addClass('is_active')
            $('.dropdown__btn').removeClass('is_active').next().slideUp(200);
            $(this).toggleClass('is_active').next().slideToggle(200);
        }
    });

    $('.dropdown__item').on('click', function (e) {
        $('.dropdown__item').removeClass('is_active');
        $(this).addClass('is_active');
        $('.stops').hide();
        $('.stops#direct' + $(this).attr('val')).show();
    });
    $('.navbar .navbar-item').on('click', function (e) {
        var id = $(this).attr('id');
        $('.navbar-item').removeClass('is_active');
        $(this).addClass('is_active');
        $('.listing').hide();
        $('.listing.' + id).show();
    });

    let timeoutStop;
    $(window).on('focus', function () {
        if (Date.now() - lastActivityTime >= inactivityTime) {
            location.reload();
        }
        lastActivityTime = Date.now();
        clearTimeout(timeoutStop);
        var map = $('#map');
        var scheme = $('#scheme');
        if (map.is(':visible')) {
            startTimer();
        } else if (scheme.is(':visible')) {
            startTimerS();
        }
    });
    $(window).on('blur', function () {
        timeoutStop = setTimeout(function () {
            stopTimer();
            stopTimerS();
        }, 5000);
    });

    $(window).on('click', function (e) {
        var map = $('#map');
        var scheme = $('#scheme');
        setTimeout(function () {
            if (!map.is(':visible')) {
                stopTimer();
            } 
            if (!scheme.is(':visible')) {
                stopTimerS();
            }
            
        }, 3000);

    });

});    

