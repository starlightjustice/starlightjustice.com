<?php
/** A simple text block **/
class Social_Block extends Block {
    //set and create block
    function __construct() {
        $block_options = array(
            'name' => __('Social', 'addict'),
            'size' => 'span4',
        );
        //create the block
        parent::__construct('social_block', $block_options);
    }
    function form($instance) {
        $defaults = array(
            'text' => '',
            'marg' => '',
            'boxed'=> ''
        );
        $instance = wp_parse_args($instance, $defaults);
        extract($instance);
        ?>
        <p class="description">
            <label for="<?php echo $this->get_field_id('title') ?>">
               <?php _e("Title (optional)", 'addict'); ?>
                <?php echo field_input('title', $block_id, $title, $size = 'full') ?>
            </label>
        </p>
        <p class="description">
            <label for="<?php echo $this->get_field_id('boxed') ?>">
             <?php _e("Boxed &nbsp;&nbsp;", 'addict'); ?>
                <?php echo field_checkbox('boxed', $block_id, $boxed, $check = 'true') ?>
            </label>
        </p>
        <p class="description">
            <label for="<?php echo $this->get_field_id('marg') ?>">
             <?php _e("Remove bottom spacing &nbsp;&nbsp;", 'addict'); ?>
                <?php echo field_checkbox('marg', $block_id, $marg, $check = 'true') ?>
            </label>
        </p>
        <?php
    }
    function pbblock($instance) {
        extract($instance);
        if($title) echo '<div class="title-wrapper"><h3 class="widget-title">'.strip_tags($title).'</h3><div class="clear"></div></div>';
         if($boxed){
             if($marg){
             echo '<div class="wcontainer highlight-no-margin" >'; ?>
                    <ul class="social-media">
             <?php if ( of_get_option('rss') ) { ?> <li><a data-original-title="Rss" data-toggle="tooltip" class="rss" target="_blank" href="<?php  echo of_get_option('rss_link');  ?>"><i class="fa fa-rss"></i> </a></li><?php } ?>
            <?php if ( of_get_option('dribbble') ) { ?> <li><a data-original-title="Dribbble" data-toggle="tooltip" class="dribbble" target="_blank" href="<?php  echo of_get_option('dribbble_link');  ?>"><i class="fa fa-dribbble"></i> </a></li><?php } ?>
            <?php if ( of_get_option('vimeo') ) { ?> <li><a data-original-title="Vimeo" data-toggle="tooltip" class="vimeo" target="_blank" href="<?php echo of_get_option('vimeo_link');   ?>"><i class="fa fa-vimeo-square"></i> </a></li><?php } ?>
            <?php if ( of_get_option('youtube') ) { ?> <li><a data-original-title="Youtube" data-toggle="tooltip" class="youtube" target="_blank" href="<?php echo of_get_option('youtube_link');   ?>"><i class="fa fa-youtube"></i> </a></li><?php } ?>
            <?php if ( of_get_option('twitch') ) { ?> <li><a data-original-title="Twitch" data-toggle="tooltip" class="twitch" target="_blank" href="<?php echo of_get_option('twitch_link');   ?>"><i class="fa fa-gamepad"></i></a></li><?php } ?>
            <?php if ( of_get_option('linkedin') ) { ?> <li><a data-original-title="Linked in" data-toggle="tooltip" class="linked-in" target="_blank" href="<?php  echo of_get_option('linkedin_link');   ?>"><i class="fa fa-linkedin"></i> </a></li><?php } ?>
            <?php if ( of_get_option('googleplus') ) { ?> <li><a data-original-title="Google plus" data-toggle="tooltip" class="google-plus" target="_blank" href="<?php echo of_get_option('google_link');   ?>"><i class="fa fa-google-plus"></i></a></li><?php } ?>
            <?php if ( of_get_option('twitter') ) { ?><li><a data-original-title="Twitter" data-toggle="tooltip" class="twitter" target="_blank" href="<?php  echo of_get_option('twitter_link');   ?>"><i class="fa fa-twitter"></i></a></li><?php } ?>
            <?php if ( of_get_option('facebook') ) { ?><li> <a data-original-title="Facebook" data-toggle="tooltip" class="facebook" target="_blank" href="<?php echo of_get_option('facebook_link');   ?>"><i class="fa fa-facebook"></i></a></li><?php } ?>
                </ul><div class="clear"></div>
             <?php echo '</div>';
             }else{
             echo '<div class="wcontainer" >'; ?>

                 <ul class="social-media">
             <?php if ( of_get_option('rss') ) { ?> <li><a data-original-title="Rss" data-toggle="tooltip" class="rss" target="_blank" href="<?php  echo of_get_option('rss_link');  ?>"><i class="fa fa-rss"></i> </a></li><?php } ?>
            <?php if ( of_get_option('dribbble') ) { ?> <li><a data-original-title="Dribbble" data-toggle="tooltip" class="dribbble" target="_blank" href="<?php  echo of_get_option('dribbble_link');  ?>"><i class="fa fa-dribbble"></i> </a></li><?php } ?>
            <?php if ( of_get_option('vimeo') ) { ?><li> <a data-original-title="Vimeo" data-toggle="tooltip" class="vimeo" target="_blank" href="<?php echo of_get_option('vimeo_link');   ?>"><i class="fa fa-vimeo-square"></i> </a></li><?php } ?>
            <?php if ( of_get_option('youtube') ) { ?> <li><a data-original-title="Youtube" data-toggle="tooltip" class="youtube" target="_blank" href="<?php echo of_get_option('youtube_link');   ?>"><i class="fa fa-youtube"></i> </a></li><?php } ?>
            <?php if ( of_get_option('twitch') ) { ?> <li><a data-original-title="Twitch" data-toggle="tooltip" class="twitch" target="_blank" href="<?php echo of_get_option('twitch_link');   ?>"><i class="fa fa-gamepad"></i></a></li><?php } ?>
            <?php if ( of_get_option('linkedin') ) { ?><li> <a data-original-title="Linked in" data-toggle="tooltip" class="linked-in" target="_blank" href="<?php  echo of_get_option('linkedin_link');   ?>"><i class="fa fa-linkedin"></i> </a></li><?php } ?>
            <?php if ( of_get_option('googleplus') ) { ?><li> <a data-original-title="Google plus" data-toggle="tooltip" class="google-plus" target="_blank" href="<?php echo of_get_option('google_link');   ?>"><i class="fa fa-google-plus"></i></a></li><?php } ?>
            <?php if ( of_get_option('twitter') ) { ?> <li><a data-original-title="Twitter" data-toggle="tooltip" class="twitter" target="_blank" href="<?php  echo of_get_option('twitter_link');   ?>"><i class="fa fa-twitter"></i></a></li><?php } ?>
            <?php if ( of_get_option('facebook') ) { ?> <li><a data-original-title="Facebook" data-toggle="tooltip" class="facebook" target="_blank" href="<?php echo of_get_option('facebook_link');   ?>"><i class="fa fa-facebook"></i></a></li><?php } ?>
                </ul><div class="clear"></div>

            <?php echo '</div>'; }
         }else{
             if($marg){
                echo '<div class="highlight-no-margin">'; ?>

                        <ul class="social-media">
             <?php if ( of_get_option('rss') ) { ?> <li><a data-original-title="Rss" data-toggle="tooltip" class="rss" target="_blank" href="<?php  echo of_get_option('rss_link');  ?>"><i class="fa fa-rss"></i> </a><?php } ?>
            <?php if ( of_get_option('dribbble') ) { ?> <li><a data-original-title="Dribbble" data-toggle="tooltip" class="dribbble" target="_blank" href="<?php  echo of_get_option('dribbble_link');  ?>"><i class="fa fa-dribbble"></i> </a></li><?php } ?>
            <?php if ( of_get_option('vimeo') ) { ?> <li><a data-original-title="Vimeo" data-toggle="tooltip" class="vimeo" target="_blank" href="<?php echo of_get_option('vimeo_link');   ?>"><i class="fa fa-vimeo-square"></i> </a></li><?php } ?>
            <?php if ( of_get_option('youtube') ) { ?> <li><a data-original-title="Youtube" data-toggle="tooltip" class="youtube" target="_blank" href="<?php echo of_get_option('youtube_link');   ?>"><i class="fa fa-youtube"></i> </a></li><?php } ?>
            <?php if ( of_get_option('twitch') ) { ?> <li><a data-original-title="Twitch" data-toggle="tooltip" class="twitch" target="_blank" href="<?php echo of_get_option('twitch_link');   ?>"><i class="fa fa-gamepad"></i></a></li><?php } ?>
            <?php if ( of_get_option('linkedin') ) { ?> <li><a data-original-title="Linked in" data-toggle="tooltip" class="linked-in" target="_blank" href="<?php  echo of_get_option('linkedin_link');   ?>"><i class="fa fa-linkedin"></i> </a></li><?php } ?>
            <?php if ( of_get_option('googleplus') ) { ?><li><a data-original-title="Google plus" data-toggle="tooltip" class="google-plus" target="_blank" href="<?php echo of_get_option('google_link');   ?>"><i class="fa fa-google-plus"></i></a></li><?php } ?>
            <?php if ( of_get_option('twitter') ) { ?><li> <a data-original-title="Twitter" data-toggle="tooltip" class="twitter" target="_blank" href="<?php  echo of_get_option('twitter_link');   ?>"><i class="fa fa-twitter"></i></a></li><?php } ?>
            <?php if ( of_get_option('facebook') ) { ?> <li><a data-original-title="Facebook" data-toggle="tooltip" class="facebook" target="_blank" href="<?php echo of_get_option('facebook_link');   ?>"><i class="fa fa-facebook"></i></a></li><?php } ?>
                </ul><div class="clear"></div>
             <?php   echo '</div>';
             }else{
                echo '<div class="mcontainer">'; ?>

                     <ul class="social-media">
             <?php if ( of_get_option('rss') ) { ?> <li><a data-original-title="Rss" data-toggle="tooltip" class="rss" target="_blank" href="<?php  echo of_get_option('rss_link');  ?>"><i class="fa fa-rss"></i> </a></li><?php } ?>
            <?php if ( of_get_option('dribbble') ) { ?> <li><a data-original-title="Dribbble" data-toggle="tooltip" class="dribbble" target="_blank" href="<?php  echo of_get_option('dribbble_link');  ?>"><i class="fa fa-dribbble"></i> </a></li><?php } ?>
            <?php if ( of_get_option('vimeo') ) { ?><li> <a data-original-title="Vimeo" data-toggle="tooltip" class="vimeo" target="_blank" href="<?php echo of_get_option('vimeo_link');   ?>"><i class="fa fa-vimeo-square"></i> </a></li><?php } ?>
            <?php if ( of_get_option('youtube') ) { ?> <li><a data-original-title="Youtube" data-toggle="tooltip" class="youtube" target="_blank" href="<?php echo of_get_option('youtube_link');   ?>"><i class="fa fa-youtube"></i> </a></li><?php } ?>
            <?php if ( of_get_option('twitch') ) { ?> <li><a data-original-title="Twitch" data-toggle="tooltip" class="twitch" target="_blank" href="<?php echo of_get_option('twitch_link');   ?>"><i class="fa fa-gamepad"></i></a></li><?php } ?>
            <?php if ( of_get_option('linkedin') ) { ?><li> <a data-original-title="Linked in" data-toggle="tooltip" class="linked-in" target="_blank" href="<?php  echo of_get_option('linkedin_link');   ?>"><i class="fa fa-linkedin"></i> </a></li><?php } ?>
            <?php if ( of_get_option('googleplus') ) { ?><li> <a data-original-title="Google plus" data-toggle="tooltip" class="google-plus" target="_blank" href="<?php echo of_get_option('google_link');   ?>"><i class="fa fa-google-plus"></i></a></li><?php } ?>
            <?php if ( of_get_option('twitter') ) { ?> <li><a data-original-title="Twitter" data-toggle="tooltip" class="twitter" target="_blank" href="<?php  echo of_get_option('twitter_link');   ?>"><i class="fa fa-twitter"></i></a></li><?php } ?>
            <?php if ( of_get_option('facebook') ) { ?> <li><a data-original-title="Facebook" data-toggle="tooltip" class="facebook" target="_blank" href="<?php echo of_get_option('facebook_link');   ?>"><i class="fa fa-facebook"></i></a></li><?php } ?>
                </ul><div class="clear"></div>

             <?php   echo'</div>'; }
         }
    }
}