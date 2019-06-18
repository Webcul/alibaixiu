<?php 

require_once 'D:/web/v-hosts/baixiu-dev/functions.php';
xiu_get_current_user();

//接收url传递的筛选参数
//=====================
$where = '1 = 1'; //为了使where条件不为空,逻辑判断返回true（1）
$search = '';
//文章分类   $_GET['category']
if (isset($_GET['category']) && $_GET['category'] !== 'all') {
  $where .= ' and posts.category_id = ' . $_GET['category'];
  $search .= '&category=' .$_GET['category'];
}

//文章状态    '{$_GET['status']}'
if (isset($_GET['status']) && $_GET['status'] !== 'all') {
  $where .= " and posts.status = '{$_GET['status']}'";
  $search .= '&status=' .$_GET['status'];
}

// var_dump($search);
// var_dump($where);
// 处理分页参数,取得是一个字符串，要将之转换为一个整形
// ==================================================
$page = empty($_GET['page']) ? 1 : (int)$_GET['page'];
$size = 10;

if ($page < 1) {
  //不可能有小鱼1的情况
  // $page = $page<1 ? 1 : $page;
  header('Location:/admin/posts.php?page=1' . $search);
}
//计算越过多少条数据
$offset = ($page - 1) * $size; 

  //获取全部数据select * from posts
  //============================
  
  $posts = xiu_fetch("SELECT 
posts.`id`,
posts.`title`,
users.`nickname` AS user_name,
categories.`name` AS category_name,
posts.`created`,
posts.`status`
FROM posts 
INNER JOIN categories ON posts.`category_id` = categories.`id`
INNER JOIN users ON posts.`user_id` = users.`id`
where {$where}
ORDER BY posts.`created` DESC
LIMIT {$offset},{$size};");

// 获取分类数据
$categories = xiu_fetch('select * from categories;');

 
//求出最大页码
  $total_count = (int)xiu_fetch("SELECT 
count(1)
as num
FROM posts 
INNER JOIN categories ON posts.`category_id` = categories.`id`
INNER JOIN users ON posts.`user_id` = users.`id`
where {$where};"
)[0]['num'];

$total_pages = (int)ceil($total_count / $size);

// $page = $page>$total_pages ? $total_pages : $page;
if ($page>$total_pages) {
  header('Location:/admin/posts.php?page=' . $total_pages . $search);
}


// 处理分页页码
// =====================

// 计算页码开始
$visiables = 5;
$region = ($visiables-1)/2; //左右区间
$begin = $page - $region;  //开始页码
$end = $begin + $visiables;  //结束页码加1
// 可能出现$begin和$end的不合理状态
// $begin必须 >0 确保$begin最小为1 
if($begin<1){

  $begin = 1;
  //begin修改意味着必须修改end,让begin和end永远差5
  $end = $begin + $visiables;
}

// $end必须 <= 最大页数
if ($end > $total_pages + 1) {
   //end超出范围
   $end = $total_pages + 1;
   //end修改意味着必须修改begin
   $begin = $end - $visiables;
   if($begin<1){
      $begin = 1;
  }
 } 

// 最大页数 $total_pages = ceil($total_count / size)

/*
  1. 当前页码显示高亮
  2. 左侧和右侧各有2个页码
  3. 开始页码不能小于1
  4. 结束页码不能大于最大页数
  5. 当前页码不为1时显示上一页
  6. 当前页码不为最大值时显示下一页
  7. 当开始页码不等于1时显示省略号
  8. 当结束页码不等于最大时显示省略号
*/


  //处理数据格式转换
  //============================
  
  function convert_status($status){
    //把所有状态都记录下来
    $dict = array(
      'published'=> '已发布',
      'drafted'=> '草稿',
      'trashed'=> '回收站',
    );
    return isset($dict[$status]) ? $dict[$status] : '未知状态';
  }

  function convert_date($created){
    // 转化为一个时间戳
    $timetamp = strtotime($created);
    return date('Y年m月d日<b\r>H:i:s',$timetamp);
  }

  //展示分类
  // function get_category($category_id){
  //   $names = xiu_fetch("select name from categories where id = {$category_id}")[0];
  //   return $names['name'];
  // }
  // //展示作者
  // function get_user($user_id){
  //   $nicknames = xiu_fetch("select nickname from users where id = {$user_id}")[0];
  //   return $nicknames['nickname'] ? $nicknames['nickname'] : 'error';
  // }
 ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Posts &laquo; Admin</title>
  <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/static/assets/vendors/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" href="/static/assets/vendors/nprogress/nprogress.css">
  <link rel="stylesheet" href="/static/assets/css/admin.css">
  <script src="/static/assets/vendors/nprogress/nprogress.js"></script>
</head>
<body>
  <script>NProgress.start()</script>

  <div class="main">
    <?php include 'inc/navbar.php'; ?>
    <div class="container-fluid">
      <div class="page-title">
        <h1>所有文章</h1>
        <a href="post-add.html" class="btn btn-primary btn-xs">写文章</a>
      </div>
      <!-- 有错误信息时展示 -->
      <!-- <div class="alert alert-danger">
        <strong>错误！</strong>发生XXX错误
      </div> -->
      <div class="page-action">
        <!-- show when multiple checked -->
        <a class="btn btn-danger btn-sm" href="javascript:;" style="display: none">批量删除</a>
        <form class="form-inline" action="<?php echo $_SERVER['PHP_SELF']; ?>">
          <select name="category" class="form-control input-sm">
            <option value="all">所有分类</option>
            <?php foreach ($categories as $item): ?>
            <option value="<?php echo $item['id']; ?>"<?php echo isset($_GET['category']) && $_GET['category'] == $item['id'] ? ' selected' : '' ?>>
              <?php echo $item['name']; ?>
            </option>
            <?php endforeach ?>
          </select>
          <select name="status" class="form-control input-sm">
            <option value="all">所有状态</option>
            <option value="drafted"<?php echo isset($_GET['status']) && $_GET['status'] == 'drafted' ? ' selected' : '' ?>>草稿</option>
            <option value="published"<?php echo isset($_GET['status']) && $_GET['status'] == 'published' ? ' selected' : '' ?>>已发布</option>
            <option value="trashed"<?php echo isset($_GET['status']) && $_GET['status'] == 'trashed' ? ' selected' : '' ?>>回收站</option>
          </select>
          <button class="btn btn-default btn-sm">筛选</button>
        </form>
        <ul class="pagination pagination-sm pull-right">
          <li><a href="#">上一页</a></li>
          <?php for ($i=$begin; $i <$end ; $i++): ?>
            <li<?php echo $i===$page ? ' class="active"' : ''; ?>><a href="?page=<?php echo $i . $search; ?>"><?php echo $i; ?></a></li>
          <?php endfor ?>
          <li><a href="#">下一页</a></li>
        </ul>
      </div>
      <table class="table table-striped table-bordered table-hover">
        <thead>
          <tr>
            <th class="text-center" width="40"><input type="checkbox"></th>
            <th>标题</th>
            <th>作者</th>
            <th>分类</th>
            <th class="text-center">发表时间</th>
            <th class="text-center">状态</th>
            <th class="text-center" width="100">操作</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($posts as $item): ?>
            <tr>
            <td class="text-center"><input type="checkbox"></td>
            <td><?php echo $item['title']; ?></td>
            <td><?php echo $item['user_name']; ?></td>
            <td><?php echo $item['category_name']; ?></td>
            <td class="text-center"><?php echo convert_date($item['created']); ?></td>
            <!-- 一旦输出的判断逻辑或者转换逻辑过于复杂，不建议直接写在混编的位置 -->
            <td class="text-center"><?php echo convert_status($item['status']); ?></td>
            <td class="text-center">
              <a href="javascript:;" class="btn btn-default btn-xs">编辑</a>
              <a href="/admin/post-delete.php?id=<?php echo $item['id']; ?>" class="btn btn-danger btn-xs">删除</a>
            </td>
          </tr>
          <?php endforeach ?>
        </tbody>
      </table>
    </div>
  </div>

  <?php $current_page = 'posts'; ?>
  <?php include 'inc/sidebar.php'; ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>NProgress.done()</script>
</body>
</html>
