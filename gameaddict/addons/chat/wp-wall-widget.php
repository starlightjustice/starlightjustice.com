<!-- WP Wall -->
<?php
	echo $before_widget;
	echo $before_title . $wall_title. $after_title;
?>

<div id="wp_wall">
		<div class="wallnav">
					<i alt="Previous" id="img_left" class="icon-circle-arrow-left"></i>
					<i alt="Next" id="img_right" class="icon-circle-arrow-right"></i>
					<?php if ( $show_all ) : ?>
					<a  href="<?php echo get_permalink($pageId) ?>">All</a>
					<?php endif; ?>
  </div>
	<div id="wallcomments">
			<?php echo WPWall_ShowComments(); ?>
	</div>
 
 

	<?php if (  $rss_feed ) : ?>

		<p><a href="<?php echo get_post_comments_feed_link($pageId); ?>" id="wall_rss"><img src="<?php echo $wp_wall_plugin_url; ?>/i/feed.png" /> <?php echo $wall_title; ?> RSS Feed</a></p>

	<?php endif; ?>

	<?php if ( ! $disable_new ) : ?>
		<?php if (  $only_registered && !$user_ID) : ?>

			<a class="logtopost" href="wp-login.php"><?php _e("Log in to post a comment.", 'addict'); ?></a>


		<?php else : ?>

			<a id="wall_post_toggle"> <i class="icon-chevron-right"></i> <?php echo $wall_reply; ?></a>

		<?php endif; ?>
	<?php endif; ?>



	<div id="wall_post">
		<form action="<?php echo get_template_directory_uri() . '/addons/chat/wp-wall-ajax.php'; ?>" method="post" id="wallform">

			<?php if ( $user_ID ) : ?>

			<p>Logged in as <a href="<?php echo site_url(); ?>/wp-admin/profile.php"><?php echo $user_identity; ?></a>.</p>

			<?php else : ?>

			<p>
			<label for="wpwall_author"><small><?php _e("Name", 'addict'); ?></small></label>
			<input type="text" name="wpwall_author" id="wpwall_author" value=""  tabindex="11"  />
			</p>

			<?php if ( $show_email ) : ?>
			<p>
			<label for="wpwall_email"><small><?php _e("Email", 'addict'); ?></small></label>
			<input type="text" name="wpwall_email" id="wpwall_email" value=""  tabindex="12"  />
			</p>
			<?php endif; ?>

			<?php endif; ?>

			<p>
			<label for="wpwall_comment"><small><?php _e("Comment", 'addict'); ?></small></label>
			<textarea name="wpwall_comment" id="wpwall_comment" rows="3" cols="" tabindex="13" ></textarea>
			</p>

			<p><input name="submit_wall_post" type="submit" id="submit_wall_post" tabindex="14" value="Leave a comment" /></p>

		</form>
	</div>

	<div id="wallresponse"></div>

</div>
<br />
<?php

	echo $after_widget;


?>