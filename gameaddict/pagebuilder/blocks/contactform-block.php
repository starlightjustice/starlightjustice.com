<?php
/** A simple text block **/
class Contactform_Block extends Block {
    //set and create block
    function __construct() {
        $block_options = array(
            'name' => __('Contact form', 'addict'),
            'size' => 'span6',
        );
        //create the block
        parent::__construct('contactform_block', $block_options);
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
}

        if($title) echo '<div class="title-wrapper"><h3 class="widget-title">'.strip_tags($title).'</h3><div class="clear"></div></div>';
        ?>

           <?php
         if($boxed){
             if($marg){
             echo '<div class="wcontainer highlight-no-margin" >'; ?>
             <?php if(isset($emailSent) && $emailSent == true) { ?>
                            <div class="thanks">
                                <p><?php _e("Thanks, your email was sent successfully.", 'addict'); ?></p>
                            </div>
                        <?php } else { ?>

                            <?php if(isset($hasError) || isset($captchaError)) { ?>
                                <p class="error"><?php _e("Sorry, an error occured.", 'addict'); ?><p>
                            <?php } ?>
                        <form action="<?php the_permalink(); ?>" id="contactForm" class="contact" method="post">
                            <ul class="contactform controls">
                            <li class="input-prepend">
                                <span class="add-on"><i class="icon-user"></i></span>
                                <input type="text" name="contactName" placeholder="Name*" id="contactName" value="<?php if(isset($_POST['contactName'])) echo $_POST['contactName'];?>" class="required requiredField" />
                                <?php if($nameError != '') { ?>
                                    <span class="error"><?php $nameError;?></span>
                                <?php } ?>
                            </li>
                            <li class="input-prepend">
                                <span class="add-on"><i class="icon-envelope"></i></span>
                                <input type="text" placeholder="Email*" name="email" id="email" value="<?php if(isset($_POST['email']))  echo $_POST['email'];?>" class="required requiredField email" />
                                <?php if($emailError != '') { ?>
                                    <span class="error"><?php $emailError;?></span>
                                <?php } ?>
                            </li>
                            <li class="input-prepend">
                                <span class="add-on"><i class="icon-comment"></i></span>
                                <input type="text" placeholder="Subject" name="subject" id="subject" value="<?php if(isset($_POST['subject']))  echo $_POST['subject'];?>" class="subject" />
                            </li>
                            <li class="input-prepend">
                                <span class="add-on"><i class="icon-align-justify"></i></span>
                                <textarea name="comments" placeholder="Your message*" id="commentsText" rows="20" cols="30" class="required requiredField"><?php if(isset($_POST['comments'])) { if(function_exists('stripslashes')) { echo stripslashes($_POST['comments']); } else { echo $_POST['comments']; } } ?></textarea>
                                <?php if($commentError != '') { ?>
                                    <span class="error"><?php $commentError;?></span>
                                <?php } ?>
                            </li>
                            <li>
                                   <input type="submit" class="button-green button-small"  value="<?php echo __("Send email", 'addict'); ?>" />
                            </li>
                        </ul>
                        <input type="hidden" name="submitted" id="submitted" value="true" />
                    </form>
                <?php } ?>

             <?php echo '</div>';
             }else{
             echo '<div class="wcontainer" >'; ?>
             <?php if(isset($emailSent) && $emailSent == true) { ?>
                            <div class="thanks">
                                <p><?php _e("Thanks, your email was sent successfully.", 'addict'); ?></p>
                            </div>
                        <?php } else { ?>

                            <?php if(isset($hasError) || isset($captchaError)) { ?>
                                <p class="error"><?php _e("Sorry, an error occured.", 'addict'); ?><p>
                            <?php } ?>
                        <form action="<?php the_permalink(); ?>" id="contactForm" class="contact" method="post">
                            <ul class="contactform controls">
                            <li class="input-prepend">
                                <span class="add-on"><i class="icon-user"></i></span>
                                <input type="text" name="contactName" placeholder="Name*" id="contactName" value="<?php if(isset($_POST['contactName'])) echo $_POST['contactName'];?>" class="required requiredField" />
                                <?php if($nameError != '') { ?>
                                    <span class="error"><?php $nameError;?></span>
                                <?php } ?>
                            </li>
                            <li class="input-prepend">
                                <span class="add-on"><i class="icon-envelope"></i></span>
                                <input type="text" placeholder="Email*" name="email" id="email" value="<?php if(isset($_POST['email']))  echo $_POST['email'];?>" class="required requiredField email" />
                                <?php if($emailError != '') { ?>
                                    <span class="error"><?php $emailError;?></span>
                                <?php } ?>
                            </li>
                            <li class="input-prepend">
                                <span class="add-on"><i class="icon-comment"></i></span>
                                <input type="text" placeholder="Subject" name="subject" id="subject" value="<?php if(isset($_POST['subject']))  echo $_POST['subject'];?>" class="subject" />
                            </li>
                            <li class="input-prepend">
                                <span class="add-on"><i class="icon-align-justify"></i></span>
                                <textarea name="comments" placeholder="Your message*" id="commentsText" rows="20" cols="30" class="required requiredField"><?php if(isset($_POST['comments'])) { if(function_exists('stripslashes')) { echo stripslashes($_POST['comments']); } else { echo $_POST['comments']; } } ?></textarea>
                                <?php if($commentError != '') { ?>
                                    <span class="error"><?php $commentError;?></span>
                                <?php } ?>
                            </li>
                            <li>
                                   <input type="submit" class="button-green button-small"  value="<?php echo __("Send email", 'addict'); ?>" />
                            </li>
                        </ul>
                        <input type="hidden" name="submitted" id="submitted" value="true" />
                    </form>
                <?php } ?>
            <?php echo '</div>'; }
         }else{
             if($marg){
                echo '<div class="highlight-no-margin">'; ?>
                <?php if(isset($emailSent) && $emailSent == true) { ?>
                            <div class="thanks">
                                <p><?php _e("Thanks, your email was sent successfully.", 'addict'); ?></p>
                            </div>
                        <?php } else { ?>

                            <?php if(isset($hasError) || isset($captchaError)) { ?>
                                <p class="error"><?php _e("Sorry, an error occured.", 'addict'); ?><p>
                            <?php } ?>
                        <form action="<?php the_permalink(); ?>" id="contactForm" class="contact" method="post">
                            <ul class="contactform controls">
                            <li class="input-prepend">
                                <span class="add-on"><i class="icon-user"></i></span>
                                <input type="text" name="contactName" placeholder="Name*" id="contactName" value="<?php if(isset($_POST['contactName'])) echo $_POST['contactName'];?>" class="required requiredField" />
                                <?php if($nameError != '') { ?>
                                    <span class="error"><?php $nameError;?></span>
                                <?php } ?>
                            </li>
                            <li class="input-prepend">
                                <span class="add-on"><i class="icon-envelope"></i></span>
                                <input type="text" placeholder="Email*" name="email" id="email" value="<?php if(isset($_POST['email']))  echo $_POST['email'];?>" class="required requiredField email" />
                                <?php if($emailError != '') { ?>
                                    <span class="error"><?php $emailError;?></span>
                                <?php } ?>
                            </li>
                            <li class="input-prepend">
                                <span class="add-on"><i class="icon-comment"></i></span>
                                <input type="text" placeholder="Subject" name="subject" id="subject" value="<?php if(isset($_POST['subject']))  echo $_POST['subject'];?>" class="subject" />
                            </li>
                            <li class="input-prepend">
                                <span class="add-on"><i class="icon-align-justify"></i></span>
                                <textarea name="comments" placeholder="Your message*" id="commentsText" rows="20" cols="30" class="required requiredField"><?php if(isset($_POST['comments'])) { if(function_exists('stripslashes')) { echo stripslashes($_POST['comments']); } else { echo $_POST['comments']; } } ?></textarea>
                                <?php if($commentError != '') { ?>
                                    <span class="error"><?php $commentError;?></span>
                                <?php } ?>
                            </li>
                            <li>
                                   <input type="submit" class="button-green button-small"  value="<?php echo __("Send email", 'addict'); ?>" />
                            </li>
                        </ul>
                        <input type="hidden" name="submitted" id="submitted" value="true" />
                    </form>
                <?php } ?>

            <?php    echo '</div>';
             }else{
                echo '<div class="mcontainer">'; ?>
                <?php if(isset($emailSent) && $emailSent == true) { ?>
                            <div class="thanks">
                                <p><?php _e("Thanks, your email was sent successfully.", 'addict'); ?></p>
                            </div>
                        <?php } else { ?>

                            <?php if(isset($hasError) || isset($captchaError)) { ?>
                                <p class="error"><?php _e("Sorry, an error occured.", 'addict'); ?><p>
                            <?php } ?>
                        <form action="<?php the_permalink(); ?>" id="contactForm" class="contact" method="post">
                            <ul class="contactform controls">
                            <li class="input-prepend">
                                <span class="add-on"><i class="icon-user"></i></span>
                                <input type="text" name="contactName" placeholder="Name*" id="contactName" value="<?php if(isset($_POST['contactName'])) echo $_POST['contactName'];?>" class="required requiredField" />
                                <?php if($nameError != '') { ?>
                                    <span class="error"><?php $nameError;?></span>
                                <?php } ?>
                            </li>
                            <li class="input-prepend">
                                <span class="add-on"><i class="icon-envelope"></i></span>
                                <input type="text" placeholder="Email*" name="email" id="email" value="<?php if(isset($_POST['email']))  echo $_POST['email'];?>" class="required requiredField email" />
                                <?php if($emailError != '') { ?>
                                    <span class="error"><?php $emailError;?></span>
                                <?php } ?>
                            </li>
                            <li class="input-prepend">
                                <span class="add-on"><i class="icon-comment"></i></span>
                                <input type="text" placeholder="Subject" name="subject" id="subject" value="<?php if(isset($_POST['subject']))  echo $_POST['subject'];?>" class="subject" />
                            </li>
                            <li class="input-prepend">
                                <span class="add-on"><i class="icon-align-justify"></i></span>
                                <textarea name="comments" placeholder="Your message*" id="commentsText" rows="20" cols="30" class="required requiredField"><?php if(isset($_POST['comments'])) { if(function_exists('stripslashes')) { echo stripslashes($_POST['comments']); } else { echo $_POST['comments']; } } ?></textarea>
                                <?php if($commentError != '') { ?>
                                    <span class="error"><?php $commentError;?></span>
                                <?php } ?>
                            </li>
                            <li>
                                   <input type="submit" class="button-green button-small"  value="<?php echo __("Send email", 'addict'); ?>" />
                            </li>
                        </ul>
                        <input type="hidden" name="submitted" id="submitted" value="true" />
                    </form>
                <?php } ?>
             <?php   echo '</div>';}
         }
    }
}