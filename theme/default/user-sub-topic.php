<?php include('header.php'); ?>
<?php include_once('user-head.php'); ?>
<div class="container user-center">
	<div class="row">
		<div class="col-lg-8 col-lg-offset-2 col">
            <?php include_once('user-nav-1.php'); ?>
			
					<div class="topic-list row">
                        <?php
                            $count = $redis->zcard('user_sub_topic_id:'.$user_id);
                            $page_now = $_GET['page'];
                            $page_size = $redis->get('page_size');
                            if(empty($page_now) || $page_now<1) :
                                $page_now = 1;
                            else :
                                $page_now = $_GET['page'];
                            endif;
                            $offset = ($page_now-1)*$page_size;
                            $db = $redis->zrevrange('user_sub_topic_id:'.$user_id,$offset,$offset+$page_size-1);
                        ?>
						<?php foreach($db as $page_id) : ?>
						<div class="topic-<?php echo $page_id; ?> topic col-md-6 col">
							<div class="topic-pr pr mb-20">
								<div class="topic-img" style="background-image: url(<?php echo maoo_fmimg($page_id,'topic'); ?>);"></div>
								<div class="topic-bg"></div>
								<a class="topic-txt" href="<?php echo maoo_url('post','topic',array('id'=>$page_id)); ?>">
									<h2 class="title">
										<?php echo $redis->hget('topic:'.$page_id,'title'); ?>
									</h2>
									<?php echo maoo_sub_count($page_id); ?>人订阅
								</a>
								<?php echo maoo_sub_btn($page_id); ?>
							</div>
						</div>
						<?php endforeach; ?>
					</div>
				
            <?php echo maoo_pagenavi($count,$page_now); ?>
		</div>
	</div>
</div>
<?php echo maoo_sub_js(); ?>
<?php include('footer.php'); ?>