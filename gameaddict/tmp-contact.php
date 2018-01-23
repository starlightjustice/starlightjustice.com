<?php
 /*
 * Template Name: Contact
 */
?>
<?php get_header(); ?>
<?php if (class_exists('MultiPostThumbnails')) : $custombck = MultiPostThumbnails::get_post_thumbnail_url(get_post_type(), 'header-image', $post->ID, 'full'); endif; ?>
<?php if(empty($custombck)){}else{ ?>
<style>
    body.page{
    background-image:url(<?php echo $custombck; ?>) !important;
    background-position:center top !important;
    background-repeat:  no-repeat !important;
}
</style>
<?php } ?>
          <?php
if(isset($_POST['submitted'])) {
    if(trim($_POST['contactName']) === '') {
        $nameError = __('Please enter your name.', 'addict');
        $hasError = true;
    } else {
        $name = trim($_POST['contactName']);
    }
    if(trim($_POST['email']) === '')  {
        $emailError = __('Please enter your email address.', 'addict');
        $hasError = true;
    } else if (!preg_match("/^[[:alnum:]][a-z0-9_.-]*@[a-z0-9.-]+\.[a-z]{2,4}$/i", trim($_POST['email']))) {
        $emailError = __('You entered an invalid email address.', 'addict');
        $hasError = true;
    } else {
        $email = trim($_POST['email']);
    }
    if(trim($_POST['comments']) === '') {
        $commentError = __('Please enter a message.', 'addict');
        $hasError = true;
    } else {
        if(function_exists('stripslashes')) {
            $comments = stripslashes(trim($_POST['comments']));
        } else {
            $comments = trim($_POST['comments']);
        }
    }
    if(!isset($hasError)) {
        $emailTo = of_get_option('contact_email');
        if (!isset($emailTo) || ($emailTo == '') ){
            $emailTo = of_get_option('contact_email');
        }
        $sub = $_POST['subject'];
        $subject = '[PHP Snippets] From '.$name;
        $body = "Name: $name \n\nEmail: $email \n\nSubject: $sub \n\nComments: $comments";
        $headers = 'From: '.$name.' <'.$emailTo.'>' . "\r\n" . 'Reply-To: ' . $email;
        wp_mail($emailTo, $subject, $body, $headers);
        $emailSent = true;
    }
} ?>
      <div class="<?php if ( of_get_option('fullwidth') ) {  }else{ ?>container<?php } ?> normal-page">
		<?php if ( of_get_option('fullwidth') ) { ?> <div class="container"> <?php } ?>
        <div class="row">
            <div class="span12 block-title centered">
                <h2><?php if ( of_get_option('contact_title') ) { echo  of_get_option('contact_title'); } ?></h2>
                <p><?php if ( of_get_option('contact_subtitle') ) { echo  of_get_option('contact_subtitle'); } ?></p>
            </div>
            <div class="span12 block-divider"></div>
            <?php
             if(of_get_option('contact_map_enable') == 1){
            ?>
            <div class="gmap span12">
                 <div id="map-canvas"></div>
            </div>
            <div class="span12 block-divider"></div>
			<div class="span12">
				<?php while ( have_posts() ) : the_post(); ?>
				<?php  the_content(); ?>
				<?php endwhile; // end of the loop. ?>
			<div class="clear"></div>
			</div>
            <?php
             }
            ?>
      </div>
 	  <?php if ( of_get_option('fullwidth') ) { ?> </div> <?php } ?>
    </div> <!-- /container -->
<?php get_footer(); ?>