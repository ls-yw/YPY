<?php
use yii\helpers\Url;
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
  <title>登录-ypy</title>
  <link rel="stylesheet" href="/plugins/layui/css/layui.css">
  <link rel="stylesheet" href="/css/login.css">
  <script src="/js/jquery.min.js"></script>
</head>
<body>
 
<div class="layui-container">
  <div class="layui-row">
    <div class="layadmin-user-login layadmin-user-display-show" id="LAY-user-login">
      <div class="layadmin-user-login-main">
      	<div class="layadmin-user-login-box layadmin-user-login-header">
          <h2>登 录</h2>
          <p></p>
        </div>
        <div class="layadmin-user-login-box layadmin-user-login-body layui-form">
          <div class="layui-form-item">
            <label class="layadmin-user-login-icon layui-icon layui-icon-username" for="LAY-user-login-username"></label>
            <input type="text" name="mobile" id="mobile" lay-verify="required|phone" placeholder="手机号" class="layui-input">
          </div>
          <div class="layui-form-item">
            <label class="layadmin-user-login-icon layui-icon layui-icon-password" for="LAY-user-login-password"></label>
            <input type="password" name="password" id="password" lay-verify="required|pass" placeholder="密码" class="layui-input">
          </div>
          <div class="layui-form-item" style="margin-bottom: 20px;">
            <input type="checkbox" name="remember" lay-skin="primary" title="记住密码" checked>
            <!-- <a lay-href="/user/forget" class="layadmin-user-jump-change layadmin-link" style="margin-top: 7px;">忘记密码？</a> -->
          </div>
          <div class="layui-form-item">
            <button class="layui-btn layui-btn-fluid" lay-submit lay-filter="LAY-user-login-submit">登 录</button>
          </div>
          <div class="layui-form-item">
          	<input type="hidden" name="_csrf" value="<?=Yii::$app->request->csrfToken?>" />
            <a class="layui-btn layui-btn-fluid layui-btn-primary" href="<?=Url::to(['login/register'])?>">注 册</a>
          </div>
        </div>
      </div>
      
    </div>
  </div>
</div>  
 
<script src="/plugins/layui/layui.js"></script>
<script src="/js/jquery.cookie.js"></script>
<script>
$(function(){
	$('#mobile').focus();
	layui.use('form', function(){
	  form = layui.form

	  //提交
	  form.on('submit(LAY-user-login-submit)', function(obj){
		  var loadObj = layer.load(1, {shade:0.3});
		  $.ajax({
				url:"<?=Url::to(["login/index"])?>",
				data:obj.field,
				dataType:'json',
				type:'POST',
				error:function(){
					layer.msg('请求失败', {icon: 2});
					return false;
				},
				success:function(res){
					//请求成功后，写入 access_token
			        if(res.code == 0){
				        $.cookie(res.data.cookie.name, res.data.cookie.value, { expires: 360, path: '/' });
			        	//登入成功的提示与跳转
				        layer.msg('登入成功', {
				          icon: 1
				          ,time: 1000
				        }, function(){
				          location.href = res.data.url;
				        });
				    }else{
				    	layer.msg(res.msg, {icon: 2});
				    	return false;
					}
				},
				complete:function(){
					layer.close(loadObj);
				}
			});
	    
	  });

	  form.verify({
		  //我们既支持上述函数式的方式，也支持下述数组的形式
		  //数组的两个值分别代表：[正则匹配、匹配不符时的提示文字]
		  pass: [
		    /^[\S]{6,12}$/
		    ,'密码必须6到12位，且不能出现空格'
		  ] 
		});  
	  
	});
});
</script>
</body>
</html>