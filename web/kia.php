<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
	<title>Kia Remoto bobans@</title>
	<link href="img/favicon.ico" rel="shortcut icon" type="image/x-icon">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
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
				<small>Kia Remoto bobans@ 2016</small>
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
	date_default_timezone_set('Etc/GMT-3'); //Выставляем часовой пояс | Пусть будет Москва
	if (LOGIN<>null) {
		echo('
			<div class="navbar navbar-inverse"  role = "navigation" >
      			<div class="container-fluid" >
					<a id = "SatellitesCount" class="navbar-brand" href = "#" > X</a >
        			<div class="navbar-header" >
        				<a class="navbar-brand" href = "kia.php" > Status</a >
						<a id = "start_menu" class="navbar-brand" href = "javascript:bootbox.confirm(\'Вы уверены, что нужно запустить двигатель?\', function(result) { if (result) { document.getElementById(\'Run\').submit();   } }	); " > Run</a >
		  				<a id = "stop_menu" class="navbar-brand" href = "javascript:bootbox.confirm(\'Вы уверены, что нужно заглушить двигатель?\', function(result) { if (result) { document.getElementById(\'Stop\').submit();   } }	); " > Stop</a >
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
			$postData=  '{"Info":{"CommandType":100}}';
			$http = http_request_kia('https://rmt.brightbox.ru/api/v2/devices','',$login,$password);
			echo('<details style="width: 200px;"><summary>Log read status</summary>' . prettyPrint($http) . '</details>');
			$res = json_decode($http);
			if ($res==null) die('Could not connect to remote server!');
			if ($res->{'Network'}->{'Status'}==0) 	echo('<script>$( "#SatellitesCount" ).text( "'.$res->{'Network'}->{'SatellitesCount'}.' | " ); </script> ');
			if ( ($res->{'Network'}->{'Status'}==0) and (!$res->{'Engine'}->{'IsOn'}) and ($res->{'Engine'}->{'Rpm'}==0) ) {
				$http = http_request_kia('https://rmt.brightbox.ru/api/v1/devices/commands',$postData,$login,$password,TRUE);
				echo('<details style="width: 200px;"><summary>Log after run engine</summary>' . prettyPrint($http) . '</details>');
				if (strlen($http)>0) {
					sleep(10);
					$http = http_request_kia('https://rmt.brightbox.ru/api/v2/devices','',$login,$password);
					echo('<details style="width: 200px;"><summary>Log after run engine / status</summary>' . prettyPrint($http) . '</details>');
					$res = json_decode($http);
					echo('Last message from car: '.$res->{'Info'}->{'Message'});

					echo('<script>$( "#start_menu" ).remove();</script>'); //Убираем кнопку запуска

				}
			} else echo('Двигатель запущен,мы не можем его запустить');
    	} else echo('Неправильный переход на страницу... Переходим на главную <script> window.location.href="kia.php";</script>');
	}

	if ((isset($_GET["step"])) and ($_GET["step"]=='Stop')) {
    	 if ($_POST["flag"]=='true') {
			$postData=  '{"Info":{"CommandType":102}}';
			$http = http_request_kia('https://rmt.brightbox.ru/api/v2/devices','',$login,$password);
			echo('<details style="width: 200px;"><summary>Log read status</summary>' . prettyPrint($http) . '</details>');
			$res = json_decode($http);
			if ($res==null) die('Could not connect to remote server!');
			if ($res->{'Network'}->{'Status'}==0) 	echo('<script>$( "#SatellitesCount" ).text( "'.$res->{'Network'}->{'SatellitesCount'}.' | " ); </script> ');
			if ( ($res->{'Network'}->{'Status'}==0) and ($res->{'Engine'}->{'IsOn'}) and ($res->{'Engine'}->{'Rpm'}>0) ) {
				$http = http_request_kia('https://rmt.brightbox.ru/api/v1/devices/commands',$postData,$login,$password,TRUE);
				echo('<details style="width: 200px;"><summary>Log of stop engine</summary>' . prettyPrint($http) . '</details>');
				if (strlen($http)>0) {
					sleep(10);
					$http = http_request_kia('https://rmt.brightbox.ru/api/v2/devices','',$login,$password);
					echo('<details style="width: 200px;"><summary>Log after stop engine / status</summary>' . prettyPrint($http) . '</details>');
					$res = json_decode($http);
					echo('Last message from car: '.$res->{'Info'}->{'Message'});

					echo('<script>$( "#stop_menu" ).remove();</script>'); //Убираем кнопку остановки

				}
			} else echo('Двигатель не запущен,мы не можем его оcтановить');
	    } else echo('Неправильный переход на страницу... Переходим на главную <script> window.location.href="kia.php";</script>');
	}

	if ((!isset($_GET["step"])) or ($_GET["step"]=='Status')) {
		$http = http_request_kia('https://rmt.brightbox.ru/api/v2/devices','',$login,$password);
		$res = json_decode($http);
		//if ($res==null) die('Could not connect to remote server!');
		echo("<h4> Status for car:</h4>");
		$engine=$res->{'Engine'}->{'IsOn'};
		$rpm=$res->{'Engine'}->{'Rpm'};
		$dateofmessage=Date('d.m.Y H:i:s',substr($res->{'Info'}->{'Date'},6,10));
		$message=$res->{'Info'}->{'Message'};
		$speed=$res->{'Engine'}->{'Speed'};

		if ($engine) {
			echo('<script>$( "#start_menu" ).remove();</script>');
		} else echo('<script>$( "#stop_menu" ).remove();</script>');

		if ($res->{'Network'}->{'Status'}==0)
		{
			echo('<script>$( "#SatellitesCount" ).text( "'.$res->{'Network'}->{'SatellitesCount'}.' | " ); </script> ');
			echo('Статус двигателя: <code>'.($engine ? 'Заведен' : 'Не работает').'</code>');
			echo('<br>Обороты:<code>'.$rpm.'</code>');
			echo('<br>Скорость:<code>'.$speed.'</code>');
			echo('<br>Сообщение:<code>'.$dateofmessage.' / '.$message.'</code>');
			echo('<br>Последние соединение модуля: '.Date('d.m.Y H:i:s',substr($res->{'Network'}->{'LastSync'},6,10)));
			echo('<br>Состояние ЦЗ: ' . ($res->{'Doors'}->{'CentralLocking'}? 'Закрыто':'Открыто'));
		} else {
			echo('<code> Нет связи с тачкой! </code>');
		}

		echo('<br><br><details style="width: 200px;"><summary>Log read status</summary>' . prettyPrint($http) . '</details>');
	}

	if ((isset($_GET["step"])) and ($_GET["step"]=='Maps')) {
		echo('Геолокация вашего автомобиля:<br>');
		echo('<div id="map" class="center" style="width:90%;height:300px"></div><br>');
		/*Для начала получим статус*/
		$http = http_request_kia('https://rmt.brightbox.ru/api/v2/devices','',$login,$password);
		$res = json_decode($http);
		if ($res->{'Engine'}->{'IsOn'}) {
			echo('<script>$( "#start_menu" ).remove();</script>');
		} else echo('<script>$( "#stop_menu" ).remove();</script>');

		if ($res->{'Network'}->{'Status'}==0) 	echo('<script>$( "#SatellitesCount" ).text( "'.$res->{'Network'}->{'SatellitesCount'}.' | " ); </script>');
		if ($res->{'Network'}->{'Status'}==0) {

			$http = http_request_kia('https://rmt.brightbox.ru/api/v2/devices/location', '', $login, $password);
			$res = json_decode($http);
			$koord1 = $res->{'Latitude'};
			$koord2 = $res->{'Longitude'};
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

?>
		</div>
</body>
</html>

