<?php  
require 'functions.php';
$guanzhu_user_id = maoo_user_id();
if(is_numeric($_GET['id']) && $guanzhu_user_id) :
	$id = $_GET['id'];
	if($guanzhu_user_id!=$id) :
		$user_guanzhu = $redis->zscore('user_guanzhu:'.$guanzhu_user_id,$id);
		if($user_guanzhu>0) :
		else :
			$guanzhu_date = strtotime("now");
			$redis->zadd('user_guanzhu:'.$guanzhu_user_id,$guanzhu_date,$id);
			$redis->zadd('user_fans:'.$id,$guanzhu_date,$guanzhu_user_id);
			$guanzhu_count = maoo_guanzhu_count($id);
			$redis->hset('user:'.$id,'guanzhu_count',$guanzhu_count+1);
			//信息
			$text = '<h4 class="title"><a href="'.maoo_url('user','index',array('id'=>$guanzhu_user_id)).'">'.maoo_user_display_name($guanzhu_user_id).'</a> 关注了你</h4>';
			maoo_add_message($guanzhu_user_id,$id,$text);
		endif;
	endif;
endif;
?>