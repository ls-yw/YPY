<?php
use yii\helpers\Url;
?>
<div class="layui-fluid">
<?php if(!empty($error)):?><blockquote class="layui-elem-quote bg-white red" id="blockquote-msg"><?=$error?></blockquote>
<?php else:?>
  <div class="layui-card">
    <div class="layui-card-header">发起上缴</div>
    <div class="layui-card-body" style="padding: 15px;">
      <form class="layui-form" action="" lay-filter="component-form-group">
        <div class="layui-form-item">
          <label class="layui-form-label">财务大人</label>
          <div class="layui-input-block">
            <select name="to_uid" lay-verify="required">
              <option value=""></option>
              <?php if($financer):foreach ($financer as $val):if($val->deleted == 0):?>
              <option value="<?=$val->to_uid?>"><?=$val->to_realname?></option>
              <?php endif;endforeach;endif;?>
            </select>
          </div>
        </div>
        
        <div class="layui-form-item">
          <label class="layui-form-label">类别</label>
          <div class="layui-input-block">
            <select name="cate_id" lay-verify="required">
              <option value=""></option>
              <option value="0">其他</option>
            </select>
          </div>
        </div>
        
        <div class="layui-form-item">
          <label class="layui-form-label">上缴标题</label>
          <div class="layui-input-block">
            <input type="text" name="title" lay-verify="title" autocomplete="off" placeholder="请输入标题" class="layui-input">
          </div>
        </div>
        
        <div class="layui-form-item layui-form-text">
          <label class="layui-form-label">上缴内容</label>
          <div class="layui-input-block">
            <textarea name="content" placeholder="请输入上缴内容" class="layui-textarea"></textarea>
          </div>
        </div>  
        
        <div class="layui-form-item">
          <div class="layui-inline">
            <label class="layui-form-label">上缴日期</label>
            <div class="layui-input-inline">
              <input type="text" name="at_date" id="LAY-component-form-group-date" lay-verify="date" placeholder="yyyy-MM-dd" autocomplete="off" class="layui-input">
            </div>
          </div>
        </div>
        
        <div class="layui-form-item">
          <div class="layui-inline">
            <label class="layui-form-label">上缴金额</label>
            <div class="layui-input-inline" style="width: 100px;">
              <input type="text" name="price" placeholder="￥" lay-verify="price" autocomplete="off" class="layui-input">
            </div>
            <div class="layui-form-mid">元</div>
          </div>
        </div>
        
        <div class="layui-form-item">
          <div class="layui-inline upload-img-box">
            <label class="layui-form-label">上缴证明</label>
            <?php if(!empty($expenseImg['exp'])):foreach ($expenseImg['exp'] as $val):?>
            <div class="layui-input-inline" style="width: 100px;">
            	<img src="<?=$val->img_url?>" width="90">
            	<i class="layui-icon layui-icon-close-fill x-icon"></i>
            	<input type="hidden" name="img[]" value="<?=$val->img_url?>"/>
            </div>
            <?php endforeach;endif;?>
            <div class="layui-input-inline" style="width: 100px;">
                <img class="layui-upload-img" src="/img/uploadimg.jpg" width="90">
            </div>
          </div>
        </div>
               
        <div class="layui-form-item">
          <div class="layui-input-block">
            <div class="layui-footer">
              <input type="hidden" name="_csrf" value="<?=Yii::$app->request->csrfToken?>" />
              <input type="hidden" name="id" autocomplete="off" class="layui-input" value="0">
              <input type="hidden" name="type" autocomplete="off" class="layui-input" value="<?=$type?>">
              <button class="layui-btn" lay-submit="" lay-filter="component-form-demo1">立即提交</button>
              <button type="reset" class="layui-btn layui-btn-primary">重置</button>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
$(function(){
	layui.use(['form', 'laydate', 'upload'], function(){
		  var element = layui.element
		  ,layer = layui.layer
		  ,laydate = layui.laydate
		  ,form = layui.form,
		  upload = layui.upload;
		  
		  form.render(null, 'component-form-group');

		  laydate.render({
		    elem: '#LAY-component-form-group-date',
		    max: '<?=date('Y-m-d')?>',
		    value: '<?=date('Y-m-d')?>'
		  });
		  
		  /* 自定义验证规则 */
		  form.verify({
		    title: function(value){
		      if(value.length < 2){
		        return '标题至少得2个字符啊';
		      }
		    }
		    ,pass: [/(.+){6,12}$/, '密码必须6到12位'],
			price:[/(^[1-9]([0-9]+)?(\.[0-9]{1,2})?$)|(^(0){1}$)|(^[0-9]\.[0-9]([0-9])?$)/, '请输入正确的金额']		    
		    ,content: function(value){
		      layedit.sync(editIndex);
		    }
		  });
		  <?php if(!empty($expense)):?>
		  form.val('component-form-group', {
			id:<?=$expense->id?>,
			to_uid:<?=$expense->to_uid?>,
			cate_id:<?=$expense->cate_id?>,
			title:'<?=$expense->title?>',
			content:'<?=$expense->content?>',
			price:'<?=$expense->price?>',
			at_date:'<?=$expense->at_date?>',
		  });
		  <?php endif;?>

    	var uploadInst = upload.render({
		      elem: '.layui-upload-img',
		      size: 500
		      ,url: '<?=Url::to(["upload/img"])?>'
		      ,before: function(){
			    layer.msg('上传中...',{time:0,shade:0.5});
		      }
		      ,done: function(res, index, upload){
			      if(res.code == 0){
			    	  layer.closeAll();
			    	  var item = this.item;
					  var html = '<div class="layui-input-inline" style="width: 100px;"><img src="'+res.data+'" width="90"><i class="layui-icon layui-icon-close-fill x-icon"></i><input type="hidden" name="img[]" value="'+res.data+'"/></div>';
					  $(item).closest('div').before(html);
				  }else{
					  layer.msg(res.msg,{icon:2});
				  }
		      }
		    })

		  
		  /* 监听提交 */
		  form.on('submit(component-form-demo1)', function(obj){
			  var loadObj = layer.load(1, {shade:0.3});
			  $.ajax({
					url:"<?=Url::to(["bill/saveexpense"])?>?type=income",
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
					        layer.msg(res.msg, {
					          icon: 1
					          ,time: 2000
					        }, function(){
					          location.href = "<?=Url::to(['bill/index'])?>";
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
		    return false;
		  });
		});
	$('.upload-img-box').on('click', '.x-icon', function(){
		$(this).closest('div').remove();
	});
});
</script>
<?php endif;?>