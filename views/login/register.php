<?php
use yii\helpers\Url;
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
  <title>注册-ypy</title>
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
          <h2>注 册</h2>
          <p></p>
        </div>
        <div class="layadmin-user-login-box layadmin-user-login-body layui-form">
          <div class="layui-form-item">
            <label class="layadmin-user-login-icon layui-icon layui-icon-cellphone" for="LAY-user-login-cellphone"></label>
            <input type="text" name="mobile" id="mobile" lay-verify="phone" placeholder="手机" class="layui-input">
          </div>
          <div class="layui-form-item">
            <label class="layadmin-user-login-icon layui-icon layui-icon-username" for="LAY-user-login-nickname"></label>
            <input type="text" name="realname" id="realname" lay-verify="required" placeholder="真实姓名" class="layui-input">
          </div>
          <div class="layui-form-item">
            <label class="layadmin-user-login-icon layui-icon layui-icon-password" for="LAY-user-login-password"></label>
            <input type="password" name="password" id="LAY-user-login-password" lay-verify="pass" placeholder="密码" class="layui-input">
          </div>
          <div class="layui-form-item">
            <label class="layadmin-user-login-icon layui-icon layui-icon-password" for="LAY-user-login-repass"></label>
            <input type="password" name="repass" id="LAY-user-login-repass" lay-verify="required" placeholder="确认密码" class="layui-input">
          </div>
          <div class="layui-form-item">
          	<input type="hidden" name="_csrf" value="<?=Yii::$app->request->csrfToken?>" />
            <button class="layui-btn layui-btn-fluid" lay-submit lay-filter="LAY-user-reg-submit">注 册</button>
          </div>
          <div class="layui-form-item">
            <a class="layui-btn layui-btn-fluid layui-btn-primary" href="<?=Url::to(['login/index'])?>">登 录</a>
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
	layui.use(['form'], function(){
		   form = layui.form
		  
		  //提交
		  form.on('submit(LAY-user-reg-submit)', function(obj){
		    var field = obj.field;
		    
		    //确认密码
		    if(field.password !== field.repass){
		      return layer.msg('两次密码输入不一致');
		    }

		    $.ajax({
				url:"<?=Url::to(["login/register"])?>",
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
			        	//登入成功的提示与跳转
				        layer.msg('注册成功', {
				          offset: '15px'
				          ,icon: 1
				          ,time: 1000
				        }, function(){
				          location.href = res.data;
				        });
				    }else{
				    	layer.msg(res.msg, {icon: 2});
				    	return false;
					}
				},
				complete:function(){
				}
			});
		    
		    return false;
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