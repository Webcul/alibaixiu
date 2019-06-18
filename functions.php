<?php 
require_once 'config.php';

//封装大家公用的函数

session_start();

// 获取当前登录的用户信息，如果没有就自动跳转到登录页面

//定义函数时 一定要注意：函数名与内置函数冲突问题
function xiu_get_current_user(){
	if (empty($_SESSION['current_login_user'])) {
  //没有当前登录用户信息，意味着没有登录
  header('Location:/admin/login.php');
  exit(); //没有必要再执行会后的代码
}
	return $_SESSION['current_login_user'];
}

//通过一个数据库查询获取数据
function xiu_fetch($sql){
	$conn = mysqli_connect(XIU_DB_HOST,XIU_DB_USER,XIU_DB_PASS,XIU_DB_NAME);
	if (!$conn) {
		exit('连接失败');
	}
	$query = mysqli_query($conn,$sql);
	if (!$query) {
		//查询失败		return一个false
		return false;
	}
	while ($row = mysqli_fetch_assoc($query)) {
		$result[] = $row;
	}
	return $result;
}


// 获取单条数据
// function xiu_fech_one($sql){
// 	$res = xiu_fetch_all($sql);
// 	return isset($res[0]) ? $res[0] : null;
// 	$conn = mysqli_connect(XIU_DB_HOST,XIU_DB_USER,XIU_DB_PASS,XIU_DB_NAME);
// 	if (!$conn) {
// 		exit('连接失败');
// 	}
// 	$query = mysqli_query($conn,$sql);
// 	if (!$query) {
// 		//查询失败		return一个false
// 		return false;
// 	}
// 	$row = mysqli_fetch_assoc($query);
// 	return $row;

// }


//执行一个增删改语句

function xiu_execute($sql){
	$conn = mysqli_connect(XIU_DB_HOST,XIU_DB_USER,XIU_DB_PASS,XIU_DB_NAME);
	if (!$conn) {
		exit('连接失败');
	}
	$query = mysqli_query($conn,$sql);
	if (!$query) {
		//查询失败		return一个false
		return false;
	}
	//对应增删改操作都是获取受影响行数，获取受影响行数   传入一个连接值
	
	$affected_rows = mysqli_affected_rows($conn);
	 mysqli_close($conn);
	return $affected_rows;

}



 ?>