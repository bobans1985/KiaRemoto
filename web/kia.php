<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
	<title>Kia Remoto bobans@ - удаленное управление автомобилем при помощи смартфона</title>
	<link href="img/favicon.ico" rel="shortcut icon" type="image/x-icon">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="keywords" content="Remoto, Ремото, Starline, сигнализация с автозапуском, автосигнализация с автозапуском,kia remoto, кия ремото, кия автозапуск, kia автозапуск, myRemoto, Личный кабинет,remoto личный кабинет, kia личный кабинет ">
        <meta name="description" content="Kia remoto bobans@ - личный кабинет системы для управления автомобилем при помощи смарфона. MyRemoto / KiaRemoto" />
	<meta name="viewport" content="width=device-width">
    <!-- Bootstrap -->
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="bootstrap/js/bootstrap.min.js"></script>
    <script src="bootstrap/js/bootbox.min.js"></script>
	<script src="bootstrap/js/jquery.maskedinput.min.js"></script>
	<!-- Yandex maps api -->
	<script src="https://api-maps.yandex.ru/2.0-stable/?load=package.standard&lang=ru-RU" type="text/javascript"></script>

	<meta name="yandex-verification" content="7f61cd17c3561f03" />
	<meta name='wmail-verification' content='9ed3fa35e4f89ea470ad1bf4ed7366e2' />
	<meta name="google-site-verification" content="g1TDVw1NeZuNt0n-Jpvn70Qr1w9Hx5JNbHpKkNWgoTc" />
	<style>
		.Absolute-Center {
			margin: auto;
			position: absolute;
			top: 0; left: 0; bottom: 0; right: 0;
		}

		.Absolute-Center.is-Responsive {
			width: 50%;
			height: 50%;
			min-width: 320px;
			max-width: 600px;
			padding: 10px;
		}
		.center {
			padding: 20px; /* Поля вокруг текста */
			margin:  auto; /* Выравниваем по центру */
		}
		.navbar-brand {
			padding: 15px 15px;
		}
	</style>
	<script>
		$(function(){
			$("#SatellitesCount").click(function(){
				$.post("kia.php?step=Logout",function(data){
					window.location.href="kia.php";
				});
			});
		});
		jQuery(function($){
			$("#login").mask("+7(999) 999-99-99");
		});
	</script>
</head>
<body role="document">
	<div class="navbar-fixed-bottom row-fluid">
		<div class="navbar-inner">
			<div class="container text-right">
				<div><img src="img/logo.png" alt="" height="8%" width="8%"></div>
				<a href="about.html"><small>Kia Remoto bobans@ 2019</small></a>
			</div>
		</div>
	</div>
	<form method="POST" action="kia.php?step=Run" id="Run">
		<input type="hidden" name="flag" value="true" />
	</form>
	<form method="POST" action="kia.php?step=Stop" id="Stop">
		<input type="hidden" name="flag" value="true" />
	</form>

	<?php
	error_reporting(E_ALL);
	ini_set('display_errors',1);
	//Авторизация
	define("IN_ADMIN", TRUE);
	require "auth.php";
	$login = $_SESSION['login'];
	$password = $_SESSION['passw'];
    $accessToken=$_SESSION['accessToken'];
	date_default_timezone_set('Etc/GMT-3'); //Выставляем часовой пояс | Пусть будет Москва
	if (LOGIN<>null) {
		echo('
			<div class="navbar navbar-inverse"  role = "navigation" >
      			<div class="container-fluid" >
					<a id = "SatellitesCount" class="navbar-brand" href = "#" > X</a >
        			<div class="navbar-header" >
        				<a class="navbar-brand" href = "kia.php" > Status</a >
						<a style="display: none;" id = "start_menu" class="navbar-brand" href = "javascript:bootbox.confirm(\'Вы уверены, что нужно запустить двигатель?\', function(result) { if (result) { document.getElementById(\'Run\').submit();   } }	); " > Run</a >
		  				<a style="display: none;" id = "stop_menu" class="navbar-brand" href = "javascript:bootbox.confirm(\'Вы уверены, что нужно заглушить двигатель?\', function(result) { if (result) { document.getElementById(\'Stop\').submit();   } }	); " > Stop</a >
		  				<a class="navbar-brand" href = "kia.php?step=Maps" >Maps</a >
        			</div >
      			</div >
    		</div >
    		');
	}

	echo('			<div class="container-fluid">');
	if ((isset($_GET["step"])) and ($_GET["step"]=='Logout')) {	destroySession(); }
	if ((isset($_GET["step"])) and ($_GET["step"]=='Run')) {
	    if ($_POST["flag"]=='true') {
			echo("<p> Run engine car:</p>");
			$postData=  '{"commandType":"StartEngine"}';
			$http = http_request_kia('https://cvp-api-gateway.bbrmt.com/api/telematics/devices/05d2ff35-4241-3335-5757-224300000000/commands',$postData,$accessToken,true);
			echo('<details style="width: 200px;"><summary>Log read status</summary>' . prettyPrint($http) . '</details>');
            sleep(3);
            $http = http_request_kia('https://cvp-api-gateway.bbrmt.com/api/telematics/devices/05d2ff35-4241-3335-5757-224300000000','',$accessToken);
            $res = json_decode($http);
			if ($res==null) die('Could not connect to remote server!');
			if ($res->{'state'}->{'status'}=='Online') 	echo('<script>$( "#SatellitesCount" ).text( "'.$res->{'location'}->{'satellitesCount'}.' | " ); </script> ');
			if ( ($res->{'state'}->{'status'}=='Online') and (!$res->{'sensors'}->{'engineOn'}) and ($res->{'sensors'}->{'engineRpm'}==0) ) {
                sleep(10);
				$http = http_request_kia('https://cvp-api-gateway.bbrmt.com/api/telematics/devices/05d2ff35-4241-3335-5757-224300000000','',$accessToken);
                $res = json_decode($http);
				 echo('<details style="width: 200px;"><summary>Log after run engine</summary>' . prettyPrint($http) . '</details>');
				 echo('Last message from car: '.$res->{'state'}->{'summary'}->{'message'});
				 echo('<script>$( "#stop_menu" ).css("display", "inline");</script>'); //Убираем кнопку запуска
			} else echo('Двигатель запущен,мы не можем его запустить');
    	} else echo('Неправильный переход на страницу... Переходим на главную <script> window.location.href="kia.php";</script>');
	}

	if ((isset($_GET["step"])) and ($_GET["step"]=='Stop')) {
    	 if ($_POST["flag"]=='true') {
			$postData=  '{"commandType":"StopEngine"}';
			$http = http_request_kia('https://cvp-api-gateway.bbrmt.com/api/telematics/devices/05d2ff35-4241-3335-5757-224300000000/commands',$postData,$accessToken,true);
			echo('<details style="width: 200px;"><summary>Log read status</summary>' . prettyPrint($http) . '</details>');
			$http = http_request_kia('https://cvp-api-gateway.bbrmt.com/api/telematics/devices/05d2ff35-4241-3335-5757-224300000000','',$accessToken);
            $res = json_decode($http);
            echo('<details style="width: 200px;"><summary>Log of stop engine</summary>' . prettyPrint($http) . '</details>');
			if ($res==null) die('Could not connect to remote server!');
			if ($res->{'state'}->{'status'}=='Online') 	echo('<script>$( "#SatellitesCount" ).text( "'.$res->{'location'}->{'satellitesCount'}.' | " ); </script> ');
			if ( ($res->{'state'}->{'status'}=='Online') and ($res->{'sensors'}->{'engineOn'}) and ($res->{'sensors'}->{'engineRpm'}>0) ) {
                sleep(10);
                $http = http_request_kia('https://cvp-api-gateway.bbrmt.com/api/telematics/devices/05d2ff35-4241-3335-5757-224300000000','',$accessToken);
				echo('<details style="width: 200px;"><summary>Log after stop engine / status</summary>' . prettyPrint($http) . '</details>');
				$res = json_decode($http);
				echo('Last message from car: '.$res->{'state'}->{'summary'}->{'message'});
                echo('<script>$( "#start_menu" ).css("display", "inline");</script>'); //Убираем кнопку остановки
			} else echo('Двигатель не запущен,мы не можем его оcтановить');
	    } else echo('Неправильный переход на страницу... Переходим на главную <script> window.location.href="kia.php";</script>');
	}

	if ((!isset($_GET["step"])) or ($_GET["step"]=='Status')) {
		$http = http_request_kia('https://cvp-api-gateway.bbrmt.com/api/telematics/devices/05d2ff35-4241-3335-5757-224300000000','',$accessToken);
		$res = json_decode($http);
		if (strpos($http,'Unauthorized request')) destroySession();
		echo("<h4> Status for car:</h4>");
		$engine=$res->{'sensors'}->{'engineOn'};
		$rpm=$res->{'sensors'}->{'engineRpm'};
		$dateofmessage=$res->{'state'}->{'lastSync'};//Date('d.m.Y H:i:s',substr($res->{'state'}->{'lastSync'},6,10));
		$message=$res->{'state'}->{'summary'}->{'message'};
		$speed=$res->{'sensors'}->{'speed'};
		$temp_in_car=$res->{'sensors'}->{'innerTemperature'}-10; //10 это погрешность измерения из-за нагрева чипа
		$temp_outer=$res->{'sensors'}->{'outsideTemperature'};

		if ($engine) {
			echo('<script>$( "#stop_menu" ).css("display", "inline");</script>');
		} else echo('<script>$( "#start_menu" ).css("display", "inline");</script>');

		if ($res->{'state'}->{'status'}=='Online')
		{
			echo('<script>$( "#SatellitesCount" ).text( "'.$res->{'location'}->{'satellitesCount'}.' | " ); </script> ');
			echo('Статус двигателя: <code>'.($engine ? 'Заведен' : 'Не работает').'</code>');
			echo('<br>Обороты:<code>'.$rpm.'</code>');
			echo('<br>Скорость:<code>'.$speed.'</code>');
			echo('<br>Сообщение:<code>'.$dateofmessage.' / '.$message.'</code>');
			echo('<br>Последние соединение модуля: '.$res->{'state'}->{'lastSync'});
			echo('<br>Состояние ЦЗ: ' . ($res->{'sensors'}->{'doors'}->{'centralLock'}? 'Закрыто':'Открыто'));
			echo('<br>Температура: ' .$temp_in_car .' / '.$temp_outer);
		} else {
			echo('<code> Нет связи с тачкой! </code>');
		}

		echo('<br><details style="width: 200px;"><summary>Log read status</summary>' . prettyPrint($http) . '</details>');
	}

	if ((isset($_GET["step"])) and ($_GET["step"]=='Maps')) {
		echo('Геолокация вашего автомобиля:<br>');
		echo('<div id="map" class="center" style="width:90%;height:300px"></div><br>');
		/*Для начала получим статус*/
        $http = http_request_kia('https://cvp-api-gateway.bbrmt.com/api/telematics/devices/05d2ff35-4241-3335-5757-224300000000','',$accessToken);
		$res = json_decode($http);
        if ($res->{'sensors'}->{'engineOn'}) {
            echo('<script>$( "#stop_menu" ).css("display", "inline");</script>');
        } else echo('<script>$( "#start_menu" ).css("display", "inline");</script>');

		if ($res->{'state'}->{'status'}=='Online') 	echo('<script>$( "#SatellitesCount" ).text( "'.$res->{'location'}->{'satellitesCount'}.' | " ); </script>');
		if ($res->{'state'}->{'status'}=='Online') {
			$koord1 = $res->{'location'}->{'latitude'};
			$koord2 = $res->{'location'}->{'longitude'};
			if (($res == null) OR ($koord1 == null) OR ($koord2 == null)) die('Could not receive data of location from remote server!');

			echo('<script type="text/javascript"> 
         		   ymaps.ready(init);
          		   var map;

           		   function init(){     
                		map = new ymaps.Map ("map", {
                  							  center: [' . $koord1 . ', ' . $koord2 . '],
                  							  zoom: 7
            								});
           				 var placemark = new ymaps.Placemark([' . $koord1 . ', ' . $koord2 . '], {balloonContent: "Автомобиль тут"}, {preset: "twirl#carIcon"});
           				 map.geoObjects.add(placemark);
           				 map.setCenter(placemark.geometry.getCoordinates(),15);
           				 map.behaviors.enable("scrollZoom");
          			     map.controls.add(new ymaps.control.ZoomControl());
            
         		   }
       			 </script>');
		} else  echo('<code> Нет связи с тачкой! </code>');

		echo('<br><details style="width: 200px;"><summary>Log read location</summary>' . prettyPrint($http) . '</details>');
	}

	include_once("library/analytics.php");
?>
		</div>
</body>
</html>

