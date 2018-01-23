<?php
/** A simple text block **/
class Animated_Block extends Block {
    //set and create block
    function __construct() {
        $block_options = array(
            'name' => __('Animated block', 'addict'),
            'size' => 'span3',
        );
        //create the block
        parent::__construct('animated_block', $block_options);
    }
    function form($instance) {
        $defaults = array(
            'title' => '',
            'delay' => '',
            'animated_image' => '',
            'type' => '',
            'marg' => ''

        );
         $type_options = array(
                'fade-in-from-left' => __('Fade in from left', 'addict'),
                'fade-in-from-right' => __('Fade in from right', 'addict'),
                'fade-in-from-bottom' => __('Fade in from bottom', 'addict'),
                'fade-in' => __('Fade in', 'addict'),
                'grow-in' => __('Grow in', 'addict')
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
            <label for="<?php echo $this->get_field_id('image') ?>">
                <?php _e("Upload image", 'addict') ?>
                <?php echo field_upload("animated_image",$block_id,$animated_image,'image');?>
            </label>
        </p>
        <p class="description">
            <label for="<?php echo $this->get_field_id('delay') ?>">
               <?php _e("Data delay: (1-1000)", 'addict'); ?>
               <?php echo field_input('delay', $block_id, $delay, $size = 'full') ?>
            </label>
        </p>
        <p class="description half">
                <label for="<?php echo $this->get_field_id('type') ?>">
                    <?php _e("Animation type", 'addict') ?><br/>
                    <?php echo field_select('type', $block_id, $type_options, $type) ?>
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
        if($marg){
            echo '<div class="animated-no-margin">';
            echo '<img alt="" src="'.$animated_image.'" data-animation="'.$type.'" data-delay="'.$delay.'" class="img-with-animation" >';
            echo '</div>';
        }else{
            echo '<img alt="" src="'.$animated_image.'" data-animation="'.$type.'" data-delay="'.$delay.'" class="img-with-animation" >';
        }
     }
}