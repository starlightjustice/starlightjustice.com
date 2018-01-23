<?php
/**
 * Widget Name: Popular Posts with a Thumbnail
 * Description: A Popular Posts widget that displays a thumbnail from your blog.
 * Version: 1.0
 */

class PopularWidget extends WP_Widget {

    function PopularWidget() {
        parent::WP_Widget(false, $name = __('Popular Posts', 'addict'));
    }

    function widget($args, $instance) {
        extract( $args );
        $title = apply_filters('widget_title', $instance['title']);
        $nopost=$instance['nopost'];
        ?>

	<?php echo $before_widget; ?>
<div class="title-wrapper"> <h3 class="widget-title"> <?php echo  $instance['title'] ; ?></h3><div class="clear"></div></div>

    <ul class="review">

<?php $pc = new WP_Query('orderby=comment_count&cat='.$instance['cat'].'&posts_per_page='.$nopost);
if ( $pc->have_posts() ) : ?>
<?php while ($pc->have_posts()) : $pc->the_post(); ?>


      <li>		<div class="img">
		  		 <a rel="bookmark" href="<?php the_permalink(); ?>">
					<?php if(has_post_thumbnail()){

							$thumb = get_post_thumbnail_id();
							$img_url = wp_get_attachment_url( $thumb,'full'); //get img URL
							$image = aq_resize( $img_url, 57, 57, true ); //resize & crop img
							?>
							<img src="<?php echo $image ?>" />

					<?php } else{ ?>
						<img src="<?php echo get_template_directory_uri().'/img/defaults/57x57.jpg'?> "/>
					<?php } ?>
					<span class="overlay-link"></span>
				</a>
				</div>
        <div class="info"> <a href="<?php the_permalink(); ?>">
          <?php the_title(); ?>
          </a><br/>
          <small>
          <i class="icon-calendar"></i> <?php the_time('F j, Y'); ?> - <i class="icon-comment"></i> <?php echo comments_number( __('No comments', 'One comment', '% comments' )); ?></small><br/>
		<?php
		// overall stars
		$postid=$pc->post->ID;
		$overall_rating_1 = get_post_meta($postid, 'overall_rating', true);

		if($overall_rating_1!="0" && $overall_rating_1=="0.5"){ ?>
		<div class="overall-score"><div class="rating r-05"></div></div>
		<?php } ?>

		<?php $overall_rating_2 = get_post_meta($postid, 'overall_rating', true);
		if($overall_rating_2!="0" && $overall_rating_2=="1"){ ?>
		<div class="overall-score"><div class="rating r-1"></div></div>
		<?php } ?>

		<?php $overall_rating_3 = get_post_meta($postid, 'overall_rating', true);
		if($overall_rating_3!="0" && $overall_rating_3=="1.5"){ ?>
		<div class="overall-score"><div class="rating r-15"></div></div>
		<?php } ?>

		<?php $overall_rating_4 = get_post_meta($postid, 'overall_rating', true);
		if($overall_rating_4!="0" && $overall_rating_4=="2"){ ?>
		<div class="overall-score"><div class="rating r-2"></div></div>
		<?php } ?>

		<?php $overall_rating_5 = get_post_meta($postid, 'overall_rating', true);
		if($overall_rating_5!="0" && $overall_rating_5=="2.5"){ ?>
		<div class="overall-score"><div class="rating r-25"></div></div>
		<?php } ?>

		<?php $overall_rating_6 = get_post_meta($postid, 'overall_rating', true);
		if($overall_rating_6!="0" && $overall_rating_6=="3"){ ?>
		<div class="overall-score"><div class="rating r-3"></div></div>
		<?php } ?>

		<?php $overall_rating_7 = get_post_meta($postid, 'overall_rating', true);
		if($overall_rating_7!="0" && $overall_rating_7=="3.5"){ ?>
		<div class="overall-score"><div class="rating r-35"></div></div>
		<?php } ?>

		<?php $overall_rating_8 = get_post_meta($postid, 'overall_rating', true);
		if($overall_rating_8!="0" && $overall_rating_8=="4"){ ?>
		<div class="overall-score"><div class="rating r-4"></div></div>
		<?php } ?>

		<?php $overall_rating_9 = get_post_meta($postid, 'overall_rating', true);
		if($overall_rating_9!="0" && $overall_rating_9=="4.5"){ ?>
		<div class="overall-score"><div class="rating r-45"></div></div>
		<?php } ?>

		<?php $overall_rating_10 = get_post_meta($postid, 'overall_rating', true);
		if($overall_rating_10!="0" && $overall_rating_10=="5"){ ?>
		<div class="overall-score"><div class="rating r-5"></div></div>

		<?php } ?>

		</div>
		<div class="clear"></div>
      </li>
      <?php endwhile;  ?>
      <?php else : ?>
      <div><?php _e("NO Popular Post", 'addict'); ?></div>
      <?php endif; ?>

    </ul>


              <?php echo $after_widget; ?>
        <?php
    }

/** @see WP_Widget::update */
    function update($new_instance, $old_instance) {
	$instance = $old_instance;

	$instance['title'] = strip_tags($new_instance['title']);
	$instance['cat'] = strip_tags($_POST['cat']);
	$instance['nopost'] = strip_tags($new_instance['nopost']);

        return $instance;
    }

/** @see WP_Widget::form */
    function form($instance) {
        $title = esc_attr($instance['title']);
        $category = esc_attr($instance['cat']);
        $nopost = esc_attr($instance['nopost']);
        ?>
         <p>
          <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'addict'); ?></label>
          <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
        </p>
         <p>
          <label for="<?php echo $this->get_field_id('cat'); ?>"><?php _e('Category:', 'addict'); ?></label>
         <?php wp_dropdown_categories('selected='. $category); ?>
        </p>
         <p>
          <label for="<?php echo $this->get_field_id('nopost'); ?>"><?php _e('No. of Posts:', 'addict'); ?></label>
          <input class="widefat" id="<?php echo $this->get_field_id('nopost'); ?>" name="<?php echo $this->get_field_name('nopost'); ?>" type="text" value="<?php echo $nopost; ?>" />
        </p>
        <?php
    }

}

// register widget
add_action('widgets_init', create_function('', 'return register_widget("PopularWidget");'));
?>