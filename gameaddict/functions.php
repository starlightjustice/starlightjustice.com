<?php
//translatable theme
load_theme_textdomain( 'addict', get_template_directory() . '/langs');
?>
<?php

/*include important files*/
require_once ('themeOptions/functions.php');
require_once ('import/addict-importer.php');
require_once ('post_templates.php');
require_once ('bootstrap-carousel.php');
require_once('widgets/rating/popular-widget.php');
require_once ('widgets/portfoliowidget/portfoliowdg.php');
require_once('widgets/latest_twitter/latest_twitter_widget.php');
require_once('themeOptions/rating.php');
require_once ('addons/smartmetabox/SmartMetaBox.php');
require_once('addons/chat/noerror.php');
require_once('addons/chat/wp-wall.php');
require_once ('addons/pricetable/pricetable.php');
require_once ( 'addons/heart/love/heart-love.php' );
require_once ( 'addons/clan-wars/wp-clanwars.php' );
require_once ( 'addons/multiple-post-thumbnails/multi-post-thumbnails.php' );
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );


/*add page builder*/
add_action('after_setup_theme', 'addict_page_builder');
function addict_page_builder() {
      include_once(get_template_directory().'/pagebuilder/page-builder.php');
}

/*register sidebars*/
add_action( 'after_setup_theme', 'addict_register_sidebars' );
function addict_register_sidebars() {
if ( function_exists('register_sidebar') )
register_sidebar(array(
'name' => __( 'Home sidebar', 'addict' ),
'id' => 'one',
'description' => __( 'Widgets in this area will be shown in the home page.' , 'addict'),
'before_widget' => '<div class="widget">',
'after_widget' => '</div>',
'before_title' => '<div class="title-wrapper"><h3 class="widget-title">',
'after_title' => '</h3><div class="clear"></div></div>', ));
if ( function_exists('register_sidebar') )
register_sidebar(array(
'name' => __( 'General sidebar', 'addict' ),
'id' => 'two',
'description' => __( 'General sidebar for use with page builder.' , 'addict'),
'before_widget' => '<div class="widget">',
'after_widget' => '</div>',
'before_title' => '<div class="title-wrapper"><h3 class="widget-title">',
'after_title' => '</h3><div class="clear"></div></div>', ));
if ( function_exists('register_sidebar') )
register_sidebar(array(
'name' => __( 'Blog sidebar', 'addict' ),
'id' => 'three',
'description' => __( 'Widgets in this area will be shown in the blog sidebar.' , 'addict'),
'before_widget' => '<div class="widget">',
'after_widget' => '</div>',
'before_title' => '<div class="title-wrapper"><h3 class="widget-title">',
'after_title' => '</h3><div class="clear"></div></div>', ));
if ( function_exists('register_sidebar') )
register_sidebar(array(
'name' => __( 'Footer area widgets', 'addict' ),
'id' => 'four',
'description' => __( 'Widgets in this area will be shown in the footer.' , 'addict'),
'before_widget' => '<div class="footer_widget span3">',
'after_widget' => '</div>',
'before_title' => '<div class="title-wrapper"><h3 class="widget-title">',
'after_title' => '</h3><div class="clear"></div></div>', ));
}
if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
if ( function_exists('register_sidebar') )
register_sidebar(array(
'name' => 'WooCommerce Sidebar',
'id' => 'woo',
'description' => __( 'Widgets in this area will be shown in the WooCommerce sidebar.' , 'addict'),
'before_widget' =>  '<div id="%1$s" class="widget widgetSidebar %2$s">',
'after_widget'  => '</div>',
'before_title' => '<div class="title-wrapper"><h3 class="widget-title">',
'after_title' => '</h3><div class="clear"></div></div>', ));
}
if ( function_exists('register_sidebar') )
register_sidebar(array(
'name' => 'ClanWars Sidebar',
'id' => 'clanwars',
'description' => __( 'Widgets in this area will be shown in the Clan Wars sidebar.' , 'addict'),
'before_widget' =>  '<div id="%1$s" class="widget widgetSidebar %2$s">',
'after_widget'  => '</div>',
'before_title' => '<div class="title-wrapper"><h3 class="widget-title">',
'after_title' => '</h3><div class="clear"></div></div>', ));
if ( is_plugin_active( 'buddypress/bp-loader.php' ) ) {
if ( function_exists('register_sidebar') )
register_sidebar(array(
'name' => 'BuddyPress Sidebar',
'id' => 'buddypress',
'description' => __( 'Widgets in this area will be shown in the BuddyPress sidebar.' , 'addict'),
'before_widget' =>  '<div id="%1$s" class="widget widgetSidebar %2$s">',
'after_widget'  => '</div>',
'before_title' => '<div class="title-wrapper"><h3 class="widget-title">',
'after_title' => '</h3><div class="clear"></div></div>', ));
}

/*add custom menu support*/
add_theme_support( 'menus' );
add_action( 'admin_menu', 'addict_create_menu' );
function addict_create_menu(){
$themeicon1 = get_template_directory_uri()."/img/favicon.png";
add_menu_page("Theme Options", "Theme Options", 'edit_theme_options', 'options-framework', 'optionsframework_page',$themeicon1,1800 );
}


add_action( 'after_setup_theme', 'addict_theme_tweak' );

function addict_theme_tweak(){


// When this theme is activated send the user to the theme options
if (is_admin() && isset($_GET['activated'] ) ) {
// Call action that sets
add_action('admin_head','gp_setup');
// Do redirect
header( 'Location: '.admin_url().'themes.php?page=options-framework' ) ;
}


/*register theme location menu*/
add_action( 'init', 'register_my_menus' );
function register_my_menus() {
  register_nav_menus(
    array(
      'header-menu' => __( 'Header Menu' , 'addict'),
      )
  );
}
}


/* Breadcrumbs */

function addict_breadcrumbs_inner(){

           function addict_pg(){
            $pages = get_pages(array(
            'meta_key' => '_wp_page_template',
            'meta_value' => 'tmp-blog-right.php'
        ));
        foreach($pages as $page){
           return $page->post_name;
        }}
        function addict_pg1(){
            $pages = get_pages(array(
            'meta_key' => '_wp_page_template',
            'meta_value' => 'tmp-blog-isotope.php'
        ));
         foreach($pages as $page){
           return $page->post_name;
        }}

        function addict_pg2(){
            $pages = get_pages(array(
            'meta_key' => '_wp_page_template',
            'meta_value' => 'tmp-blog-full.php'
        ));
         foreach($pages as $page){
           return $page->post_name;
        }}

        function addict_pg3(){
            $pages = get_pages(array(
            'meta_key' => '_wp_page_template',
            'meta_value' => 'tmp-blog-left.php'
        ));
         foreach($pages as $page){
           return $page->post_name;
        }}
        function addict_get_page_id($name){
        global $wpdb;
        /* get page id using custom query */
        $page_id = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE ( post_name = '".$name."' or post_title = '".$name."' ) and post_status = 'publish' and post_type='page' ");
        return $page_id;
        }
        function addict_get_page_permalink($name){
        $page_id = addict_get_page_id($name);
        return get_permalink($page_id);
        }

        if (!is_home()) {
        echo '<a href="';
        echo home_url();
        echo '">';
        echo __('Home', 'addict');
        echo "</a> / ";
        if (is_single()) {
        if(get_post_type( get_the_ID() ) == 'portfolio'){
            if(!of_get_option('portfolio_breadcrumbs')){$breadcrumb = "Portfolio";
            }else{
              $breadcrumb = of_get_option('portfolio_breadcrumbs');
            };
        echo __($breadcrumb, 'addict');
            if (is_single()) {
                echo " / ";
                the_title();
            }
        }else{
        echo '<a href="';
        if(addict_pg() != null){
            echo addict_get_page_permalink(''. addict_pg());
        }elseif(addict_pg1() != null){
             echo addict_get_page_permalink(''. addict_pg1());
        }elseif(addict_pg2() != null){
             echo addict_get_page_permalink(''. addict_pg2());
        }elseif(addict_pg3() != null){
             echo addict_get_page_permalink(''. addict_pg3());
        }
        echo '">';
        echo __('Blog', 'addict');
        echo "</a> ";
            if (is_single()) {
                echo " / ";
                the_title();
            }
        }
        }elseif(is_category()){
        echo  __('Category: ', 'addict');
        echo single_cat_title();
        }elseif(is_404()){
        echo '404';
        }elseif(is_search()){
        echo __('Search: ', 'addict');
        echo get_search_query();
         }elseif(is_author()){
        $curauth = (get_query_var('author_name')) ? get_user_by('slug', get_query_var('author_name')) : get_userdata(get_query_var('author')); echo $curauth->user_nicename;
        } elseif (is_page()) {
            echo the_title();
        }elseif(is_tag()){
         echo   __('Tag: ', 'addict');
             echo GetTagName(get_query_var('tag_id'));
        }elseif(is_archive()){
          echo   __('Archive', 'addict');
        }
    }
}

function addict_breadcrumbs(){

if(function_exists('is_bbpress')){
    if(is_bbpress()){
        bbp_breadcrumb();
    }else{
        addict_breadcrumbs_inner();}
}else{
        addict_breadcrumbs_inner();
  }
}


/*custom excerpt lenght*/
add_filter( 'excerpt_length', 'custom_excerpt_length', 999 );
function custom_excerpt_length( $length ){
    return 55;
}
add_filter( 'excerpt_length', 'custom_excerpt_length_pro', 999 );
function custom_excerpt_length_pro( $length ) {
    return 40;
}


/*pagination*/
function kriesi_pagination($pages = '', $range = 1){
$showitems = ($range * 1)+1;
$general_show_page  = of_get_option('general_post_show');
global $paged;
global $paginate;
if(empty($paged)) $paged = 1;
if($pages == '')
{
global $wp_query;
$pages = $wp_query->max_num_pages;
if(!$pages)
{
$pages = 1;
}
}
if(1 != $pages){
$url= get_template_directory_uri();
$leftpager= '&laquo;';
$rightpager= '&raquo;';
if($paged > 2 && $paged > $range+1 && $showitems < $pages) $paginate.=  "";
if($paged > 1 ) $paginate.=  "<a class='page-selector' href='".get_pagenum_link($paged - 1)."'>". $leftpager. "</a>";
for ($i=1; $i <= $pages; $i++){
if (1 != $pages &&( !($i >= $paged+$range+1 || $i <= $paged-$range-1) || $pages <= $showitems )){
$paginate.=  ($paged == $i)? "<li class='active'><a href='".get_pagenum_link($i)."'>".$i."</a></li>":"<li><a href='".get_pagenum_link($i)."' class='inactive' >".$i."</a></li>";
}
}
if ($paged < $pages ) $paginate.=  "<li><a class='page-selector' href='".get_pagenum_link($paged + 1)."' >". $rightpager. "</a></li>";
}
return $paginate;
}


/*add featured image support*/
add_theme_support( 'post-thumbnails' );
if ( function_exists( 'add_image_size' ) ) {
   set_post_thumbnail_size( 287, 222, true );
   set_post_thumbnail_size( 305, 305, true );
}


/*
 * Include the TGM_Plugin_Activation class.
 */
require_once dirname( __FILE__ ) . '/pluginactivation.php';
add_action( 'tgmpa_register', 'addict_my_theme_register_required_plugins' );
/*
 * Register the required plugins for this theme.
 *
 * In this example, we register two plugins - one included with the TGMPA library
 * and one from the .org repo.
 *
 * The variable passed to tgmpa_register_plugins() should be an array of plugin
 * arrays.
 *
 * This function is hooked into tgmpa_init, which is fired within the
 * TGM_Plugin_Activation class constructor.
 */
function addict_my_theme_register_required_plugins() {
    /**
     * Array of plugin arrays. Required keys are name and slug.
     * If the source is NOT from the .org repo, then source is also required.
     */
    $plugins = array(
        // This is an example of how to include a plugin pre-packaged with a theme
        array(
            'name'                  => 'Paralax slider', // The plugin name
            'slug'                  => 'layerslider', // The plugin slug (typically the folder name)
            'source'                => 'http://skywarriorthemes.com/plugins/layerslider.zip', // The plugin source
            'required'              => false, // If false, the plugin is only 'recommended' instead of required
            'version'               => '', // E.g. 1.0.0. If set, the active plugin must be this version or higher, otherwise a notice is presented
            'force_activation'      => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
            'force_deactivation'    => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
            'external_url'          => '', // If set, overrides default API URL and points to an external URL
        ),array(
            'name'                  => 'Isotope gallery', // The plugin name
            'slug'                  => 'sk_isotope_gallery', // The plugin slug (typically the folder name)
            'source'                => 'http://skywarriorthemes.com/plugins/sk_isotope_gallery.zip', // The plugin source
            'required'              => false, // If false, the plugin is only 'recommended' instead of required
            'version'               => '', // E.g. 1.0.0. If set, the active plugin must be this version or higher, otherwise a notice is presented
            'force_activation'      => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
            'force_deactivation'    => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
            'external_url'          => '', // If set, overrides default API URL and points to an external URL
        ),array(
            'name'                  => 'BBpress', // The plugin name
            'slug'                  => 'bbpress', // The plugin slug (typically the folder name)
            'source'                => 'http://skywarriorthemes.com/plugins/bbpress.zip', // The plugin source
            'required'              => false, // If false, the plugin is only 'recommended' instead of required
            'version'               => '', // E.g. 1.0.0. If set, the active plugin must be this version or higher, otherwise a notice is presented
            'force_activation'      => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
            'force_deactivation'    => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
            'external_url'          => '', // If set, overrides default API URL and points to an external URL
        ),array(
            'name'                  => 'WooCommerce', // The plugin name
            'slug'                  => 'woocommerce', // The plugin slug (typically the folder name)
            'source'                => 'http://skywarriorthemes.com/plugins/woocommerce.zip', // The plugin source
            'required'              => false, // If false, the plugin is only 'recommended' instead of required
            'version'               => '', // E.g. 1.0.0. If set, the active plugin must be this version or higher, otherwise a notice is presented
            'force_activation'      => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
            'force_deactivation'    => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
            'external_url'          => '', // If set, overrides default API URL and points to an external URL
        ),

    );
    // Change this to your theme text domain, used for internationalising strings
    $theme_text_domain = 'addict';
    /**
     * Array of configuration settings. Amend each line as needed.
     * If you want the default strings to be available under your own theme domain,
     * leave the strings uncommented.
     * Some of the strings are added into a sprintf, so see the comments at the
     * end of each line for what each argument will be.
     */
    $config = array(
        'domain'            => $theme_text_domain,          // Text domain - likely want to be the same as your theme.
        'default_path'      => '',                          // Default absolute path to pre-packaged plugins
        'parent_menu_slug'  => 'themes.php',                // Default parent menu slug
        'parent_url_slug'   => 'themes.php',                // Default parent URL slug
        'menu'              => 'install-required-plugins',  // Menu slug
        'has_notices'       => true,                        // Show admin notices or not
        'is_automatic'      => true,                       // Automatically activate plugins after installation or not
        'message'           => '',                          // Message to output right before the plugins table
        'strings'           => array(
            'page_title'                                => __( 'Install Required Plugins', 'addict' ),
            'menu_title'                                => __( 'Install Plugins', 'addict' ),
            'installing'                                => __( 'Installing Plugin: %s', 'addict' ), // %1$s = plugin name
            'oops'                                      => __( 'Something went wrong with the plugin API.', 'addict' ),
            'notice_can_install_required'               => _n_noop( 'This theme requires the following plugin: %1$s.', 'This theme requires the following plugins: %1$s.' ), // %1$s = plugin name(s)
            'notice_can_install_recommended'            => _n_noop( 'This theme recommends the following plugin: %1$s.', 'This theme recommends the following plugins: %1$s.' ), // %1$s = plugin name(s)
            'notice_cannot_install'                     => _n_noop( 'Sorry, but you do not have the correct permissions to install the %s plugin. Contact the administrator of this site for help on getting the plugin installed.', 'Sorry, but you do not have the correct permissions to install the %s plugins. Contact the administrator of this site for help on getting the plugins installed.' ), // %1$s = plugin name(s)
            'notice_can_activate_required'              => _n_noop( 'The following required plugin is currently inactive: %1$s.', 'The following required plugins are currently inactive: %1$s.' ), // %1$s = plugin name(s)
            'notice_can_activate_recommended'           => _n_noop( 'The following recommended plugin is currently inactive: %1$s.', 'The following recommended plugins are currently inactive: %1$s.' ), // %1$s = plugin name(s)
            'notice_cannot_activate'                    => _n_noop( 'Sorry, but you do not have the correct permissions to activate the %s plugin. Contact the administrator of this site for help on getting the plugin activated.', 'Sorry, but you do not have the correct permissions to activate the %s plugins. Contact the administrator of this site for help on getting the plugins activated.' ), // %1$s = plugin name(s)
            'notice_ask_to_update'                      => _n_noop( 'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.', 'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.' ), // %1$s = plugin name(s)
            'notice_cannot_update'                      => _n_noop( 'Sorry, but you do not have the correct permissions to update the %s plugin. Contact the administrator of this site for help on getting the plugin updated.', 'Sorry, but you do not have the correct permissions to update the %s plugins. Contact the administrator of this site for help on getting the plugins updated.' ), // %1$s = plugin name(s)
            'install_link'                              => _n_noop( 'Begin installing plugin', 'Begin installing plugins' ),
            'activate_link'                             => _n_noop( 'Activate installed plugin', 'Activate installed plugins' ),
            'return'                                    => __( 'Return to Required Plugins Installer', 'addict' ),
            'plugin_activated'                          => __( 'Plugin activated successfully.', 'addict' ),
            'complete'                                  => __( 'All plugins installed and activated successfully. %s', 'addict' ), // %1$s = dashboard link
            'nag_type'                                  => 'updated' // Determines admin notice type - can only be 'updated' or 'error'
        )
    );
    tgmpa( $plugins, $config );
}


/*theme styles*/
add_action( 'wp_enqueue_scripts', 'addict_mytheme_style' );
function addict_mytheme_style() {
  wp_enqueue_style( 'addict_mytheme_style-style',  get_bloginfo( 'stylesheet_url' ), array(), '20130401' );
}
add_action('wp_enqueue_scripts', 'addict_external_styles');
function addict_external_styles(){
  wp_register_style( 'custom-style',  'http://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800,400italic,600italic,700italic',  array(), '20130401');
  wp_enqueue_style( 'custom-style' );
  wp_register_style( 'custom-style1',  get_template_directory_uri().'/css/jquery.fancybox.css',  array(), '20130401');
  wp_enqueue_style( 'custom-style1' );
  wp_register_style( 'custom-style2',  get_template_directory_uri().'/addons/chat/wp-wall.css',  array(), '20130401');
  wp_enqueue_style( 'custom-style2' );
  wp_register_style( 'pricetable1',  get_template_directory_uri().'/addons/pricetable/css/pricetable.css',  array(), '20130401');
  wp_enqueue_style( 'pricetable1' );

}



/*theme scripts*/
add_action('wp_enqueue_scripts', 'addict_my_scripts');
function addict_my_scripts(){
wp_register_script( 'bootstrap1', get_template_directory_uri().'/js/bootstrap-transition.js','','',true);
wp_enqueue_script('bootstrap1');
wp_register_script( 'bootstrap2', get_template_directory_uri().'/js/bootstrap-tooltip.js','','',true);
wp_enqueue_script('bootstrap2');
wp_register_script( 'bootstrap3', get_template_directory_uri().'/js/bootstrap-button.js','','',true);
wp_enqueue_script('bootstrap3');
wp_register_script( 'bootstrap4', get_template_directory_uri().'/js/bootstrap-carousel.js','','',true);
wp_enqueue_script('bootstrap4');
wp_register_script( 'bootstrap5', get_template_directory_uri().'/js/bootstrap-collapse.js','','',true);
wp_enqueue_script('bootstrap5');
wp_register_script( 'bootstrap6', get_template_directory_uri().'/js/bootstrap-modal.js','','',true);
wp_enqueue_script('bootstrap6');
wp_register_script( 'bootstrap7', get_template_directory_uri().'/js/bootstrap-popover.js','','',true);
wp_enqueue_script('bootstrap7');


if(of_get_option('scrollbar') == 1){
wp_register_script( 'custom_js8',   get_template_directory_uri().'/js/theme.min.js','','',true);
wp_enqueue_script('custom_js8');
}

wp_register_script( 'isotope',   get_template_directory_uri().'/js/isotope.js','','',true);
wp_enqueue_script('isotope');
wp_register_script( 'easing',  get_template_directory_uri().'/js/easing.js','','',true);
wp_enqueue_script('easing');
wp_register_script( 'main',  get_template_directory_uri().'/js/main.js','','',true);
wp_enqueue_script('main');
wp_register_script( 'fancybox',  get_template_directory_uri().'/js/jquery.fancybox.js','','',true);
wp_enqueue_script('fancybox');
wp_register_script( 'totop',  get_template_directory_uri().'/js/jquery.ui.totop.js','','',true);
wp_enqueue_script('totop');
wp_register_script( 'custom_js1',  get_template_directory_uri().'/js/jquery.validate.min.js','','',true);
wp_enqueue_script('custom_js1');
wp_register_script( 'custom_js2',  get_template_directory_uri().'/js/jquery-ui-1.10.3.custom.min.js','','',true);
wp_enqueue_script('custom_js2');
wp_register_script( 'custom_js3',  get_template_directory_uri().'/js/jquery.carouFredSel-6.2.1-packed.js','','',true);
wp_enqueue_script('custom_js3');
wp_register_script( 'custom_js6',   get_template_directory_uri().'/js/appear.js','','',true);
wp_enqueue_script('custom_js6');
wp_register_script( 'custom_js4',   get_template_directory_uri().'/js/parallax.js','','',true);
wp_enqueue_script('custom_js4');
wp_register_script( 'custom_js5',   get_template_directory_uri().'/js/global.js','','',true);
wp_enqueue_script('custom_js5');
wp_register_script( 'imagescale',   get_template_directory_uri().'/js/imagescale.js','','',true);
wp_enqueue_script('imagescale');
wp_register_script( 'transit',   get_template_directory_uri().'/js/transit.js','','',true);
wp_enqueue_script('transit');
wp_register_script( 'ajaxcomments',   get_template_directory_uri().'/js/ajaxcomments.js','','',true);
wp_enqueue_script('ajaxcomments');
wp_localize_script('ajaxcomments', 'addict_script_vars', array(
            'success' => __('Thanks for your comment. We appreciate your response.', 'addict'),
            'error' => __('Please wait a while before posting your next comment!', 'addict'),
            'info' => __('Processing...', 'addict'),
            'error2' => __('You might have left one of the fields blank, or be posting too quickly!', 'addict')

        )
    );
}

/*admin sctipts*/
add_action('admin_enqueue_scripts', 'addict_scripts_admin');
function addict_scripts_admin(){
wp_register_script( 'custom_js55',  get_template_directory_uri().'/js/simple-slider.js');
wp_enqueue_script('custom_js55');
wp_register_script( 'custom_js66',  get_template_directory_uri().'/js/jquery.elastic.source.js');
wp_enqueue_script('custom_js66');
wp_register_script( 'custom_js77',  get_template_directory_uri().'/ckeditor/ckeditor.js');
wp_enqueue_script('custom_js77');
wp_register_script( 'custom99', get_template_directory_uri().'/js/bootstrap-tooltip.js','','',true);
wp_enqueue_script('custom99');
}

/*admin styles*/
add_action('admin_enqueue_scripts', 'addict_styles_admin');
function addict_styles_admin(){
  wp_register_style( 'custom-style11',  get_template_directory_uri().'/css/simple-slider.css',  array(), '20130401');
  wp_enqueue_style( 'custom-style11' );
  wp_register_style( 'custom-style22',  get_template_directory_uri().'/css/simple-slider-volume.css',  array(), '20130401');
  wp_enqueue_style( 'custom-style22' );
  wp_register_style( 'custom-style44',  get_template_directory_uri().'/css/font-awesome.css',  array(), '20130401');
  wp_enqueue_style( 'custom-style44' );
   wp_register_style( 'custom-style55',  get_template_directory_uri().'/css/admin.css',  array(), '20130401');
  wp_enqueue_style( 'custom-style55' );
}

/*add last item in footer sidebar class*/
add_filter('dynamic_sidebar_params','addict_widget_first_last_classes');
function addict_widget_first_last_classes($params) {
    global $my_widget_num; // Global a counter array
    $this_id = $params[0]['id']; // Get the id for the current sidebar we're processing
    $arr_registered_widgets = wp_get_sidebars_widgets(); // Get an array of ALL registered widgets
    if(!$my_widget_num) {// If the counter array doesn't exist, create it
        $my_widget_num = array();
    }
    if(!isset($arr_registered_widgets[$this_id]) || !is_array($arr_registered_widgets[$this_id])) { // Check if the current sidebar has no widgets
        return $params; // No widgets in this sidebar... bail early.
    }
    if(isset($my_widget_num[$this_id])) { // See if the counter array has an entry for this sidebar
        $my_widget_num[$this_id] ++;
    } else { // If not, create it starting with 1
        $my_widget_num[$this_id] = 1;
    }
    $class = 'class="widget-' . $my_widget_num[$this_id] . ' '; // Add a widget number class for additional styling options
    if($my_widget_num[$this_id] == 1) { // If this is the first widget
        $class .= 'first ';
    } elseif($my_widget_num[$this_id] == count($arr_registered_widgets[$this_id])) { // If this is the last widget
        $class .= 'last ';
    }
    $params[0]['before_widget'] = str_replace('class="', $class, $params[0]['before_widget']); // Insert our new classes into "before widget"
    return $params;
}


/*custom comments*/
function custom_comments($comment, $args, $depth) {
  $GLOBALS['comment'] = $comment;
    $GLOBALS['comment_depth'] = $depth;
  ?>
    <li class="comment">
        <div class="wcontainer"><?php commenter_avatar() ?>
  <?php if ($comment->comment_approved == '0') _e("\t\t\t\t\t<span class='unapproved'>Your comment is awaiting moderation.</span>\n", 'addict') ?>
          <div class="comment-body">
             <div class="comment-author"><?php commenter_link() ?></div>
             <i><small><?php comment_time('M j, Y @ G:i a'); ?></small> </i><br />
             <?php comment_text() ?>
              <?php comment_reply_link(array_merge( $args, array('depth' => $depth, 'max_depth' => $args['max_depth']))) ?>
        </div>
        <div class="clear"></div>
        </div>
    </li>
<?php }


/*custom pings*/
function custom_pings($comment, $args, $depth) {
       $GLOBALS['comment'] = $comment;
        ?>
         <div class="project-comment row">
                <div class="comment-author"><?php printf(__('By %1$s on %2$s at %3$s', 'addict'),
                        get_comment_author_link(),
                        get_comment_date(),
                        get_comment_time() );
                        edit_comment_link(__('Edit', 'addict'), ' <span class="meta-sep">|</span> <span class="edit-link">', '</span>'); ?></div>
    <?php if ($comment->comment_approved == '0') _e('\t\t\t\t\t<span class="unapproved">Your trackback is awaiting moderation.</span>\n', 'addict') ?>
            <div class="comment-content span6">
                <?php comment_text() ?>
            </div>
            </div>
<?php
}

/*Produces an avatar image with the hCard-compliant photo class*/
function commenter_link() {
   $commenter = get_comment_author_link();
    if ( preg_match( '/<a[^>]* class=[^>]+>/', $commenter ) ) {
        $commenter = preg_replace( '/(<a[^>]* class=[\'"]?)/', '\\1url ' , $commenter );
    } else {
        $commenter = preg_replace( '/(<a )/', '\\1class="url "/' , $commenter );
    }
    echo ' <span class="comment-info">' . $commenter . '</span>';
}

/*Commenter avatar*/
function commenter_avatar() {
    $avatar_email = get_comment_author_email();
    $avatar = str_replace( "class='avatar", "class='photo avatar", get_avatar( $avatar_email, 80 ) );
    echo $avatar;
}

/*register portfolio post types*/
add_action('init', 'addict_portfolio_register');
function addict_portfolio_register() {
    $labels = array(
        'name' => _x('My Portfolio', 'post type general name','addict'),
        'singular_name' => _x('Portfolio Item', 'post type singular name','addict'),
        'add_new' => _x('Add New', 'portfolio item' ,'addict'),
        'add_new_item' => __('Add New Portfolio Item','addict'),
        'edit_item' => __('Edit Portfolio Item','addict'),
        'new_item' => __('New Portfolio Item','addict'),
        'view_item' => __('View Portfolio Item','addict'),
        'search_items' => __('Search Portfolio','addict'),
        'not_found' =>  __('Nothing found','addict'),
        'not_found_in_trash' => __('Nothing found in Trash','addict'),
        'parent_item_colon' => ''
    );
    $args = array(
        'labels' => $labels,
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'query_var' => true,
        'menu_icon' => get_template_directory_uri() . '/img/portfolio.png',
        'rewrite' => true,
        'capability_type' => 'post',
        'hierarchical' => false,
        'menu_position' => null,
        'supports' => array('title','editor','thumbnail'),
        'taxonomies' => array('post_tag')
      );
    register_post_type( 'portfolio' , $args );
}

/*register portfolio categories*/
add_action( 'init', 'addict_custom_taxonomies', 0 );
function addict_custom_taxonomies() {
    // Add new "Locations" taxonomy to Posts
    register_taxonomy('portfolio-category', 'portfolio', array(
        // Hierarchical taxonomy (like categories)
        'hierarchical' => true,
        // This array of options controls the labels displayed in the WordPress Admin UI
        'labels' => array(
            'name' => _x( 'Categories', 'taxonomy general name','addict' ),
            'singular_name' => _x( 'Category', 'taxonomy singular name','addict' ),
            'search_items' =>  __( 'Search Category','addict' ),
            'all_items' => __( 'All Categories','addict' ),
            'parent_item' => __( 'Parent Category','addict' ),
            'parent_item_colon' => __( 'Parent Category:','addict' ),
            'edit_item' => __( 'Edit Category','addict' ),
            'update_item' => __( 'Update Category','addict' ),
            'add_new_item' => __( 'Add New Category' ,'addict'),
            'new_item_name' => __( 'New Category Name','addict' ),
            'menu_name' => __( 'Categories','addict' ),
        ),
        // Control the slugs used for this taxonomy
        'rewrite' => array(
            'slug' => 'categories', // This controls the base slug that will display before each term
            'with_front' => false, // Don't display the category base before "/locations/"
            'hierarchical' => true // This will allow URL's like "/locations/boston/cambridge/"
        ),
    ));
}

/*register match post types*/
add_action('init', 'addict_matches_register');
function addict_matches_register() {
    $labels = array(
        'name' => _x('Matches', 'post type general name','addict'),
        'singular_name' => _x('Match', 'post type singular name','addict'),
        'add_new' => _x('Add New', 'match item' ,'addict'),
        'add_new_item' => __('Add New Match','addict'),
        'edit_item' => __('Edit Match','addict'),
        'new_item' => __('New Match','addict'),
        'view_item' => __('View Match','addict'),
        'search_items' => __('Search Matches','addict'),
        'not_found' =>  __('Nothing found','addict'),
        'not_found_in_trash' => __('Nothing found in Trash','addict'),
        'parent_item_colon' => ''
    );
    $args = array(
        'labels' => $labels,
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'query_var' => true,
        'menu_icon' => get_template_directory_uri() . '/img/portfolio.png',
        'rewrite' => true,
        'capability_type' => 'post',
          'capabilities' => array(
            'create_posts' => false, // Removes support for the "Add New" function
          ),
        'map_meta_cap' => true,
        'hierarchical' => false,
        'menu_position' => null,
        'supports' => array('title','editor','thumbnail')

      );
    register_post_type( 'matches' , $args );
}
function addict_remove_matches_menu() {
    remove_menu_page( 'edit.php?post_type=matches' );
}
add_action( 'admin_menu', 'addict_remove_matches_menu' );


/*custom admin columns for custom post type portfolio*/
add_filter('manage_edit-portfolio_columns', 'addict_add_new_portfolio_columns');
function addict_add_new_portfolio_columns($portfolio_columns) {
     $columns = array(
        "cb" => "<input type=\"checkbox\" />",
        "title" => _("Title",'addict' ),
        "author" => _("Author",'addict' ),
        "slug" => _("URL Slug",'addict' ),
        "custtype-type" => _("Categories",'addict' ),
        "date" => _("Date",'addict' ),
    );
    return $columns;
}

add_action("manage_posts_custom_column", "addict_custtype_custom_columns",10,2);
function addict_custtype_custom_columns($column,$id) {
    global $wpdb;
    global $post;
        switch ($column) {
        case 'custtype-type':
           $terms = get_the_terms( $post->ID , 'portfolio-category' );
                if($terms){
                foreach ( $terms as $term ) {
                echo $term->name;echo ', ';
                }}
            break;
        case 'slug':
            $text = basename(get_post_permalink($id));
            echo $text;
            break;
        default:
            break;
        } // end switch
}

/*custom admin columns for custom post type taxonomies*/
add_filter('manage_edit-portfolio-category_columns', 'addict_portfolio_category_columns', 2);
add_action('manage_portfolio-category_custom_column', 'addict_portfolio_category_custom_columns', 2, 3);
function addict_portfolio_category_columns($defaults) {
    $defaults['portfolio-category_ids'] = __('Category ID' ,'addict');
    return $defaults;
}
function addict_portfolio_category_custom_columns($value, $column_name, $id) {
    if( $column_name == 'portfolio-category_ids' ) {
        return (int)$id;
    }
}


/*add smartmetaboxes*/
add_action( 'add_meta_boxes', 'addict_my_custom_box' );
add_action( 'save_post', 'saving_my_data' );
function addict_my_custom_box() {
  add_meta_box( 'my_box', 'Sidebar text', 'my_wp_editor', 'portfolio', 'normal', 'high' );
}
function my_wp_editor( $post ) {
  $field_value = get_post_meta( $post->ID, '_smartmeta_my-awesome-field', false );
  if(!isset($field_value[0])){ wp_editor( '', '_smartmeta_my-awesome-field' );
  }else{ wp_editor( $field_value[0], '_smartmeta_my-awesome-field' );}

}
function saving_my_data( $post_id ) {
    if ( isset ( $_POST['_smartmeta_my-awesome-field'] ) ) {
    update_post_meta( $post_id, '_smartmeta_my-awesome-field', $_POST['_smartmeta_my-awesome-field'] );
  }
}
add_smart_meta_box('my-meta-box2', array(
'title' => __('Project link','addict' ), // the title of the meta box
'pages' => array('portfolio'),  // post types on which you want the metabox to appear
'context' => 'normal', // meta box context (see above)
'priority' => 'high', // meta box priority (see above)
'fields' => array( // array describing our fields
array(
'name' => __('Enable project link','addict' ),
'id' => 'my-awesome-field5',
'type' => 'checkbox',),
array(
'name' => __('Put the project link in here','addict' ),
'id' => 'my-awesome-field2',
'type' => 'text',
),array(
'name' => __('Put the project button name in here','addict' ),
'id' => 'my-awesome-field4',
'type' => 'text',)
)));
add_smart_meta_box('my-meta-box3', array(
'title' => __('Video link','addict' ), // the title of the meta box
'pages' => array('portfolio'),  // post types on which you want the metabox to appear
'context' => 'normal', // meta box context (see above)
'priority' => 'high', // meta box priority (see above)
'fields' => array( // array describing our fields
array(
'name' => __('Put your portfolio embed video here','addict' ),
'id' => 'my-awesome-field3',
'type' => 'textarea',
),)));
add_smart_meta_box('my-meta-box6', array(
'title' => __('Client','addict' ), // the title of the meta box
'pages' => array('portfolio'),  // post types on which you want the metabox to appear
'context' => 'normal', // meta box context (see above)
'priority' => 'low', // meta box priority (see above)
'fields' => array( // array describing our fields
array(
'name' => __('Put client name here','addict' ),
'id' => 'my-awesome-field6',
'type' => 'textarea',
),)));


/*prevent headers alread sent*/
add_action('init', 'do_output_buffer');
function do_output_buffer() {
        ob_start();
}

/**
 * Returns an array of system fonts
 * Feel free to edit this, update the font fallbacks, etc.
 */
function options_typography_get_os_fonts() {
    // OS Font Defaults
    $os_faces = array(
        'Arial, sans-serif' => 'Arial',
        '"Avant Garde", sans-serif' => 'Avant Garde',
        'Cambria, Georgia, serif' => 'Cambria',
        'Copse, sans-serif' => 'Copse',
        'Garamond, "Hoefler Text", Times New Roman, Times, serif' => 'Garamond',
        'Georgia, serif' => 'Georgia',
        '"Helvetica Neue", Helvetica, sans-serif' => 'Helvetica Neue',
        'Tahoma, Geneva, sans-serif' => 'Tahoma'
    );
    return $os_faces;
}
/**
 * Returns a select list of Google fonts
 * Feel free to edit this, update the fallbacks, etc.
 */
function options_typography_get_google_fonts() {
    // Google Font Defaults
    $google_faces = array(
        'Arvo, serif' => 'Arvo',
        'Copse, sans-serif' => 'Copse',
        'Droid Sans, sans-serif' => 'Droid Sans',
        'Droid Serif, serif' => 'Droid Serif',
        'Lobster, cursive' => 'Lobster',
        'Nobile, sans-serif' => 'Nobile',
        'Open Sans, sans-serif' => 'Open Sans',
        'Oswald, sans-serif' => 'Oswald',
        'Pacifico, cursive' => 'Pacifico',
        'Rokkitt, serif' => 'Rokkit',
        'PT Sans, sans-serif' => 'PT Sans',
        'Quattrocento, serif' => 'Quattrocento',
        'Raleway, cursive' => 'Raleway',
        'Ubuntu, sans-serif' => 'Ubuntu',
        'Yanone Kaffeesatz, sans-serif' => 'Yanone Kaffeesatz'
    );
    return $google_faces;
}
/**
 * Checks font options to see if a Google font is selected.
 * If so, options_typography_enqueue_google_font is called to enqueue the font.
 * Ensures that each Google font is only enqueued once.
 */
if ( !function_exists( 'options_typography_google_fonts' ) ) {
    function options_typography_google_fonts() {
        $all_google_fonts = array_keys( options_typography_get_google_fonts() );
        // Define all the options that possibly have a unique Google font
        $google_font = of_get_option('google_font', 'Rokkitt, serif');
        $google_mixed = of_get_option('google_mixed', false);
        $google_mixed_2 = of_get_option('google_mixed_2', 'Arvo, serif');
        // Get the font face for each option and put it in an array
        $selected_fonts = array(
            $google_font['face'],
            $google_mixed['face'],
            $google_mixed_2['face'] );
        // Remove any duplicates in the list
        $selected_fonts = array_unique($selected_fonts);
        // Check each of the unique fonts against the defined Google fonts
        // If it is a Google font, go ahead and call the function to enqueue it
        foreach ( $selected_fonts as $font ) {
            if ( in_array( $font, $all_google_fonts ) ) {
                options_typography_enqueue_google_font($font);
            }
        }
    }
}
add_action( 'wp_enqueue_scripts', 'options_typography_google_fonts' );
/**
 * Enqueues the Google $font that is passed
 */
function options_typography_enqueue_google_font($font) {
    $font = explode(',', $font);
    $font = $font[0];
    // Certain Google fonts need slight tweaks in order to load properly
    // Like our friend "Raleway"
    if ( $font == 'Raleway' )
        $font = 'Raleway:100';
    $font = str_replace(" ", "+", $font);
    wp_enqueue_style( "options_typography_$font", "http://fonts.googleapis.com/css?family=$font", false, null, 'all' );
}
/*
 * Outputs the selected option panel styles inline into the <head>
 */
function options_typography_styles() {
     $output = '';
     $input = '';
     if ( of_get_option( 'google_font' ) ) {
          $input = of_get_option( 'google_font' );
      $output .= options_typography_font_styles( of_get_option( 'google_font' ) , '.google-font');
     }
     if ( $output != '' ) {
    echo $output;
     }
}
function options_typography_styles2() {
     $output = '';
     $input = '';
     if ( of_get_option( 'google_mixed_2' ) ) {
          $input = of_get_option( 'google_mixed_2' );
      $output .= options_typography_font_styles( of_get_option( 'google_mixed_2' ) , '.google_mixed_2');
     }
     if ( $output != '' ) {
    echo $output;
     }
}
/*
 * Returns a typography option in a format that can be outputted as inline CSS
 */
function options_typography_font_styles($option) {
             $output = '';
        $output .= ' color:' . $option['color'] .'; ';
        $output .= 'font-family:' . $option['face'] . '; ';
              $output .= "\n";
        return $output;
}



/*Styling for the custom post type icon*/
add_action( 'admin_head', 'wpt_portfolio_icons' );
function wpt_portfolio_icons() {
    ?>
    <style type="text/css" media="screen">
    #icon-edit.icon32-posts-portfolio {background: url(<?php echo get_template_directory_uri(); ?>/img/portfolio_big.jpg) no-repeat;}
    </style>
<?php }


/*remove gallery from portfolio*/
add_filter( 'the_content', 'addict_remove_gallery_from_portfolio' );
function addict_remove_gallery_from_portfolio( $content = null ){
    global $post;
    if( $post->post_type == 'portfolio' ){
        $pattern = get_shortcode_regex();
        preg_match('/'.$pattern.'/s', $content, $matches);
        if ( isset($matches[2]) && is_array($matches) && $matches[2] == 'gallery') {
            //shortcode is being used
            $content = str_replace( $matches['0'], '', $content );
        }
    }
    return $content;
}


/*remove slider from home*/
add_filter( 'the_content', 'addict_remove_slider_from_home' );
function addict_remove_slider_from_home( $content = null ){
    global $post;
    if( is_page_template('tmp-home.php') ){
        $pattern = get_shortcode_regex();
        preg_match('/'.$pattern.'/s', $content, $matches);
        if ( isset($matches[2]) && is_array($matches) && $matches[2] == 'layerslider') {
            //shortcode is being used
            $content = str_replace( $matches['0'], '', $content );
        }
    }
    return $content;
}



/*get tag name*/
function GetTagName($meta){
    if (is_string($meta) || (is_numeric($meta) && !is_double($meta))
            || is_int($meta)){
                if (is_numeric($meta))
                    $meta = (int)$meta;
                        if (is_int($meta))
                            $TagSlug = get_term_by('id', $meta, 'post_tag');
                        else
                            $TagSlug = get_term_by('slug', $meta, 'post_tag');
                    return $TagSlug->name;
            }
}

/*image resize*/
function aq_resize( $url, $width = null, $height = null, $crop = null, $single = true, $upscale = false ) {

    // Validate inputs.
    if ( ! $url || ( ! $width && ! $height ) ) return false;

    // Caipt'n, ready to hook.
    if ( true === $upscale ) add_filter( 'image_resize_dimensions', 'aq_upscale', 10, 6 );

    // Define upload path & dir.
    $upload_info = wp_upload_dir();
    $upload_dir = $upload_info['basedir'];
    $upload_url = $upload_info['baseurl'];

    $http_prefix = "http://";
    $https_prefix = "https://";

    /* if the $url scheme differs from $upload_url scheme, make them match
       if the schemes differe, images don't show up. */
    if(!strncmp($url,$https_prefix,strlen($https_prefix))){ //if url begins with https:// make $upload_url begin with https:// as well
        $upload_url = str_replace($http_prefix,$https_prefix,$upload_url);
    }
    elseif(!strncmp($url,$http_prefix,strlen($http_prefix))){ //if url begins with http:// make $upload_url begin with http:// as well
        $upload_url = str_replace($https_prefix,$http_prefix,$upload_url);
    }


    // Check if $img_url is local.
    if ( false === strpos( $url, $upload_url ) ) return false;

    // Define path of image.
    $rel_path = str_replace( $upload_url, '', $url );
    $img_path = $upload_dir . $rel_path;

    // Check if img path exists, and is an image indeed.
    if ( ! file_exists( $img_path ) or ! getimagesize( $img_path ) ) return false;

    // Get image info.
    $info = pathinfo( $img_path );
    $ext = $info['extension'];
    list( $orig_w, $orig_h ) = getimagesize( $img_path );

    // Get image size after cropping.
    $dims = image_resize_dimensions( $orig_w, $orig_h, $width, $height, $crop );
    $dst_w = $dims[4];
    $dst_h = $dims[5];

    // Return the original image only if it exactly fits the needed measures.
    if ( ! $dims && ( ( ( null === $height && $orig_w == $width ) xor ( null === $width && $orig_h == $height ) ) xor ( $height == $orig_h && $width == $orig_w ) ) ) {
        $img_url = $url;
        $dst_w = $orig_w;
        $dst_h = $orig_h;
    } else {
        // Use this to check if cropped image already exists, so we can return that instead.
        $suffix = "{$dst_w}x{$dst_h}";
        $dst_rel_path = str_replace( '.' . $ext, '', $rel_path );
        $destfilename = "{$upload_dir}{$dst_rel_path}-{$suffix}.{$ext}";

        if ( ! $dims || ( true == $crop && false == $upscale && ( $dst_w < $width || $dst_h < $height ) ) ) {
            // Can't resize, so return false saying that the action to do could not be processed as planned.
            return false;
        }
        // Else check if cache exists.
        elseif ( file_exists( $destfilename ) && getimagesize( $destfilename ) ) {
            $img_url = "{$upload_url}{$dst_rel_path}-{$suffix}.{$ext}";
        }
        // Else, we resize the image and return the new resized image url.
        else {

            // Note: This pre-3.5 fallback check will edited out in subsequent version.
            if ( function_exists( 'wp_get_image_editor' ) ) {

                $editor = wp_get_image_editor( $img_path );

                if ( is_wp_error( $editor ) || is_wp_error( $editor->resize( $width, $height, $crop ) ) )
                    return false;

                $resized_file = $editor->save();

                if ( ! is_wp_error( $resized_file ) ) {
                    $resized_rel_path = str_replace( $upload_dir, '', $resized_file['path'] );
                    $img_url = $upload_url . $resized_rel_path;
                } else {
                    return false;
                }

            } else {

                $resized_img_path = wp_get_image_editor( $img_path, $width, $height, $crop ); // Fallback foo.
                if ( ! is_wp_error( $resized_img_path ) ) {
                    $resized_rel_path = str_replace( $upload_dir, '', $resized_img_path );
                    $img_url = $upload_url . $resized_rel_path;
                } else {
                    return false;
                }

            }

        }
    }

    // Okay, leave the ship.
    if ( true === $upscale ) remove_filter( 'image_resize_dimensions', 'aq_upscale' );

    // Return the output.
    if ( $single ) {
        // str return.
        $image = $img_url;
    } else {
        // array return.
        $image = array (
            0 => $img_url,
            1 => $dst_w,
            2 => $dst_h
        );
    }

    return $image;
}


function aq_upscale( $default, $orig_w, $orig_h, $dest_w, $dest_h, $crop ) {
    if ( ! $crop ) return null; // Let the wordpress default function handle this.

    // Here is the point we allow to use larger image size than the original one.
    $aspect_ratio = $orig_w / $orig_h;
    $new_w = $dest_w;
    $new_h = $dest_h;

    if ( ! $new_w ) {
        $new_w = intval( $new_h * $aspect_ratio );
    }

    if ( ! $new_h ) {
        $new_h = intval( $new_w / $aspect_ratio );
    }

    $size_ratio = max( $new_w / $orig_w, $new_h / $orig_h );

    $crop_w = round( $new_w / $size_ratio );
    $crop_h = round( $new_h / $size_ratio );

    $s_x = floor( ( $orig_w - $crop_w ) / 2 );
    $s_y = floor( ( $orig_h - $crop_h ) / 2 );

    return array( 0, 0, (int) $s_x, (int) $s_y, (int) $new_w, (int) $new_h, (int) $crop_w, (int) $crop_h );
}

//add video input field
add_smart_meta_box('my-meta-box77', array(
'title' => __('Video url', 'addict'), // the title of the meta box
'pages' => array('post'),
'context' => 'normal', // meta box context (see above)
'priority' => 'high', // meta box priority (see above)
'fields' => array( // array describing our fields
array(
'name' => __('Put your embed video URL here', 'addict'),
'id' => 'my-awesome-field77',
'type' => 'textarea',
),)));


//woocommerce
add_theme_support( 'woocommerce' );
add_filter('add_to_cart_fragments', 'woocommerce_header_add_to_cart_fragment');

function woocommerce_header_add_to_cart_fragment( $fragments ) {
    global $woocommerce;

    ob_start(); ?>
    <a class="cart-contents" href="<?php echo $woocommerce->cart->get_cart_url(); ?>"><div class="cart-icon-wrap"><i class="icon-shopping-cart"></i> <div class="cart-wrap"><span><?php echo $woocommerce->cart->cart_contents_count; ?> </span></div> </div></a>
    <?php

    $fragments['a.cart-contents'] = ob_get_clean();

    return $fragments;
}

//ajax comments
add_action('comment_post', 'addict_ajaxify_comments',20, 2);
function addict_ajaxify_comments($comment_ID, $comment_status){
    if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
        switch($comment_status){
            case "0":
            wp_notify_moderator($comment_ID);
            case "1": //Approved comment
            echo "success";
            $commentdata =& get_comment($comment_ID, ARRAY_A);
            $post =& get_post($commentdata['comment_post_ID']);
            wp_notify_postauthor($comment_ID, $commentdata['comment_type']);
            break;
            default:
            echo "error";
        }
    exit;
    }
}

//multiple featured images
  if (class_exists('MultiPostThumbnails')) {
                new MultiPostThumbnails(
                    array(
                        'label' => 'Header Image',
                        'id' => 'header-image-portfolio',
                        'post_type' => 'portfolio'
                    )
                );

                 new MultiPostThumbnails(
                    array(
                        'label' => 'Header Image',
                        'id' => 'header-image',
                        'post_type' => 'page'
                    )
                );

                 new MultiPostThumbnails(
                    array(
                        'label' => 'Header Image',
                        'id' => 'header-image-post',
                        'post_type' => 'post'
                    )
                );
            }
function addict_change_mce_options( $init ) {
    // Command separated string of extended elements
    $ext = 'pre[id|name|class|style]';

    // Add to extended_valid_elements if it alreay exists
    if ( isset( $init['extended_valid_elements'] ) ) {
        $init['extended_valid_elements'] .= ',' . $ext;
    } else {
        $init['extended_valid_elements'] = $ext;
    }

    // Super important: return $init!
    return $init;
}

add_filter('tiny_mce_before_init', 'addict_change_mce_options');
?>