<?php 
//载入配置文件
require_once 'D:/web/v-hosts/baixiu-dev/config.php';

//给用户找一个箱子（如果之前有就用之前的，没有就给个新的）
session_start();

function login(){
// 接受并校验
// 持久化
// 响应
if (empty($_POST['email'])) {
  $GLOBALS['message'] = '邮箱未填';
  return;
}

if (empty($_POST['password'])) {
  $GLOBALS['message'] = '密码未填';
  return;
}

// 接受客户端传过来的值
$email = $_POST['email'];
$password = $_POST['password'];

//当客户端提交过来的完整的表单信息就开始对其进行数据校验

// 数据库连接、校验
$conn = mysqli_connect(XIU_DB_HOST,XIU_DB_USER,XIU_DB_PASS,XIU_DB_NAME);
if (!$conn) {
  exit('<h1>连接数据库失败</h1>');
}

//查询
//找到第一条数据时就不再向下找了 limit 1  {$email}注意用大括号引起来
$query = mysqli_query($conn,"select * from users where email = '{$email}' limit 1;");

//如果数据与数据库中的页面不匹配
if (!$query) {
  $GLOBALS['message'] = '登录失败，请重试';
  return;
}

//取出每一条数据,,没有必要遍历，，因为limit 1已限制了只能有一条数据
//获取登录用户
$user = mysqli_fetch_assoc($query);

//判断是否有user用户
if (!$user) {
  $GLOBALS['message'] = '密码与邮箱不匹配';
  return;
}

if ($user['password']!==$password) {
  //密码不正确
  $GLOBALS['message'] = '密码与邮箱不匹配';
}

//存一个登录标识
//为了后续可以直接获取当前登录的用户信息，这里直接将用户信息放到session中
$_SESSION['current_login_user'] = $user;


//一切OK，可以跳转
header('Location:/admin/index.php');

}

if ($_SERVER['REQUEST_METHOD']==='POST') {
  login();
}

//退出登录
if ($_SERVER['REQUEST_METHOD']==='GET' && isset($_GET['action']) && $_GET['action']==='logout') {
  //删除了登录标识
  unset($_SESSION['current_login_user']);
}


 ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Sign in &laquo; Admin</title>
  <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/static/assets/vendors/animate/animate.css">
  <link rel="stylesheet" href="/static/assets/css/admin.css">
</head>
<body>
  <div class="login">
    <!-- 关闭客户端校验，在form上添加novalidate -->
    <!-- autocomplete="off"关闭客户端的自动完成功能 -->
    <form class="login-wrap<?php echo isset($message) ? ' swing animated' :'' ?>" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" novalidate autocomplete="off">   
      <img class="avatar" src="/static/assets/img/default.png">
      <!-- 有错误信息时展示 -->
      <?php if (isset($message)): ?>
        <div class="alert alert-danger">
        <strong>错误！</strong> <?php echo $message; ?>
      </div>
      <?php endif ?>
      <div class="form-group">
        <label for="email" class="sr-only">邮箱</label>
        <input id="email" name="email" type="email" class="form-control" placeholder="邮箱" autofocus value="<?php echo empty($_POST['email']) ? '' : $_POST['email'] ?>">
      </div>
      <div class="form-group">
        <label for="password" class="sr-only">密码</label>
        <input id="password" name="password" type="password" class="form-control" placeholder="密码">
      </div>
      <button class="btn btn-primary btn-block">登 录</button>
    </form>
  </div>

<script src="/static/assets/vendors/jquery/jquery.js"></script>

<script>
  $(function($){
    // 1.单独作用域
    // 2.确保页面加载过后执行
    //目标：在用户输入自己的邮箱过后，页面上展示对应的头像
    //实现：
    //时机-邮箱文本框失去焦点,拿到文本框中填写的邮箱时
    //事情-获取文本框填写的对应的头像地址展示上面的img元素上
    
    //创建正则对象，将对象放在//中间
    var emilFormat = /^[a-zA-Z0-9]+@[a-zA-Z0-9]+\.[a-zA-Z0-9]+$/;

    $('#email').on('blur',function(){
      var value = $(this).val();
      //忽略文本框为空或者不是一个邮箱
      if (!value || !emilFormat.test(value)) return; 
        //用户输入了一个合理的邮箱地址
        //因为客户端的JS无法操作数据库，应该通过JS发送AJAX请求 告诉服务端的某个接口
        //让这个接口帮助客户端获取头像地址
        //以对象的方式创建自变量{email:value}
        $.get('/admin/api/avatar.php',{email:value},function(res){
            //希望res=> 这个邮箱对应的头像地址
            if (!res) return;
            //展示到上面的 img 元素上
            $('.avatar').fadeOut(function(){
              //等到 淡出完成
              $(this).on('load',function(){
                //图片完全加载完成功过后
                $(this).fadeIn();
              }).attr('src',res);
            })
        });
    })
  })
</script>

</body>
</html>
