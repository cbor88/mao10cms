<div class="user-head text-center mb-40">
	<img src="<?php echo maoo_user_avatar($user_id); ?>" alt="<?php echo maoo_user_display_name($user_id); ?>">
	<h1 class="title mb-10"><?php echo maoo_user_display_name($user_id); ?></h1>
	<div class="mb-20">
		<?php echo $redis->hget('user:'.$user_id,'description'); ?>
	</div>
	<?php echo maoo_guanzhu_btn($user_id); ?>
</div>