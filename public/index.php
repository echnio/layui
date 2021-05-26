<?php
$method      = strtolower($_SERVER['REQUEST_METHOD']);
$requestPath = parse_url($_SERVER['REQUEST_URI']);

$path        = isset($requestPath['path']) ? $requestPath['path'] : '';
$user        = isset($_REQUEST['user']) ? $_REQUEST['user'] : "";
$isAdmin     = $user === "zhangrenping";
$userList    = [
    'zhangrenping',
    'wangxiaojun',
    'lihongyan'
];
if (! in_array($user, $userList)) {
    die('无权限');
}

if($path){

}
return include "../show.php";