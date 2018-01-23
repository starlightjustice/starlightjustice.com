</div> <!-- End of container -->

    <footer class="<?php if ( of_get_option('fullwidth') ) {  }else{ ?>container<?php } ?>">
      <?php if ( of_get_option('fullwidth') ) { ?><div class="container"><?php } ?>
      <div class="span12">
           <?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Footer widgets') ) : ?>
         		<?php dynamic_sidebar('four'); ?>
           <?php endif; ?>
      </div>
    <?php if ( of_get_option('fullwidth') ) { ?></div><?php } ?>
    <div class="copyright <?php if ( of_get_option('fullwidth') ) {  }else{ ?>span12<?php } ?>">
    	<?php if ( of_get_option('fullwidth') ) { ?><div class="container"><?php } ?>
        	<p>© <?php if(of_get_option('year')!=""){echo of_get_option('year');}?>&nbsp;<?php if(of_get_option('copyright')!=""){ echo of_get_option('copyright');} ?>
        		&nbsp;
            <div class="social">
            <?php if ( of_get_option('rss') ) { ?> <a data-original-title="Rss" data-toggle="tooltip" class="rss" target="_blank" href="<?php  echo of_get_option('rss_link');  ?>"><i class="fa fa-rss"></i> </a><?php } ?>
            <?php if ( of_get_option('dribbble') ) { ?> <a data-original-title="Dribbble" data-toggle="tooltip" class="dribbble" target="_blank" href="<?php  echo of_get_option('dribbble_link');  ?>"><i class="fa fa-dribbble"></i> </a><?php } ?>
            <?php if ( of_get_option('vimeo') ) { ?> <a data-original-title="Vimeo" data-toggle="tooltip" class="vimeo" target="_blank" href="<?php echo of_get_option('vimeo_link');   ?>"><i class="fa fa-vimeo-square"></i> </a><?php } ?>
            <?php if ( of_get_option('youtube') ) { ?> <a data-original-title="Youtube" data-toggle="tooltip" class="youtube" target="_blank" href="<?php echo of_get_option('youtube_link');   ?>"><i class="fa fa-youtube"></i> </a><?php } ?>
            <?php if ( of_get_option('twitch') ) { ?> <a data-original-title="Twitch" data-toggle="tooltip" class="twitch" target="_blank" href="<?php echo of_get_option('twitch_link');   ?>"><i class="fa fa-gamepad"></i></a><?php } ?>
            <?php if ( of_get_option('linkedin') ) { ?> <a data-original-title="Linked in" data-toggle="tooltip" class="linked-in" target="_blank" href="<?php  echo of_get_option('linkedin_link');   ?>"><i class="fa fa-linkedin"></i> </a><?php } ?>
            <?php if ( of_get_option('googleplus') ) { ?> <a data-original-title="Google plus" data-toggle="tooltip" class="google-plus" target="_blank" href="<?php echo of_get_option('google_link');   ?>"><i class="fa fa-google-plus"></i></a><?php } ?>
            <?php if ( of_get_option('twitter') ) { ?> <a data-original-title="Twitter" data-toggle="tooltip" class="twitter" target="_blank" href="<?php  echo of_get_option('twitter_link');   ?>"><i class="fa fa-twitter"></i></a><?php } ?>
            <?php if ( of_get_option('facebook') ) { ?> <a data-original-title="Facebook" data-toggle="tooltip" class="facebook" target="_blank" href="<?php echo of_get_option('facebook_link');   ?>"><i class="fa fa-facebook"></i></a><?php } ?>
                </div>
    </div>

    <?php if ( of_get_option('fullwidth') ) { ?></div><?php } ?>
<?php
if(is_page_template('tmp-contact.php')){
?>
<!--Google map -->
<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false"></script>
<script>
    function initialize() {
  var mapOptions = {
    zoom: 12,
    center: new google.maps.LatLng("<?php echo of_get_option('maplat');?>", "<?php  echo of_get_option('maplon');?>"),
    mapTypeId: google.maps.MapTypeId.ROADMAP
  }
  var map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
  var contentString = 'Hello!';
  var infowindow = new google.maps.InfoWindow({
      content: contentString
  });

  var image = "<?php echo of_get_option('contact_marker_logo');?>";
  var myLatLng = new google.maps.LatLng("<?php echo of_get_option('maplat');?>", "<?php echo of_get_option('maplon');?>");
  var marker = new google.maps.Marker({
      position: myLatLng,
      map: map,
      icon: image
  });
  google.maps.event.addListener(marker, 'click', function() {
    infowindow.open(map,marker);
  });
}
google.maps.event.addDomListener(window, 'load', initialize);
</script>
<?php } ?>
<?php  echo of_get_option('googlean'); ?>
<?php if(function_exists('bbp_has_forums')){
			if ( bbp_has_forums() ){ ?>
<script>
/*global jQuery:false */
var forumtitle = jQuery('.bbpress .title_wrapper h1');
var newforumtitle = "<?php  the_title();?>";
forumtitle.html(newforumtitle);
</script>

<?php }} ?>
<?php wp_footer(); ?>

</footer>
</div>
</body></html>