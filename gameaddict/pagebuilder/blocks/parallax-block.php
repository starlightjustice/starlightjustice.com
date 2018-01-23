<?php

/** A simple text block **/

class Parallax_Block extends Block {

    /* PHP5 constructor */

    function __construct() {

        $block_options = array(

            'name' => __('Parallax block','addict'),

            'size' => __('span6','addict')

        );

        //create the widget

        parent::__construct('parallax_block', $block_options);

    }

    function form($instance) {

        echo '<p class="empty-parallax">',

        __('Drag block items into this parallax box', 'addict'),

        '</p>';

        echo '<ul class="blocks parallax-blocks cf"></ul>';



    }

    function form_callback($instance = array()) {



        $instance = is_array($instance) ? wp_parse_args($instance, $this->block_options) : $this->block_options;

        //insert the dynamic block_id & block_saving_id into the array

        $this->block_id = 'block_' . $instance['number'];

        $instance['block_saving_id'] = 'blocks[block_'. $instance['number'] .']';

        extract($instance);

        $col_order = $order;

        //parallax block header

        if(isset($template_id)) {

            echo '<li id="template-block-'.$number.'" class="block block-parallax_block '.$size.'">',

                    '<div class="block-settings-parallax cf" id="block-settings-'.$number.'">',

                        '<p class="empty-parallax">',

                            __('Drag block items into this parallax box', 'addict'),

                        '</p>',

                        '<ul class="blocks parallax-blocks cf">';

            //check if parallax has blocks inside it

            $blocks = get_blocks($template_id);

            //outputs the blocks

            if($blocks) {

                foreach($blocks as $key => $child) {

                    global $registered_blocks;

                    extract($child);

                    //get the block object

                    $block = $registered_blocks[$id_base];

                    if($parent == $col_order) {

                        $block->form_callback($child);

                    }

                }

            }

            echo        '</ul>';

        } else {

            $this->before_form($instance);

            $this->form($instance);

        }

        //form footer

         $this->inputs($instance);

        $this->after_form($instance);
    }

   //form footer

    function after_form($instance) {



        extract($instance);

        $block_saving_id = 'blocks[block_'.$number.']';



            echo '<div class="block-control-actions cf"><a href="#" class="delete">Delete</a></div>';

            echo '<input type="hidden" class="id_base" name="'.$this->get_field_name('id_base').'" value="'.$id_base.'" />';

            echo '<input type="hidden" class="name" name="'.$this->get_field_name('name').'" value="'.$name.'" />';

            echo '<input type="hidden" class="order" name="'.$this->get_field_name('order').'" value="'.$order.'" />';

            echo '<input type="hidden" class="size" name="'.$this->get_field_name('size').'" value="'.$size.'" />';

            echo '<input type="hidden" class="parent" name="'.$this->get_field_name('parent').'" value="'.$parent.'" />';

            echo '<input type="hidden" class="number" name="'.$this->get_field_name('number').'" value="'.$number.'" />';

            echo '</div></li>';







    }





    function inputs($instance){

         extract($instance);

        $block_saving_id = '9999';
        $block_id = 'block_'.$number.'';
        $type_options = array(
                'light' => __('Light', 'addict'),
                'dark' => __('Dark', 'addict')

            );

        echo '<div class="fullwidth_block_wrapper">';

        echo '<div class="fullwidth_block_wrapper_title">'.__("Choose between light and dark version", 'addict').'</div>';

        echo field_select('type', $block_id, $type_options, $type);

        echo '<div class="fullwidth_block_wrapper_title">'.__("Choose background color", 'addict').'</div>';

        echo field_color_picker('color', $block_id, $color, $default = '#ffffff');

        echo '<div class="fullwidth_block_wrapper_title">'.__("Please upload image for parallax background. (Note: Video has priority over image!)", 'addict').'</div>';

        echo field_upload('imagebck', $block_id, $imagebck, $media_type = 'image');

        echo '<div class="fullwidth_block_wrapper_title">'.__("Video WebM Upload", 'addict').'</div>';

        echo '<div class="fullwidth_block_wrapper_title">'.__("Please upload webm video format. Video will be automatically played on page load.", 'addict').'</div>';

        echo field_upload('webm', $block_id, $webm, $media_type = 'video/webm');

        echo '<div class="fullwidth_block_wrapper_title">'.__("Video MP4 Upload", 'addict').'</div>';

        echo '<div class="fullwidth_block_wrapper_content">'.__("Please upload mp4 video format because of cross browser compatibility.", 'addict').'</div>';

        echo field_upload('mp4', $block_id, $mp4, $media_type = 'video/mp4');

        echo '<div class="fullwidth_block_wrapper_title">'.__("Preview image", 'addict').'</div>';

        echo '<div class="fullwidth_block_wrapper_content">'.__("Please upload preview image for video. Image will be displayed while video is loading.", 'addict').'</div>';

        echo field_upload('preview', $block_id, $preview, $media_type = 'image');


        echo '<div class="fullwidth_block_wrapper_title">'.__("Remove inner bottom spacing", 'addict').'  '.field_checkbox('marg', $block_id, $marg, $check = 'true').'</div>';

        echo '<div class="fullwidth_block_wrapper_title">'.__("Remove inner top spacing", 'addict').'  '.field_checkbox('margtop', $block_id, $margtop, $check = 'true').'</div>';

        echo '</div>';
    }

     function block_callback($instance) {

        $instance = is_array($instance) ? wp_parse_args($instance, $this->block_options) : $this->block_options;

        extract($instance);

        $col_order = $order;

        $col_size = absint(preg_replace("/[^0-9]/", '', $size));

        //parallax block header

        if(isset($template_id)) {

             $this->before_block($instance);

             //define vars

            $overgrid = 0; $span = 0; $first = false;

            //check if parallax has blocks inside it

            $blocks = get_blocks($template_id);

            if ( !of_get_option('fullwidth') ) {
                $class = 'boxed';
            }

             if($marg && $margtop){
             echo "<div id=".md5(uniqid(rand(), true))." class='".$class." full-width-section parallax_section parallax-no-padding parallax-no-padding-top' style='background-image: url(".$imagebck."); background-color:".$color."'>";
             }elseif(!$marg && $margtop){
             echo "<div id=".md5(uniqid(rand(), true))." class='".$class." full-width-section parallax_section parallax-no-padding-top' style='background-image: url(".$imagebck."); background-color:".$color."'>";
             }elseif($marg && !$margtop){
             echo "<div id=".md5(uniqid(rand(), true))." class='".$class." full-width-section parallax_section parallax-no-padding' style='background-image: url(".$imagebck."); background-color:".$color."'>";
             }else{
             echo "<div id=".md5(uniqid(rand(), true))." class='".$class." full-width-section parallax_section' style='background-image: url(".$imagebck."); background-color:".$color."'>";
             }



 echo "

        <video  autoplay=\"true\" loop=\"true\" preload=\"auto\" poster=\"".$preview."\" class=\"slider-video\" style=\"left: -380px; width: 1920px;display: block;position: relative;\" src=\"".$webm."\">

        <source src=\"".$webm."\" type=\"video/webm\"></source>

        <source src=\"".$mp4."\" type=\"video/mp4\">

        </source></video>";


 echo "<div class=\"".$type."\">";


            //outputs the blocks

            if($blocks) {
                foreach($blocks as $key => $child) {
                    global $registered_blocks;
                    extract($child);
                    if(class_exists($id_base)) {
                        //get the block object
                       $block = $registered_blocks[$id_base];
                       //insert template_id into $child
                        $child['template_id'] = $template_id;
                        //display the block
                        if($parent == $col_order) {
                           $child_col_size = absint(preg_replace("/[^0-9]/", '', $size));
                            $overgrid = $span + $child_col_size;

                            if($overgrid > $col_size || $span == $col_size || $span == 0) {

                                $span = 0;

                                $first = true;

                            }

                            if($first == true) {

                                $child['first'] = true;

                            }

                            $block->block_callback($child);

                            $span = $span + $child_col_size;

                            $overgrid = 0; //reset $overgrid

                            $first = false; //reset $first

                        }

                    }

                }

            }

            echo "<div class=\"clear\"></div>";

            $this->after_block($instance);



            echo "</div></div>";


       } else {

            //show nothing

        }

    }

}