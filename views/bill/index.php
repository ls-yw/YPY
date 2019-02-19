<?php
use yii\helpers\Url;
?>
<div class="layui-row">

<?php if($blockquote):?><blockquote class="layui-elem-quote bg-white" id="blockquote-msg"><?=$blockquote?></blockquote><?php endif;?>

<div id="list"></div>

</div>

<script>
$(function(){
	layui.use(['layer', 'flow'], function(){
		  var layer = layui.layer;
		  var flow = layui.flow;

		  $('#LAY_app').on('click', '.addCwdr', function(){
			  var index = layer.prompt({title: '添加财务大人的手机号码'}, function(value, index){
				  	if(value.length != 11){
				  		layer.msg('请填写正确的手机号码', {icon:2, time:2000});
				  		return false;
					 }
				  	var loadObj = layer.load(1, {shade:0.3});
				  	$.ajax({
						url:"<?=Url::to(["user/addrelation"])?>",
						data:{'mobile':value,'_csrf':'<?=Yii::$app->request->csrfToken?>'},
						dataType:'json',
						type:'POST',
						error:function(){
							layer.msg('请求失败', {icon: 2});
							return false;
						},
						success:function(res){
							//请求成功后，写入 access_token
					        if(res.code == 0){
					        	layer.close(index);
					        	//登入成功的提示与跳转
						        layer.msg(res.msg, {
						          icon: 1
						          ,time: 1000
						        }, function(){
						        	$('#blockquote-msg').html('等待财务大人('+res.data.realName+')的审核，<a href="javascript:;" class="red cancelCwdr" data-touid="'+res.data.toUid+'">取消申请</a>？');
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
		 });

		$('#LAY_app').on('click', '.cancelCwdr', function(){
			var obj = $(this);
			layer.confirm('确定取消申请？', function(index){
				var loadObj = layer.load(1, {shade:0.3});
			  //do something
			  $.ajax({
					url:"<?=Url::to(["user/cancelrelation"])?>",
					data:{'touid':obj.data('touid'),'_csrf':'<?=Yii::$app->request->csrfToken?>'},
					dataType:'json',
					type:'POST',
					error:function(){
						layer.msg('请求失败', {icon: 2});
						return false;
					},
					success:function(res){
						//请求成功后，写入 access_token
				        if(res.code == 0){
				        	layer.close(index);
				        	//登入成功的提示与跳转
					        layer.msg(res.msg, {
					          icon: 1
					        });
					        $('#blockquote-msg').hide();
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
		});

		//同意
		$('#LAY_app').on('click', '.okBao', function(){
			var obj = $(this);
			layer.confirm('确定<span class="green">同意</span>申请？', function(index){
				var loadObj = layer.load(1, {shade:0.3});
			  //do something
			  $.ajax({
					url:"<?=Url::to(["user/exrelation"])?>",
					data:{'id':obj.data('id'),'t':'ok','_csrf':'<?=Yii::$app->request->csrfToken?>'},
					dataType:'json',
					type:'POST',
					error:function(){
						layer.msg('请求失败', {icon: 2});
						return false;
					},
					success:function(res){
						layer.close(index);
						//请求成功后，写入 access_token
				        if(res.code == 0){
				        	//登入成功的提示与跳转
					        layer.msg(res.msg, {
					          icon: 1
					        });
					        $('#blockquote-msg').hide();
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
		});

		//拒绝
		$('#LAY_app').on('click', '.noBao', function(){
			var obj = $(this);
			layer.confirm('确定<span class="red">拒绝</span>申请？', function(index){
				var loadObj = layer.load(1, {shade:0.3});
			  //do something
			  $.ajax({
					url:"<?=Url::to(["user/exrelation"])?>",
					data:{'id':obj.data('id'),'t':'no','_csrf':'<?=Yii::$app->request->csrfToken?>'},
					dataType:'json',
					type:'POST',
					error:function(){
						layer.msg('请求失败', {icon: 2});
						return false;
					},
					success:function(res){
						layer.close(index);
						//请求成功后，写入 access_token
				        if(res.code == 0){
				        	//登入成功的提示与跳转
					        layer.msg(res.msg, {
					          icon: 1
					        });
					        $('#blockquote-msg').hide();
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
		});

		flow.load({
		    elem: '#list', //指定列表容器
		    isAuto:true,
		    done: function(page, next){ //到达临界点（默认滚动触发），触发下一页
		      var lis = [];
		      //以jQuery的Ajax请求为例，请求下一页数据（注意：page是从2开始返回）
		      $.ajax({
					url:'<?=Url::to(['bill/expense'])?>?page='+page,
					type:'GET',
					error:function(){
						layer.msg('列表请求失败', {icon: 2});
						return false;
					},
					success:function(res){
						//假设你的列表返回在data集合中
				        layui.each(res.data, function(index, item){
							var html = '<div class="layui-card list-status-'+item.at_status+' type-'+item.at_type+'"><div class="layui-card-header"> ';
							if(item.role == 'expenser'){
								html += '您向'+item.to_realname+'发起申请'+(item.at_type == 'income' ? '上缴' : '报销')+'：'+item.title;
							}else{
								html += item.realname+'向您发起申请'+(item.at_type == 'income' ? '上缴' : '报销')+'：'+item.title;
							}

							var htmlHanld = '';
							if(item.at_status == 1){
								html +='<i class="layui-icon" style="font-size:14px;">待审核</i>';
								htmlHanld += (item.role == 'expenser') ? '<button class="layui-btn layui-btn-sm expense-edit">修改</button><button class="layui-btn layui-btn-sm layui-btn-danger expense-cancel">取消</button>' : '<button class="layui-btn layui-btn-sm expense-agree">同意</button><button class="layui-btn layui-btn-sm layui-btn-danger expense-refuse">拒绝</button>';
							}else if(item.at_status == 2){
								html +='<i class="layui-icon" style="font-size:14px;">待打款</i>';
								if(item.at_type == 'income'){
									htmlHanld += (item.role == 'expenser') ? '<button class="layui-btn layui-btn-sm expense-pay">已打款</button><button class="layui-btn layui-btn-sm layui-btn-danger expense-cancel">取消</button>' : '';
								}else{
									htmlHanld += (item.role == 'expenser') ? '<button class="layui-btn layui-btn-sm layui-btn-danger expense-cancel">取消</button>' : '<button class="layui-btn layui-btn-sm expense-pay">已打款</button><button class="layui-btn layui-btn-sm layui-btn-danger expense-refuse">拒绝</button>';
								}
							}else if(item.at_status == 3){
								html +='<i class="layui-icon" style="font-size:14px;">待确认</i>';
								if(item.at_type == 'income'){
									htmlHanld += (item.role == 'expenser') ? '' : '<button class="layui-btn layui-btn-sm expense-done">收到款</button><button class="layui-btn layui-btn-sm layui-btn-warm expense-nopay">未打款</button>';
								}else{
									htmlHanld += (item.role == 'expenser') ? '<button class="layui-btn layui-btn-sm expense-done">收到款</button><button class="layui-btn layui-btn-sm layui-btn-warm expense-nopay">未打款</button>' : '';
								}
							}else if(item.at_status == 4){
								html +='<i class="layui-icon" style="font-size:14px;">已完成</i>';
							}else if(item.at_status == 5){
								html +='<i class="layui-icon" style="font-size:14px;">已取消</i>';
							}else if(item.at_status == 6){
								html +='<i class="layui-icon" style="font-size:14px;">已拒绝</i>';
							}
							
							html +='</div><div class="layui-card-body layui-text layadmin-text"><p>报销内容：'+(item.content == '' ? '无' : item.content)+'</p><p>报销日期：'+item.at_date+'</p><p>报销金额：'+item.price+' 元</p><p>发起时间：'+item.create_time+'</p>';

							if(item.expenseImg.length > 0){
								html += '<p>报销明细：';
								$.each(item.expenseImg, function(i, imgs){
									if(imgs.at_type == 'exp')html += '<img src="'+imgs.img_url+'" onclick="showImg(\''+imgs.img_url+'\');" width="60" height="60" style="margin-right:10px;cursor:pointer;">';
								});
								html += '</p>';
							}

							if(htmlHanld != '')html += '<p style="text-align:right" data-id="'+item.id+'">'+htmlHanld+'</p>';
							html += '</div></div>';
					        
				          lis.push(html);
				        }); 
				        
				        //执行下一页渲染，第二参数为：满足“加载更多”的条件，即后面仍有分页
				        //pages为Ajax返回的总页数，只有当前页小于总页数的情况下，才会继续出现加载更多
				        next(lis.join(''), page < res.pageInfo.pages);    
					}
				});

		    }
		  });

		//修改
		$('#list').on('click', '.expense-edit', function(){
			var id = $(this).closest('p').data('id');
			location.href = "<?=Url::to(['bill/editexpense'])?>?id="+id;
		});

		//取消
		$('#list').on('click', '.expense-cancel', function(){
			var id = $(this).closest('p').data('id');
			layer.confirm('确认取消？', {icon: 3, title:'提示'}, function(index){
			 changeStatus(id, 5)
			});
		});

		//同意
		$('#list').on('click', '.expense-agree', function(){
			var id = $(this).closest('p').data('id');
			layer.confirm('确认同意？', {icon: 3, title:'提示'}, function(index){
			 changeStatus(id, 2)
			});
		});

		//打款
		$('#list').on('click', '.expense-pay', function(){
			var id = $(this).closest('p').data('id');
			layer.confirm('确认已打款？', {icon: 3, title:'提示'}, function(index){
			 changeStatus(id, 3)
			});
		});

		//未打款
		$('#list').on('click', '.expense-nopay', function(){
			var id = $(this).closest('p').data('id');
			layer.confirm('确认未收到打款？', {icon: 3, title:'提示'}, function(index){
			 changeStatus(id, 2)
			});
		});

		//完成
		$('#list').on('click', '.expense-done', function(){
			var id = $(this).closest('p').data('id');
			layer.confirm('确认已收到打款？', {icon: 3, title:'提示'}, function(index){
			 changeStatus(id, 4)
			});
		});

		//拒绝
		$('#list').on('click', '.expense-refuse', function(){
			var id = $(this).closest('p').data('id');
			layer.confirm('确认拒绝？', {icon: 3, title:'提示'}, function(index){
			 changeStatus(id, 6)
			});
		});
		  
	});    
});
function changeStatus(id, status)
{
	var loadObj = layer.load(1, {shade:0.3});
	$.ajax({
		url:"<?=Url::to(["bill/changeexpense"])?>",
		data:{'id':id,'status':status,'_csrf':'<?=Yii::$app->request->csrfToken?>'},
		dataType:'json',
		type:'POST',
		error:function(){
			layer.msg('请求失败', {icon: 2});
			return false;
		},
		success:function(res){
			//请求成功后，写入 access_token
	        if(res.code == 0){
	        	location.href=location.href;
		    }else{
		    	layer.msg(res.msg, {icon: 2});
		    	return false;
			}
		},
		complete:function(){
			layer.close(loadObj);
		}
	});
}
function showImg(url){
	
	var img_infor = "<img src='" + url + "' width='100%' />";
	layer.open({    
        type: 1, 
        closeBtn: 1,
        shade: 0.3,    
        title: false, //不显示标题
       	//skin: 'layui-layer-nobg', //没有背景色
        shadeClose: true,
        //area: [img.width + 'px', img.height+'px'],    
        content: img_infor //捕获的元素，注意：最好该指定的元素要存放在body最外层，否则可能被其它的相对元素所影响    
    	});    
}
</script>

