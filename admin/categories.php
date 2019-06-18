<?php 
require_once 'D:/web/v-hosts/baixiu-dev/functions.php';
xiu_get_current_user();
function add_category(){
  if (empty($_POST['name']) || empty($_POST['slug'])) {
    $GLOBALS['message'] = '完整填写表单';
    $GLOBALS['success'] = false;
    return;
  }
  $name = $_POST['name'];
  $slug = $_POST['slug'];
  // var_dump ($name);
  $rows = xiu_execute("insert into categories values(null,'{$name}','{$slug}');"); 
    $GLOBALS['success'] = $rows>0;
    $GLOBALS['message'] = $rows<=0 ? '添加失败' :'添加成功';
}

function edit_category(){
  global $current_edit_category;
  //只有是编辑并点击保存
  // if (empty($_POST['name']) || empty($_POST['slug'])) {
  //   $GLOBALS['message'] = '完整填写表单';
  //   $GLOBALS['success'] = false;
  //   return;
  // }
  $id = $current_edit_category[0]['id'];
  $name = empty($_POST['name']) ? $current_edit_category[0]['name'] : $_POST['name'];
  //同步数据
  $current_edit_category[0]['name'] = $name;
  $slug = empty($_POST['slug']) ? $current_edit_category[0]['slug'] : $_POST['slug'];
  $current_edit_category[0]['slug'] = $slug;
  // var_dump ($name);
  $rows = xiu_execute("update categories set slug = '{$slug}',name = '{$name}' where id={$id}"); 
    $GLOBALS['success'] = $rows>0;
    $GLOBALS['message'] = $rows<=0 ? '更新失败' :'更新成功';
}


// 判断是编辑主线还是添加主线
if (empty($_GET['id'])) {

  // 添加
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    add_category();
  }

} else {
  // 编辑
  // 客户端通过 URL 传递了一个 ID
  // => 客户端是要来拿一个修改数据的表单
  // => 需要拿到用户想要修改的数据
  $current_edit_category = xiu_fetch('select * from categories where id = ' . $_GET['id']);
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    edit_category();
  }
}

//查询全部分类数据
$categories = xiu_fetch('select * from categories');





// if (!empty($_GET['id'])) {
  // 客户端通过URL传递了一个ID =>客户端哪一个修改数据的表单
  // 需要拿到用户想要修改的数据
//   $current_edit_category= xiu_fetch('select * from categories where id = ' .$_GET['id']);
// }

 ?>


<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Categories &laquo; Admin</title>
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
        <h1>分类目录</h1>
      </div>
      <!-- 有错误信息时展示 -->
      <?php if (isset($message)): ?>
        <?php if ($success): ?>
            <div class="alert alert-success">
              <strong>成功</strong><?php echo $message ?>
            </div>
          <?php else: ?>
            <div class="alert alert-danger">
              <strong>错误！</strong><?php echo $message ?>
            </div>
        <?php endif ?>
      <?php endif ?>
      <div class="row">
        <div class="col-md-4">
          <?php if (isset($current_edit_category)): ?>
              <form action="<?php echo $_SERVER['PHP_SELF']; ?>?id=<?php echo $current_edit_category[0]['id']; ?>" method="post">
                <h2>编辑《<?php echo $current_edit_category[0]['name']; ?>》</h2>
                <div class="form-group">
                  <label for="name">名称</label>
                  <input id="name" class="form-control" name="name" type="text" value="<?php echo $current_edit_category[0]['name']; ?>">
                </div>
                <div class="form-group">
                  <label for="slug">别名</label>
                  <input id="slug" class="form-control" name="slug" type="text" value="<?php echo $current_edit_category[0]['slug']; ?>">
                  <p class="help-block">https://zce.me/category/<strong>slug</strong></p>
                </div>
                <div class="form-group">
                  <button class="btn btn-primary" type="submit">修改</button>
               </div>
          </form>
            <?php else: ?>
              <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                <h2>添加新分类目录</h2>
                <div class="form-group">
                  <label for="name">名称</label>
                  <input id="name" class="form-control" name="name" type="text" placeholder="分类名称">
                </div>
                <div class="form-group">
                  <label for="slug">别名</label>
                  <input id="slug" class="form-control" name="slug" type="text" placeholder="slug">
                  <p class="help-block">https://zce.me/category/<strong>slug</strong></p>
                </div>
                <div class="form-group">
                  <button class="btn btn-primary" type="submit">添加</button>
                </div>
          </form>
          <?php endif ?>
        </div>
        <div class="col-md-8">
          <div class="page-action">
            <!-- show when multiple checked -->
            <a id="btn_delete" class="btn btn-danger btn-sm" href="/admin/category-delete.php" style="display: none">批量删除</a>
          </div>
          <table class="table table-striped table-bordered table-hover">
            <thead>
              <tr>
                <th class="text-center" width="40"><input type="checkbox"></th>
                <th>名称</th>
                <th>Slug</th>
                <th class="text-center" width="100">操作</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($categories as $item): ?>
                <tr>
                <td class="text-center"><input type="checkbox" data-id="<?php echo $item['id']; ?>"></td>
                <td><?php echo $item['name']; ?></td>
                <td><?php echo $item['slug']; ?></td>
                <td class="text-center">
                  <a href="/admin/categories.php?id=<?php echo $item['id']; ?>" class="btn btn-info btn-xs">编辑</a>
                  <a href="/admin/category-delete.php?id=<?php echo $item['id']; ?>" class="btn btn-danger btn-xs">删除</a>
                </td>
              </tr>
              <?php endforeach ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <?php $current_page = 'categories'; ?>
  <?php include 'inc/sidebar.php'; ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>
    //批量删除按钮的现实和隐藏
   $(function($){
    var $tbodyCheckboxs = $('tbody input');
    var $btnDelete = $('#btn_delete');
    //记录被选中的
    var allCheckeds = [];

    $tbodyCheckboxs.on('change',function(){
      //获取当前选中项的ID
      var id = $(this).data('id');
      //如果有checked属性，将ID放到数组中
      if($(this).prop('checked')){
        allCheckeds.includes(id) || allCheckeds.push(id);
      }else{
        allCheckeds.splice(allCheckeds.indexOf(id),1);
      }
      //只要数组不为空，就显示批量删除按钮
      allCheckeds.length ? $btnDelete.fadeIn() : $btnDelete.fadeOut();
      $btnDelete.prop('search','?id='+allCheckeds);
    })

      //全选与全不选
     $('thead input').on('change',function(){
      //接收一个布尔值
      var checked = $(this).prop('checked');
      // console.log(checked);
      $tbodyCheckboxs.prop('checked',checked).trigger('change');
    })


   })

   // #method 1====================================================
   // $tbodyCheckboxs.on('change',function(){
   //    var flag = false;
   //    $tbodyCheckboxs.each(function(i,item){
   //      if($(item).prop('checked')){
   //        flag = true;
   //      }
   //    })
   //    flag ? $btnDelete.fadeIn() : $btnDelete.fadeOut();
   //  })
  </script>

  <script>NProgress.done()</script>
</body>
</html>
