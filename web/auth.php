<?php
require_once __DIR__ . '/library/autoload.php';
if(!defined("IN_ADMIN")) die;

$login = "login";
$passw = "password";
$WrongPassword = "Введите логин и пароль необходимы для входа в систему";

if(!empty($_POST['enter']))
{
    $_SESSION['login'] = $_POST['login'];
    $_SESSION['passw'] = $_POST['passw'];
    $_SESSION['agent']=md5($_SERVER['HTTP_USER_AGENT']);
    $_SESSION['ip'] = md5($_SERVER['REMOTE_ADDR']);
    if (http_request_kia_login($_POST['login'],$_POST['passw'])) $_SESSION['Login']= md5('TRUE');

}

// Если ввода не было, или они не верны
// просим их ввести
if ((!empty($_POST['enter'])) and
    (empty($_SESSION['login']) or
        empty($_SESSION['Login']) or
        md5('TRUE')!=$_SESSION['Login'] )
) $WrongPassword='<p class="text-danger">Неверный логин либо пароль!</p>';

if(empty($_SESSION['login']) or
    md5($_SERVER['HTTP_USER_AGENT' ]) !=$_SESSION['agent'] or
    md5($_SERVER['REMOTE_ADDR']) != $_SESSION['ip'] or
    empty($_SESSION['Login']) or
    md5('TRUE')!=$_SESSION['Login']
    or (!http_request_kia_login($_SESSION['login'],$_SESSION['passw']))
)
{
    echo ('
            <div class="container">
                <div class="row">
                    <div class="Absolute-Center is-Responsive">
                        <div class="form-group text-center" id = "WrongPassword">'.$WrongPassword.'
                        </div>
                        <div class="col-sm-12 col-md-10 col-md-offset-1">
                            <form name="form_auth" method="post" action="" id="loginForm">
                                <div class="form-group input-group">
                                    <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                                    <input  class="form-control" type="text" name="login" placeholder="username"/>
                                 </div>
                                <div class="form-group input-group">
                                    <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                                    <input class="form-control" type="password" name="passw" placeholder="password"/>
                                </div>
                                 <div class="form-group">
                                    <input type=hidden name=enter value=yes>
                                    <button  type="submit" class="btn btn-def btn-block">Login</button>
                                 </div>
                            </form>
                           </div>
                        </div>
                    </div>
                </div>
                <!--футер----->
            </div>
            </body>
            </html>
        ');
    die;
    } else define("LOGIN", TRUE);
