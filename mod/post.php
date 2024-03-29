<?php
class Maoo {
	public function latest(){
		global $redis;
		if($_GET['page']>1) :
			$maoo_title_page = ' - 第'.$_GET['page'].'页';
		endif;
		$maoo_title = '最新'.$maoo_title_page.' - '.$redis->get('site_name');
		include ROOT_PATH.'/theme/'.maoo_theme().'/latest.php';
	}
	public function publish(){
		global $redis;
		if(maoo_user_id()) {
			$user_id = maoo_user_id();
			if($_GET['topic_id']>0) {
				$topic_id = $_GET['topic_id'];
			} elseif($redis->hget('user_draft_post:'.$user_id,'topic')) {
				$topic_id = $redis->hget('user_draft_post:'.$user_id,'topic');
			};
			$maoo_title = '发布文章 - '.$redis->get('site_name');
			include ROOT_PATH.'/theme/'.maoo_theme().'/publish-post.php';
		} else {
			$maoo_title = '用户登录 - '.$redis->get('site_name');
			include ROOT_PATH.'/theme/'.maoo_theme().'/login.php';
		}
	}
	public function edit(){
		global $redis;
		if($_GET['id']>0) {
			$id = $_GET['id'];
		};
		if(maoo_user_id()) {
			$user_id = maoo_user_id();
			if($redis->hget('post:'.$id,'topic')>0) {
				$topic_id = $redis->hget('post:'.$id,'topic');
			} elseif($user_id && $redis->hget('user_draft_post:'.$user_id,'topic')) {
				$topic_id = $redis->hget('user_draft_post:'.$user_id,'topic');
			};
			if($redis->hget('post:'.$id,'permission')==3 || $redis->hget('post:'.$id,'permission')==31) {
				if($redis->hget('topic:'.$topic_id,'author')==$user_id) {
					$maoo_title = '投稿审核 - '.$redis->get('site_name');
					include ROOT_PATH.'/theme/'.maoo_theme().'/publish-post.php';
				} else {
					$error = '您没有权限进行此操作';
					$maoo_title = '错误404 - '.$redis->get('site_name');
					include ROOT_PATH.'/theme/'.maoo_theme().'/404.php';
				}
			} else {
				if($redis->hget('post:'.$id,'author')==$user_id || $redis->hget('user:'.maoo_user_id(),'user_level')==10 || $redis->hget('user:'.maoo_user_id(),'user_level')==8) {
					$maoo_title = '编辑文章 - '.$redis->get('site_name');
					include ROOT_PATH.'/theme/'.maoo_theme().'/publish-post.php';
				} else {
					$error = '您没有权限进行此操作';
					$maoo_title = '错误404 - '.$redis->get('site_name');
					include ROOT_PATH.'/theme/'.maoo_theme().'/404.php';
				}
			}
		} else {
			$maoo_title = '用户登录 - '.$redis->get('site_name');
			include ROOT_PATH.'/theme/'.maoo_theme().'/login.php';
		}
	}
	public function publishtopic(){
		global $redis;
		if(maoo_user_id()) {
			$maoo_title = '发起话题 - '.$redis->get('site_name');
			include ROOT_PATH.'/theme/'.maoo_theme().'/publish-topic.php';
		} else {
			$maoo_title = '用户登录 - '.$redis->get('site_name');
			include ROOT_PATH.'/theme/'.maoo_theme().'/login.php';
		}
	}
	public function single(){
		global $redis;
		if($_GET['id']>0) {
			$id = $_GET['id'];
			$author = $redis->hget('post:'.$id,'author');
			if($redis->hget('post:'.$id,'del')==1) {
				$error = '该文章已被删除';
				$maoo_title = '错误404 - '.$redis->get('site_name');
				include ROOT_PATH.'/theme/'.maoo_theme().'/404.php';
			} elseif($redis->hget('post:'.$id,'permission')==3) {
				if($author==maoo_user_id() || $redis->hget('topic:'.$redis->hget('post:'.$id,'topic'),'author')==maoo_user_id() || $redis->hget('user:'.maoo_user_id(),'user_level')==10) {
					$maoo_title = $redis->hget('post:'.$id,'title').' - 待审核 - '.$redis->get('site_name');
					include ROOT_PATH.'/theme/'.maoo_theme().'/post-single.php';
				} else {
					$error = '该文章正在审核中';
					$maoo_title = '错误404 - '.$redis->get('site_name');
					include ROOT_PATH.'/theme/'.maoo_theme().'/404.php';
				}
			} else {
				$maoo_title = $redis->hget('post:'.$id,'title').' - '.$redis->get('site_name');
				maoo_set_views($id);
				include ROOT_PATH.'/theme/'.maoo_theme().'/post-single.php';
			}
		} else {
			$error = '您访问的页面没有找到';
			$maoo_title = '错误404 - '.$redis->get('site_name');
			include ROOT_PATH.'/theme/'.maoo_theme().'/404.php';
		}
	}
	public function topic(){
		global $redis;
		if($_GET['id']>0) {
			$id = $_GET['id'];
			$maoo_title = $redis->hget('topic:'.$id,'title').' - '.$redis->get('site_name');
			include ROOT_PATH.'/theme/'.maoo_theme().'/post-topic-single.php';
		} else {
			if($_GET['page']>1) :
				$maoo_title_page = ' - 第'.$_GET['page'].'页';
			endif;
			$maoo_title = '话题广场'.$maoo_title_page.' - '.$redis->get('site_name');
			include ROOT_PATH.'/theme/'.maoo_theme().'/post-topic.php';
		}
	}
	public function topicset(){
		global $redis;
		if($_GET['id']>0) {
			$id = $_GET['id'];
			if($_GET['step']==1 || $_GET['step']==2 || $_GET['step']==3 || $_GET['step']==4) {
				$step = $_GET['step'];
				if($step==2) :
					if($redis->hget('topic:'.$id,'permission')==2) :
						$partners = $redis->smembers('topic_partner:'.$id);
					elseif($redis->hget('topic:'.$id,'permission')==3) :
						$contribute = $redis->smembers('con_topic_post_id:'.$id);
					endif;
				endif;
			} else {
				$step = 1;
			};
			$maoo_title = '管理 - '.$redis->hget('topic:'.$id,'title').' - '.$redis->get('site_name');
			include ROOT_PATH.'/theme/'.maoo_theme().'/post-topic-set.php';
		} else {
			$error = '没有找到可用的话题';
			$maoo_title = '错误404 - '.$redis->get('site_name');
			include ROOT_PATH.'/theme/'.maoo_theme().'/404.php';
		};
	}
	public function term(){
		global $redis;
		if($_GET['id']>0) {
			$id = $_GET['id'];
		};
		if($_GET['page']>1) :
			$maoo_title_page = ' - 第'.$_GET['page'].'页';
		endif;
		$maoo_title = maoo_term_title($id).$maoo_title_page.' - '.$redis->get('site_name');
		include ROOT_PATH.'/theme/'.maoo_theme().'/post-term.php';
	}
	public function tag(){
		global $redis;
		if($_GET['id']>0) {
			$id = $_GET['id'];
		};
		if($_GET['page']>1) :
			$maoo_title_page = ' - 第'.$_GET['page'].'页';
		endif;
		$tag = $_GET['tag'];
		$maoo_title = $tag.$maoo_title_page.' - '.$redis->get('site_name');
		include ROOT_PATH.'/theme/'.maoo_theme().'/post-tag.php';
	}
}
