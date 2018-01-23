<?php get_header(); ?>
    <div class="<?php if ( of_get_option('fullwidth') ) {  }else{ ?>container<?php } ?> normal-page">   	<?php if ( of_get_option('fullwidth') ) { ?> <div class="container"> <?php } ?>        <div class="row">
            <div class="span12">
               <div class="four0four">
                    <p class="huge"> OOPS! 404 <i class="icon-file"></i></p>
                    <?php _e("Page not found, sorry", 'addict') ?> :(
               </div>
            </div>
       </div>		<?php if ( of_get_option('fullwidth') ) { ?> </div> <?php } ?> <!-- /container -->    </div> <!-- /container -->
<?php get_footer(); ?>