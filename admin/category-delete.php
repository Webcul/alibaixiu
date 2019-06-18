<?php 

// 个人你就传过来的ID珊瑚对应数据

require_once 'D:/web/v-hosts/baixiu-dev/functions.php';
if (empty($_GET['id'])) {
	exit('缺少必要参数');
}
//转一下int型，防止sql注入
$id = $_GET['id'];
// => '1 or 1 = 1'
// sql注入
$rows = xiu_execute('delete from categories where id in('. $id .');');
// if ($rows>0) {}

header('Location:/admin/categories.php');
 ?>