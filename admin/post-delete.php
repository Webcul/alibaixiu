<?php

/**
 * 根据客户端传递过来的ID删除对应数据
 */

require_once '../functions.php';

if (empty($_GET['id'])) {
  exit('缺少必要参数');
}

// $id = (int)$_GET['id'];
$id = $_GET['id'];
// => '1 or 1 = 1'
// sql 注入
// 1,2,3,4

$rows = xiu_execute('delete from posts where id in (' . $id . ');');

// if ($rows > 0) {}
// http 中的 referer 用来标识当前请求的来源
// 为了避免删除后跳转
header('Location: ' . $_SERVER['HTTP_REFERER']);
