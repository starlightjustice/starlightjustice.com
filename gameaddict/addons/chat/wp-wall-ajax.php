<?php

require_once("../../../../../wp-config.php");

if ($_POST['submit_wall_post'])
{

	$options = WPWall_GetOptions();

	$comment_post_ID=$options['pageId'];
	$actual_post=get_post($comment_post_ID);

	// sanity check to see if our page exists
	if (!$comment_post_ID || !$actual_post || ($comment_post_ID!=$actual_post->ID) )
	{
		wp_die('Sorry, there was a problem posting your comment. Please try again.');
	}

	if ($options['disable_new'])
	{
		wp_die('Sorry, the comments are disabled at the moment.');
	}

	// extract data we need
	$comment_author       = trim(strip_tags($_POST['wpwall_author']));
	$comment_content      = trim($_POST['wpwall_comment']);
	$comment_author_email = trim($_POST['wpwall_email']);

	// If the user is logged in get his name
	$user = wp_get_current_user();
	if ( $user->ID ) {
		$comment_author  = $wpdb->escape($user->display_name);
	  $comment_author_email = $wpdb->escape($user->user_email);

	}
	else if (get_user_by('login',$comment_author))
			wp_die('Sorry, you have to pick another name.');
	else if ( $options['only_registered'] )
		wp_die('Sorry, you must be logged in to post a comment.' );

	// check if the fields are filled
	if ( '' == $comment_author )
		wp_die('Error: please type a name.');

	if ( '' == $comment_content )
		wp_die('Error: please type a comment.');

	if ($options['clickable_links'])
	 $comment_content=WPWall_MakeClickable($comment_content);

	// insert the comment
	$commentdata = compact('comment_post_ID', 'comment_author', 'comment_author_email', 'comment_content', 'user_ID');

	$comment_id = wp_new_comment( $commentdata );

	// check if the comment is approved
	$comment = get_comment($comment_id);

	if ($comment->comment_approved==0)
		wp_die('Your comment is awaiting moderation.');

	// return status
	nocache_headers();
	die ( WPWall_ShowComments() );
}
else if ($_GET['refresh'])
{
	nocache_headers();
	die ( WPWall_ShowComments($_GET['refresh']) );
}

?>