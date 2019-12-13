<?php
require_once __DIR__ . '/library/autoload.php';
if (!defined("IN_ADMIN")) die;
$ini_array = parse_ini_file('sessions.ini');


$login = "login";
$passw = "password";
$WrongPassword = "Введите телефон и пин-код, необходимый для входа в систему";

if (!empty($_POST['enter'])) {
    $_SESSION['login'] = str_replace(array('(', ')', ' ', '-'), '', $_POST['login']); //Выкидываем ненужные символы
    $_SESSION['passw'] = $_POST['passw'];
    $_SESSION['agent'] = md5($_SERVER['HTTP_USER_AGENT']);
    $_SESSION['ip'] = md5($_SERVER['REMOTE_ADDR']);
    if (array_key_exists($_SESSION['login'],$ini_array)) {
        $sessionToken = $ini_array[$_SESSION['login']];
        if (!empty($sessionToken)) {
            $accessToken = http_request_kia_pin($_SESSION['login'], $_SESSION['passw'], $sessionToken);
            //echo $accessToken;
            if (!empty($accessToken) and $accessToken != 'Неверный токен' and $accessToken != 'Неверный пин-код') {
                $_SESSION['Login'] = md5('TRUE');
                $_SESSION['accessToken'] = $accessToken;
            } else echo('Системная ошибка: '. $accessToken);
        }
    } else echo('Не найден токен!');

}


// Если ввода не было, или они не верны
// просим их ввести
if ((!empty($_POST['enter'])) and
    (empty($_SESSION['login']) or
        empty($_SESSION['Login']) or
        md5('TRUE') != $_SESSION['Login'])
    and empty($_SESSION['accessToken'])
) $WrongPassword = '<p class="text-danger">Неверный логин либо пароль!</p>';

if (empty($_SESSION['login']) or
    md5($_SERVER['HTTP_USER_AGENT']) != $_SESSION['agent'] or
    md5($_SERVER['REMOTE_ADDR']) != $_SESSION['ip'] or
    empty($_SESSION['Login']) or
    md5('TRUE') != $_SESSION['Login'] or
    empty($_SESSION['accessToken'])
    // (!http_request_kia_login($_SESSION['login'],$_SESSION['passw']))
) {
    echo('
            <div class="container">
                <div class="row">
                    <div class="Absolute-Center is-Responsive">
                        <div class="text-center" id = "WrongPassword">' . $WrongPassword . '
                        </div>
			 <div class="form-group text-center"><h1>Kia remoto</h1></div>
 
                        <div class="col-sm-12 col-md-10 col-md-offset-1">
                            <form name="form_auth" method="post" action="" id="loginForm">
                                <div class="form-group input-group">
                                    <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                                    <input  class="form-control" type="text" name="login" id="login" placeholder="логин/номер телефона"/>
                                 </div>
                                <div class="form-group input-group">
                                    <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                                    <input class="form-control" type="password" name="passw" placeholder="пароль/пин-код"/>
                                </div>
                                 <div class="form-group">
                                    <input type=hidden name=enter value=yes>
                                    <button  type="submit" class="btn btn-primary btn-def btn-block">Login</button>
                                 </div>
                            </form>
                           </div>
                        </div>
                    </div>
                </div>
                <!--футер----->
            </div>
            <!-- Yandex.Metrika counter --> <script type="text/javascript" > (function (d, w, c) { (w[c] = w[c] || []).push(function() { try { w.yaCounter41404049 = new Ya.Metrika({ id:41404049, clickmap:true, trackLinks:true, accurateTrackBounce:true, webvisor:true }); } catch(e) { } }); var n = d.getElementsByTagName("script")[0], s = d.createElement("script"), f = function () { n.parentNode.insertBefore(s, n); }; s.type = "text/javascript"; s.async = true; s.src = "https://mc.yandex.ru/metrika/watch.js"; if (w.opera == "[object Opera]") { d.addEventListener("DOMContentLoaded", f, false); } else { f(); } })(document, window, "yandex_metrika_callbacks"); </script> <noscript><div><img src="https://mc.yandex.ru/watch/41404049" style="position:absolute; left:-9999px;" alt="" /></div></noscript> <!-- /Yandex.Metrika counter -->
            </body>
            </html>
        ');
    die;
} else define("LOGIN", TRUE);
