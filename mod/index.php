<?php
class Maoo {
	public function index(){
		global $redis;
		if($_GET['page']>1) :
			$maoo_title_page = ' - 第'.$_GET['page'].'页';
		endif;
		if($_GET['s']) :
			$s = $_GET['s'];
			if($_GET['type']==2) :
				if(!$redis->exists('search:topic:'.$s)) :
					foreach($redis->zrevrange('topic_rank_list',0,1199) as $s_page_id) :
						if(strstr($redis->hget('topic:'.$s_page_id,'title'),$s) || strstr($redis->hget('topic:'.$s_page_id,'content'),$s)) :
							$redis->sadd('search:topic:'.$s,$s_page_id);
						endif;
					endforeach;
				endif;
				$type = 'topic';
			elseif($_GET['type']==3) :
				$users = $redis->keys('user_display_name:*'.$s.'*');
				if(!$redis->exists('search:user:'.$s)) :
					foreach($users as $s_key) :
						$s_page_id = $redis->get($s_key);
						$redis->sadd('search:user:'.$s,$s_page_id);
					endforeach;
				endif;
				$type = 'user';
			else :
				if(!$redis->exists('search:post:'.$s)) :
					foreach($redis->zrevrange('rank_list',0,1199) as $s_page_id) :
						if(strstr($redis->hget('post:'.$s_page_id,'title'),$s) || strstr($redis->hget('post:'.$s_page_id,'content'),$s)) :
							$redis->sadd('search:post:'.$s,$s_page_id);
						endif;
					endforeach;
				endif;
				$type = 'post';
			endif;
			$redis->expire('search:'.$type.':'.$s,7200);
			$count = $redis->scard('search:'.$type.':'.$s);
			$page_now = $_GET['page'];
			$page_size = $redis->get('page_size');
			if(empty($page_now) || $page_now<1) :
				$page_now = 1;
			else :
				$page_now = $_GET['page'];
			endif;
			$offset = ($page_now-1)*$page_size;
			$db = $redis->sort('search:'.$type.':'.$s,array('sort'=>'desc','limit'=>array($offset,$page_size)));
			$maoo_title = '搜索：'.$s.$maoo_title_page.' - '.$redis->get('site_name');
			include ROOT_PATH.'/theme/'.maoo_theme().'/search.php';
		else :
			if($redis->get('site_title')) :
				$maoo_title = $redis->get('site_title');
			else :
				$maoo_title = $redis->get('site_name');
			endif;
			$maoo_title = $maoo_title.$maoo_title_page;
			$maoo_keywords = $redis->get('site_keywords');
			$maoo_description = $redis->get('site_description');
			if($_GET['hometheme']=='pro' || $redis->get('hometheme')==2) :
				include ROOT_PATH.'/theme/'.maoo_theme().'/pro-index.php';
			else :
				include ROOT_PATH.'/theme/'.maoo_theme().'/index.php';
			endif;
		endif;
	}
	public function page(){
		global $redis;
		if($_GET['id']>0) {
			$id = $_GET['id'];
			if($redis->hget('post:page:'.$id,'title')) {
				$maoo_title = $redis->hget('post:page:'.$id,'title').' - '.$redis->get('site_name');
				include ROOT_PATH.'/theme/'.maoo_theme().'/page-single.php';
			} else {
				$error = '页面不存在';
				$maoo_title = '错误404 - '.$redis->get('site_name');
				include ROOT_PATH.'/theme/'.maoo_theme().'/404.php';

			}
		} else {
			$error = '您访问的页面没有找到';
			$maoo_title = '错误404 - '.$redis->get('site_name');
			include ROOT_PATH.'/theme/'.maoo_theme().'/404.php';
		}
	}
	public function authors(){
		global $redis;
		$maoo_title = '推荐作者 - '.$redis->get('site_name');
		include ROOT_PATH.'/theme/'.maoo_theme().'/authors.php';
	}
	public function search(){
		global $redis;
		$maoo_title = '搜索 - '.$redis->get('site_name');
		include ROOT_PATH.'/theme/'.maoo_theme().'/search.php';
	}
}
