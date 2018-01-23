<?php
if ( is_admin() ){

/* Call the html code */
add_action('admin_menu', 'addict_data_import_admin_menu');

function addict_data_import_admin_menu() {
add_options_page('ClanWars data import', 'ClanWars data import', 'administrator',
'dummy-data-import', 'addict_dummy_data_import');
}
}
?>
<?php
function addict_dummy_data_import() {
?>
<div>
<h2><?php _e("ClanWars data import", 'addict'); ?></h2>
<p><?php _e("Click import button to import data", 'addict'); ?></p>

<input type="submit" class="dummyimportbutton" name="import" value="import" />
<div id="succimp"></div>
<div id="failimp"></div>
<?php
}
add_action( 'admin_footer', 'import_javascript' );

function import_javascript() {
?>

<script type="text/javascript" >

jQuery(document).ready(function(){
    jQuery('.dummyimportbutton').click(function(){
        var clickBtnValue = jQuery(this).val();

        data =  {action: clickBtnValue};
        jQuery.post(ajaxurl, data, function (response) {
            // Response div goes here.
            if(response == 0){
                jQuery( "#failimp" ).append( "<p>Error! Data maye be imported already!</p>" );
            }else{
                jQuery( "#succimp" ).append( "<p>Data successfully imported!</p>" );
            }

        });
    });

});
</script>
<?php
}


add_action( 'wp_ajax_import', 'import_callback' );

function import_callback() {
     global $wpdb;


$wpdb->get_results(
"INSERT INTO `".$wpdb->prefix."cw_games` (`id`, `title`, `abbr`, `icon`) VALUES
(1, 'Counter Strike: Global Offensive', 'CS:GO', 0),
(2, 'Battlefield 4', 'BF4', 0),
(3, 'Dota 2', 'Dota 2', 0),
(4, 'League of Legends', 'LoL', 0),
(5, 'Street Fighter', 'SF', 0),
(6, 'title', 'abbr', 0);");

$wpdb->get_results(
"INSERT INTO `".$wpdb->prefix."cw_maps` (`id`, `game_id`, `title`, `screenshot`) VALUES
(1, 3, 'Monkey Island', 1257),
(2, 3, 'The tower', 1258),
(3, 3, 'Waterworld', 1259),
(4, 3, 'Outerspace', 1260),
(5, 4, 'The coast', 1261),
(6, 4, 'Mountain pass', 1262),
(7, 4, 'The tower', 1263),
(8, 4, 'Thunder storm', 1264),
(9, 1, 'de_dust2', 1265),
(10, 1, 'de_aztec', 1266),
(11, 1, 'cs_assault', 1267),
(12, 1, 'de_train', 1268),
(13, 2, 'Close quarters', 1269),
(14, 2, 'Zavod 311', 1270),
(15, 2, 'Dragon pass', 1271),
(16, 2, 'Operation locker', 1272);");

$wpdb->get_results(
"INSERT INTO `".$wpdb->prefix."cw_matches` (`id`, `title`, `date`, `post_id`, `team1`, `team2`, `game_id`, `match_status`, `description`, `external_url`) VALUES
(1, 'Game Addict vs Fnatic - Friendly', '2014-02-20 21:12:00', 1252, 5, 2, 2, 0, 'Sed commodo, libero ut dignissim imperdiet, lorem nibh bibendum nisi, vel blandit est eros sit amet elit. Mauris nec arcu vel tellus aliquam congue. Mauris fermentum sem ut tortor ultricies dictum. Praesent at porttitor mauris. Curabitur pulvinar suscipit tortor venenatis faucibus. Vivamus a lectus mi. Nullam ultricies nisl id ante volutpat feugiat. Ut viverra elit et nisi pharetra sed hendrerit lacus placerat. Sed laoreet ante vitae justo rhoncus nec posuere diam tincidunt. Duis interdum quam id risus sollicitudin vel commodo lorem ultrices. Maecenas ut erat scelerisque tortor vestibulum pretium. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Sed iaculis dui vitae nisl lobortis eu vehicula lacus dignissim.\r\n<blockquote>Curabitur lorem mauris, dictum et tempus eu, feugiat pharetra sapien. Donec vel turpis orci, ut congue tortor. Aenean sed interdum quam. Vivamus laoreet posuere pharetra. Donec quis nisi nec nulla malesuada scelerisque vitae ac turpis. In eget elit eu risus gravida adipiscing.</blockquote>\r\nMorbi vel ipsum vel augue mattis ultricies non et mauris. Phasellus rhoncus euismod massa. Etiam euismod tristique venenatis. In lobortis tellus non augue tempor eleifend. Vestibulum ut dignissim tellus. Morbi ornare, enim a semper venenatis, odio justo luctus enim, consectetur sodales justo est id leo. Mauris risus augue, fermentum sit amet congue sit amet, elementum quis arcu. Duis lacinia nisi vel lorem scelerisque interdum. Maecenas ut erat scelerisque tortor vestibulum pretium. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Sed iaculis dui vitae nisl lobortis eu vehicula lacus dignissim. Quisque vel augue sit amet diam lobortis posuere eu sit amet tortor. Curabitur porta arcu non dui facilisis pharetra. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Pellentesque ultrices hendrerit nisl, eu mollis leo semper dictum. Cras ut magna metus, ac tristique enim. Integer quis orci nisi, sit amet commodo purus. Fusce id justo ac lacus aliquet lobortis. Curabitur lorem mauris, dictum et tempus eu, feugiat pharetra sapien. Donec vel turpis orci, ut congue tortor. Aenean sed interdum quam. Vivamus laoreet posuere pharetra. Donec quis nisi nec nulla malesuada scelerisque vitae ac turpis. In eget elit eu risus gravida adipiscing. Morbi vel ipsum vel augue mattis ultricies non et mauris. Phasellus rhoncus euismod massa. Etiam euismod tristique venenatis. In lobortis tellus non augue tempor eleifend. Vestibulum ut dignissim tellus. Morbi ornare, enim a semper venenatis, odio justo luctus enim, consectetur sodales justo est id leo. Mauris risus augue, fermentum sit amet congue sit amet, elementum quis arcu. Duis lacinia nisi vel lorem scelerisque interdum. Etiam massa mauris, fermentum a congue id, congue sit amet erat. Donec dignissim dictum enim, ac cursus dolor auctor et. Aliquam scelerisque luctus nisl, at accumsan tortor pulvinar tincidunt. Aenean sit amet adipiscing mauris. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Integer interdum sollicitudin condimentum.\r\n<ul>\r\n <li>Quisque vel augue sit amet</li>\r\n <li>Proin vitae augue leo</li>\r\n  <li>Duis interdum quam id risus sollicitudin vel commodo lorem ultrices</li>\r\n    <li>Rhoncus nec posuere diam tincidunt</li>\r\n <li>Donec quis nisi nec nulla malesuada scelerisque vitae ac turpis. In eget elit eu risus gravida adipiscing.</li>\r\n</ul>\r\nEtiam massa mauris, fermentum a congue id, congue sit amet erat. Donec dignissim dictum enim, ac cursus dolor auctor et. Aliquam scelerisque luctus nisl, at accumsan tortor pulvinar tincidunt. Aenean sit amet adipiscing mauris. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Integer interdum sollicitudin condimentum.', ''),
(2, 'Pro league semifinal', '2015-03-01 21:12:00', 1253, 3, 3, 1, 1, 'Sed commodo, libero ut dignissim imperdiet, lorem nibh bibendum nisi, vel blandit est eros sit amet elit. Mauris nec arcu vel tellus aliquam congue. Mauris fermentum sem ut tortor ultricies dictum. Praesent at porttitor mauris. Curabitur pulvinar suscipit tortor venenatis faucibus. Vivamus a lectus mi. Nullam ultricies nisl id ante volutpat feugiat. Ut viverra elit et nisi pharetra sed hendrerit lacus placerat. Sed laoreet ante vitae justo rhoncus nec posuere diam tincidunt. Duis interdum quam id risus sollicitudin vel commodo lorem ultrices. Maecenas ut erat scelerisque tortor vestibulum pretium. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Sed iaculis dui vitae nisl lobortis eu vehicula lacus dignissim.\r\n<blockquote>Curabitur lorem mauris, dictum et tempus eu, feugiat pharetra sapien. Donec vel turpis orci, ut congue tortor. Aenean sed interdum quam. Vivamus laoreet posuere pharetra. Donec quis nisi nec nulla malesuada scelerisque vitae ac turpis. In eget elit eu risus gravida adipiscing.</blockquote>\r\nMorbi vel ipsum vel augue mattis ultricies non et mauris. Phasellus rhoncus euismod massa. Etiam euismod tristique venenatis. In lobortis tellus non augue tempor eleifend. Vestibulum ut dignissim tellus. Morbi ornare, enim a semper venenatis, odio justo luctus enim, consectetur sodales justo est id leo. Mauris risus augue, fermentum sit amet congue sit amet, elementum quis arcu. Duis lacinia nisi vel lorem scelerisque interdum. Maecenas ut erat scelerisque tortor vestibulum pretium. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Sed iaculis dui vitae nisl lobortis eu vehicula lacus dignissim. Quisque vel augue sit amet diam lobortis posuere eu sit amet tortor. Curabitur porta arcu non dui facilisis pharetra. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Pellentesque ultrices hendrerit nisl, eu mollis leo semper dictum. Cras ut magna metus, ac tristique enim. Integer quis orci nisi, sit amet commodo purus. Fusce id justo ac lacus aliquet lobortis. Curabitur lorem mauris, dictum et tempus eu, feugiat pharetra sapien. Donec vel turpis orci, ut congue tortor. Aenean sed interdum quam. Vivamus laoreet posuere pharetra. Donec quis nisi nec nulla malesuada scelerisque vitae ac turpis. In eget elit eu risus gravida adipiscing. Morbi vel ipsum vel augue mattis ultricies non et mauris. Phasellus rhoncus euismod massa. Etiam euismod tristique venenatis. In lobortis tellus non augue tempor eleifend. Vestibulum ut dignissim tellus. Morbi ornare, enim a semper venenatis, odio justo luctus enim, consectetur sodales justo est id leo. Mauris risus augue, fermentum sit amet congue sit amet, elementum quis arcu. Duis lacinia nisi vel lorem scelerisque interdum. Etiam massa mauris, fermentum a congue id, congue sit amet erat. Donec dignissim dictum enim, ac cursus dolor auctor et. Aliquam scelerisque luctus nisl, at accumsan tortor pulvinar tincidunt. Aenean sit amet adipiscing mauris. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Integer interdum sollicitudin condimentum.\r\n<ul>\r\n <li>Quisque vel augue sit amet</li>\r\n <li>Proin vitae augue leo</li>\r\n  <li>Duis interdum quam id risus sollicitudin vel commodo lorem ultrices</li>\r\n    <li>Rhoncus nec posuere diam tincidunt</li>\r\n <li>Donec quis nisi nec nulla malesuada scelerisque vitae ac turpis. In eget elit eu risus gravida adipiscing.</li>\r\n</ul>\r\nEtiam massa mauris, fermentum a congue id, congue sit amet erat. Donec dignissim dictum enim, ac cursus dolor auctor et. Aliquam scelerisque luctus nisl, at accumsan tortor pulvinar tincidunt. Aenean sit amet adipiscing mauris. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Integer interdum sollicitudin condimentum.', ''),
(3, 'Dota final', '2014-01-10 18:07:00', 1273, 5, 1, 3, 1, 'Sed commodo, libero ut dignissim imperdiet, lorem nibh bibendum nisi, vel blandit est eros sit amet elit. Mauris nec arcu vel tellus aliquam congue. Mauris fermentum sem ut tortor ultricies dictum. Praesent at porttitor mauris. Curabitur pulvinar suscipit tortor venenatis faucibus. Vivamus a lectus mi. Nullam ultricies nisl id ante volutpat feugiat. Ut viverra elit et nisi pharetra sed hendrerit lacus placerat. Sed laoreet ante vitae justo rhoncus nec posuere diam tincidunt. Duis interdum quam id risus sollicitudin vel commodo lorem ultrices. Maecenas ut erat scelerisque tortor vestibulum pretium. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Sed iaculis dui vitae nisl lobortis eu vehicula lacus dignissim.\r\n<blockquote>Curabitur lorem mauris, dictum et tempus eu, feugiat pharetra sapien. Donec vel turpis orci, ut congue tortor. Aenean sed interdum quam. Vivamus laoreet posuere pharetra. Donec quis nisi nec nulla malesuada scelerisque vitae ac turpis. In eget elit eu risus gravida adipiscing.</blockquote>\r\nMorbi vel ipsum vel augue mattis ultricies non et mauris. Phasellus rhoncus euismod massa. Etiam euismod tristique venenatis. In lobortis tellus non augue tempor eleifend. Vestibulum ut dignissim tellus. Morbi ornare, enim a semper venenatis, odio justo luctus enim, consectetur sodales justo est id leo. Mauris risus augue, fermentum sit amet congue sit amet, elementum quis arcu. Duis lacinia nisi vel lorem scelerisque interdum. Maecenas ut erat scelerisque tortor vestibulum pretium. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Sed iaculis dui vitae nisl lobortis eu vehicula lacus dignissim. Quisque vel augue sit amet diam lobortis posuere eu sit amet tortor. Curabitur porta arcu non dui facilisis pharetra. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Pellentesque ultrices hendrerit nisl, eu mollis leo semper dictum. Cras ut magna metus, ac tristique enim. Integer quis orci nisi, sit amet commodo purus. Fusce id justo ac lacus aliquet lobortis. Curabitur lorem mauris, dictum et tempus eu, feugiat pharetra sapien. Donec vel turpis orci, ut congue tortor. Aenean sed interdum quam. Vivamus laoreet posuere pharetra. Donec quis nisi nec nulla malesuada scelerisque vitae ac turpis. In eget elit eu risus gravida adipiscing. Morbi vel ipsum vel augue mattis ultricies non et mauris. Phasellus rhoncus euismod massa. Etiam euismod tristique venenatis. In lobortis tellus non augue tempor eleifend. Vestibulum ut dignissim tellus. Morbi ornare, enim a semper venenatis, odio justo luctus enim, consectetur sodales justo est id leo. Mauris risus augue, fermentum sit amet congue sit amet, elementum quis arcu. Duis lacinia nisi vel lorem scelerisque interdum. Etiam massa mauris, fermentum a congue id, congue sit amet erat. Donec dignissim dictum enim, ac cursus dolor auctor et. Aliquam scelerisque luctus nisl, at accumsan tortor pulvinar tincidunt. Aenean sit amet adipiscing mauris. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Integer interdum sollicitudin condimentum.\r\n<ul>\r\n   <li>Quisque vel augue sit amet</li>\r\n <li>Proin vitae augue leo</li>\r\n  <li>Duis interdum quam id risus sollicitudin vel commodo lorem ultrices</li>\r\n    <li>Rhoncus nec posuere diam tincidunt</li>\r\n <li>Donec quis nisi nec nulla malesuada scelerisque vitae ac turpis. In eget elit eu risus gravida adipiscing.</li>\r\n</ul>\r\nEtiam massa mauris, fermentum a congue id, congue sit amet erat. Donec dignissim dictum enim, ac cursus dolor auctor et. Aliquam scelerisque luctus nisl, at accumsan tortor pulvinar tincidunt. Aenean sit amet adipiscing mauris. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Integer interdum sollicitudin condimentum.', ''),
(4, 'League of Legends - Sunday fun', '2014-02-10 18:23:00', 1274, 2, 4, 4, 0, 'Sed commodo, libero ut dignissim imperdiet, lorem nibh bibendum nisi, vel blandit est eros sit amet elit. Mauris nec arcu vel tellus aliquam congue. Mauris fermentum sem ut tortor ultricies dictum. Praesent at porttitor mauris. Curabitur pulvinar suscipit tortor venenatis faucibus. Vivamus a lectus mi. Nullam ultricies nisl id ante volutpat feugiat. Ut viverra elit et nisi pharetra sed hendrerit lacus placerat. Sed laoreet ante vitae justo rhoncus nec posuere diam tincidunt. Duis interdum quam id risus sollicitudin vel commodo lorem ultrices. Maecenas ut erat scelerisque tortor vestibulum pretium. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Sed iaculis dui vitae nisl lobortis eu vehicula lacus dignissim.\r\n<blockquote>Curabitur lorem mauris, dictum et tempus eu, feugiat pharetra sapien. Donec vel turpis orci, ut congue tortor. Aenean sed interdum quam. Vivamus laoreet posuere pharetra. Donec quis nisi nec nulla malesuada scelerisque vitae ac turpis. In eget elit eu risus gravida adipiscing.</blockquote>\r\nMorbi vel ipsum vel augue mattis ultricies non et mauris. Phasellus rhoncus euismod massa. Etiam euismod tristique venenatis. In lobortis tellus non augue tempor eleifend. Vestibulum ut dignissim tellus. Morbi ornare, enim a semper venenatis, odio justo luctus enim, consectetur sodales justo est id leo. Mauris risus augue, fermentum sit amet congue sit amet, elementum quis arcu. Duis lacinia nisi vel lorem scelerisque interdum. Maecenas ut erat scelerisque tortor vestibulum pretium. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Sed iaculis dui vitae nisl lobortis eu vehicula lacus dignissim. Quisque vel augue sit amet diam lobortis posuere eu sit amet tortor. Curabitur porta arcu non dui facilisis pharetra. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Pellentesque ultrices hendrerit nisl, eu mollis leo semper dictum. Cras ut magna metus, ac tristique enim. Integer quis orci nisi, sit amet commodo purus. Fusce id justo ac lacus aliquet lobortis. Curabitur lorem mauris, dictum et tempus eu, feugiat pharetra sapien. Donec vel turpis orci, ut congue tortor. Aenean sed interdum quam. Vivamus laoreet posuere pharetra. Donec quis nisi nec nulla malesuada scelerisque vitae ac turpis. In eget elit eu risus gravida adipiscing. Morbi vel ipsum vel augue mattis ultricies non et mauris. Phasellus rhoncus euismod massa. Etiam euismod tristique venenatis. In lobortis tellus non augue tempor eleifend. Vestibulum ut dignissim tellus. Morbi ornare, enim a semper venenatis, odio justo luctus enim, consectetur sodales justo est id leo. Mauris risus augue, fermentum sit amet congue sit amet, elementum quis arcu. Duis lacinia nisi vel lorem scelerisque interdum. Etiam massa mauris, fermentum a congue id, congue sit amet erat. Donec dignissim dictum enim, ac cursus dolor auctor et. Aliquam scelerisque luctus nisl, at accumsan tortor pulvinar tincidunt. Aenean sit amet adipiscing mauris. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Integer interdum sollicitudin condimentum.\r\n<ul>\r\n   <li>Quisque vel augue sit amet</li>\r\n <li>Proin vitae augue leo</li>\r\n  <li>Duis interdum quam id risus sollicitudin vel commodo lorem ultrices</li>\r\n    <li>Rhoncus nec posuere diam tincidunt</li>\r\n <li>Donec quis nisi nec nulla malesuada scelerisque vitae ac turpis. In eget elit eu risus gravida adipiscing.</li>\r\n</ul>\r\nEtiam massa mauris, fermentum a congue id, congue sit amet erat. Donec dignissim dictum enim, ac cursus dolor auctor et. Aliquam scelerisque luctus nisl, at accumsan tortor pulvinar tincidunt. Aenean sit amet adipiscing mauris. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Integer interdum sollicitudin condimentum.', ''),
(5, 'Junior League', '2014-05-30 21:55:00', 1298, 4, 3, 1, 0, 'Sed commodo, libero ut dignissim imperdiet, lorem nibh bibendum nisi, vel blandit est eros sit amet elit. Mauris nec arcu vel tellus aliquam congue. Mauris fermentum sem ut tortor ultricies dictum. Praesent at porttitor mauris. Curabitur pulvinar suscipit tortor venenatis faucibus. Vivamus a lectus mi. Nullam ultricies nisl id ante volutpat feugiat. Ut viverra elit et nisi pharetra sed hendrerit lacus placerat. Sed laoreet ante vitae justo rhoncus nec posuere diam tincidunt. Duis interdum quam id risus sollicitudin vel commodo lorem ultrices. Maecenas ut erat scelerisque tortor vestibulum pretium. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Sed iaculis dui vitae nisl lobortis eu vehicula lacus dignissim.\r\n<blockquote>Curabitur lorem mauris, dictum et tempus eu, feugiat pharetra sapien. Donec vel turpis orci, ut congue tortor. Aenean sed interdum quam. Vivamus laoreet posuere pharetra. Donec quis nisi nec nulla malesuada scelerisque vitae ac turpis. In eget elit eu risus gravida adipiscing.</blockquote>\r\nMorbi vel ipsum vel augue mattis ultricies non et mauris. Phasellus rhoncus euismod massa. Etiam euismod tristique venenatis. In lobortis tellus non augue tempor eleifend. Vestibulum ut dignissim tellus. Morbi ornare, enim a semper venenatis, odio justo luctus enim, consectetur sodales justo est id leo. Mauris risus augue, fermentum sit amet congue sit amet, elementum quis arcu. Duis lacinia nisi vel lorem scelerisque interdum. Maecenas ut erat scelerisque tortor vestibulum pretium. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Sed iaculis dui vitae nisl lobortis eu vehicula lacus dignissim. Quisque vel augue sit amet diam lobortis posuere eu sit amet tortor. Curabitur porta arcu non dui facilisis pharetra. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Pellentesque ultrices hendrerit nisl, eu mollis leo semper dictum. Cras ut magna metus, ac tristique enim. Integer quis orci nisi, sit amet commodo purus. Fusce id justo ac lacus aliquet lobortis. Curabitur lorem mauris, dictum et tempus eu, feugiat pharetra sapien. Donec vel turpis orci, ut congue tortor. Aenean sed interdum quam. Vivamus laoreet posuere pharetra. Donec quis nisi nec nulla malesuada scelerisque vitae ac turpis. In eget elit eu risus gravida adipiscing. Morbi vel ipsum vel augue mattis ultricies non et mauris. Phasellus rhoncus euismod massa. Etiam euismod tristique venenatis. In lobortis tellus non augue tempor eleifend. Vestibulum ut dignissim tellus. Morbi ornare, enim a semper venenatis, odio justo luctus enim, consectetur sodales justo est id leo. Mauris risus augue, fermentum sit amet congue sit amet, elementum quis arcu. Duis lacinia nisi vel lorem scelerisque interdum. Etiam massa mauris, fermentum a congue id, congue sit amet erat. Donec dignissim dictum enim, ac cursus dolor auctor et. Aliquam scelerisque luctus nisl, at accumsan tortor pulvinar tincidunt. Aenean sit amet adipiscing mauris. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Integer interdum sollicitudin condimentum.\r\n<ul>\r\n    <li>Quisque vel augue sit amet</li>\r\n <li>Proin vitae augue leo</li>\r\n  <li>Duis interdum quam id risus sollicitudin vel commodo lorem ultrices</li>\r\n    <li>Rhoncus nec posuere diam tincidunt</li>\r\n <li>Donec quis nisi nec nulla malesuada scelerisque vitae ac turpis. In eget elit eu risus gravida adipiscing.</li>\r\n</ul>\r\nEtiam massa mauris, fermentum a congue id, congue sit amet erat. Donec dignissim dictum enim, ac cursus dolor auctor et. Aliquam scelerisque luctus nisl, at accumsan tortor pulvinar tincidunt. Aenean sit amet adipiscing mauris. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Integer interdum sollicitudin condimentum.', ''),
(6, 'Dota 2 friendly', '2014-01-20 22:56:00', 1299, 1, 2, 3, 0, 'Sed commodo, libero ut dignissim imperdiet, lorem nibh bibendum nisi, vel blandit est eros sit amet elit. Mauris nec arcu vel tellus aliquam congue. Mauris fermentum sem ut tortor ultricies dictum. Praesent at porttitor mauris. Curabitur pulvinar suscipit tortor venenatis faucibus. Vivamus a lectus mi. Nullam ultricies nisl id ante volutpat feugiat. Ut viverra elit et nisi pharetra sed hendrerit lacus placerat. Sed laoreet ante vitae justo rhoncus nec posuere diam tincidunt. Duis interdum quam id risus sollicitudin vel commodo lorem ultrices. Maecenas ut erat scelerisque tortor vestibulum pretium. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Sed iaculis dui vitae nisl lobortis eu vehicula lacus dignissim.\r\n<blockquote>Curabitur lorem mauris, dictum et tempus eu, feugiat pharetra sapien. Donec vel turpis orci, ut congue tortor. Aenean sed interdum quam. Vivamus laoreet posuere pharetra. Donec quis nisi nec nulla malesuada scelerisque vitae ac turpis. In eget elit eu risus gravida adipiscing.</blockquote>\r\nMorbi vel ipsum vel augue mattis ultricies non et mauris. Phasellus rhoncus euismod massa. Etiam euismod tristique venenatis. In lobortis tellus non augue tempor eleifend. Vestibulum ut dignissim tellus. Morbi ornare, enim a semper venenatis, odio justo luctus enim, consectetur sodales justo est id leo. Mauris risus augue, fermentum sit amet congue sit amet, elementum quis arcu. Duis lacinia nisi vel lorem scelerisque interdum. Maecenas ut erat scelerisque tortor vestibulum pretium. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Sed iaculis dui vitae nisl lobortis eu vehicula lacus dignissim. Quisque vel augue sit amet diam lobortis posuere eu sit amet tortor. Curabitur porta arcu non dui facilisis pharetra. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Pellentesque ultrices hendrerit nisl, eu mollis leo semper dictum. Cras ut magna metus, ac tristique enim. Integer quis orci nisi, sit amet commodo purus. Fusce id justo ac lacus aliquet lobortis. Curabitur lorem mauris, dictum et tempus eu, feugiat pharetra sapien. Donec vel turpis orci, ut congue tortor. Aenean sed interdum quam. Vivamus laoreet posuere pharetra. Donec quis nisi nec nulla malesuada scelerisque vitae ac turpis. In eget elit eu risus gravida adipiscing. Morbi vel ipsum vel augue mattis ultricies non et mauris. Phasellus rhoncus euismod massa. Etiam euismod tristique venenatis. In lobortis tellus non augue tempor eleifend. Vestibulum ut dignissim tellus. Morbi ornare, enim a semper venenatis, odio justo luctus enim, consectetur sodales justo est id leo. Mauris risus augue, fermentum sit amet congue sit amet, elementum quis arcu. Duis lacinia nisi vel lorem scelerisque interdum. Etiam massa mauris, fermentum a congue id, congue sit amet erat. Donec dignissim dictum enim, ac cursus dolor auctor et. Aliquam scelerisque luctus nisl, at accumsan tortor pulvinar tincidunt. Aenean sit amet adipiscing mauris. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Integer interdum sollicitudin condimentum.\r\n<ul>\r\n  <li>Quisque vel augue sit amet</li>\r\n <li>Proin vitae augue leo</li>\r\n  <li>Duis interdum quam id risus sollicitudin vel commodo lorem ultrices</li>\r\n    <li>Rhoncus nec posuere diam tincidunt</li>\r\n <li>Donec quis nisi nec nulla malesuada scelerisque vitae ac turpis. In eget elit eu risus gravida adipiscing.</li>\r\n</ul>\r\nEtiam massa mauris, fermentum a congue id, congue sit amet erat. Donec dignissim dictum enim, ac cursus dolor auctor et. Aliquam scelerisque luctus nisl, at accumsan tortor pulvinar tincidunt. Aenean sit amet adipiscing mauris. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Integer interdum sollicitudin condimentum.', NULL),
(7, 'Close quarters match', '2014-01-20 22:57:00', 1300, 5, 3, 2, 0, 'Sed commodo, libero ut dignissim imperdiet, lorem nibh bibendum nisi, vel blandit est eros sit amet elit. Mauris nec arcu vel tellus aliquam congue. Mauris fermentum sem ut tortor ultricies dictum. Praesent at porttitor mauris. Curabitur pulvinar suscipit tortor venenatis faucibus. Vivamus a lectus mi. Nullam ultricies nisl id ante volutpat feugiat. Ut viverra elit et nisi pharetra sed hendrerit lacus placerat. Sed laoreet ante vitae justo rhoncus nec posuere diam tincidunt. Duis interdum quam id risus sollicitudin vel commodo lorem ultrices. Maecenas ut erat scelerisque tortor vestibulum pretium. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Sed iaculis dui vitae nisl lobortis eu vehicula lacus dignissim.\r\n<blockquote>Curabitur lorem mauris, dictum et tempus eu, feugiat pharetra sapien. Donec vel turpis orci, ut congue tortor. Aenean sed interdum quam. Vivamus laoreet posuere pharetra. Donec quis nisi nec nulla malesuada scelerisque vitae ac turpis. In eget elit eu risus gravida adipiscing.</blockquote>\r\nMorbi vel ipsum vel augue mattis ultricies non et mauris. Phasellus rhoncus euismod massa. Etiam euismod tristique venenatis. In lobortis tellus non augue tempor eleifend. Vestibulum ut dignissim tellus. Morbi ornare, enim a semper venenatis, odio justo luctus enim, consectetur sodales justo est id leo. Mauris risus augue, fermentum sit amet congue sit amet, elementum quis arcu. Duis lacinia nisi vel lorem scelerisque interdum. Maecenas ut erat scelerisque tortor vestibulum pretium. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Sed iaculis dui vitae nisl lobortis eu vehicula lacus dignissim. Quisque vel augue sit amet diam lobortis posuere eu sit amet tortor. Curabitur porta arcu non dui facilisis pharetra. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Pellentesque ultrices hendrerit nisl, eu mollis leo semper dictum. Cras ut magna metus, ac tristique enim. Integer quis orci nisi, sit amet commodo purus. Fusce id justo ac lacus aliquet lobortis. Curabitur lorem mauris, dictum et tempus eu, feugiat pharetra sapien. Donec vel turpis orci, ut congue tortor. Aenean sed interdum quam. Vivamus laoreet posuere pharetra. Donec quis nisi nec nulla malesuada scelerisque vitae ac turpis. In eget elit eu risus gravida adipiscing. Morbi vel ipsum vel augue mattis ultricies non et mauris. Phasellus rhoncus euismod massa. Etiam euismod tristique venenatis. In lobortis tellus non augue tempor eleifend. Vestibulum ut dignissim tellus. Morbi ornare, enim a semper venenatis, odio justo luctus enim, consectetur sodales justo est id leo. Mauris risus augue, fermentum sit amet congue sit amet, elementum quis arcu. Duis lacinia nisi vel lorem scelerisque interdum. Etiam massa mauris, fermentum a congue id, congue sit amet erat. Donec dignissim dictum enim, ac cursus dolor auctor et. Aliquam scelerisque luctus nisl, at accumsan tortor pulvinar tincidunt. Aenean sit amet adipiscing mauris. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Integer interdum sollicitudin condimentum.\r\n<ul>\r\n <li>Quisque vel augue sit amet</li>\r\n <li>Proin vitae augue leo</li>\r\n  <li>Duis interdum quam id risus sollicitudin vel commodo lorem ultrices</li>\r\n    <li>Rhoncus nec posuere diam tincidunt</li>\r\n <li>Donec quis nisi nec nulla malesuada scelerisque vitae ac turpis. In eget elit eu risus gravida adipiscing.</li>\r\n</ul>\r\nEtiam massa mauris, fermentum a congue id, congue sit amet erat. Donec dignissim dictum enim, ac cursus dolor auctor et. Aliquam scelerisque luctus nisl, at accumsan tortor pulvinar tincidunt. Aenean sit amet adipiscing mauris. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Integer interdum sollicitudin condimentum.', ''),
(8, 'Snipers only 5v5', '2014-02-15 23:10:00', 1301, 1, 2, 2, 0, 'Sed commodo, libero ut dignissim imperdiet, lorem nibh bibendum nisi, vel blandit est eros sit amet elit. Mauris nec arcu vel tellus aliquam congue. Mauris fermentum sem ut tortor ultricies dictum. Praesent at porttitor mauris. Curabitur pulvinar suscipit tortor venenatis faucibus. Vivamus a lectus mi. Nullam ultricies nisl id ante volutpat feugiat. Ut viverra elit et nisi pharetra sed hendrerit lacus placerat. Sed laoreet ante vitae justo rhoncus nec posuere diam tincidunt. Duis interdum quam id risus sollicitudin vel commodo lorem ultrices. Maecenas ut erat scelerisque tortor vestibulum pretium. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Sed iaculis dui vitae nisl lobortis eu vehicula lacus dignissim.\r\n<blockquote>Curabitur lorem mauris, dictum et tempus eu, feugiat pharetra sapien. Donec vel turpis orci, ut congue tortor. Aenean sed interdum quam. Vivamus laoreet posuere pharetra. Donec quis nisi nec nulla malesuada scelerisque vitae ac turpis. In eget elit eu risus gravida adipiscing.</blockquote>\r\nMorbi vel ipsum vel augue mattis ultricies non et mauris. Phasellus rhoncus euismod massa. Etiam euismod tristique venenatis. In lobortis tellus non augue tempor eleifend. Vestibulum ut dignissim tellus. Morbi ornare, enim a semper venenatis, odio justo luctus enim, consectetur sodales justo est id leo. Mauris risus augue, fermentum sit amet congue sit amet, elementum quis arcu. Duis lacinia nisi vel lorem scelerisque interdum. Maecenas ut erat scelerisque tortor vestibulum pretium. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Sed iaculis dui vitae nisl lobortis eu vehicula lacus dignissim. Quisque vel augue sit amet diam lobortis posuere eu sit amet tortor. Curabitur porta arcu non dui facilisis pharetra. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Pellentesque ultrices hendrerit nisl, eu mollis leo semper dictum. Cras ut magna metus, ac tristique enim. Integer quis orci nisi, sit amet commodo purus. Fusce id justo ac lacus aliquet lobortis. Curabitur lorem mauris, dictum et tempus eu, feugiat pharetra sapien. Donec vel turpis orci, ut congue tortor. Aenean sed interdum quam. Vivamus laoreet posuere pharetra. Donec quis nisi nec nulla malesuada scelerisque vitae ac turpis. In eget elit eu risus gravida adipiscing. Morbi vel ipsum vel augue mattis ultricies non et mauris. Phasellus rhoncus euismod massa. Etiam euismod tristique venenatis. In lobortis tellus non augue tempor eleifend. Vestibulum ut dignissim tellus. Morbi ornare, enim a semper venenatis, odio justo luctus enim, consectetur sodales justo est id leo. Mauris risus augue, fermentum sit amet congue sit amet, elementum quis arcu. Duis lacinia nisi vel lorem scelerisque interdum. Etiam massa mauris, fermentum a congue id, congue sit amet erat. Donec dignissim dictum enim, ac cursus dolor auctor et. Aliquam scelerisque luctus nisl, at accumsan tortor pulvinar tincidunt. Aenean sit amet adipiscing mauris. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Integer interdum sollicitudin condimentum.\r\n<ul>\r\n <li>Quisque vel augue sit amet</li>\r\n <li>Proin vitae augue leo</li>\r\n  <li>Duis interdum quam id risus sollicitudin vel commodo lorem ultrices</li>\r\n    <li>Rhoncus nec posuere diam tincidunt</li>\r\n <li>Donec quis nisi nec nulla malesuada scelerisque vitae ac turpis. In eget elit eu risus gravida adipiscing.</li>\r\n</ul>\r\nEtiam massa mauris, fermentum a congue id, congue sit amet erat. Donec dignissim dictum enim, ac cursus dolor auctor et. Aliquam scelerisque luctus nisl, at accumsan tortor pulvinar tincidunt. Aenean sit amet adipiscing mauris. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Integer interdum sollicitudin condimentum.', NULL);");

$wpdb->get_results(
"INSERT INTO `".$wpdb->prefix."cw_rounds` (`id`, `match_id`, `group_n`, `map_id`, `tickets1`, `tickets2`) VALUES
(1, 2, 1390925577, 11, 0, 0),
(2, 2, 1390925577, 11, 0, 0),
(3, 1, 1390951869, 13, 0, 0),
(4, 1, 1390951869, 13, 0, 0),
(5, 1, 1390951869, 13, 0, 0),
(6, 1, 1390951869, 13, 0, 0),
(7, 1, 1390951870, 15, 0, 0),
(8, 1, 1390951870, 15, 0, 0),
(9, 1, 1390951870, 15, 0, 0),
(10, 1, 1390951870, 15, 0, 0),
(11, 1, 1390951871, 16, 0, 0),
(12, 1, 1390951871, 16, 0, 0),
(13, 1, 1390951871, 16, 0, 0),
(14, 1, 1390951871, 16, 0, 0),
(15, 1, 1390951872, 14, 0, 0),
(16, 1, 1390951872, 14, 0, 0),
(17, 1, 1390951872, 14, 0, 0),
(18, 1, 1390951872, 14, 0, 0),
(19, 2, 1390925577, 11, 0, 0),
(20, 2, 1390925577, 11, 0, 0),
(21, 2, 1391000604, 10, 0, 0),
(22, 2, 1391000604, 10, 0, 0),
(23, 2, 1391000604, 10, 0, 0),
(24, 2, 1391000604, 10, 0, 0),
(25, 2, 1391000605, 9, 0, 0),
(26, 2, 1391000605, 9, 0, 0),
(27, 2, 1391000605, 9, 0, 0),
(28, 2, 1391000605, 9, 0, 0),
(29, 2, 1391000606, 12, 0, 0),
(30, 2, 1391000606, 12, 0, 0),
(31, 2, 1391000606, 12, 0, 0),
(32, 2, 1391000606, 12, 0, 0),
(33, 3, 1391000859, 1, 2, 5),
(34, 3, 1391000859, 1, 5, 3),
(35, 3, 1391000860, 4, 3, 3),
(36, 3, 1391000860, 4, 4, 5),
(37, 3, 1391000861, 2, 7, 2),
(38, 3, 1391000861, 2, 2, 8),
(39, 3, 1391000862, 3, 5, 6),
(40, 3, 1391000862, 3, 0, 2),
(41, 4, 1391001322, 6, 8, 2),
(42, 4, 1391001322, 6, 6, 8),
(43, 4, 1391001322, 6, 5, 10),
(44, 4, 1391001323, 8, 9, 5),
(45, 4, 1391001323, 8, 7, 5),
(46, 4, 1391001323, 8, 3, 2),
(47, 4, 1391001324, 7, 8, 6),
(48, 4, 1391001324, 7, 3, 5),
(49, 4, 1391001324, 7, 5, 2),
(50, 4, 1391001325, 5, 12, 5),
(51, 4, 1391001325, 5, 6, 2),
(52, 4, 1391001325, 5, 10, 0),
(53, 5, 1391104501, 11, 0, 0),
(54, 5, 1391104501, 11, 0, 0),
(55, 5, 1391104502, 9, 0, 0),
(56, 5, 1391104502, 9, 0, 0),
(57, 5, 1391104503, 10, 0, 0),
(58, 5, 1391104503, 10, 0, 0),
(59, 5, 1391104504, 10, 0, 0),
(60, 5, 1391104504, 10, 0, 0),
(61, 6, 1391104569, 1, 2, 0),
(62, 6, 1391104569, 1, 3, 0),
(63, 6, 1391104569, 1, 5, 9),
(64, 6, 1391104570, 1, 5, 0),
(65, 6, 1391104570, 1, 2, 1),
(66, 6, 1391104570, 1, 3, 2),
(67, 6, 1391104571, 2, 11, 7),
(68, 6, 1391104571, 2, 2, 9),
(69, 6, 1391104571, 2, 5, 3),
(70, 6, 1391104572, 3, 1, 2),
(71, 6, 1391104572, 3, 0, 5),
(72, 6, 1391104572, 3, 2, 7),
(73, 7, 1391104656, 13, 5, 25),
(74, 7, 1391104656, 13, 47, 0),
(75, 7, 1391104657, 13, 25, 34),
(76, 7, 1391104657, 13, 65, 23),
(77, 7, 1391104658, 13, 34, 25),
(78, 7, 1391104658, 13, 9, 13),
(79, 7, 1391105376, 13, 8, 45),
(80, 7, 1391105376, 13, 7, 6),
(81, 8, 1391105447, 15, 0, 0),
(82, 8, 1391105447, 15, 0, 0),
(83, 8, 1391105447, 15, 0, 0),
(84, 8, 1391105448, 14, 0, 0),
(85, 8, 1391105448, 14, 0, 0),
(86, 8, 1391105448, 14, 0, 0),
(87, 8, 1391105449, 15, 0, 0),
(88, 8, 1391105449, 15, 0, 0),
(89, 8, 1391105449, 15, 0, 0),
(90, 8, 1391105450, 14, 0, 0),
(91, 8, 1391105450, 14, 0, 0),
(92, 8, 1391105450, 14, 0, 0);");

$wpdb->get_results(
"INSERT INTO `".$wpdb->prefix."cw_teams` (`id`, `title`, `logo`, `home_team`) VALUES
(1, 'Fnatic', 1248, 0),
(2, 'SK Gaming', 1249, 0),
(3, 'Aliance', 1250, 0),
(4, 'mYm', 1251, 0),
(5, 'Game Addict', 1254, 1);");


die();
}
?>