<?php


class WP_ClanWars_Widget extends WP_Widget {

	var $default_settings = array();
	var $newer_than_options = array();

	function __construct()
	{
		$widget_ops = array('classname' => 'widget_clanwars', 'description' => __('ClanWars widget', 'addict'));
		parent::__construct('clanwars', __('ClanWars', 'addict'), $widget_ops);

		$this->default_settings = array('title' => __('ClanWars', 'addict'),
				'show_limit' => 10,
				'hide_title' => false,
				'hide_older_than' => '1m',
                'visible_games' => array());

		$this->newer_than_options = array(
			'all' => array('title' => __('Show all', 'addict'), 'value' => 0),
			'1w' => array('title' => __('1 week', 'addict'), 'value' => 60*60*24*7),
			'2w' => array('title' => __('2 weeks', 'addict'), 'value' => 60*60*24*14),
			'3w' => array('title' => __('3 weeks', 'addict'), 'value' => 60*60*24*21),
			'1m' => array('title' => __('1 month', 'addict'), 'value' => 60*60*24*30),
			'2m' => array('title' => __('2 months', 'addict'), 'value' => 60*60*24*30*2),
			'3m' => array('title' => __('3 months', 'addict'), 'value' => 60*60*24*30*3),
			'6m' => array('title' => __('6 months', 'addict'), 'value' => 60*60*24*30*6),
			'1y' => array('title' => __('1 year', 'addict'), 'value' => 60*60*24*30*12)
		);

		wp_register_script('jquery-cookie', WP_CLANWARS_URL . '/js/jquery.cookie.pack.js', array('jquery'), WP_CLANWARS_VERSION);
		wp_register_script('wp-cw-tabs', WP_CLANWARS_URL . '/js/tabs.js', array('jquery', 'jquery-cookie'), WP_CLANWARS_VERSION);

		wp_enqueue_script('wp-cw-tabs');
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

		$from_date = 0;
		if(isset($this->newer_than_options[$instance['hide_older_than']])) {
			$age = (int)$this->newer_than_options[$instance['hide_older_than']]['value'];
			// 0 means show all matches
			if($age > 0)
				$from_date = $now - $age;
		}

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
		<?php if ( $title && !$instance['hide_title'] )
			echo $before_title . $title . $after_title; ?>

<ul class="clanwar-list">

	<li>
		<ul class="tabs">
		<?php
		$obj = new stdClass();
		$obj->id = 0;
		$obj->title = __('All', 'addict');
		$obj->abbr = __('All', 'addict');
		$obj->icon = 0;

		array_unshift($games, $obj);

		for($i = 0; $i < sizeof($games); $i++) :
			$game = $games[$i];
			$link = ($game->id == 0) ? 'all' : 'game-' . $game->id;
		?>
			<li<?php if($i == 0) echo ' class="selected"'; ?>><a href="#<?php echo $link; ?>" title="<?php echo esc_attr($game->title); ?>"><?php echo esc_html($game->abbr); ?></a><div class="clear"></div></li>
		<?php endfor; ?>
		</ul>
		<div class="clear"></div>
	</li>

	<?php foreach($matches as $i => $match) :
			$is_upcoming = false;
			$t1 = $match->team1_tickets;
			$t2 = $match->team2_tickets;
			$wld_class = $t1 == $t2 ? 'draw' : ($t1 > $t2 ? 'win' : 'loose');
			$date = mysql2date(get_option('date_format') . ', ' . get_option('time_format'), $match->date);
			$timestamp = mysql2date('U', $match->date);
            $table = array(
                'teams' => 'cw_teams',
                'games' => 'cw_games',
            );
            $table = array_map(create_function('$t', 'global $table_prefix; return $table_prefix . $t; '), $table);
			$game_icon = wp_get_attachment_url($match->game_icon);
            $gameid = $match->game_id;
            global $wpdb;
            $team1id = $match->team1;
            $logo1 = $wpdb->get_results('SELECT logo FROM `' . $table['teams'] . '` WHERE `id`= ' .$team1id. ' '  );
            $gameabr = $wpdb->get_results('SELECT abbr FROM `' . $table['games'] . '` WHERE `id`= ' .$gameid. ' '  );
            $thumb1 = $logo1[0]->logo;
            $img_url1 = wp_get_attachment_url( $thumb1,'full'); //get img URL
            $image1 = aq_resize( $img_url1, 25, 25, true ); //resize & crop img

			$is_upcoming = $timestamp > $now;
			$is_playing = ($now > $timestamp && $now < $timestamp + 3600) && ($t1 == 0 && $t2 == 0);
	?>
	<li class="clanwar-item<?php if($i % 2 != 0) echo ' alt'; ?> game-<?php echo $match->game_id; ?>">
		<?php
				$team2_title = esc_html($match->team2_title);

				if($match->post_id != 0)
					$team2_title = '<a href="' . get_permalink($match->post_id) . '" title="' . esc_attr($match->title) . '">';

				echo $team2_title;
			?>
			<div class="wrap">
				<?php if($is_upcoming) : ?>
				<div class="upcoming"><?php _e('Upcoming', 'addict'); ?></div>
				<?php elseif($is_playing) : ?>
				<div class="playing"><?php _e('Playing', 'addict'); ?></div>
				<?php else : ?>
				<div class="scores <?php echo $wld_class; ?>"><?php echo sprintf(__('%d:%d', 'addict'), $t1, $t2); ?></div>
				<?php endif; ?>

				<div class="match-wrap">
					<img src="<?php echo $image1;?>" class="clan1img">
					<!--<div class="home-team"><?php// echo esc_html($match->team1_title); ?></div>-->
					<span class="vs">vs.</span>
					<div class="opponent-team">
					<?php
						$team2_title = esc_html($match->team2_title);


						echo $team2_title;
					?>
					</div>



				<div class="clear"></div>
				</div>
				<div class="date"><?php echo $gameabr[0]->abbr; ?> - <?php echo $date; ?></div>
				<div class="clear"></div>
			</div>
		</a>
	</li>
		<?php endforeach; ?>
</ul>

			<?php echo $after_widget; ?>

		<?php
	}

	function update($new_instance, $old_instance) {
		return $new_instance;
	}

	function form($instance) {
        global $wpClanWars;

		$instance = wp_parse_args((array)$instance, $this->default_settings);

		$show_limit = (int)$instance['show_limit'];
		$title = esc_attr($instance['title']);
        $visible_games = $instance['visible_games'];

        $games = $wpClanWars->get_game('id=all&orderby=title&order=asc');
	?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'addict'); ?></label> <input class="widefat" name="<?php echo $this->get_field_name('title'); ?>" id="<?php echo $this->get_field_id('title'); ?>" value="<?php echo esc_attr($title); ?>" type="text" /></p>

		<p>
			<input class="checkbox" name="<?php echo $this->get_field_name('hide_title'); ?>" id="<?php echo $this->get_field_id('hide_title'); ?>" value="1" type="checkbox" <?php checked($instance['hide_title'], true)?>/> <label for="<?php echo $this->get_field_id('hide_title'); ?>"><?php _e('Hide title', 'addict'); ?></label>
		</p>


        <p><?php _e('Show games:', 'addict'); ?></p>
        <p style="overflow: auto; max-height: 100px; border: 1px solid #dfdfdf; background: #fff;" class="widefat">
            <?php foreach($games as $item) : ?>
            <label for="<?php echo $this->get_field_id('visible_games-' . $item->id); ?>"><input type="checkbox" name="<?php echo $this->get_field_name('visible_games'); ?>[]" id="<?php echo $this->get_field_id('visible_games-' . $item->id); ?>" value="<?php echo esc_attr($item->id); ?>" <?php checked(true, in_array($item->id, $visible_games)); ?>/> <?php echo esc_html($item->title); ?></label><br/>
            <?php endforeach; ?>
        </p>
        <p class="description"><?php _e('Do not check any game if you want to show all games.', 'addict'); ?></p>

		<p><label for="<?php echo $this->get_field_id('show_limit'); ?>"><?php _e('Show matches:', 'addict'); ?></label> <input style="width: 45px;" name="<?php echo $this->get_field_name('show_limit'); ?>" id="<?php echo $this->get_field_id('show_limit'); ?>" value="<?php echo esc_attr($show_limit); ?>" type="text" /></p>
		<p><label for="<?php echo $this->get_field_id('hide_older_than'); ?>"><?php _e('Hide older than', 'addict'); ?></label><br/><select name="<?php echo $this->get_field_name('hide_older_than'); ?>" id="<?php echo $this->get_field_id('hide_older_than'); ?>">
			<?php foreach($this->newer_than_options as $key => $option) : ?>
				<option value="<?php esc_attr_e($key); ?>"<?php selected($key, $instance['hide_older_than']); ?>><?php esc_html_e($option['title']); ?></option>
			<?php endforeach; ?>
		</select></p>
	<?php
	}
}

?>