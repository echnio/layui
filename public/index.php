<?php
$method    = strtolower($_SERVER['REQUEST_METHOD']);
$func      = isset($_REQUEST['method']) ? $_REQUEST['method'] : "";
$loginUser = isset($_REQUEST['user']) ? $_REQUEST['user'] : "";
$loginUser = intval($loginUser);
$isAdmin   = $loginUser === 30303;

$userList = [
    10101 => '王晓军',
    20202 => '李红艳',
    30303 => '张仁平'
];

$userName = null;
foreach ($userList as $key => $v) {
    if ($loginUser === $key) {
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
$tmp    = [];
foreach ($data as $key => $value) {
    [$denomination, $code, $status] = array_filter(explode("-", $value));
    $tmp[md5($code)]['denomination'] = $denomination;
    $tmp[md5($code)]['status']       = $status;
    $tmp[md5($code)]['plaintext']    = $code;
}
$lists = [];
foreach ($tmp as $key => $value) {
    if ($value['status'] == 1) {
        $lists[$key] = $value;
        unset($tmp[$key]);
    }
}
$lists = array_merge($lists, $tmp);

if ($method === 'post' && $func === 'copy') {
    $code     = isset($_REQUEST['code']) ? trim($_REQUEST['code']) : "w";
    $codeInfo = isset($lists[$code]) ? $lists[$code] : [];
    $isCopy   = isset($codeInfo['status']) && $codeInfo['status'] == 1;
    if (! $isCopy) {
        exit(json_encode(['status' => false, 'msg' => '信息不存在或已被别人复制']));
    }
    $coping          = "";
    $copingPlaintext = "";
    foreach ($lists as $k => $doCopy) {
        if ($k == $code) {
            $coping          .= "{$doCopy['denomination']}-{$doCopy['plaintext']}-{$userName}\n";
            $copingPlaintext = $doCopy['plaintext'];
        } else {
            $coping .= "{$doCopy['denomination']}-{$doCopy['plaintext']}-{$doCopy['status']}\n";
        }
    }
    if (empty($copingPlaintext)) {
        exit(json_encode(['status' => false, 'msg' => '信息不存在或已被别人复制']));
    }
    file_put_contents($codePath, $coping);
    exit(json_encode(['status' => true, 'msg' => $copingPlaintext]));
}
if ($isAdmin && $method === 'post' && $func === 'delete') {
    $code     = isset($_REQUEST['code']) ? trim($_REQUEST['code']) : "w";
    $codeInfo = isset($lists[$code]) ? $lists[$code] : [];
    if (empty($codeInfo)) {
        exit(json_encode(['status' => false, 'msg' => '信息不存在或已删除']));
    }
    $delCode = "";
    foreach ($lists as $k => $doCopy) {
        if ($k != $code) {
            $delCode .= "{$doCopy['denomination']}-{$doCopy['plaintext']}-{$doCopy['status']}\n";
        }
    }
    file_put_contents($codePath, $delCode);
    exit(json_encode(['status' => true, 'msg' => '']));
}
if ($isAdmin && $method === 'post' && $func === 'add') {
    $params       = isset($_REQUEST['params']) ? $_REQUEST['params'] : "";
    $params       = json_decode($params, true);
    $code         = isset($params['code']) ? $params['code'] : "";
    $denomination = isset($params['denomination']) ? $params['denomination'] : "";
    $code         = trim($code);
    $denomination = intval($denomination);

    $code = trim(strval($code));
    if (! in_array($denomination, [200, 500, 1000])) {
        exit(json_encode(['status' => false, 'msg' => '面额只能是200、500、1000']));
    }
    if (empty($code)) {
        exit(json_encode(['status' => false, 'msg' => '卡密不能为空']));
    }
    if (in_array(md5($code), array_keys($lists))) {
        exit(json_encode(['status' => false, 'msg' => '卡密已存在']));
    }
    file_put_contents($codePath, "\n{$denomination}-{$code}-1", FILE_APPEND);
    exit(json_encode(['status' => true, 'msg' => '']));
}
return include "../show.php";
