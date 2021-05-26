<?php
$method    = strtolower($_SERVER['REQUEST_METHOD']);
$func      = isset($_REQUEST['method']) ? $_REQUEST['method'] : "";
$loginUser = isset($_REQUEST['user']) ? $_REQUEST['user'] : "";
$isAdmin   = $loginUser === md5('zhangrenping');

$userList = [
    'zhangrenping',
    'wangxiaojun',
    'lihongyan'
];
$userName = null;
foreach ($userList as $v) {
    if ($loginUser === md5($v)) {
        $userName = $v;
    }
}

if (empty($userName)) {
    exit(json_encode(['status' => false, 'msg' => '无权限']));
}
$codePath = dirname(dirname(__FILE__)) . "/code";
if (! file_exists($codePath)) {
    file_put_contents($codePath, '');
}
$domain = "https://" . $_SERVER['SERVER_NAME'] . "/";
$data   = array_filter(explode(PHP_EOL, file_get_contents($codePath)));
$lists  = [];
foreach ($data as $key => $value) {
    [$denomination, $code, $status] = array_filter(explode("-", $value));
    $lists[$key]['denomination'] = $denomination;
    $lists[$key]['code']         = $code;
    $lists[$key]['status']       = $status;
}

if ($method === 'post' && $func === 'add') {
    $params       = isset($_REQUEST['params']) ? $_REQUEST['params'] : "";
    $params       = json_decode($params, true);
    $code         = isset($params['code']) ? $params['code'] : "";
    $denomination = isset($params['denomination']) ? $params['denomination'] : "";
    $denomination = intval($denomination);

    $code = trim(strval($code));
    if (! in_array($denomination, [200, 500])) {
        exit(json_encode(['status' => false, 'msg' => '面额只能是200或者500']));
    }
    if (empty($code)) {
        exit(json_encode(['status' => false, 'msg' => '卡密不能为空']));
    }
    if (in_array($code, array_column($lists, 'code'))) {
        exit(json_encode(['status' => false, 'msg' => '卡密已存在']));
    }
    file_put_contents($codePath, "\n{$denomination}-{$code}-1", FILE_APPEND);
    exit(json_encode(['status' => true, 'msg' => '']));
}
return include "../show.php";