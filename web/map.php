<?php session_start(); ?>
   <html>
   <head>
       <title>Kia Remoto bobans@</title>
       <link href="img/favicon.ico" rel="shortcut icon" type="image/x-icon">
       <script src="https://api-maps.yandex.ru/2.0-stable/?load=package.standard&lang=ru-RU" type="text/javascript"></script>
       <script type="text/javascript">

       </script>

    </head>
<body>
    <br>
    Геолокация вашего автомобиля:
    <div id="map" style="width:70%;height:400px"></div>
<?php
error_reporting(E_ALL);
ini_set('display_errors',1);
//Авторизация
define("IN_ADMIN", TRUE);
require "auth.php";
$login = $_SESSION['login'];
$password = $_SESSION['passw'];
date_default_timezone_set('Etc/GMT-3'); //Выставляем часовой пояс | Пусть будет Москва
if (LOGIN<>null) {
    $http = http_request_kia('https://rmt.brightbox.ru/api/v2/devices/location','',$login,$password);
    echo('<br><details style="width: 200px;"><summary>Log read location</summary>' . prettyPrint($http) . '</details>');
    $res = json_decode($http);
    $koord1=$res->{'Latitude'};
    $koord2=$res->{'Longitude'};
    if (($res==null) OR ($koord1==null) OR ($koord2==null) ) die('Could not connect to remote server!');

    echo('<script type="text/javascript"> 
            ymaps.ready(init);
            var map;

            function init(){     
                map = new ymaps.Map ("map", {
                    center: ['.$koord1.', '.$koord2.'],
                    zoom: 7
            });
            var placemark = new ymaps.Placemark(['.$koord1.', '.$koord2.'], {balloonContent: "Автомобиль тут"}, {preset: "twirl#carIcon"});
            map.geoObjects.add(placemark);
            map.setCenter(placemark.geometry.getCoordinates(),15);
            map.behaviors.enable("scrollZoom");
            map.controls.add(new ymaps.control.ZoomControl());
            
         }

 
        
       </script>');

}

?>
</body>
</html>