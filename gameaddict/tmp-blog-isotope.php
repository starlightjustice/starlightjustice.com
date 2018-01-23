<?php
/*
 * Template name: Blog - Isotope
*/
?>
<?php get_header();?>
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
<!-- Page content
    ================================================== -->
<!-- Wrap the rest of the page in another container to center all the content. -->
<div class="<?php if ( of_get_option('fullwidth') ) {  }else{ ?>container boxed<?php } ?> blog normal-page container-wrap">
    <?php if ( of_get_option('fullwidth') ) { ?> <div class="container"> <?php } ?>
  <div class="row">
         <div class="isoblog span12"> <!-- new line -->
        <?php
        $category_id = of_get_option('blogcat');
        $showposts = of_get_option('blognum');
        $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
        $new_query = new WP_Query();
        $new_query->query( 'showposts='.$showposts.'&cat='.$category_id.'&paged='.$paged );
       ?>
        <?php if ( $new_query->have_posts() ) : while ( $new_query->have_posts() ) : $new_query->the_post(); ?>
    <div class="blog-post">


        <div class="blog-image img_thumb entry-thumb">
             <?php
                 $key_1_value = get_post_meta($post->ID, '_smartmeta_my-awesome-field77', true);
                if($key_1_value != '') {
                echo $key_1_value;
                }elseif ( has_post_thumbnail() ) { ?>
                  <a href="<?php the_permalink(); ?>">  <?php
                            $thumb = get_post_thumbnail_id();
                            $img_url = wp_get_attachment_url( $thumb,'full'); //get img URL
                            $image = aq_resize( $img_url, 817, 310, true, '', true ); //resize & crop img
                            ?>
                            <img class="attachment-small wp-post-image" src="<?php echo $image[0]; ?>" /></a>
             <?php } ?>
             <?php if ( has_post_thumbnail() or  $key_1_value != '') { ?>
             <div class="blog-date isodate-blog">
             <?php }else{?>
             <div class="blog-date-noimg isodate-blog">
             <?php } ?>
                <span class="date"><?php the_time('M'); ?><br /><?php the_time('d'); ?></span>
                <div class="plove"><?php if( function_exists('heart_love') ) heart_love(); ?></div>
             </div>

                    <div class="blog-rating">
                    <?php
                    // overall stars
                    $overall_rating_1 = get_post_meta(get_the_ID(), 'overall_rating', true);
                    if($overall_rating_1!="0" && $overall_rating_1=="0.5"){ ?>
                    <div class="overall-score"><div class="rating r-05"></div></div>
                    <?php } ?>

                    <?php $overall_rating_2 = get_post_meta(get_the_ID(), 'overall_rating', true);
                    if($overall_rating_2!="0" && $overall_rating_2=="1"){ ?>
                    <div class="overall-score"><div class="rating r-1"></div></div>
                    <?php } ?>

                    <?php $overall_rating_3 = get_post_meta(get_the_ID(), 'overall_rating', true);
                    if($overall_rating_3!="0" && $overall_rating_3=="1.5"){ ?>
                    <div class="overall-score"><div class="rating r-15"></div></div>
                    <?php } ?>

                    <?php $overall_rating_4 = get_post_meta(get_the_ID(), 'overall_rating', true);
                    if($overall_rating_4!="0" && $overall_rating_4=="2"){ ?>
                    <div class="overall-score"><div class="rating r-2"></div></div>
                    <?php } ?>

                    <?php $overall_rating_5 = get_post_meta(get_the_ID(), 'overall_rating', true);
                    if($overall_rating_5!="0" && $overall_rating_5=="2.5"){ ?>
                    <div class="overall-score"><div class="rating r-25"></div></div>
                    <?php } ?>

                    <?php $overall_rating_6 = get_post_meta(get_the_ID(), 'overall_rating', true);
                    if($overall_rating_6!="0" && $overall_rating_6=="3"){ ?>
                    <div class="overall-score"><div class="rating r-3"></div></div>
                    <?php } ?>

                    <?php $overall_rating_7 = get_post_meta(get_the_ID(), 'overall_rating', true);
                    if($overall_rating_7!="0" && $overall_rating_7=="3.5"){ ?>
                    <div class="overall-score"><div class="rating r-35"></div></div>
                    <?php } ?>

                    <?php $overall_rating_8 = get_post_meta(get_the_ID(), 'overall_rating', true);
                    if($overall_rating_8!="0" && $overall_rating_8=="4"){ ?>
                    <div class="overall-score"><div class="rating r-4"></div></div>
                    <?php } ?>

                    <?php $overall_rating_9 = get_post_meta(get_the_ID(), 'overall_rating', true);
                    if($overall_rating_9!="0" && $overall_rating_9=="4.5"){ ?>
                    <div class="overall-score"><div class="rating r-45"></div></div>
                    <?php } ?>

                    <?php $overall_rating_10 = get_post_meta(get_the_ID(), 'overall_rating', true);
                    if($overall_rating_10!="0" && $overall_rating_10=="5"){ ?>
                    <div class="overall-score"><div class="rating r-5"></div></div>

                    <?php } ?>
                     </div><!-- blog-rating -->

        </div><!-- blog-image -->

          <div class="blog-content <?php if ( has_post_thumbnail() or  $key_1_value != '') {  }else{?> blog-content-no-img <?php } ?>">
                    <h2><a href="<?php the_permalink(); ?>"> <?php the_title(); ?> </a></h2>
                    <?php the_excerpt(); ?>
          </div><!-- blog-content -->



         <div class="blog-info">


                    <div class="post-pinfo">
                        <span class="icon-user"></span> <a><?php echo get_the_author(); ?></a> &nbsp;
                        <span class="icon-comment"></span> <?php if ( is_plugin_active( 'disqus-comment-system/disqus.php' )){ ?>
                        <a  href="<?php echo the_permalink(); ?>#comments" >
                        <?php comments_number( __('No comments', 'One comment', '% comments' )); ?></a> &nbsp;
                       <?php }else{ ?>
                        <a data-original-title="<?php comments_number( __('No comments in this post', 'One comment in this post', '% comments in this post')); ?>" href="<?php echo the_permalink(); ?>#comments" data-toggle="tooltip">
                        <?php comments_number( __('No comments', 'One comment', '% comments' )); ?></a> &nbsp;

                       <?php } ?>
                        <?php $posttags = get_the_tags();if ($posttags) {?>  <span class="icon-tags"></span>  <?php foreach($posttags as $tag) { ?>  <a href="<?php echo get_tag_link($tag->term_id); ?>"> <?php echo $tag->name . ', '; ?> </a><?php }}?>
                    </div><!-- post-pinfo -->

                    <a href="<?php the_permalink(); ?>" class="button-small"><?php _e("Read more", 'addict'); ?></a>

                    <div class="clear"></div>

         </div><!-- blog-info -->





        </div><!-- /.blog-post -->


        <?php endwhile; endif; ?>

</div>


  </div>
  <div class="pagination">
            <ul>
              <li>
                <?php
            $additional_loop = new WP_Query('showposts='.$showposts.'&cat='.$category_id.'&paged='.$paged );
            $page=$additional_loop->max_num_pages;
            echo kriesi_pagination($additional_loop->max_num_pages);
            ?>
            <?php wp_reset_query(); ?>
              </li>
            </ul>
         </div>
        <div class="clear"></div>
  <!-- /.row -->
 <?php if ( of_get_option('fullwidth') ) { ?> </div> <?php } ?> <!-- /container -->
</div>
<!-- /.container -->
<?php get_footer(); ?>