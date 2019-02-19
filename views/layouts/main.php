<?php
use yii\helpers\Url;

?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
  <title><?=$title ?? 'PYP';?></title>
  <link rel="stylesheet" href="/css/base.css">
  <link rel="stylesheet" href="/plugins/layui/css/layui.css">
  <link rel="stylesheet" href="/css/admin.css">
  <script src="/js/jquery.min.js"></script>
</head>
<body>
 
 <div id="LAY_app" class="layadmin-tabspage-none">
<div class="layui-container">
  <div class="layui-row">
    <div class="layui-layout layui-layout-admin">
      <div class="layui-header">
        <!-- 头部区域 -->
        <ul class="layui-nav layui-layout-left">
          <li class="layui-nav-item layadmin-flexible" lay-unselect>
            <a href="javascript:;" layadmin-event="flexible" title="侧边伸缩">
              <i class="layui-icon layui-icon-shrink-right" id="LAY_app_flexible"></i>
            </a>
          </li>
          <!--<li class="layui-nav-item layui-this layui-hide-xs layui-hide-sm layui-show-md-inline-block">
            <a lay-href="" title="">
              控制台
            </a>
          </li>-->
          <li class="layui-nav-item" lay-unselect>
            <a href="<?=Url::to(['bill/index'])?>" title="首页">
              <i class="layui-icon layui-icon-home"></i>
            </a>
          </li>
        </ul>
        <ul class="layui-nav layui-layout-right" lay-filter="layadmin-layout-right">
          
          <li class="layui-nav-item" lay-unselect>
            <a  layadmin-event="message">
              <i class="layui-icon layui-icon-notice"></i>
              <!-- 如果有新消息，则显示小圆点 -->
            	<span class="layui-badge-dot"></span>
              
            </a>
          </li>
          <li class="layui-nav-item layui-hide-xs" lay-unselect>
            <a href="javascript:;" layadmin-event="note">
              <i class="layui-icon layui-icon-note"></i>
            </a>
          </li>
          <li class="layui-nav-item" lay-unselect>
          <a href="javascript:;">
            <cite><?=Yii::$app->user->identity->realname ?? 0;?></cite>
          </a>
          <dl class="layui-nav-child">
            <dd><a lay-href="set/user/info">基本资料</a></dd>
            <dd><a lay-href="set/user/password">修改密码</a></dd>
            <hr>
            <dd style="text-align: center;"><a href="<?=Url::to(['login/logout'])?>">退出</a></dd>
          </dl>
          </li>
          
        </ul>
      </div>
      
      <!-- 侧边菜单 -->
      <div class="layui-side layui-side-menu">
        <div class="layui-side-scroll">
        <div class="layui-logo" lay-href="">
          <span>YPY</span>
        </div>
        
        <ul class="layui-nav layui-nav-tree" lay-shrink="all" id="LAY-system-side-menu" lay-filter="layadmin-system-side-menu">
<!--           <li data-name="a" data-jump="" class="layui-nav-item layui-nav-itemed"> -->
<!--             <a href="javascript:;" lay-tips="主页" lay-direction="2"> -->
<!--               <i class="layui-icon layui-icon-home"></i> -->
<!--               <cite>主页</cite> -->
<!--             </a> -->
<!--               <dl class="layui-nav-child"> -->
<!--                 <dd  data-name="fenyeyi"  data-jump="a/yi"> -->
<!--                   <a href="javascript:;" lay-href="a/yi/b"  class="layui-this">分页一</a> -->
<!--                     <dl class="layui-nav-child"> -->
<!--                         <dd data-name=""  data-jump="" class="layui-this"> -->
<!--                           <a href="javascript:;" lay-href="{{ url3 }}" >三级</a> -->
<!--                         </dd> -->
<!--                     </dl> -->
                </dd>
                
<!--                 <dd  data-name="fenyeyi"  data-jump="a/yi"> -->
<!--                   <a href="javascript:;" lay-href="a/yi/b">分页二</a> -->
<!--                 </dd> -->
<!--             </dl> -->
<!--           </li> -->
          
          
          
          <li data-name="a" data-jump="" class="layui-nav-item layui-nav-itemed">
            <a href="<?=Url::to(['bill/addexpense'])?>?type=expense" lay-tips="shouquan" lay-direction="2">
              <i class="layui-icon layui-icon-add-circle-fine"></i>
              <cite>发起报销</cite>
            </a>
          </li>
          <li data-name="a" data-jump="" class="layui-nav-item layui-nav-itemed">
            <a href="<?=Url::to(['bill/addexpense'])?>?type=income" lay-tips="shouquan" lay-direction="2">
              <i class="layui-icon layui-icon-add-circle-fine"></i>
              <cite>发起上缴</cite>
            </a>
          </li>
        
        </ul>
        </div>
      </div>
      
      
      <!-- 主体内容 -->
      <div class="layui-body" id="LAY_app_body">
        <div class="layadmin-tabsbody-item layui-show">
        	<div class="layui-fluid">
        	<?=$content?>
        	</div>
        </div>
      </div>
      
      <!-- 辅助元素，一般用于移动设备下遮罩 -->
      <div class="layadmin-body-shade" layadmin-event="shade"></div>
      
    </div>

  </div>
</div>  
</div> 
<script src="/plugins/layui/layui.js"></script>
<script src="/js/jquery.cookie.js"></script>
<script>
/**

@Name：全局配置
@Author：贤心
@Site：http://www.layui.com/admin/
@License：LPPL（layui付费产品协议）
   
*/

layui.define(['laytpl', 'layer', 'element', 'util'], function(exports){
 exports('setter', {
   container: 'LAY_app' //容器ID
   ,base: layui.cache.base //记录layuiAdmin文件夹所在路径
   ,views: layui.cache.base + 'views/' //视图所在目录
   ,entry: 'index' //默认视图文件名
   ,engine: '.html' //视图文件后缀名
   ,pageTabs: false //是否开启页面选项卡功能。单页版不推荐开启
   
   ,name: 'layuiAdmin Pro'
   ,tableName: 'layuiAdmin' //本地存储表名
   ,MOD_NAME: 'admin' //模块事件名
   
   ,debug: true //是否开启调试模式。如开启，接口异常时会抛出异常 URL 等信息
   
   ,interceptor: false //是否开启未登入拦截
   
   //自定义请求字段
   ,request: {
     tokenName: 'access_token' //自动携带 token 的字段名。可设置 false 不携带。
   }
   
   //自定义响应字段
   ,response: {
     statusName: 'code' //数据状态的字段名称
     ,statusCode: {
       ok: 0 //数据状态一切正常的状态码
       ,logout: 1001 //登录状态失效的状态码
     }
     ,msgName: 'msg' //状态信息的字段名称
     ,dataName: 'data' //数据详情的字段名称
   }

   //主题配置
   ,theme: {
     //内置主题配色方案
     color: [{
       main: '#20222A' //主题色
       ,selected: '#009688' //选中色
       ,alias: 'default' //默认别名
     },{
       main: '#03152A'
       ,selected: '#3B91FF'
       ,alias: 'dark-blue' //藏蓝
     },{
       main: '#2E241B'
       ,selected: '#A48566'
       ,alias: 'coffee' //咖啡
     },{
       main: '#50314F'
       ,selected: '#7A4D7B'
       ,alias: 'purple-red' //紫红
     },{
       main: '#344058'
       ,logo: '#1E9FFF'
       ,selected: '#1E9FFF'
       ,alias: 'ocean' //海洋
     },{
       main: '#3A3D49'
       ,logo: '#2F9688'
       ,selected: '#5FB878'
       ,alias: 'green' //墨绿
     },{
       main: '#20222A'
       ,logo: '#F78400'
       ,selected: '#F78400'
       ,alias: 'red' //橙色
     },{
       main: '#28333E'
       ,logo: '#AA3130'
       ,selected: '#AA3130'
       ,alias: 'fashion-red' //时尚红
     },{
       main: '#24262F'
       ,logo: '#3A3D49'
       ,selected: '#009688'
       ,alias: 'classic-black' //经典黑
     },{
       logo: '#226A62'
       ,header: '#2F9688'
       ,alias: 'green-header' //墨绿头
     },{
       main: '#344058'
       ,logo: '#0085E8'
       ,selected: '#1E9FFF'
       ,header: '#1E9FFF'
       ,alias: 'ocean-header' //海洋头
     },{
       header: '#393D49'
       ,alias: 'classic-black-header' //经典黑
     },{
       main: '#50314F'
       ,logo: '#50314F'
       ,selected: '#7A4D7B'
       ,header: '#50314F'
       ,alias: 'purple-red-header' //紫红头
     },{
       main: '#28333E'
       ,logo: '#28333E'
       ,selected: '#AA3130'
       ,header: '#AA3130'
       ,alias: 'fashion-red-header' //时尚红头
     },{
       main: '#28333E'
       ,logo: '#009688'
       ,selected: '#009688'
       ,header: '#009688'
       ,alias: 'green-header' //墨绿头
     }]
     
     //初始的颜色索引，对应上面的配色方案数组索引
     //如果本地已经有主题色记录，则以本地记录为优先，除非请求本地数据（localStorage）
     ,initColorIndex: 0
   }
 });
});

$(function(){
	$('#LAY_app').on('click', '#LAY_app_flexible', function(){
		var e = $(window).width();
		if(e > 768){
			var c = 'layadmin-side-shrink';
		}else{
			var c = 'layadmin-side-spread-sm';
		}
		if($('#LAY_app').hasClass(c)){
			$('#LAY_app').removeClass(c);
		}else{
			$('#LAY_app').addClass(c);
		}
	});
	$('#LAY_app').on('click', '.layadmin-body-shade', function(){
		var c = 'layadmin-side-spread-sm';
		if($('#LAY_app').hasClass(c)){
			$('#LAY_app').removeClass(c);
		}
	});
});

</script>
</body>
</html>