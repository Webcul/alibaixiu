<?php 
//	服务端
// 根据用户邮箱获取用户头像，JS代码发送AJAX请求调用本api
//email => image
require_once 'D:/web/v-hosts/baixiu-dev/config.php';
//1.接受前台（login.php）传递过来的邮箱
if (empty($_GET['email'])) {
	return;
}
$email = $_GET['email'];
//2.查询对应的头像地址
$conn = mysqli_connect(XIU_DB_HOST,XIU_DB_USER,XIU_DB_PASS,XIU_DB_NAME);
if (empty($conn)) {
	exit('连接数据库失败');
}
$res = mysqli_query($conn,"select avatar from users where email = '{$email}' limit 1;"); 
 if (!$res) {
 	exit('查询失败');
 }
 $row = mysqli_fetch_assoc($res);
//3.echo

echo $row['avatar'];
 ?>