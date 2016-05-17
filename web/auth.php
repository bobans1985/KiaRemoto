<?php
// Данный файл всегда будит "включаться" в другие файлы
// директивой include поэтому следует запретить его самостоятельный вызов
// из строки запроса путём указания его имени
// Если не определена константа IN_ADMIN - завершаем работу скрипта
require_once __DIR__ . '/library/autoload.php';

if(!defined("IN_ADMIN")) die;
// Начинаем сессию
session_start();
$login = "login";
$passw = "password";
// Проверям были ли посланы данные
if(!empty($_POST['enter']))
{
    $_SESSION['login'] = md5($_POST['login']);
    $_SESSION['passw'] = md5($_POST['passw']);
    $_SESSION['agent']=md5($_SERVER['HTTP_USER_AGENT']);
    $_SESSION['ip'] = md5($_SERVER['REMOTE_ADDR']);
}

// Если ввода не было, или они не верны
// просим их ввести
if(empty($_SESSION['login']) or
    md5($login) != $_SESSION['login'] or
    md5($passw) != $_SESSION['passw']
)  echo('<div class="col-sm-6 col-md-4 col-md-offset-4 text-danger">Неверный логин либо пароль!</div>');
if(empty($_SESSION['login']) or
    md5($login) != $_SESSION['login'] or
    md5($_SERVER['HTTP_USER_AGENT' ]) !=$_SESSION['agent'] or
    md5($passw) != $_SESSION['passw'] or
    md5($_SERVER['REMOTE_ADDR']) != $_SESSION['ip']
)
{
    echo ('
            <div class="container">
                <div class="row">
                    <div class="Absolute-Center is-Responsive">
                        <div class="col-sm-6 col-md-4 col-md-offset-4">
                            <form name="form_auth" method="post" action="" id="loginForm">
                                <div class="form-group input-group">
                                    <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                                    <input width="100px" class="form-control" type="text" name="login" placeholder="username"/>
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
        ');
    die;
    }
?>