/**
 * 添加按钮操作
 */
$("#button-add").click(function(){
	var url = SCOPE.add_url;
	window.location.href=url;
});

/**提交表单操作
 * 
 */
$("#singcms-button-submit").click(function(){
	var data = $("#singcms-form").serializeArray();
	postData = {};
	$(data).each(function(i){
		postData[this.name] = this.value;
	});
	//console.log(postData);
	//将获取的数据post给服务器
	url = SCOPE.save_url;
	jump_url = SCOPE.jump_url
	$.post(url,postData,function(result){
		if(result.status == 1){
			return dialog.success(result.message,jump_url);
		}else{
			return dialog.error(result.message);
		}
	},"JSON")
});

$(".singcms-table #singcms-edit").on('click',function(){
	var id = $(this).attr('attr-id');
	var url = SCOPE.edit_url+'&id='+id;
	window.location.href=url;
});

$(".singcms-table #singcms-delete").on('click',function(){
	var id = $(this).attr('attr-id');
	var a = $(this).attr('attr-a');
	var message = $(this).attr('attr-message');
	var url = SCOPE.set_status_url;
	
	data = {};
	data['id'] = id;
	data['status'] = -1;
	
	layer.open({
		type : 0,
		title :'是否提交?',
		btn: ['yes','no'],
		icon :3,
		closeBtn :2,
		content:'是否确定'+message,
		scrollbar:true,
		yes:function(){
			todelete(url,data);
			/*deletebyzhr(id);*/
		},
	});
});

$(".singcms-table #singcms-view").on('click',function(){
	var id = $(this).attr('attr-id');
	var a = $(this).attr('attr-a');
	var url = "/index.php?c=detail&a=view&id="+id;
	window.location.href=url;
});

$(".singcms-table #singcms-on-off").on('click',function(){
	var id = $(this).attr('attr-id');
	var message = $(this).attr('attr-message');
	var status = $(this).attr('attr-status');
	if(status==0){
		status =1;
	}else if(status==1){
		status =0;
	}
	
	var url = SCOPE.set_status_url;
	
	data = {};
	data['id'] = id;
	data['status'] = status;
	
	layer.open({
		type : 0,
		title :'是否提交?',
		btn: ['yes','no'],
		icon :3,
		closeBtn :2,
		content:'是否确定'+message,
		scrollbar:true,
		yes:function(){
			todelete(url,data);
		},
	});
});
function todelete(url,data){
	$.post(url,data,function(result){
		if(result.status == 1){
			return dialog.success(result.message,SCOPE.delete_url);
		}else{
			return dialog.error(result.message);
		}
	},"JSON");
}
/**
 * 自己写的删除方法，完全从数据库中删除
 * @param $menu_id
 */
function deletebyzhr($menu_id){
	postData = {};
	postData['menu_id'] = $menu_id;
	$.post(SCOPE.delete_url,postData,function(result){
		if(result.status == 1){
			return dialog.success(result.message,SCOPE.delete_url);
		}else{
			return dialog.error(result.message);
		}
	},"JSON")
}


/**
 * 排序操作
 */
$("#button-listorder").click(function(){
	/**/
	var data = $("#singcms-listorder").serializeArray();
	postData ={};
	$(data).each(function(i){
		postData[this.name] = this.value;
	});
	var url = SCOPE.listorder_url;
	$.post(url,postData,function(result){
		if(result.status == 1){
			return dialog.success(result.message,SCOPE.listordered);
		}else if(result.status == 0){//失败
			return dialog.error(result.message);
		}
	},"JSON");
	console.log(postData);
	/*$.post(url,postData,function(result){
		//成功
		if(result.status == 1){
			return dialog.success(result.message,result['data']['jump_url']);
		}else if(result.status == 0){//失败
			return dialog.error(result.message,result['data']['jump_url']);
		}
		
	},"JSON");*/
});

/**
 * 推送JS相关
 * 
 */

$("#singcms-push").click(function(){
	var id = $("#select-push").val();
	if(id == 0){
		return dialog.error("请选择推荐位");
	}
	push = {};
	postData = {};
	$("input[name='pushcheck']:checked").each(function(i){
		push[i] = $(this).val();
	});
	
	postData['push'] =push;
	postData['position_id'] = id;
	var url = SCOPE.push_url;
	//console.log(postData);
	$.post(url,postData,function(result){
		if(result.status ==1){
			return dialog.success(result.message,result['data']['jump_url']);
		}
		if(result.status ==0){
			return dialog.error(result.message);
		}
	},"JSON");
});
