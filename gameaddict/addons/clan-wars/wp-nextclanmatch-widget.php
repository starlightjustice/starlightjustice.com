<?php


class WP_NextClanMatch_Widget extends WP_Widget {

    var $default_settings = array();
    var $newer_than_options = array();

    function __construct()
    {
        $widget_ops = array('classname' => 'NextClanMatch', 'description' => __('Displays next clan match', 'addict'));
        parent::__construct('NextClanMatch', __('Next Clan Match', 'addict'), $widget_ops);

        $this->default_settings = array('title' => __('Next Clan Match', 'addict'));

    }

    function current_time_fixed( $type, $gmt = 0 ) {
        $t =  ( $gmt ) ? gmdate( 'Y-m-d H:i:s' ) : gmdate( 'Y-m-d H:i:s', ( time() + ( get_option( 'gmt_offset' ) * 3600 ) ) );
        switch ( $type ) {
            case 'mysql':
                return $t;
                break;
            case 'timestamp':
                return strtotime($t);
                break;
        }
    }

    function widget($args, $instance) {
        global $wpClanWars;
        global $wpdb;
        extract($args);

        $now = $this->current_time_fixed('timestamp');

        $instance = wp_parse_args((array)$instance, $this->default_settings);

        $title = apply_filters('widget_title', empty($instance['title']) ? __('ClanWars', 'addict') : $instance['title']);

        $matches = array();
        $games = array();
        $_games = $wpClanWars->get_game(array(
                'id' => empty($instance['visible_games']) ? 'all' : $instance['visible_games'],
                'orderby' => 'title',
                'order' => 'asc'
            ));



        foreach($_games as $g) {
            $m = $wpClanWars->get_match(array('from_date' => $from_date, 'game_id' => $g->id, 'limit' => $instance['show_limit'], 'order' => 'desc', 'orderby' => 'date', 'sum_tickets' => true));

            if(sizeof($m)) {
                $games[] = $g;
                $matches = array_merge($matches, $m);
            }
        }

        usort($matches, create_function('$a, $b', '
            $t1 = mysql2date("U", $a->date);
            $t2 = mysql2date("U", $b->date);

            if($t1 == $t2) return 0;

            return $t1 > $t2 ? -1 : 1;
            '));

        ?>

        <?php echo $before_widget; ?>
        <div class="wcontent wprojects nextmatch_widget">
        <?php if ( $title && !$instance['hide_title'] )

            echo $before_title . $title . $after_title;

            $table = array(
                'teams' => 'cw_teams',
                'games' => 'cw_games',
                'matches' => 'cw_matches',
            );
            $table = array_map(create_function('$t', 'global $table_prefix; return $table_prefix . $t; '), $table);
            ?>

    <?php foreach($matches as $i => $match) :
            $is_upcoming = false;
            $date = mysql2date(get_option('date_format') . ', ' . get_option('time_format'), $match->date);
            $timestamp = mysql2date('U', $match->date);

            $is_upcoming = $timestamp > $now;
            global $wpdb;

            if($is_upcoming){

            $team1id = $match->team1;
            $team2id = $match->team2;


            $logo1 = $wpdb->get_results('SELECT logo FROM `' . $table['teams'] . '` WHERE `id`= ' .$team1id. ' '  );
            $logo2 = $wpdb->get_results('SELECT logo FROM `' . $table['teams'] . '` WHERE `id`= ' .$team2id. ' '  );


            $team1_title = esc_html($match->team1_title);
            $team2_title = esc_html($match->team2_title);
            $dates[] = array('dates'=>$timestamp, 'id'=>$match->id, 'team1'=> $team1_title, 'team2'=> $team2_title);

             ?>

        <?php }
 endforeach;

        $nearest = $dates[0]['dates'];
        $id = $dates[0]['id'];
        for ($i = 0; $i < count($dates); $i++) {
          $date = $dates[$i]['dates'];
          $id =   $dates[$i]['id'];
          $team1 =   $dates[$i]['team1'];
          $team2 =   $dates[$i]['team2'];
          if (abs($date - $now) < abs($nearest - $now))
            $nearest = $date;
            $nextid = $id;
        }
        $nextmatch = $wpdb->get_results('SELECT * FROM `' . $table['matches'] . '` WHERE `id`= ' .$nextid. ' '  );
        $gameid = $nextmatch[0]->game_id;
        $gametitle = $wpdb->get_results('SELECT abbr FROM `' . $table['games'] . '` WHERE `id`= ' .$gameid. ' '  );
        $thumb1 = $logo1[0]->logo;
        $img_url1 = wp_get_attachment_url( $thumb1,'full'); //get img URL
        $image1 = aq_resize( $img_url1, 140, 102, true, true, true ); //resize & crop img
        $thumb2 = $logo2[0]->logo;
        $img_url2 = wp_get_attachment_url( $thumb2,'full'); //get img URL
        $image2 = aq_resize( $img_url2, 140, 102, true, true, true ); //resize & crop img

        echo '<a href="' . get_permalink($nextmatch[0]->post_id) . '" title="' . esc_attr($nextmatch[0]->title) . '"><div class="nextmatch_wrap">
                <img src="'.$image1.'" class="clan1">
                <img src="'.$image2.'" class="clan2">
                <div class="clear"></div>
                <div class="nm-clans navbar-inverse">
                    <div class="r-home-team">
                        <span>'.$team1.'</span>
                    </div>
                    <div class="versus">'.__("VS", "addict").'</div>
                    <div class="r-opponent-team">
                        <span>'.$team2.'</span>
                    </div>
                    <div class="clear"></div>
                </div>
                <div class="nm-date">
                    '.$gametitle[0]->abbr.' - '.date('F d, Y,', $nearest).' <span>'.date('g:ia', $nearest).'</span>
                </div>

            </div></a>';

        ?>
</div>
            <?php echo $after_widget; ?>

        <?php
    }

    function update($new_instance, $old_instance) {
        return $new_instance;
    }

    function form($instance) {
        global $wpClanWars;
        $instance = wp_parse_args((array)$instance, $this->default_settings);
        $title = esc_attr($instance['title']);
 ?>
        <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'addict'); ?></label> <input class="widefat" name="<?php echo $this->get_field_name('title'); ?>" id="<?php echo $this->get_field_id('title'); ?>" value="<?php echo esc_attr($title); ?>" type="text" /></p>

    <?php
    }
}

?>