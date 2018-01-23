<?php
/** A simple text block **/
class Carousel_Block extends Block {
   function __construct() {
            $block_options = array(
                'name' => __('Carousel', 'addict'),
                'size' => 'span12',
            );
            //create the widget
            parent::__construct('Carousel_Block', $block_options);
            //add ajax functions
            add_action('wp_ajax_block_item_add_new', array($this, 'add_item'));
        }
        function form($instance) {
            $defaults = array(
                'textitem' => '',
                'items' => array(
                    1 => array(
                        'title' => __('New carousel item', 'addict'),
                        'image' => '',
                    )
                ),
                'type'  => 'item',
            );
            $instance = wp_parse_args($instance, $defaults);
            extract($instance);
            $item_types = array(
                'item' => 'Items',
        );
            ?>
            <div class="description cf">
             <p class="description">
            <label for="<?php echo $this->get_field_id('title') ?>">
                <?php _e("Title (optional)", 'addict'); ?>
                <?php echo field_input('title', $block_id, $title, $size = 'full') ?>
            </label>
        </p>
                <ul id="sortable-list-<?php echo $block_id ?>" class="sortable-list" rel="<?php echo $block_id ?>">
                    <?php
                    $items = is_array($items) ? $items : $defaults['items'];
                    $count = 1;
                    foreach($items as $item) {
                        $this->item($item, $count);
                        $count++;
                    }
                    ?>
                </ul>
                <p></p>
                <a href="#" rel="item" class="sortable-add-new button"><?php _e("Add New", 'addict'); ?></a>
                <p></p>
            </div>
            <?php
        }
        function item($item = array(), $count = 0) {
    ?>
            <li id="<?php echo $this->get_field_id('items') ?>-sortable-item-<?php echo $count ?>" class="sortable-item" rel="<?php echo $count ?>">
                <div class="sortable-head cf">
                    <div class="sortable-title">
                        <strong><?php echo $item['title'] ?></strong>
                    </div>
                    <div class="sortable-handle">
                        <a href="#"><?php _e("Open / Close", 'addict'); ?></a>
                    </div>
                </div>
                <div class="sortable-body">
                    <p class="item-desc description">
                        <label for="<?php echo $this->get_field_id('items') ?>-<?php echo $count ?>-title">
                            <?php _e("Item", 'addict') ?><br/>
                            <input type="text" id="<?php echo $this->get_field_id('items') ?>-<?php echo $count ?>-title" class="input-full" name="<?php echo $this->get_field_name('items') ?>[<?php echo $count ?>][title]" value="<?php echo $item['title'] ?>" />
                        </label>
                    </p>
<!--                    <p class="item-desc description">
                        <label for="<?php echo $this->get_field_id('items') ?>-<?php echo $count ?>-image">
                            <?php _e("Item image link", 'addict') ?><br/>
                            <input type="text" id="<?php echo $this->get_field_id('items') ?>-<?php echo $count ?>-image" class="input-full" name="<?php echo $this->get_field_name('items') ?>[<?php echo $count ?>][image]" value="<?php echo $item['image'] ?>" />
                        </label>
                    </p>-->
                    <p class="item-desc description">
                        <label for="<?php echo $this->get_field_id('items') ?>-<?php echo $count ?>-image">
                            <?php _e("Item image link", 'addict') ?><br/>
                            <?php echo field_upload_for_carusel("[items][".$count."][image]",$this->get_field_id('items'),$item['image'],'image');?>
                        </label>
                    </p>

                     <p class="item-desc description">
                        <label for="<?php echo $this->get_field_id('items') ?>-<?php echo $count ?>-link">
                            <?php _e("Item external link", 'addict') ?><br/>
                            <input type="text" id="<?php echo $this->get_field_id('items') ?>-<?php echo $count ?>-link" class="input-full" name="<?php echo $this->get_field_name('items') ?>[<?php echo $count ?>][link]" value="<?php echo $item['link'] ?>" />
                        </label>
                    </p>
                    <p class="item-desc description"><a href="#" class="sortable-delete"> <?php _e("Delete", 'addict') ?></a></p>
                </div>
            </li>
            <?php
        }
        function pbblock($instance) {
            extract($instance);
            if($title) echo '<h3>'.$title.'</h3>';
            echo '<div class="list_carousel responsive wcontainer " ><ul id="foo5">';
            wp_enqueue_script('jquery-ui-tabs');
            $output = '';
                    $i = 1;
                    foreach( $items as $item ){
                        if(empty($item['link'])){
                      $output .= '<li><img alt="Alt text" src="'.$item['image'].'" /></li>';
                      }else{
                       $output .= '<li><a href="'.$item['link'].'" target="_blank"><img alt="Alt text" src="'.$item['image'].'" /></a></li>';
                      }
                      $i++;
                    }
            echo $output;
            echo '</ul><div class="clear"></div>
                <a id="prev3" class="prev" href="#">&nbsp;</a>
                <a id="next3" class="next" href="#">&nbsp;</a>
            </div>';
        }
        /* AJAX add item */
        function add_item() {
            $nonce = $_POST['security'];
            if (! wp_verify_nonce($nonce, 'pb-settings-page-nonce') ) die('-1');
            $count = isset($_POST['count']) ? absint($_POST['count']) : false;
            $this->block_id = isset($_POST['block_id']) ? $_POST['block_id'] : 'block-9999';
            //default key/value for the item
            $item = array(
                'title' => __('New Item', 'addict'),
                'image' => ''
            );
            if($count) {
                $this->item($item, $count);
            } else {
                die(-1);
            }
            die();
        }
        function update($new_instance, $old_instance) {
            $new_instance = recursive_sanitize($new_instance);
            return $new_instance;
        }
}