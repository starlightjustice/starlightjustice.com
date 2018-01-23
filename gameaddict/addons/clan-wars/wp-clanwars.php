<?php
global $wpClanWars;

define('WP_CLANWARS_VERSION', '1.5.5');

define('WP_CLANWARS_CATEGORY', '_wp_clanwars_category');
define('WP_CLANWARS_DEFAULTCSS', '_wp_clanwars_defaultcss');
define('WP_CLANWARS_ACL', '_wp_clanwars_acl');
define('WP_CLANWARS_URL', get_template_directory_uri().'/addons/clan-wars');
define('WP_CLANWARS_IMPORTDIR', 'import');
define('WP_CLANWARS_IMPORTPATH', dirname(__FILE__) . '/' . WP_CLANWARS_IMPORTDIR);
define('WP_CLANWARS_IMPORTURL', WP_CLANWARS_URL . '/' . WP_CLANWARS_IMPORTDIR);

require (dirname(__FILE__) . '/wp-clanwars-widget.php');
require (dirname(__FILE__) . '/wp-nextclanmatch-widget.php');

class WP_ClanWars {

	var $tables = array(
		'teams' => 'cw_teams',
		'games' => 'cw_games',
		'maps' => 'cw_maps',
		'matches' => 'cw_matches',
		'rounds' => 'cw_rounds'
	);

	var $match_status = array();
	var $acl_keys = array();
	var $page_hooks = array();
	var $page_notices = array();

	const ErrorOK = 0;
	const ErrorDatabase = -199;
	const ErrorUploadMaxFileSize = -208;
	const ErrorUploadHTMLMaxFileSize = -209;
	const ErrorUploadPartially = -210;
	const ErrorUploadNoFile = -211;
	const ErrorUploadMissingTemp = -212;
	const ErrorUploadDiskWrite = -213;
	const ErrorUploadStoppedByExt = -214;
	const ErrorUploadFileTypeNotAllowed = -215;

	function __construct() {
		$this->tables = array_map(create_function('$t', 'global $table_prefix; return $table_prefix . $t; '), $this->tables);

		load_plugin_textdomain('addict', WP_PLUGIN_DIR  . '/' . dirname(plugin_basename(__FILE__)) . '/langs/', //2.5 Compatibility
							   dirname(plugin_basename(__FILE__)) . '/langs/'); //2.6+, Works with custom wp-content dirs.

		add_action('widgets_init', array($this, 'on_widgets_init'));
	}


	/**
	 * WP init hook
	 *
	 * Plugin initialization method used to load textdomain,
	 * register hooks, scripts and styles.
	 *
	 * @return void
	 */

	function on_init()
	{
	    global $wpdb;

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        $dbstruct1 = '';
        $dbstruct2 = '';
        $dbstruct3 = '';
        $dbstruct4 = '';
        $dbstruct5 = '';

        $dbstruct1 .= "CREATE TABLE IF NOT EXISTS `{$this->tables['games']}` (
                      `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                      `title` varchar(200) NOT NULL,
                      `abbr` varchar(20) DEFAULT NULL,
                      `icon` bigint(20) unsigned DEFAULT NULL,
                      PRIMARY KEY (`id`),
                      KEY `icon` (`icon`),
                      KEY `title` (`title`),
                      KEY `abbr` (`abbr`)
                    );";

        dbDelta($dbstruct1);

        $dbstruct2 .= "CREATE TABLE IF NOT EXISTS `{$this->tables['maps']}` (
                      `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                      `game_id` int(10) unsigned NOT NULL,
                      `title` varchar(200) NOT NULL,
                      `screenshot` bigint(20) unsigned DEFAULT NULL,
                      PRIMARY KEY (`id`),
                      KEY `game_id` (`game_id`,`screenshot`)
                    );";

        dbDelta($dbstruct2);

        $dbstruct3 .= "CREATE TABLE IF NOT EXISTS `{$this->tables['matches']}` (
                      `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                      `title` varchar(200) DEFAULT NULL,
                      `date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
                      `post_id` bigint(20) unsigned DEFAULT NULL,
                      `team1` int(11) unsigned NOT NULL,
                      `team2` int(11) unsigned NOT NULL,
                      `game_id` int(11) unsigned NOT NULL,
                      `match_status` tinyint(1) DEFAULT '0',
                      `description` text NOT NULL,
                      `external_url` varchar(200) DEFAULT NULL,
                      PRIMARY KEY (`id`),
                      KEY `post_id` (`post_id`),
                      KEY `post_title` (`title`),
                      KEY `game_id` (`game_id`),
                      KEY `team1` (`team1`),
                      KEY `team2` (`team2`),
                      KEY `match_status` (`match_status`),
                      KEY `date` (`date`)
                    );";

        dbDelta($dbstruct3);

        $dbstruct4 .= "CREATE TABLE IF NOT EXISTS `{$this->tables['rounds']}` (
                      `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                      `match_id` int(10) unsigned NOT NULL,
                      `group_n` int(11) NOT NULL,
                      `map_id` int(10) unsigned NOT NULL,
                      `tickets1` int(11) NOT NULL,
                      `tickets2` int(11) NOT NULL,
                      PRIMARY KEY (`id`),
                      KEY `match_id` (`match_id`),
                      KEY `group_n` (`group_n`),
                      KEY `map_id` (`map_id`)
                    );";

        dbDelta($dbstruct4);

        $dbstruct5 .= "CREATE TABLE IF NOT EXISTS `{$this->tables['teams']}` (
                      `id` int(11) NOT NULL AUTO_INCREMENT,
                      `title` varchar(200) NOT NULL,
                      `logo` bigint(20) unsigned DEFAULT NULL,
                      `home_team` tinyint(1) DEFAULT '0',
                      PRIMARY KEY (`id`),
                      KEY `home_team` (`home_team`),
                      KEY `title` (`title`)
                    );";

        dbDelta($dbstruct5);

        add_option(WP_CLANWARS_CATEGORY, -1);
        add_option(WP_CLANWARS_DEFAULTCSS, true);
        add_option(WP_CLANWARS_ACL, array());

        // update database


		$this->acl_keys = array(
			'manage_matches' => __('Manage matches', 'addict'),
			'manage_games' => __('Manage games', 'addict'),
			'manage_teams' => __('Manage teams', 'addict')
		);

		$this->match_status = array(
			__('PCW', 'addict'),
			__('Official', 'addict')
		);



		add_action('admin_menu', array($this, 'on_admin_menu'));
		add_action('template_redirect', array($this, 'on_template_redirect'));
		add_action('wp_footer', array($this, 'on_wp_footer'));

		add_action('admin_post_wp-clanwars-deleteteams', array($this, 'on_admin_post_deleteteams'));
		add_action('admin_post_wp-clanwars-sethometeam', array($this, 'on_admin_post_sethometeam'));
		add_action('admin_post_wp-clanwars-gamesop', array($this, 'on_admin_post_gamesop'));
		add_action('admin_post_wp-clanwars-deletemaps', array($this, 'on_admin_post_deletemaps'));
		add_action('admin_post_wp-clanwars-deletematches', array($this, 'on_admin_post_deletematches'));

		add_action('admin_post_wp-clanwars-settings', array($this, 'on_admin_post_settings'));
		add_action('admin_post_wp-clanwars-acl', array($this, 'on_admin_post_acl'));
		add_action('admin_post_wp-clanwars-deleteacl', array($this, 'on_admin_post_deleteacl'));
		add_action('admin_post_wp-clanwars-import', array($this, 'on_admin_post_import'));

		add_action('wp_ajax_get_maps', array($this, 'on_ajax_get_maps'));
		add_shortcode('wp-clanwars', array($this, 'on_shortcode'));

		$this->register_cssjs();
	}

	/**
	 * WP admin_menu hook
	 *
	 * Page, Assets registration, load-* action hooks
	 *
	 * @return void
	 */

	function on_admin_menu()
	{
		global $current_user;

		$acl_table = array(
			'manage_matches' => 'manage_options',
			'manage_games' => 'manage_options',
			'manage_teams' => 'manage_options'
		);

		$keys = array_keys($acl_table);
		$user_role = $current_user->roles[0];

		for($i = 0; $i < sizeof($keys); $i++) {
			if($this->acl_user_can($keys[$i]))
				$acl_table[$keys[$i]] = $user_role;
		}

		$top = add_menu_page(__('ClanWars', 'addict'), __('ClanWars', 'addict'), 'subscriber', __FILE__, null, WP_CLANWARS_URL . '/images/plugin-icon.png');

		$this->page_hooks['matches'] = add_submenu_page(__FILE__, __('Matches', 'addict'), __('Matches', 'addict'), $acl_table['manage_matches'], 'wp-clanwars-matches', array($this, 'on_manage_matches'));
		$this->page_hooks['teams'] = add_submenu_page(__FILE__, __('Teams', 'addict'), __('Teams', 'addict'), $acl_table['manage_teams'], 'wp-clanwars-teams', array($this, 'on_manage_teams'));
		$this->page_hooks['games'] = add_submenu_page(__FILE__, __('Games', 'addict'), __('Games', 'addict'), $acl_table['manage_games'], 'wp-clanwars-games', array($this, 'on_manage_games'));
		$this->page_hooks['import'] = add_submenu_page(__FILE__, __('Import', 'addict'), __('Import', 'addict'), 'manage_options', 'wp-clanwars-import', array($this, 'on_import'));
		$this->page_hooks['settings'] = add_submenu_page(__FILE__, __('Settings', 'addict'), __('Settings', 'addict'), 'manage_options', 'wp-clanwars-settings', array($this, 'on_settings'));


		add_action('load-' . $this->page_hooks['matches'], array($this, 'on_load_manage_matches'));
		add_action('load-' . $this->page_hooks['teams'], array($this, 'on_load_manage_teams'));
		add_action('load-' . $this->page_hooks['games'], array($this, 'on_load_manage_games'));

		foreach($this->page_hooks as $page_hook)
			add_action('load-' . $page_hook, array($this, 'on_load_any'));
	}

	function register_cssjs()
	{
		wp_register_script('wp-cw-matches', WP_CLANWARS_URL . '/js/matches.js', array('jquery'), WP_CLANWARS_VERSION);
		wp_register_script('wp-cw-admin', WP_CLANWARS_URL . '/js/admin.js', array('jquery'), WP_CLANWARS_VERSION);

		wp_register_style('wp-cw-admin', WP_CLANWARS_URL . '/css/admin.css', array(), WP_CLANWARS_VERSION);

		wp_register_script('jquery-tipsy', WP_CLANWARS_URL . '/js/tipsy/jquery.tipsy.js', array('jquery'), '0.1.7');
		wp_register_style('jquery-tipsy', WP_CLANWARS_URL . '/js/tipsy/tipsy.css', array(), '0.1.7');

		wp_register_script('wp-cw-public', WP_CLANWARS_URL . '/js/public.js', array('jquery-tipsy'), WP_CLANWARS_VERSION);

		wp_register_style('wp-cw-sitecss', WP_CLANWARS_URL . '/css/site.css', array(), WP_CLANWARS_VERSION);
		wp_register_style('wp-cw-widgetcss', WP_CLANWARS_URL . '/css/widget.css', array(), WP_CLANWARS_VERSION);
	}

	function acl_user_can($action, $value = false, $user_id = false)
	{
		global $user_ID;

		$acl = $this->acl_get();
		$is_super = false;
		$caps = array(
			'games' => array(),
			'permissions' => array_fill_keys(array_keys($this->acl_keys), false)
		);

		if(empty($user_id))
			$user_id = $user_ID;

		if(!empty($acl) && isset($acl[$user_id]))
			$caps = $acl[$user_id];

		$user = new WP_User($user_id);
		if(!empty($user))
			$is_super = $user->has_cap('manage_options');

		if($is_super) {
			$caps['games'] = array('all');
			array_walk($caps['permissions'], create_function('&$v, &$k', '$v = true;'));
		}

		switch($action)
		{
			case 'which_games':

				$where = array_search(0, $caps['games']);

				if($where === false)
					return $caps['games'];

				return 'all';

			break;

			case 'manage_game':

				if($value == 'all')
					$value = 0;

				$ret = array_search($value, $caps['games']) !== false;

				if(!$ret) {
					$ret = array_search(0, $caps['games']) !== false;
				}

				return $ret;

			break;
		}

		return isset($caps['permissions'][$action]) && $caps['permissions'][$action];
	}

	function acl_get() {
		$acl = get_option(WP_CLANWARS_ACL);

		if(!is_array($acl))
			$acl = array();

		return $acl;
	}

	function acl_update($user_id, $data) {

		$acl = $this->acl_get();

		$acl[$user_id] =  array(
			'games' => array(0),
			'permissions' => array('manage_matches')
		);

		$default_perms = array(
			'manage_matches' => false,
			'manage_teams' => false,
			'manage_games' => false
		);

		$acl[$user_id]['games'] = isset($data['games']) ? array_unique(array_values($data['games'])) : array(0);
		$acl[$user_id]['permissions'] = isset($data['permissions']) ? $this->extract_args($data['permissions'], $default_perms) : $default_perms;

		update_option(WP_CLANWARS_ACL, $acl);

		return true;
	}

	function acl_delete($user_id) {

		$acl = $this->acl_get();

		if(isset($acl[$user_id])) {
			unset($acl[$user_id]);
			update_option(WP_CLANWARS_ACL, $acl);

			return true;
		}

		return false;
	}

	function on_template_redirect() {
		wp_enqueue_script('wp-cw-public');
		wp_enqueue_style('jquery-tipsy');

	}

	function on_wp_footer() {
		if(get_option(WP_CLANWARS_DEFAULTCSS)) {
			wp_enqueue_style('wp-cw-sitecss');
			wp_enqueue_style('wp-cw-widgetcss');
		}
	}

	function on_load_any() {
		wp_enqueue_style('wp-cw-admin');

		wp_enqueue_script('wp-cw-admin');
		wp_localize_script('wp-cw-admin',
				'wpCWAdminL10n',
				array(
					'confirmDeleteMap' => __('Are you sure you want to delete this map?', 'addict'),
					'confirmDeleteGame' => __('Are you sure you want to delete this game?', 'addict'),
					'confirmDeleteTeam' => __('Are you sure you want to delete this team?', 'addict'),
					'confirmDeleteMatch' => __('Are you sure you want to delete this match?', 'addict')
				)
			);
	}

	function on_widgets_init()
	{
        register_widget('WP_NextClanMatch_Widget');
		register_widget('WP_ClanWars_Widget');
        return;

	}





	function html_notice_helper($message, $type = 'updated', $echo = true) {

		$text = '<div class="' . $type . ' fade"><p>' . $message . '</p></div>';

		if($echo) echo $text;

		return $text;
	}

	function print_table_header($columns, $id = true)
	{
		foreach ( $columns as $column_key => $column_display_name ) {
				$class = ' class="manage-column';

				$class .= " column-$column_key";

				if ( 'cb' == $column_key )
					$class .= ' check-column';
				elseif ( in_array($column_key, array('posts', 'comments', 'links')) )
					$class .= ' num';

				$class .= '"';
		?>
			<th scope="col" <?php echo $id ? "id=\"$column_key\"" : ""; echo $class; ?>><?php echo $column_display_name; ?></th>
		<?php }
	}

	function add_notice($message, $type = 'updated') {

		if(empty($type)) $type = 'updated';

		if(!isset($this->page_notices[$type])) {
			$this->page_notices[$type] = array();
		}

		$this->page_notices[$type][] = $message;
	}

	function print_notices() {
		foreach($this->page_notices as $type => $e) {
			foreach($e as $msg) {
				$this->html_notice_helper($msg, $type, true);
			}
		}
	}

	/**
	 * Image uploading handling, used internally by plugin
	 *
	 * @param string $name $_FILES array key for a file which should be uploaded
	 *
	 * @return void
	 */

	function handle_upload($name)
	{
		$mimes = apply_filters('upload_mimes',
				array('jpg|jpeg|jpe' => 'image/jpeg',
					  'gif' => 'image/gif',
					  'png' => 'image/png'));

		$upload = isset($_FILES[$name]) ? $_FILES[$name] : false;
		$upload_errors = array(self::ErrorOK,
							   self::ErrorUploadMaxFileSize,
							   self::ErrorUploadHTMLMaxFileSize,
							   self::ErrorUploadPartially,
							   self::ErrorUploadNoFile,
							   self::ErrorOK,
							   self::ErrorUploadMissingTemp,
							   self::ErrorUploadDiskWrite,
							   self::ErrorUploadStoppedByExt);

		if(!empty($upload))
		{
			if($upload['error'] > 0)
				return $upload_errors[$upload['error']];

			extract(wp_check_filetype($upload['name'], $mimes));

			if(!$type || !$ext)
			{
				return self::ErrorUploadFileTypeNotAllowed;
			}
			else
			{
				$file_data = wp_handle_upload($upload,
					array('test_type' => false,
						  'test_form' => false,
						  'upload_error_handler' =>
						   create_function('&$file, $message',
										   '$code = $file["error"];
										   $errors = array(' . implode(', ', $upload_errors) . ');
										   if(isset($errors[$code]))
											  return $errors[$code];
										   return $errors[0];')));

				if(!empty($file_data) && is_array($file_data))
				{
					$file_data['type'] = $type;
					if(!isset($file_data['error']))
					{
						$fileinfo = pathinfo($file_data['file']);
						$attach_title = basename($fileinfo['basename'], '.' . $fileinfo['extension']);
						$attach_id = wp_insert_attachment(array('guid' => $file_data['url'],
																'post_title' => $attach_title,
																'post_content' => '',
																'post_status' => 'publish',
																'post_mime_type' => $file_data['type']),
															$file_data['file']);

						$metadata = wp_generate_attachment_metadata($attach_id, $file_data['file']);

						if(!empty($metadata))
							wp_update_attachment_metadata($attach_id, $metadata);

						if(!empty($attach_id) && is_int($attach_id))
							return $attach_id;
					} else {
						return $upload_errors[$file_data['error']];
					}
				}
			}
		}

		return self::ErrorOK;
	}

	/**
	 * Parse arguments and return a list of values with keys from defaults
	 *
	 * @param array|string $args Input values
	 * @param array $defaults Array of default values
	 * @return array Merged array. Same behaviour as wp_parse_args except it generates array which only consists of keys from $defaults array
	 */

	function extract_args($args, $defaults) {
		$rslt = array();

		$options = wp_parse_args($args, $defaults);

		if(is_array($defaults))
			foreach(array_keys($defaults) as $key)
				$rslt[$key] = $options[$key];

		return $rslt;
	}




	function get_team($p, $count = false)
	{
		global $wpdb;

		extract($this->extract_args($p, array(
					'id' => false,
					'title' => false,
					'limit' => 0,
					'offset' => 0,
					'orderby' => 'id',
					'order' => 'ASC')));

		$where_query = '';
		$limit_query = '';
		$order_query = '';

		$order = strtolower($order);
		if($order != 'asc' && $order != 'desc')
			$order = 'asc';

		$order_query = 'ORDER BY `' . $orderby . '` ' . $order;

		if($id != 'all' && $id !== false) {

			if(!is_array($id))
				$id = array($id);

			$id = array_map('intval', $id);
			$where_query[] = 'id IN (' . implode(', ', $id) . ')';
		}

		if($title !== false) {
			$where_query[] = $wpdb->prepare('title=%s', $title);
		}

		if($limit > 0) {
			$limit_query = $wpdb->prepare('LIMIT %d, %d', $offset, $limit);
		}


		if(!empty($where_query))
			$where_query = 'WHERE ' . implode(' AND ', $where_query);

		if($count) {

			$rslt = $wpdb->get_row('SELECT COUNT(id) AS m_count FROM `' . $this->tables['teams'] . '` ' . $where_query);

			$ret = array('total_items' => 0, 'total_pages' => 1);

			$ret['total_items'] = $rslt->m_count;

			if($limit > 0)
				$ret['total_pages'] = ceil($ret['total_items'] / $limit);

			return $ret;
		}

		$rslt = $wpdb->get_results('SELECT * FROM `' . $this->tables['teams'] . '` ' . implode(' ', array($where_query, $order_query, $limit_query)));

		return $rslt;
	}

	function add_team($p)
	{
		global $wpdb;

		$data = $this->extract_args($p, array(
					'title' => '',
					'logo' => 0,
					'home_team' => 0));

		if($wpdb->insert($this->tables['teams'], $data, array('%s', '%d', '%s', '%d')))
		{
			$insert_id = $wpdb->insert_id;

			if($home_team)
				$this->set_hometeam($insert_id);

			return $insert_id;
		}

		return false;
	}

	function set_hometeam($id) {
		global $wpdb;

		$wpdb->query($wpdb->prepare('UPDATE `' . $this->tables['teams'] . '` SET home_team=0'));
		return $wpdb->update($this->tables['teams'], array('home_team' => 1), array('id' => $id), array('%d'), array('%d'));
	}

	function update_team($id, $p)
	{
		global $wpdb;

		$fields = array('title' => '%s', 'home_team' => '%d', 'logo' => '%d');

		$data = wp_parse_args($p, array());

		$update_data = array();
		$update_mask = array();

		foreach($fields as $fld => $mask) {
			if(isset($data[$fld])) {
				$update_data[$fld] = $data[$fld];
				$update_mask[] = $mask;
			}
		}

		$result = $wpdb->update($this->tables['teams'], $update_data, array('id' => $id), $update_mask, array('%d'));

		if(isset($update_data['home_team'])) {
			$this->set_hometeam($id);
		}

		return $result;
	}

	function delete_team($id)
	{
		global $wpdb;

		if(!is_array($id))
			$id = array($id);

		$id = array_map('intval', $id);

		// delete matches belongs to this team
		$this->delete_match_by_team($id);

		return $wpdb->query('DELETE FROM `' . $this->tables['teams'] . '` WHERE id IN(' . implode(',', $id) . ')');
	}

	function on_admin_post_deleteteams()
	{
		if(!$this->acl_user_can('manage_teams'))
			wp_die( __('Cheatin&#8217; uh?', 'addict') );

		check_admin_referer('wp-clanwars-deleteteams');

		$referer = remove_query_arg(array('add', 'update'), $_REQUEST['_wp_http_referer']);

		if($_REQUEST['do_action'] == 'delete' || $_REQUEST['do_action2'] == 'delete') {
			extract($this->extract_args($_REQUEST, array('delete' => array())));

			$error = $this->delete_team($delete);
			$referer = add_query_arg('delete', $error, $referer);
		}

		wp_redirect($referer);
	}

	function on_admin_post_sethometeam()
	{
		if(!$this->acl_user_can('manage_teams'))
			wp_die( __('Cheatin&#8217; uh?', 'addict') );

		check_admin_referer('wp-clanwars-sethometeam');

		$referer = $_REQUEST['_wp_http_referer'];

		extract($this->extract_args($_REQUEST, array('id' => array())));

		$error = $this->set_hometeam($id);

		wp_redirect($referer);
	}

	function on_add_team()
	{
		return $this->team_editor(__('New Team', 'addict'), 'wp-clanwars-addteam', __('Add Team', 'addict'));
	}

	function on_edit_team()
	{
		$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

		return $this->team_editor(__('Edit Team', 'addict'), 'wp-clanwars-editteam', __('Update Team', 'addict'), $id);
	}

	function team_editor($page_title, $page_action, $page_submit, $team_id = 0)
	{
		$defaults = array('title' => '', 'logo' => 0, 'home_team' => 0, 'action' => '');
		$data = array();

		if($team_id > 0) {
			$t = $this->get_team(array('id' => $team_id));
			if(!empty($t))
				$data = (array)$t[0];
		}

		extract($this->extract_args(stripslashes_deep($_POST), $this->extract_args($data, $defaults)));

		$this->print_notices();
        $attach = wp_get_attachment_image($logo, 'thumbnail');

		?>

			<div class="wrap">
				<h2><?php echo $page_title; ?></h2>

					<form name="team-editor" id="team-editor" method="post" action="<?php echo esc_attr($_SERVER['REQUEST_URI']); ?>" enctype="multipart/form-data">

						<input type="hidden" name="action" value="<?php echo esc_attr($page_action); ?>" />
						<input type="hidden" name="id" value="<?php echo esc_attr($team_id); ?>" />

						<?php wp_nonce_field($page_action); ?>

						<table class="form-table">

						<tr class="form-field form-required">
							<th scope="row" valign="top"><label for="title"><span class="alignleft"><?php _e('Title', 'addict'); ?></span><span class="alignright"><abbr title="<?php _e('required', 'addict'); ?>" class="required">*</abbr></span><br class="clear" /></label></th>
							<td>
								<input name="title" id="title" type="text" class="regular-text" value="<?php echo esc_attr($title); ?>" maxlength="200" autocomplete="off" aria-required="true" />
							</td>
						</tr>
<!-- logo ubaci -->
                        <tr>
                            <th scope="row" valign="top"><label for="logo_file"><?php _e('Logo', 'addict'); ?></label></th>
                            <td>
                                <input type="file" name="logo_file" id="logo_file" />

                                <?php if(!empty($attach)) : ?>
                                <div class="screenshot"><?php echo $attach; ?></div>
                                <div>
                                <label for="delete-image"><input type="checkbox" name="delete_image" id="delete-image" /> <?php _e('Delete Logo', 'addict'); ?></label>
                                </div>
                                <?php endif; ?>
                            </td>
                        </tr>
						</table>

						<p class="submit"><input type="submit" class="button-primary" name="submit" value="<?php echo $page_submit; ?>" /></p>

					</form>

			</div>

		<?php
	}

	function on_load_manage_teams()
	{
		$act = isset($_GET['act']) ? $_GET['act'] : '';
		$id = isset($_GET['id']) ? $_GET['id'] : 0;

		// ACL checks on edit
		if($act == 'edit') {
			$t = $this->get_team(array('id' => $id));

			if($id != 0 && empty($t))
				wp_die( __('Cheatin&#8217; uh?', 'addict') );
		}

		if(sizeof($_POST)) {

			if(isset($_POST['title']) && !empty($_POST['title'])) {

				switch($act) {
					case 'add':
                        $defaults = array('title' => '', 'logo' => 0);
                        $data = $this->extract_args(stripslashes_deep($_POST), $defaults);
                        extract($data);

                        if(!empty($title)) {

                            $data['logo'] = $this->handle_upload('logo_file');

                            if($data['logo'] == self::ErrorUploadNoFile)
                                $data['logo'] = 0;

                            if($data['logo'] >= 0) {

                                if($this->add_team($data)) {
                                    wp_redirect(admin_url('admin.php?page=wp-clanwars-teams&add=1'));
                                    exit();
                                } else
                                    $this->add_notice(__('An error occurred.', 'addict'), 'error');
                            } else
                                $this->add_notice(__('An error occurred.', 'addict'), 'error');


                        } else
                            $this->add_notice(__('Team title is required field.', 'addict'), 'error');

					/*	if($this->add_team(stripslashes_deep($_POST))) {
							wp_redirect(admin_url('admin.php?page=wp-clanwars-teams&add=1'));
							exit();
						} else
							$this->add_notice(__('An error occurred.', 'addict'), 'error'); */
					break;

					case 'edit':

                        $defaults = array('title' => '', 'delete_image' => false);
                        $data = $this->extract_args(stripslashes_deep($_POST), $defaults);
                        extract($data);
                        unset($data['delete_image']);

                        if(!empty($title)) {

                            if(!empty($delete_image))
                                $data['logo'] = 0;

                            $attach_id = $this->handle_upload('logo_file');

                            if($attach_id == self::ErrorUploadNoFile)
                                $attach_id = 0;
                            else if($attach_id > 0)
                                $data['logo'] = $attach_id;

                            if($attach_id >= 0) {

                                if($this->update_team($id, $data) !== false) {
                                    wp_redirect(admin_url('admin.php?page=wp-clanwars-teams&update=1'));
                                    exit();
                                } else
                                    $this->add_notice(__('An error occurred.', 'addict'), 'error');

                            } else
                                $this->add_notice(__('An error occurred.', 'addict'), 'error');

                        } else
                            $this->add_notice(__('Team title is required field.', 'addict'), 'error');




					/*	if($this->update_team($id, stripslashes_deep($_POST)) !== false) {
							wp_redirect(admin_url('admin.php?page=wp-clanwars-teams&update=1'));
							exit();
						} else
							$this->add_notice(__('An error occurred.', 'addict'), 'error'); */
						break;
				}

			} else
				$this->add_notice(__('Team title is required field.', 'addict'), 'error');

		}
	}

	function on_manage_teams()
	{
		$act = isset($_GET['act']) ? $_GET['act'] : '';
		$current_page = isset($_GET['paged']) ? $_GET['paged'] : 1;
		$limit = 10;

		switch($act) {
			case 'add':
				return $this->on_add_team();
				break;
			case 'edit':
				return $this->on_edit_team();
				break;
		}

		$teams = $this->get_team('id=all&order=asc&orderby=title&limit=' . $limit . '&offset=' . ($limit * ($current_page-1)));
		$stat = $this->get_team('id=all&limit=' . $limit, true);

		$page_links = paginate_links( array(
				'base' => add_query_arg('paged', '%#%'),
				'format' => '',
				'prev_text' => __('&laquo;', 'addict'),
				'next_text' => __('&raquo;', 'addict'),
				'total' => $stat['total_pages'],
				'current' => $current_page
		));

		$page_links_text = sprintf( '<span class="displaying-num">' . __( 'Displaying %s&#8211;%s of %s', 'addict') . '</span>%s',
				number_format_i18n( (($current_page - 1) * $limit) + 1 ),
				number_format_i18n( min( $current_page * $limit, $stat['total_items'] ) ),
				'<span class="total-type-count">' . number_format_i18n( $stat['total_items'] ) . '</span>',
				$page_links
		);

		$table_columns = array('cb' => '<input type="checkbox" />',
					  'title' => __('Title', 'addict'));


		if(isset($_GET['add'])) {
			$this->add_notice(__('Team is successfully added.', 'addict'), 'updated');
		}

		if(isset($_GET['update'])) {
			$this->add_notice(__('Team is successfully updated.', 'addict'), 'updated');
		}

		if(isset($_GET['delete'])) {
			$deleted = (int)$_GET['delete'];
			$this->add_notice(sprintf(_n('%d Team deleted.', '%d Teams deleted', $deleted, 'addict'), $deleted), 'updated');
		}

		$this->print_notices();

	?>
		<div class="wrap wp-cw-teams">
			<h2><?php _e('Teams', 'addict'); ?> <a href="<?php echo admin_url('admin.php?page=wp-clanwars-teams&act=add'); ?>" class="add-new-h2"><?php _e('Add New', 'addict'); ?></a></h2>

			<div id="poststuff" class="metabox-holder">

				<div id="post-body">
					<div id="post-body-content" class="has-sidebar-content">

					<form id="wp-clanwars-manageform" action="admin-post.php" method="post">
						<?php wp_nonce_field('wp-clanwars-deleteteams'); ?>

						<input type="hidden" name="action" value="wp-clanwars-deleteteams" />

						<div class="tablenav">

							<div class="alignleft actions">
								<select name="do_action">
									<option value="" selected="selected"><?php _e('Bulk Actions', 'addict'); ?></option>
									<option value="delete"><?php _e('Delete', 'addict'); ?></option>
								</select>
								<input type="submit" value="<?php _e('Apply', 'addict'); ?>" name="doaction" id="wp-clanwars-doaction" class="button-secondary action" />
							</div>

							<div class="alignright actions" style="display: none;">
								<label class="screen-reader-text" for="teams-search-input"><?php _e('Search Teams:', 'addict'); ?></label>
								<input id="teams-search-input" name="s" value="<?php echo esc_html($search_title); ?>" type="text" />

								<input id="teams-search-submit" value="<?php _e('Search Teams', 'addict'); ?>" class="button" type="button" />
							</div>

						<br class="clear" />

						</div>

						<div class="clear"></div>

						<table class="widefat fixed" cellspacing="0">
						<thead>
						<tr>
						<?php $this->print_table_header($table_columns); ?>
						</tr>
						</thead>

						<tfoot>
						<tr>
						<?php $this->print_table_header($table_columns, false); ?>
						</tr>
						</tfoot>

						<tbody>

						<?php foreach($teams as $i => $item) : ?>

							<tr class="iedit<?php if($i % 2 == 0) echo ' alternate'; ?>">
								<th scope="row" class="check-column"><input type="checkbox" name="delete[]" value="<?php echo $item->id; ?>" /></th>
								<td class="title column-title">
									<a class="row-title" href="<?php echo admin_url('admin.php?page=wp-clanwars-teams&amp;act=edit&amp;id=' . $item->id); ?>" title="<?php echo sprintf(__('Edit &#8220;%s&#8221; Team', 'addict'), esc_attr($item->title)); ?>"> <?php echo esc_html($item->title); ?> <?php if($item->home_team) _e('(Home Team)', 'addict'); ?></a><br />
									<div class="row-actions">
										<span class="edit"><a href="<?php echo admin_url('admin.php?page=wp-clanwars-teams&amp;act=edit&amp;id=' . $item->id); ?>"><?php _e('Edit', 'addict'); ?></a></span> | <span class="delete">
												<a href="<?php echo wp_nonce_url('admin-post.php?action=wp-clanwars-deleteteams&amp;do_action=delete&amp;delete[]=' . $item->id . '&amp;_wp_http_referer=' . urlencode($_SERVER['REQUEST_URI']), 'wp-clanwars-deleteteams'); ?>"><?php _e('Delete', 'addict'); ?></a></span> | <span class="edit">
												<a href="<?php echo wp_nonce_url('admin-post.php?action=wp-clanwars-sethometeam&amp;do_action=sethometeam&amp;id=' . $item->id . '&amp;_wp_http_referer=' . urlencode($_SERVER['REQUEST_URI']), 'wp-clanwars-sethometeam'); ?>"><?php _e('Set As Home Team', 'addict'); ?></a>
											</span>
									</div>
								</td>

							</tr>

						<?php endforeach; ?>

						</tbody>

						</table>

						<div class="tablenav">

							<div class="tablenav-pages"><?php echo $page_links_text; ?></div>

							<div class="alignleft actions">
							<select name="do_action2">
							<option value="" selected="selected"><?php _e('Bulk Actions', 'addict'); ?></option>
							<option value="delete"><?php _e('Delete', 'addict'); ?></option>
							</select>
							<input type="submit" value="<?php _e('Apply', 'addict'); ?>" name="doaction2" id="wp-clanwars-doaction2" class="button-secondary action" />
							</div>

							<br class="clear" />

						</div>

					</form>

					</div>
				</div>
				<br class="clear"/>

			</div>
		</div>
	<?php
	}

	/*
	 * Games Managment
	 */

	function get_game($p, $count = false)
	{
		global $wpdb;

		extract($this->extract_args($p, array(
			'id' => false,
			'limit' => 0,
			'offset' => 0,
			'orderby' => 'id',
			'order' => 'ASC')));

		$where_query = '';
		$limit_query = '';
		$order_query = '';

		$order = strtolower($order);
		if($order != 'asc' && $order != 'desc')
			$order = 'asc';

		$order_query = 'ORDER BY `' . $orderby . '` ' . $order;

		if($id != 'all' && $id !== false) {

			if(!is_array($id))
				$id = array($id);

			$id = array_map('intval', $id);
			$where_query[] = 'id IN (' . implode(', ', $id) . ')';
		}

		if($limit > 0) {
			$limit_query = $wpdb->prepare('LIMIT %d, %d', $offset, $limit);
		}


		if(!empty($where_query))
			$where_query = 'WHERE ' . implode(' AND ', $where_query);

		if($count) {

			$rslt = $wpdb->get_row('SELECT COUNT(id) AS m_count FROM `' . $this->tables['games'] . '` ' . $where_query);

			$ret = array('total_items' => 0, 'total_pages' => 1);

			$ret['total_items'] = $rslt->m_count;

			if($limit > 0)
				$ret['total_pages'] = ceil($ret['total_items'] / $limit);

			return $ret;
		}

		$rslt = $wpdb->get_results('SELECT * FROM `' . $this->tables['games'] . '` ' . implode(' ', array($where_query, $order_query, $limit_query)));

		return $rslt;
	}

	function add_game($p)
	{
		global $wpdb;

		$data = $this->extract_args($p, array('title' => '', 'abbr' => '', 'icon' => 0));

		if($wpdb->insert($this->tables['games'], $data, array('%s', '%s', '%d')))
		{
			$insert_id = $wpdb->insert_id;

			return $insert_id;
		}

		return false;
	}

	function update_game($id, $p)
	{
		global $wpdb;

		$fields = array('title' => '%s', 'abbr' => '%s', 'icon' => '%d');

		$data = wp_parse_args($p, array());

		$update_data = array();
		$update_mask = array();

		foreach($fields as $fld => $mask) {
			if(isset($data[$fld])) {
				$update_data[$fld] = $data[$fld];
				$update_mask[] = $mask;
			}
		}

		return $wpdb->update($this->tables['games'], $update_data, array('id' => $id), $update_mask, array('%d'));
	}

	function delete_game($id)
	{
		global $wpdb;

		if(!is_array($id))
			$id = array($id);

		$id = array_map('intval', $id);

		$this->delete_map_by_game($id);
		$this->delete_match_by_game($id);

		return $wpdb->query('DELETE FROM `' . $this->tables['games'] . '` WHERE id IN(' . implode(',', $id) . ')');
	}

	function _get_file_content($filename) {
		$content = '';

		$fp = @fopen($filename, 'rb');

		if($fp) {
			while(!feof($fp))
				$content .= fread($fp, 128);

			fclose($fp);

			return $content;
		}

		return null;
	}

	function on_admin_post_gamesop()
	{
		if(!$this->acl_user_can('manage_games'))
			wp_die( __('Cheatin&#8217; uh?', 'addict') );

		check_admin_referer('wp-clanwars-gamesop');

		$referer = remove_query_arg(array('add', 'update', 'export'), $_REQUEST['_wp_http_referer']);

		$args = $this->extract_args($_REQUEST, array('do_action' => '', 'do_action2' => '', 'items' => array()));
		extract($args);

		$action = !empty($do_action) ? $do_action : (!empty($do_action2) ? $do_action2 : '');

		if(!empty($items)) {

			switch($action) {
				case 'delete':
					$error = $this->delete_game($items);
					$referer = add_query_arg('delete', $error, $referer);
				break;
				case 'export':

					$data = $this->export_games($items);

					header('Content-Type: application/x-gzip-compressed');
					header('Content-Disposition: attachment; filename="wp-clanwars-gamepack-' . date('Y-m-d', $this->current_time_fixed('timestamp')) . '.gz"');

					$json = json_encode($data);
					$gzdata = gzcompress($json, 9);

					header('Content-Length: ' . strlen($gzdata));

					echo $gzdata;

					die();

				break;
			}

		}

		wp_redirect($referer);
	}

	function export_games($id)
	{
		$data = array();
		$games = $this->get_game(array('id' => $id));

		foreach($games as $game) {
			$game_data = $this->extract_args($game, array(
					'title' => '', 'abbr' => '',
					'icon' => '', 'maplist' => array()
				));

			$maplist = $this->get_map(array('game_id' => $game->id));

			if($game->icon != 0) {
				$attach = get_attached_file($game->icon);
				$mimetype = get_post_mime_type($game->icon);
				$pathinfo = pathinfo($attach);

				if(!empty($attach)){
					$content = $this->_get_file_content($attach);

					if(!empty($content))
						$game_data['icon'] = array(
							'filename' => $pathinfo['basename'],
							'mimetype' => $mimetype,
							'data' => base64_encode($content));
				}
			}

			foreach($maplist as $map) {
				$map_data = array('title' => $map->title, 'screenshot' => '');

				if($map->screenshot != 0) {
					$attach = get_attached_file($map->screenshot);
					$mimetype = get_post_mime_type($map->screenshot);
					$pathinfo = pathinfo($attach);

					if(!empty($attach)){
						$content = $this->_get_file_content($attach);

						if(!empty($content))
							$map_data['screenshot'] = array(
								'filename' => $pathinfo['basename'],
								'mimetype' => $mimetype,
								'data' => base64_encode($content));
					}
				}

				$game_data['maplist'][] = $map_data;
			}

			$data[] = $game_data;
		}

		return $data;
	}

	function _import_image($p) {

		if(!empty($p)) {
			$upload = wp_upload_bits($p['filename'], null, base64_decode($p['data']));

			if($upload['error'] === false) {
				$attach = array('guid' => $upload['url'],
								'post_title' => sanitize_title($p['filename']),
								'post_content' => '',
								'post_status' => 'publish',
								'post_mime_type' => $p['mimetype']);

				$attach_id = wp_insert_attachment($attach, $upload['file']);

				if(!empty($attach_id)) {
					$metadata = wp_generate_attachment_metadata($attach_id, $upload['file']);

					if(!empty($metadata))
						wp_update_attachment_metadata($attach_id, $metadata);

					return $attach_id;
				}
			}
		}

		return 0;
	}

	function import_games($data) {

		if(is_string($data))
			$data = @json_decode(gzuncompress($data));

		if(empty($data) || !is_array($data))
			return false;

		foreach($data as $game) {

			$game_data = $this->extract_args($game, array(
				'title' => '', 'abbr' => '',
				'icon' => '', 'maplist' => array()
			));

			if(!empty($game_data['title'])) {
				$p = $game_data;
				$p['icon'] = $this->_import_image((array)$p['icon']);
				$maplist = $p['maplist'];

				unset($p['maplist']);

				$game_id = $this->add_game($p);

				if(!empty($game_id)) {

					foreach($maplist as $map) {
						$p = (array)$map;
						$p['screenshot'] = $this->_import_image((array)$p['screenshot']);
						$p['game_id'] = $game_id;

						if(!empty($p['title']))
							$this->add_map($p);
					}

				}
			}

		}

		return true;
	}

	function on_add_game()
	{
		return $this->game_editor(__('New Game', 'addict'), 'wp-clanwars-addgame', __('Add Game', 'addict'));
	}

	function on_edit_game()
	{

		$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

		return $this->game_editor(__('Edit Game', 'addict'), 'wp-clanwars-editgame', __('Update Game', 'addict'), $id);
	}

	function on_load_manage_games()
	{
		$act = isset($_GET['act']) ? $_GET['act'] : '';
		$id = isset($_GET['id']) ? $_GET['id'] : 0;
		$game_id = isset($_GET['game_id']) ? $_GET['game_id'] : 0;
		$die = false;

		// Check game or map is really exists
		if($act == 'add' && !$this->acl_user_can('manage_game', 'all')) {
			$die = true;
		}
		else if($act == 'edit' || $act == 'maps' || $act == 'addmap') {

			$g = $this->get_game(array('id' =>
					($act == 'maps' || $act == 'addmap' ? $game_id : $id)
				));

			$die = empty($g) || !$this->acl_user_can('manage_game', $g[0]->id);

		} else if($act == 'editmap') {

			$m = $this->get_map(array('id' => $id));
			$die = empty($m);
		}

		if($die)
			wp_die( __('Cheatin&#8217; uh?', 'addict') );

		if(sizeof($_POST)) {

			$edit_maps_errors = array(
				self::ErrorDatabase => __('Database error.', 'addict'),
				self::ErrorOK => __('The game is updated.', 'addict'),
				self::ErrorUploadMaxFileSize => __('The uploaded file exceeds the <code>upload_max_filesize</code> directive in <code>php.ini</code>.', 'addict'),
				self::ErrorUploadHTMLMaxFileSize => __('The uploaded file exceeds the <em>MAX_FILE_SIZE</em> directive that was specified in the HTML form.', 'addict'),
				self::ErrorUploadPartially => __('The uploaded file was only partially uploaded.', 'addict'),
				self::ErrorUploadNoFile => __('No file was uploaded.', 'addict'),
				self::ErrorUploadMissingTemp => __('Missing a temporary folder.', 'addict'),
				self::ErrorUploadDiskWrite => __('Failed to write file to disk.', 'addict'),
				self::ErrorUploadStoppedByExt => __('File upload stopped by extension.', 'addict'),
				self::ErrorUploadFileTypeNotAllowed => __('File type does not meet security guidelines. Try another.', 'addict')
			);

				switch($act) {
					case 'add':

						$defaults = array('title' => '', 'abbr' => '', 'icon' => 0);
						$data = $this->extract_args(stripslashes_deep($_POST), $defaults);
						extract($data);

						if(!empty($title)) {

							$data['icon'] = $this->handle_upload('icon_file');

							if($data['icon'] == self::ErrorUploadNoFile)
								$data['icon'] = 0;

							if($data['icon'] >= 0) {

								if($this->add_game($data)) {
									wp_redirect(admin_url('admin.php?page=wp-clanwars-games&add=1'));
									exit();
								} else
									$this->add_notice(__('An error occurred.', 'addict'), 'error');
							} else
								$this->add_notice($edit_maps_errors[$attach_id], 'error');


						} else
							$this->add_notice(__('Game title is required field.', 'addict'), 'error');
					break;

					case 'edit':
						$defaults = array('title' => '', 'abbr' => '', 'delete_image' => false);
						$data = $this->extract_args(stripslashes_deep($_POST), $defaults);
						extract($data);

						unset($data['delete_image']);

						if(!empty($title)) {

							if(!empty($delete_image))
								$data['icon'] = 0;

							$attach_id = $this->handle_upload('icon_file');

							if($attach_id == self::ErrorUploadNoFile)
								$attach_id = 0;
							else if($attach_id > 0)
								$data['icon'] = $attach_id;

							if($attach_id >= 0) {

								if($this->update_game($id, $data) !== false) {
									wp_redirect(admin_url('admin.php?page=wp-clanwars-games&update=1'));
									exit();
								} else
									$this->add_notice(__('An error occurred.', 'addict'), 'error');

							} else
								$this->add_notice($edit_maps_errors[$attach_id], 'error');

						} else
							$this->add_notice(__('Game title is required field.', 'addict'), 'error');
						break;

					case 'addmap':
						$defaults = array('title' => '', 'game_id' => 0, 'id' => 0);
						$data = $this->extract_args(stripslashes_deep($_POST), $defaults);
						extract($data);

						if(!empty($title)) {

							$attach_id = $this->handle_upload('screenshot_file');

							if($attach_id == self::ErrorUploadNoFile)
								$attach_id = 0;

							if($attach_id >= 0) {

								if($this->add_map(array('title' => $title, 'screenshot' => $attach_id, 'game_id' => $game_id)) !== false) {
									wp_redirect(admin_url(sprintf('admin.php?page=wp-clanwars-games&act=maps&game_id=%d&add=1', $game_id)));
									exit();
								} else
									$this->add_notice(__('An error occurred.', 'addict'), 'error');

							} else
								$this->add_notice($edit_maps_errors[$attach_id], 'error');

						} else
							$this->add_notice(__('Map title is required field.', 'addict'), 'error');

						break;

					case 'editmap':
						$defaults = array('title' => '', 'game_id' => 'all', 'id' => 0, 'delete_image' => false);
						$data = $this->extract_args(stripslashes_deep($_POST), $defaults);
						extract($data);

						$update_data = array('title' => $title);

						if(!empty($title)) {

							if(!empty($delete_image))
								$update_data['screenshot'] = 0;

							$attach_id = $this->handle_upload('screenshot_file');

							if($attach_id == self::ErrorUploadNoFile)
								$attach_id = 0;
							else if($attach_id > 0)
								$update_data['screenshot'] = $attach_id;

							if($attach_id >= 0) {

								if($this->update_map($id, $update_data) !== false) {
									wp_redirect(admin_url(sprintf('admin.php?page=wp-clanwars-games&act=maps&game_id=%d&update=1', $game_id)));
									exit();
								} else
									$this->add_notice(__('An error occurred.', 'addict'), 'error');

							} else
								$this->add_notice($edit_maps_errors[$attach_id], 'error');

						} else
							$this->add_notice(__('Map title is required field.', 'addict'), 'error');

						break;
				}

		}
	}

	function on_manage_games()
	{
		$act = isset($_GET['act']) ? $_GET['act'] : '';
		$current_page = isset($_GET['paged']) ? $_GET['paged'] : 1;
		$filter_games = $this->acl_user_can('which_games');
		$limit = 10;

		switch($act) {
			case 'add':
				return $this->on_add_game();
				break;
			case 'edit':
				return $this->on_edit_game();
				break;
			case 'maps':
				return $this->on_edit_maps();
				break;
			case 'addmap':
				return $this->on_add_map();
				break;
			case 'editmap':
				return $this->on_edit_map();
				break;
		}

		$teams = $this->get_game(array(
					'id' => $filter_games,
					'orderby' => 'title', 'order' => 'asc',
					'limit' => $limit, 'offset' => ($limit * ($current_page-1))
				));
		$stat = $this->get_game(array('id' => $filter_games, 'limit' => $limit), true);

		$page_links = paginate_links( array(
				'base' => add_query_arg('paged', '%#%'),
				'format' => '',
				'prev_text' => __('&laquo;', 'addict'),
				'next_text' => __('&raquo;', 'addict'),
				'total' => $stat['total_pages'],
				'current' => $current_page
		));

		$page_links_text = sprintf( '<span class="displaying-num">' . __( 'Displaying %s&#8211;%s of %s', 'addict' ) . '</span>%s',
				number_format_i18n( (($current_page - 1) * $limit) + 1 ),
				number_format_i18n( min( $current_page * $limit, $stat['total_items'] ) ),
				'<span class="total-type-count">' . number_format_i18n( $stat['total_items'] ) . '</span>',
				$page_links
		);

		$table_columns = array('cb' => '<input type="checkbox" />',
					  'title' => __('Title', 'addict'),
					  'abbr' => __('Abbreviation', 'addict'));

		if(isset($_GET['add'])) {
			$this->add_notice(__('Game is successfully added.', 'addict'), 'updated');
		}

		if(isset($_GET['update'])) {
			$this->add_notice(__('Game is successfully updated.', 'addict'), 'updated');
		}

		if(isset($_GET['delete'])) {
			$deleted = (int)$_GET['delete'];
			$this->add_notice(sprintf(_n('%d Game deleted.', '%d Games deleted', $deleted, 'addict'), $deleted), 'updated');
		}

		$this->print_notices();

	?>
		<div class="wrap wp-cw-games">
			<h2><?php _e('Games', 'addict'); ?>
				<?php if($this->acl_user_can('manage_game', 'all')) : ?> <a href="<?php echo admin_url('admin.php?page=wp-clanwars-games&act=add'); ?>" class="add-new-h2"><?php _e('Add New', 'addict'); ?></a><?php endif; ?>
			</h2>

			<div id="poststuff" class="metabox-holder">

				<div id="post-body">
					<div id="post-body-content" class="has-sidebar-content">

					<form id="wp-clanwars-manageform" action="admin-post.php" method="post">
						<?php wp_nonce_field('wp-clanwars-gamesop'); ?>

						<input type="hidden" name="action" value="wp-clanwars-gamesop" />

						<div class="tablenav">

							<div class="alignleft actions">
								<select name="do_action">
									<option value="" selected="selected"><?php _e('Bulk Actions', 'addict'); ?></option>
									<option value="delete"><?php _e('Delete', 'addict'); ?></option>
									<option value="export"><?php _e('Export', 'addict'); ?></option>
								</select>
								<input type="submit" value="<?php _e('Apply', 'addict'); ?>" name="doaction" id="wp-clanwars-doaction" class="button-secondary action" />
							</div>

							<div class="alignright actions" style="display: none;">
								<label class="screen-reader-text" for="games-search-input"><?php _e('Search Teams:', 'addict'); ?></label>
								<input id="games-search-input" name="s" value="<?php echo esc_html($search_title); ?>" type="text" />

								<input id="games-search-submit" value="<?php _e('Search Games', 'addict'); ?>" class="button" type="button" />
							</div>

						<br class="clear" />

						</div>

						<div class="clear"></div>

						<table class="widefat fixed" cellspacing="0">
						<thead>
						<tr>
						<?php $this->print_table_header($table_columns); ?>
						</tr>
						</thead>

						<tfoot>
						<tr>
						<?php $this->print_table_header($table_columns, false); ?>
						</tr>
						</tfoot>

						<tbody>

						<?php foreach($teams as $i => $item) : ?>

							<tr class="iedit<?php if($i % 2 == 0) echo ' alternate'; ?>">
								<th scope="row" class="check-column"><input type="checkbox" name="items[]" value="<?php echo $item->id; ?>" /></th>
								<td class="title column-title">
									<a class="row-title" href="<?php echo admin_url('admin.php?page=wp-clanwars-games&amp;act=edit&amp;id=' . $item->id); ?>" title="<?php echo sprintf(__('Edit &#8220;%s&#8221; Team', 'addict'), esc_attr($item->title)); ?>"> <?php echo esc_html($item->title); ?></a><br />
									<div class="row-actions">
										<span class="edit"><a href="<?php echo admin_url('admin.php?page=wp-clanwars-games&amp;act=edit&amp;id=' . $item->id); ?>"><?php _e('Edit', 'addict'); ?></a></span> |
												<span class="edit"><a href="<?php echo admin_url('admin.php?page=wp-clanwars-games&amp;act=maps&amp;game_id=' . $item->id); ?>"><?php _e('Maps', 'addict'); ?></a></span> | <span class="delete">
												<a href="<?php echo wp_nonce_url('admin-post.php?action=wp-clanwars-gamesop&amp;do_action=delete&amp;items[]=' . $item->id . '&amp;_wp_http_referer=' . urlencode($_SERVER['REQUEST_URI']), 'wp-clanwars-gamesop'); ?>"><?php _e('Delete', 'addict'); ?></a></span>
									</div>
								</td>
								<td class="abbr column-abbr">
									<?php echo esc_html($item->abbr); ?>
								</td>
							</tr>

						<?php endforeach; ?>

						</tbody>

						</table>

						<div class="tablenav">

							<div class="tablenav-pages"><?php echo $page_links_text; ?></div>

							<div class="alignleft actions">
							<select name="do_action2">
								<option value="" selected="selected"><?php _e('Bulk Actions', 'addict'); ?></option>
								<option value="delete"><?php _e('Delete', 'addict'); ?></option>
								<option value="export"><?php _e('Export', 'addict'); ?></option>
							</select>
							<input type="submit" value="<?php _e('Apply', 'addict'); ?>" name="doaction2" id="wp-clanwars-doaction2" class="button-secondary action" />
							</div>

							<br class="clear" />

						</div>

					</form>

					</div>
				</div>
				<br class="clear"/>

			</div>
		</div>
	<?php
	}

	function game_editor($page_title, $page_action, $page_submit, $game_id = 0)
	{
		$defaults = array('title' => '', 'icon' => 0, 'abbr' => '', 'action' => '');

		if($game_id > 0) {
			$t = $this->get_game(array('id' => $game_id));
			if(!empty($t))
				$data = (array)$t[0];
		}

		extract($this->extract_args(stripslashes_deep($_POST), $this->extract_args($data, $defaults)));

		$this->print_notices();

		$attach = wp_get_attachment_image($icon, 'thumbnail');

		?>

			<div class="wrap wp-cw-gameeditor">
				<h2><?php echo $page_title; ?></h2>

					<form name="team-editor" id="team-editor" method="post" action="<?php echo esc_attr($_SERVER['REQUEST_URI']); ?>" enctype="multipart/form-data">

						<input type="hidden" name="action" value="<?php echo esc_attr($page_action); ?>" />
						<input type="hidden" name="id" value="<?php echo esc_attr($game_id); ?>" />

						<?php wp_nonce_field($page_action); ?>

						<table class="form-table">

						<tr class="form-field form-required">
							<th scope="row" valign="top"><label for="title"><span class="alignleft"><?php _e('Title', 'addict'); ?></span><span class="alignright"><abbr title="<?php _e('required', 'addict'); ?>" class="required">*</abbr></span><br class="clear" /></label></th>
							<td>
								<input name="title" id="title" type="text" class="regular-text" value="<?php echo esc_attr($title); ?>" maxlength="200" autocomplete="off" aria-required="true" />
							</td>
						</tr>

						<tr class="form-field">
							<th scope="row" valign="top"><label for="title"><?php _e('Abbreviation', 'addict'); ?></label></th>
							<td>
								<input name="abbr" id="abbr" type="text" class="regular-text" value="<?php echo esc_attr($abbr); ?>" maxlength="20" autocomplete="off" />
							</td>
						</tr>

					<!--	<tr>
							<th scope="row" valign="top"><label for="icon_file"><?php _e('Icon', 'addict'); ?></label></th>
							<td>
								<input type="file" name="icon_file" id="icon_file" />

								<?php if(!empty($attach)) : ?>
								<div class="screenshot"><?php echo $attach; ?></div>
								<div>
								<label for="delete-image"><input type="checkbox" name="delete_image" id="delete-image" /> <?php _e('Delete Icon', 'addict'); ?></label>
								</div>
								<?php endif; ?>
							</td>
						</tr> -->

						</table>

						<p class="submit"><input type="submit" class="button-primary" name="submit" value="<?php echo $page_submit; ?>" /></p>

					</form>

			</div>

		<?php
	}

	/*
	 * Maps managment
	 */

	function on_admin_post_deletemaps()
	{
		if(!$this->acl_user_can('manage_games'))
			wp_die( __('Cheatin&#8217; uh?', 'addict') );

		check_admin_referer('wp-clanwars-deletemaps');

		$referer = remove_query_arg(array('add', 'update'), $_REQUEST['_wp_http_referer']);

		if($_REQUEST['do_action'] == 'delete' || $_REQUEST['do_action2'] == 'delete') {
			extract($this->extract_args($_REQUEST, array('delete' => array())));

			$error = $this->delete_map($delete);
			$referer = add_query_arg('delete', $error, $referer);
		}

		wp_redirect($referer);
	}

	function on_edit_maps()
	{
		$game_id = isset($_GET['game_id']) ? (int)$_GET['game_id'] : 0;
		$current_page = isset($_GET['paged']) ? $_GET['paged'] : 1;
		$limit = 10;

		$maps = $this->get_map('id=all&orderby=title&order=asc&game_id=' . $game_id . '&limit=' . $limit . '&offset=' . ($limit * ($current_page-1)));
		$stat = $this->get_map('id=all&game_id=' . $game_id . '&limit=' . $limit, true);

		$page_links = paginate_links( array(
				'base' => add_query_arg('paged', '%#%'),
				'format' => '',
				'prev_text' => __('&laquo;', 'addict'),
				'next_text' => __('&raquo;', 'addict'),
				'total' => $stat['total_pages'],
				'current' => $current_page
		));

		$page_links_text = sprintf( '<span class="displaying-num">' . __( 'Displaying %s&#8211;%s of %s', 'addict' ) . '</span>%s',
				number_format_i18n( (($current_page - 1) * $limit) + 1 ),
				number_format_i18n( min( $current_page * $limit, $stat['total_items'] ) ),
				'<span class="total-type-count">' . number_format_i18n( $stat['total_items'] ) . '</span>',
				$page_links
		);

		$table_columns = array('cb' => '<input type="checkbox" />',
					  'icon' => '',
					  'title' => __('Title', 'addict'));

		if(isset($_GET['add'])) {
			$this->add_notice(__('Map is successfully added.', 'addict'), 'updated');
		}

		if(isset($_GET['update'])) {
			$this->add_notice(__('Map is successfully updated.', 'addict'), 'updated');
		}

		if(isset($_GET['delete'])) {
			$deleted = (int)$_GET['delete'];
			$this->add_notice(sprintf(_n('%d Map deleted.', '%d Maps deleted', $deleted, 'addict'), $deleted), 'updated');
		}

		$this->print_notices();

	?>
		<div class="wrap wp-cw-maps">
			<h2><?php _e('Maps', 'addict'); ?> <a href="<?php echo admin_url('admin.php?page=wp-clanwars-games&act=addmap&game_id=' . $game_id); ?>" class="add-new-h2"><?php _e('Add New', 'addict'); ?></a></h2>

			<div id="poststuff" class="metabox-holder">

				<div id="post-body">
					<div id="post-body-content" class="has-sidebar-content">

					<form id="wp-clanwars-manageform" action="admin-post.php" method="post">
						<?php wp_nonce_field('wp-clanwars-deletemaps'); ?>

						<input type="hidden" name="action" value="wp-clanwars-deletemaps" />
						<input type="hidden" name="game_id" value="<?php echo $game_id; ?>" />

						<div class="tablenav">

							<div class="alignleft actions">
								<select name="do_action">
									<option value="" selected="selected"><?php _e('Bulk Actions', 'addict'); ?></option>
									<option value="delete"><?php _e('Delete', 'addict'); ?></option>
								</select>
								<input type="submit" value="<?php _e('Apply', 'addict'); ?>" name="doaction" id="wp-clanwars-doaction" class="button-secondary action" />
							</div>

							<div class="alignright actions" style="display: none;">
								<label class="screen-reader-text" for="maps-search-input"><?php _e('Search Maps:', 'addict'); ?></label>
								<input id="maps-search-input" name="s" value="<?php echo esc_html($search_title); ?>" type="text" />

								<input id="maps-search-submit" value="<?php _e('Search Maps', 'addict'); ?>" class="button" type="button" />
							</div>

						<br class="clear" />

						</div>

						<div class="clear"></div>

						<table class="widefat fixed" cellspacing="0">
						<thead>
						<tr>
						<?php $this->print_table_header($table_columns); ?>
						</tr>
						</thead>

						<tfoot>
						<tr>
						<?php $this->print_table_header($table_columns, false); ?>
						</tr>
						</tfoot>

						<tbody>

						<?php foreach($maps as $i => $item) : ?>

							<tr class="iedit<?php if($i % 2 == 0) echo ' alternate'; ?>">
								<th scope="row" class="check-column"><input type="checkbox" name="delete[]" value="<?php echo $item->id; ?>" /></th>
								<td class="column-icon media-icon">
									<?php $attach = wp_get_attachment_image($item->screenshot, 'thumbnail');
									if(!empty($attach)) echo $attach;
									?>
								</td>
								<td class="title column-title">
									<a class="row-title" href="<?php echo admin_url('admin.php?page=wp-clanwars-games&amp;act=editmap&amp;id=' . $item->id); ?>" title="<?php echo sprintf(__('Edit &#8220;%s&#8221; Map', 'addict'), esc_attr($item->title)); ?>"> <?php echo esc_html($item->title); ?></a><br />
									<div class="row-actions">
										<span class="edit"><a href="<?php echo admin_url('admin.php?page=wp-clanwars-games&amp;act=editmap&amp;id=' . $item->id); ?>"><?php _e('Edit', 'addict'); ?></a></span> | <span class="delete">
												<a href="<?php echo wp_nonce_url('admin-post.php?action=wp-clanwars-deletemaps&amp;do_action=delete&amp;delete[]=' . $item->id . '&amp;_wp_http_referer=' . urlencode($_SERVER['REQUEST_URI']), 'wp-clanwars-deletemaps'); ?>"><?php _e('Delete', 'addict'); ?></a></span>
									</div>
								</td>
							</tr>

						<?php endforeach; ?>

						</tbody>

						</table>

						<div class="tablenav">

							<div class="tablenav-pages"><?php echo $page_links_text; ?></div>

							<div class="alignleft actions">
							<select name="do_action2">
							<option value="" selected="selected"><?php _e('Bulk Actions', 'addict'); ?></option>
							<option value="delete"><?php _e('Delete', 'addict'); ?></option>
							</select>
							<input type="submit" value="<?php _e('Apply', 'addict'); ?>" name="doaction2" id="wp-clanwars-doaction2" class="button-secondary action" />
							</div>

							<br class="clear" />

						</div>

					</form>

					</div>
				</div>
				<br class="clear"/>

			</div>

		</div>
<?php

	}

	function get_map($p, $count = false)
	{
		global $wpdb;

		extract($this->extract_args($p, array(
			'id' => false,
			'game_id' => false,
			'limit' => 0,
			'offset' => 0,
			'orderby' => 'id',
			'order' => 'ASC')));
		$where_query = '';
		$limit_query = '';
		$order_query = '';

		$order = strtolower($order);
		if($order != 'asc' && $order != 'desc')
			$order = 'asc';

		$order_query = 'ORDER BY `' . $orderby . '` ' . $order;

		if($id != 'all' && $id !== false) {

			if(!is_array($id))
				$id = array($id);

			$id = array_map('intval', $id);
			$where_query[] = 'id IN (' . implode(', ', $id) . ')';
		}

		if($game_id != 'all' && $game_id !== false) {

			if(!is_array($game_id))
				$game_id = array($game_id);

			$game_id = array_map('intval', $game_id);
			$where_query[] = 'game_id IN (' . implode(', ', $game_id) . ')';
		}

		if($limit > 0) {
			$limit_query = $wpdb->prepare('LIMIT %d, %d', $offset, $limit);
		}

		if(!empty($where_query))
			$where_query = 'WHERE ' . implode(' AND ', $where_query);

		if($count) {

			$rslt = $wpdb->get_row('SELECT COUNT(id) AS m_count FROM `' . $this->tables['maps'] . '` ' . $where_query);

			$ret = array('total_items' => 0, 'total_pages' => 1);

			$ret['total_items'] = $rslt->m_count;

			if($limit > 0)
				$ret['total_pages'] = ceil($ret['total_items'] / $limit);

			return $ret;
		}

		$rslt = $wpdb->get_results('SELECT * FROM `' . $this->tables['maps'] . '` ' . implode(' ', array($where_query, $order_query, $limit_query)));

		return $rslt;
	}

	function add_map($p)
	{
		global $wpdb;

		$data = $this->extract_args($p, array(
					'title' => '',
					'screenshot' => 0,
					'game_id' => 0));

		if($wpdb->insert($this->tables['maps'], $data, array('%s', '%d', '%d')))
		{
			$insert_id = $wpdb->insert_id;

			return $insert_id;
		}

		return false;
	}

	function update_map($id, $p)
	{
		global $wpdb;

		$fields = array('title' => '%s', 'screenshot' => '%d', 'game_id' => '%d');

		$data = wp_parse_args($p, array());

		$update_data = array();
		$update_mask = array();

		foreach($fields as $fld => $mask) {
			if(isset($data[$fld])) {
				$update_data[$fld] = $data[$fld];
				$update_mask[] = $mask;
			}
		}

		$result = $wpdb->update($this->tables['maps'], $update_data, array('id' => $id), $update_mask, array('%d'));

		return $result;
	}

	function delete_map($id)
	{
		global $wpdb;

		if(!is_array($id))
			$id = array($id);

		$id = array_map('intval', $id);

		return $wpdb->query('DELETE FROM `' . $this->tables['maps'] . '` WHERE id IN(' . implode(',', $id) . ')');
	}

	function delete_map_by_game($id)
	{
		global $wpdb;

		if(!is_array($id))
			$id = array($id);

		$id = array_map('intval', $id);

		return $wpdb->query('DELETE FROM `' . $this->tables['maps'] . '` WHERE game_id IN(' . implode(',', $id) . ')');
	}

	function on_add_map()
	{
	   $game_id = isset($_GET['game_id']) ? (int)$_GET['game_id'] : 0;

	   $this->map_editor(__('Add Map', 'addict'), 'wp-clanwars-addmap', __('Add Map', 'addict'), $game_id);
	}

	function on_edit_map()
	{
	   $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

	   $this->map_editor(__('Edit Map', 'addict'), 'wp-clanwars-editmap', __('Update Map', 'addict'), 0, $id);
	}

	function page_not_found($title, $message) {

		echo '<div class="wrap"><h2>' . $title . '</h2>' . $message . '</div>';

	}

	function map_editor($page_title, $page_action, $page_submit, $game_id, $id = 0)
	{
		$defaults = array('title' => '', 'screenshot' => 0, 'abbr' => '', 'action' => '');

		if($id > 0) {
			$t = $this->get_map(array('id' => $id, 'game_id' => $game_id));

			if(!empty($t)){
				$data = (array)$t[0];
				$game_id = $data['game_id'];
			}
		}

		extract($this->extract_args(stripslashes_deep($_POST), $this->extract_args($data, $defaults)));

		$attach = wp_get_attachment_image($screenshot, 'thumbnail');

		$this->print_notices();
		?>

			<div class="wrap wp-cw-mapeditor">
				<h2><?php echo $page_title; ?></h2>

					<form name="map-editor" id="map-editor" method="post" action="<?php echo esc_attr($_SERVER['REQUEST_URI']); ?>" enctype="multipart/form-data">

						<input type="hidden" name="action" value="<?php echo esc_attr($page_action); ?>" />
						<input type="hidden" name="game_id" value="<?php echo esc_attr($game_id); ?>" />
						<input type="hidden" name="id" value="<?php echo esc_attr($id); ?>" />

						<?php wp_nonce_field($page_action); ?>

						<table class="form-table">

						<tr class="form-field form-required">
							<th scope="row" valign="top"><label for="title"><span class="alignleft"><?php _e('Title', 'addict'); ?></span><span class="alignright"><abbr title="<?php _e('required', 'addict'); ?>" class="required">*</abbr></span><br class="clear" /></label></th>
							<td>
								<input name="title" id="title" type="text" class="regular-text" value="<?php echo esc_attr($title); ?>" maxlength="200" autocomplete="off" aria-required="true" />
							</td>
						</tr>

						<tr>
							<th scope="row" valign="top"><label for="screenshot_file"><?php _e('Screenshot', 'addict'); ?></label></th>
							<td>
								<input type="file" name="screenshot_file" id="screenshot_file" />

								<?php if(!empty($attach)) : ?>
								<div class="screenshot"><?php echo $attach; ?></div>
								<div>
								<label for="delete-image"><input type="checkbox" name="delete_image" id="delete-image" /> <?php _e('Delete Screenshot', 'addict'); ?></label>
								</div>
								<?php endif; ?>
							</td>
						</tr>

						</table>

						<p class="submit"><input type="submit" class="button-primary" name="submit" value="<?php echo $page_submit; ?>" /></p>

					</form>

			</div>

		<?php
	}

	/*
	 * Matches managment
	 */

	function on_admin_post_deletematches()
	{
		if(!$this->acl_user_can('manage_matches'))
			wp_die( __('Cheatin&#8217; uh?', 'addict') );

		check_admin_referer('wp-clanwars-deletematches');

		$referer = remove_query_arg(array('add', 'update'), $_REQUEST['_wp_http_referer']);

		if($_REQUEST['do_action'] == 'delete' || $_REQUEST['do_action2'] == 'delete') {
			extract($this->extract_args($_REQUEST, array('delete' => array())));

			$error = $this->delete_match($delete);
			$referer = add_query_arg('delete', $error, $referer);
		}

		wp_redirect($referer);
	}

	function current_time_fixed( $type, $gmt = 0 ) {
		$t = ( $gmt ) ? gmdate( 'Y-m-d H:i:s' ) : gmdate( 'Y-m-d H:i:s', ( time() + ( get_option( 'gmt_offset' ) * 3600 ) ) );
		switch ( $type ) {
			case 'mysql':
				return $t;
				break;
			case 'timestamp':
				return strtotime($t);
				break;
		}
	}

	function html_date_helper( $prefix, $time = 0, $tab_index = 0 )
	{
		global $wp_locale;

		$tab_index_attribute = '';
		$tab_index = (int)$tab_index;
		if ($tab_index > 0)
			$tab_index_attribute = " tabindex=\"$tab_index\"";

		if($time == 0)
			$time_adj = $this->current_time_fixed('timestamp', 0);
		else
			$time_adj = $time;

		$jj = date( 'd', $time_adj );
		$mm = date( 'm', $time_adj );
		$hh = date( 'H', $time_adj );
		$mn = date( 'i', $time_adj );
		$yy = date( 'Y', $time_adj );

		$month = "<select name=\"{$prefix}[mm]\"$tab_index_attribute>\n";
		for ( $i = 1; $i < 13; $i = $i +1 ) {
				$month .= "\t\t\t" . '<option value="' . zeroise($i, 2) . '"';
				if ( $i == $mm )
						$month .= ' selected="selected"';
				$month .= '>' . $wp_locale->get_month( $i ) . "</option>\n";
		}
		$month .= '</select>';

		$day = '<input type="text" name="'.$prefix.'[jj]" value="' . $jj . '" size="2" maxlength="2"' . $tab_index_attribute . ' autocomplete="off"  />';
		$hour = '<input type="text" name="'.$prefix.'[hh]" value="' . $hh . '" size="2" maxlength="2"' . $tab_index_attribute . ' autocomplete="off"  />';
		$minute = '<input type="text" name="'.$prefix.'[mn]" value="' . $mn . '" size="2" maxlength="2"' . $tab_index_attribute . ' autocomplete="off"  />';
		$year = '<input type="text" name="'.$prefix.'[yy]" value="' . $yy . '" size="3" maxlength="4"' . $tab_index_attribute . ' autocomplete="off"  />';

		printf(before_last_bar(__('%1$s%5$s %2$s @ %3$s : %4$s|1: month input, 2: day input, 3: hour input, 4: minute input, 5: year input', 'addict')), $month, $day, $hour, $minute, $year);
	}

	function date_array2time_helper($date)
	{
		if(is_array($date) &&
			isset($date['hh'], $date['mn'], $date['mm'], $date['jj'], $date['yy']))
		{
			return mktime($date['hh'], $date['mn'], 0, $date['mm'], $date['jj'], $date['yy']);
		}

		return $date;
	}

	function get_match($p, $count = false)
	{
		global $wpdb;

		extract($this->extract_args($p, array(
			'from_date' => 0,
			'id' => false,
			'game_id' => false,
			'sum_tickets' => false,
			'limit' => 0,
			'offset' => 0,
			'orderby' => 'id',
			'order' => 'ASC')));

		$where_query = '';
		$limit_query = '';
		$order_query = '';

		$order = strtolower($order);
		if($order != 'asc' && $order != 'desc')
			$order = 'asc';

		$order_query = 'ORDER BY t1.`' . $orderby . '` ' . $order;

		if($id != 'all' && $id !== false) {

			if(!is_array($id))
				$id = array($id);

			$id = array_map('intval', $id);
			$where_query[] = 't1.id IN (' . implode(', ', $id) . ')';
		}

		if($game_id != 'all' && $game_id !== false) {

			if(!is_array($game_id))
				$game_id = array($game_id);

			$game_id = array_map('intval', $game_id);
			$where_query[] = 't1.game_id IN (' . implode(', ', $game_id) . ')';
		}

		if($from_date > 0) {
			$where_query[] = 't1.date >= FROM_UNIXTIME(' . intval($from_date) . ')';
		}

		if($limit > 0) {
			$limit_query = $wpdb->prepare('LIMIT %d, %d', $offset, $limit);
		}

		if(!empty($where_query)) {
			$where_query = 'WHERE ' . implode(' AND ', $where_query);
		}

		if($count) {
			$rslt = $wpdb->get_row('SELECT COUNT(id) AS m_count FROM `' . $this->tables['matches'] . '` AS t1 ' . $where_query);
			$ret = array('total_items' => 0, 'total_pages' => 1);
			$ret['total_items'] = $rslt->m_count;

			if($limit > 0)
				$ret['total_pages'] = ceil($ret['total_items'] / $limit);

			return $ret;
		}

		if($sum_tickets) {
			$rslt = $wpdb->get_results(
					'SELECT t1.*, t2.title AS game_title, t2.abbr AS game_abbr, t2.icon AS game_icon,
							tt1.title AS team1_title, tt2.title AS team2_title,
							tt1.logo AS team1_logo, tt2.logo AS team2_logo,
							(SELECT SUM(sumt1.tickets1) FROM `' . $this->tables['rounds'] . '` AS sumt1 WHERE sumt1.match_id = t1.id) AS team1_tickets,
							(SELECT SUM(sumt2.tickets2) FROM `' . $this->tables['rounds'] . '` AS sumt2 WHERE sumt2.match_id = t1.id) AS team2_tickets

					 FROM `' . $this->tables['matches'] . '` AS t1
					 LEFT JOIN `' . $this->tables['games'] . '` AS t2 ON t1.game_id=t2.id
					 LEFT JOIN `' . $this->tables['teams'] . '` AS tt1 ON t1.team1=tt1.id
					 LEFT JOIN `' . $this->tables['teams'] . '` AS tt2 ON t1.team2=tt2.id ' .
					 implode(' ', array($where_query, $order_query, $limit_query)));

		} else {
			$rslt = $wpdb->get_results(
					'SELECT t1.*, t2.title AS game_title, t2.abbr AS game_abbr, t2.icon AS game_icon,
							tt1.title AS team1_title, tt2.title AS team2_title,
							tt1.logo AS team1_logo, tt2.logo AS team2_logo
					 FROM `' . $this->tables['matches'] . '` AS t1
					 LEFT JOIN `' . $this->tables['games'] . '` AS t2 ON t1.game_id=t2.id
					 LEFT JOIN `' . $this->tables['teams'] . '` AS tt1 ON t1.team1=tt1.id
					 LEFT JOIN `' . $this->tables['teams'] . '` AS tt2 ON t1.team2=tt2.id ' .
					 implode(' ', array($where_query, $order_query, $limit_query)));
		}

		//echo '<pre>'. $wpdb->last_query.'</pre>';

		return $rslt;
	}

	function update_match_post($match_id) {

		global $wpdb;

		$post_category = get_option(WP_CLANWARS_CATEGORY, -1);

		$postarr = array(
			'post_status' => 'publish',
			'post_content' => '',
			'post_excerpt' => '',
			'post_title' => '',
			'post_type' => 'matches'

		);

		if($post_category != -1)
			$postarr['post_category'] = array((int)$post_category);

		$matches = $this->get_match(array('id' => $match_id, 'sum_tickets' => true));

		if(!empty($matches)) {

			$m = $matches[0];

			$post = get_post($m->post_id);

			if(!is_null($post)) {
				$postarr['ID'] = $post->ID;
			}

			$post_title = $m->title;

			if(empty($post_title)) {

				$t1 = $this->get_team(array('id' => $team1));
				$t2 = $this->get_team(array('id' => $team2));

				if(!empty($t1) && !empty($t2))
					$post_title = sprintf(__('%s Versus %s', 'addict'), $t1->title, $t2->title);
				else
					$post_title = __('Regular match', 'addict');
			}

			$post_excerpt = '';
            $t1 = $m->team1_tickets;
            $t2 = $m->team2_tickets;
            $round_class1 = $t1 < $t2 ? 'lose' : ($t1 > $t2 ? 'victory' : 'draw');
            $round_class2 = $t1 > $t2 ? 'lose' : ($t1 < $t2 ? 'victory' : 'draw');


			$post_content = '<div class="blog-post match-page wp-clanwars-page">';
            $post_content .= '<div class="results-main-bg">
                <div class="results-main navbar-inverse">
                    <div class="r-home-team">
                        '.  $m->team1_title . '
                    </div>
                    <div class="r-home-team-score '.  $round_class1 . ' navbar-inverse">
                          '.  $t1 . '
                    </div>
                    <div class="versus">
                       ' . __('Versus', 'addict') . '
                    </div>
                    <div class="r-opponent-team '.  $round_class2 . '">
                        ' . $m->team2_title . '
                    </div>
                    <div class="r-opponent-team-score navbar-inverse">
                        '. $t2 . '
                    </div>
                </div>
            </div>';

			$post_excerpt .= sprintf(_x('%s vs %s', 'match_excerpt', 'addict'), $m->team1_title, $m->team2_title) . "\n";

			$r = $this->get_rounds($m->id);
			$rounds = array();

			// group rounds by map
			foreach($r as $v) {

				if(!isset($rounds[$v->group_n]))
					$rounds[$v->group_n] = array();

				$rounds[$v->group_n][] = $v;
			}


			// render maps/rounds
			foreach($rounds as $map_group) {

				$first = $map_group[0];
				$image = wp_get_attachment_image_src($first->screenshot);



				if(sizeof($rounds) == 1) {
					$item_class .= ' aligncenter';
				}


				$post_content .= '<div class="match-map span3"><div class="map-image">';


				if(!empty($image))
					$post_content .= '<img src="' . $image[0] . '" alt="' . esc_attr($first->title) . '" style="width: ' . $image[1] . 'px; height: ' . $image[2] . 'px;" />';

                $post_content .= '<div class="map-title">' . esc_html($first->title) . '</div>';
				$post_content .= '</div>';

				foreach($map_group as $round) {

					$t1 = $round->tickets1;
					$t2 = $round->tickets2;
					$round_class = $t1 < $t2 ? 'loose' : ($t1 > $t2 ? 'win' : 'draw');

					$post_content .= '<div class="navbar-inverse map-score">';
					$post_content .= sprintf(__('%d:%d', 'addict'), $t1, $t2);
					$post_content .= '</div>';

				}
                $post_content .= '</div>';


			}

            $post_content .= '<div class="clear"></div>';


			// match description
			$sn = $m->match_status;
			$date = mysql2date(get_option('date_format') . ', ' . get_option('time_format'), $m->date);

			$post_content .= '<div class="title-wrapper"><h3 class="widget-title">' . __('Match description', 'addict') . '</h3><div class="clear"></div></div>';

			$post_content .= '<div class="match-description"><ul class="match-props">';
			$post_content .= '<li class="date">' . $date . '</li>';

			if(isset($this->match_status[$sn]))
				$post_content .= '<li class="status type-' . $sn . '">' . $this->match_status[$sn] . '</li>';

			if(!empty($m->external_url))
			{
				$post_content .= '<li class="external_url">';
				$post_content .= '<a href="' . esc_attr($m->external_url) . '" target="_blank">' . esc_url($m->external_url) . '</a>';
				$post_content .= '</li>';
			}

			$post_content .= '</ul>'; // match-props

			if(!empty($m->description))
			{
				$description = nl2br($m->description);
				$description = make_clickable($description);
				$description = wptexturize($description);
				$description = convert_smilies($description);

				// add target=_blank to all links
				$description = preg_replace('#(<a.*?)(>.*?</a>)#i', '$1 target="_blank"$2', $description);

				$post_excerpt .= $description . "\n";
				$post_content .= '<div class="match-description-body">' . $description . '</div>';
			}

			$post_content .= '</div></div>'; // page

			$postarr['post_title'] = $post_title;
			$postarr['post_content'] = $post_content;
			$postarr['post_excerpt'] = wp_trim_excerpt($post_excerpt);

			$new_post_ID = 0;

			if(isset($postarr['ID']))
				$new_post_ID = wp_update_post($postarr);
			else
				$new_post_ID = wp_insert_post($postarr);

			$result = $wpdb->update($this->tables['matches'], array('post_id' => $new_post_ID), array('id' => $match_id), array('%d'), array('%d'));

			return $new_post_ID;

		}

		return false;

	}

	function add_match($p)
	{
		global $wpdb;

		$data = $this->extract_args($p, array(
					'title' => '',
					'date' => $this->current_time_fixed('timestamp', 0),
					'post_id' => 0,
					'team1' => 0,
					'team2' => 0,
					'game_id' => 0,
					'match_status' => 0,
					'description' => ''
			));

		if($wpdb->insert($this->tables['matches'], $data, array('%s', '%s', '%d', '%d', '%d', '%d', '%s')))
		{
			$insert_id = $wpdb->insert_id;

			return $insert_id;
		}

		return false;
	}

	function update_match($id, $p)
	{
		global $wpdb;

		$fields = array(
			'title' => '%s',
			'date' => '%s',
			'post_id' => '%d',
			'team1' => '%d',
			'team2' => '%d',
			'game_id' => '%d',
			'match_status' => '%d',
			'description' => '%s',
			'external_url' => '%s'
		);

		$data = wp_parse_args($p, array());

		$update_data = array();
		$update_mask = array();

		foreach($fields as $fld => $mask) {
			if(isset($data[$fld])) {
				$update_data[$fld] = $data[$fld];
				$update_mask[] = $mask;
			}
		}

		// filter external_url field
		if(isset($update_data['external_url'])) {
			$update_data['external_url'] = esc_url_raw($update_data['external_url']);
		}

		$result = $wpdb->update($this->tables['matches'], $update_data, array('id' => $id), $update_mask, array('%d'));

		return $result;
	}

	// @TODO: remove post
	function delete_match($id)
	{
		global $wpdb;

		if(!is_array($id))
			$id = array($id);

		$id = array_map('intval', $id);

		$this->delete_rounds_by_match($id);

		return $wpdb->query('DELETE FROM `' . $this->tables['matches'] . '` WHERE id IN(' . implode(',', $id) . ')');
	}

	function delete_match_by_team($id) {
		global $wpdb;

		if(!is_array($id))
			$id = array($id);

		$id = array_map('intval', $id);
		$id_list = implode(',', $id);

		return $wpdb->query('DELETE FROM `' . $this->tables['matches'] . '` WHERE team1 IN(' . $id_list . ') OR team2 IN(' . $id_list . ')');
	}

	function delete_match_by_game($id) {
		global $wpdb;

		if(!is_array($id))
			$id = array($id);

		$id = array_map('intval', $id);

		return $wpdb->query('DELETE FROM `' . $this->tables['matches'] . '` WHERE game_id IN(' . implode(',', $id) . ')');

	}

	function get_rounds($match_id)
	{
		global $wpdb;

		return $wpdb->get_results(
				$wpdb->prepare(
						'SELECT t1.*, t2.title, t2.screenshot FROM `' . $this->tables['rounds'] . '` AS t1
						 LEFT JOIN `' . $this->tables['maps'] . '` AS t2
						 ON t2.id = t1.map_id
						 WHERE t1.match_id=%d ORDER BY t1.id ASC, t1.group_n ASC',
						$match_id)
				);
	}

	function add_round($p)
	{
		global $wpdb;

		$data = $this->extract_args($p, array(
					'match_id' => 0,
					'group_n' => 0,
					'map_id' => 0,
					'tickets1' => 0,
					'tickets2' => 0
			));

		if($wpdb->insert($this->tables['rounds'], $data, array('%d', '%d', '%d', '%d', '%d')))
		{
			$insert_id = $wpdb->insert_id;

			return $insert_id;
		}

		return false;
	}

	function update_round($id, $p)
	{
		global $wpdb;

		$fields = array(
			'match_id' => '%d',
			'group_n' => '%d',
			'map_id' => '%d',
			'tickets1' => '%d',
			'tickets2' => '%d'
		);

		$data = wp_parse_args($p, array());

		$update_data = array();
		$update_mask = array();

		foreach($fields as $fld => $mask) {
			if(isset($data[$fld])) {
				$update_data[$fld] = $data[$fld];
				$update_mask[] = $mask;
			}
		}

		$result = $wpdb->update($this->tables['rounds'], $update_data, array('id' => $id), $update_mask, array('%d'));

		return $result;
	}

	function delete_round($id)
	{
		global $wpdb;

		if(!is_array($id))
			$id = array($id);

		$id = array_map('intval', $id);

		return $wpdb->query('DELETE FROM `' . $this->tables['rounds'] . '` WHERE id IN(' . implode(',', $id) . ')');
	}

	function delete_rounds_not_in($match_id, $id)
	{
		global $wpdb;

		if(!is_array($id))
			$id = array($id);

		$id = array_map('intval', $id);

		return $wpdb->query($wpdb->prepare('DELETE FROM `' . $this->tables['rounds'] . '` WHERE match_id=%d AND id NOT IN(' . implode(',', $id) . ')', $match_id));
	}

	function delete_rounds_by_match($match_id)
	{
		global $wpdb;

		if(!is_array($match_id))
			$match_id = array($match_id);

		$match_id = array_map('intval', $match_id);

		return $wpdb->query('DELETE FROM `' . $this->tables['rounds'] . '` WHERE match_id IN(' . implode(',', $match_id) . ')');
	}

	function on_add_match()
	{
		return $this->match_editor(__('Add Match', 'addict'), 'wp-clanwars-matches', __('Add Match', 'addict'));
	}

	function on_edit_match()
	{
		$id = isset($_GET['id']) ? $_GET['id'] : 0;

		return $this->match_editor(__('Edit Match', 'addict'), 'wp-clanwars-matches', __('Edit Match', 'addict'), $id);
	}

	function on_ajax_get_maps()
	{
		if(!$this->acl_user_can('manage_games') &&
		   !$this->acl_user_can('manage_matches'))
			wp_die( __('Cheatin&#8217; uh?', 'addict') );

		$game_id = isset($_POST['game_id']) ? (int)$_POST['game_id'] : 0;

		if($game_id > 0) {
			$maps = $this->get_map(array('game_id' => $game_id, 'order' => 'asc', 'orderby' => 'title'));

			for($i = 0; $i < sizeof($maps); $i++) {
				$url = wp_get_attachment_thumb_url($maps[$i]->screenshot);

				$maps[$i]->screenshot_url = !empty($url) ? $url : '';
			}

			echo json_encode($maps); die();
		}
	}

	function match_editor($page_title, $page_action, $page_submit, $id = 0)
	{
		$data = array();
		$current_time = $this->current_time_fixed('timestamp', 0);
		$post_id = 0;

		$defaults = array('game_id' => 0,
			'title' => '',
			'team1' => 0,
			'team2' => 0,
			'scores' => array(),
			'match_status' => 0,
			'action' => '',
			'description' => '',
			'external_url' => '',
			'date' => array('mm' => date('m', $current_time),
							'yy' => date('Y', $current_time),
							'jj' => date('j', $current_time),
							'hh' => date('H', $current_time),
							'mn' => date('i', $current_time)
				));


		if($id > 0) {
			$t = $this->get_match(array('id' => $id));

			if(!empty($t)){
				$data = (array)$t[0];
				$data['date'] = strtotime($data['date']);
				$data['scores'] = array();

				$post_id = $data['post_id'];

				$rounds = $this->get_rounds($data['id']);

				foreach($rounds as $round) {
					$data['scores'][$round->group_n]['map_id'] = $round->map_id;
					$data['scores'][$round->group_n]['round_id'][] = $round->id;
					$data['scores'][$round->group_n]['team1'][] = $round->tickets1;
					$data['scores'][$round->group_n]['team2'][] = $round->tickets2;
				}
			}
		}

		$games = $this->get_game(array('id' => $this->acl_user_can('which_games'), 'orderby' => 'title', 'order' => 'asc'));
		$teams = $this->get_team('id=all&orderby=title&order=asc');

		extract($this->extract_args(stripslashes_deep($_POST), $this->extract_args($data, $defaults)));
		$date = $this->date_array2time_helper($date);

		if(isset($_GET['update'])) {
			$this->add_notice(__('Match is successfully updated.', 'addict'), 'updated');
		}

		$this->print_notices();
		?>

		<script type="text/javascript">
		//<![CDATA[
		jQuery(document).ready(function ($) {
			var data = <?php echo json_encode($scores); ?>;

			$.each(data, function (i, item) {
				var m = wpMatchManager.addMap(i, item.map_id);
				for(var j = 0; j < item.team1.length; j++) {
					m.addRound(item.team1[j], item.team2[j], item.round_id[j]);
				};
			});
		});
		//]]>
		</script>

			<div class="wrap wp-cw-matcheditor">

				<h2><?php echo $page_title; ?>
				<?php if($post_id) : ?>
				<ul class="linkbar">
					<li class="post-link"><a href="<?php echo esc_attr(get_permalink($post_id)); ?>" target="_blank" class="icon-link"><?php echo $post_id; ?></a></li>
					<li class="post-comments"><a href="<?php echo get_comments_link($post_id); ?>" target="_blank"><?php echo get_comments_number($post_id); ?></a></li>
				</ul>
				<?php endif; ?>
				</h2>

					<form name="match-editor" id="match-editor" method="post" action="<?php echo esc_attr($_SERVER['REQUEST_URI']); ?>" enctype="multipart/form-data">

						<input type="hidden" name="action" value="<?php echo esc_attr($page_action); ?>" />
						<input type="hidden" name="id" value="<?php echo esc_attr($id); ?>" />

						<?php wp_nonce_field($page_action); ?>

						<table class="form-table">

						<tr class="form-field form-required">
							<th scope="row" valign="top"><label for="game_id"><?php _e('Game', 'addict'); ?></label></th>
							<td>
								<select id="game_id" name="game_id">
									<?php foreach($games as $item) : ?>
									<option value="<?php echo $item->id; ?>"<?php selected($item->id, $game_id); ?>><?php echo esc_html($item->title); ?></option>
									<?php endforeach; ?>
								</select>
								<p class="description"><?php _e('The scores will be removed on game change.', 'addict'); ?></p>
							</td>
						</tr>

						<tr class="form-field form-required">
							<th scope="row" valign="top"><label for="title"><?php _e('Title', 'addict'); ?></label></th>
							<td>
								<input name="title" id="title" type="text" value="<?php echo esc_attr($title); ?>" maxlength="200" autocomplete="off" aria-required="true" />
							</td>
						</tr>

						<tr class="form-field">
							<th scope="row" valign="top"><label for="description"><?php _e('Description', 'addict'); ?></label></th>
							<td>
							    <?php $settings = array( 'textarea_name' => 'description' );
                                wp_editor( $description, 'description', $settings ); ?>
    						</td>
						</tr>

						<tr class="form-field">
							<th scope="row" valign="top"><label for="external_url"><?php _e('External URL', 'addict'); ?></label></th>
							<td>
								<input type="text" name="external_url" id="external_url" value="<?php echo esc_attr($external_url); ?>" />

								<p class="description"><?php _e('Enter league URL or external match URL.', 'addict'); ?></p>
							</td>
						</tr>

						<tr class="form-required">
							<th scope="row" valign="top"><label for=""><?php _e('Match status', 'addict'); ?></label></th>
							<td>
								<?php foreach($this->match_status as $index => $text) : ?>
								<label for="match_status_<?php echo $index; ?>"><input type="radio" value="<?php echo $index; ?>" name="match_status" id="match_status_<?php echo $index; ?>"<?php checked($index, $match_status, true); ?> /> <?php echo $text; ?></label><br/>
								<?php endforeach; ?>
							</td>
						</tr>

						<tr class="form-required">
							<th scope="row" valign="top"><label for=""><?php _e('Date', 'addict'); ?></label></th>
							<td>
								<?php $this->html_date_helper('date', $date); ?>
							</td>
						</tr>

						<tr class="form-required">
							<th scope="row" valign="top"></th>
							<td>
								<div class="match-results" id="matchsite">

									<div class="teams">
									<select name="team1" class="team-select">
									<?php foreach($teams as $t) : ?>
										<option value="<?php echo $t->id; ?>"<?php selected(true, $team1 > 0 ? ($t->id == $team1) : $t->home_team, true); ?>><?php echo esc_html($t->title); ?></option>
									<?php endforeach; ?>
									</select>&nbsp;<?php _e('vs', 'addict'); ?>&nbsp;
									<select name="team2" class="team-select">
									<?php foreach($teams as $t) : ?>
										<option value="<?php echo $t->id; ?>"<?php selected(true, $t->id==$team2, true); ?>><?php echo esc_html($t->title); ?></option>
									<?php endforeach; ?>
									</select>
									</div>

									<div class="team2-inline">
										<label for="new_team_title"><?php _e('or just type opponent team here:', 'addict'); ?></label><br/>
										<input name="new_team_title" id="new_team_title" type="text" value="" maxlength="200" autocomplete="off" aria-required="true" />

									</div>
									<br class="clear"/>

									<div id="mapsite"></div>

									<div class="add-map" id="wp-cw-addmap">
										<input type="button" class="button button-secondary" value="<?php _e('Add map', 'addict'); ?>" />
									</div>

								</div>
							</td>
						</tr>

						</table>

						<p class="submit"><input type="submit" class="button-primary" id="wp-cw-submit" name="submit" value="<?php echo $page_submit; ?>" /></p>

					</form>

			</div>

		<?php
	}

	function quick_pick_team($title) {
		$team = $this->get_team(array('title' => $title, 'limit' => 1));
		$team_id = 0;
		if(empty($team)) {
			$new_team_id = $this->add_team(array('title' => $title));
			if($new_team_id !== false)
				$team_id = $new_team_id;
		} else {
			$team_id = $team[0]->id;
		}

		return $team_id;
	}

	function on_load_manage_matches()
	{
		$id = isset($_REQUEST['id']) ? $_GET['id'] : 0;
		$act = isset($_GET['act']) ? $_GET['act'] : '';

		wp_enqueue_script('wp-cw-matches');
		wp_localize_script('wp-cw-matches',
				'wpCWL10n',
				array(
				'plugin_url' => WP_CLANWARS_URL,
				'addRound' => __('Add Round', 'addict'),
				'excludeMap' => __('Exclude map from match', 'addict'),
				'removeRound' => __('Remove round', 'addict')
				)
			);

		// Check match is really exists
		if($act == 'edit') {
			$m = $this->get_match(array('id' => $id));

			if($id != 0 && empty($m))
				wp_die( __('Cheatin&#8217; uh?', 'addict') );

			if(!$this->acl_user_can('manage_game', $m[0]->game_id))
				wp_die( __('Cheatin&#8217; uh?', 'addict') );
		}

		if(sizeof($_POST) > 0)
		{

			if(isset($_POST['game_id']) && !$this->acl_user_can('manage_game', $_POST['game_id']))
				wp_die( __('Cheatin&#8217; uh?', 'addict') );

			switch($act) {

				case 'add':

					extract($this->extract_args(stripslashes_deep($_POST), array(
						'game_id' => 0,
						'title' => '',
						'description' => '',
						'external_url' => '',
						'date' => $this->current_time_fixed('timestamp', 0),
						'team1' => 0,
						'team2' => 0,
						'scores' => array(),
						'new_team_title' => '',
						'match_status' => 0
						)));

					$date = $this->date_array2time_helper($date);

					if(!empty($new_team_title)) {
						$pickteam = $this->quick_pick_team($new_team_title);

						if($pickteam > 0)
							$team2 = $pickteam;
					}

					$match_id = $this->add_match(array(
							'title' => $title,
							'date' => date('Y-m-d H:i:s', $date),
							'post_id' => 0,
							'team1' => $team1,
							'team2' => $team2,
							'game_id' => $game_id,
							'match_status' => $match_status,
							'description' => $description
					));

					if($match_id) {

						foreach($scores as $round_group => $r) {
							for($i = 0; $i < sizeof($r['team1']); $i++) {
								$this->add_round(array('match_id' => $match_id,
									'group_n' => abs($round_group),
									'map_id' => $r['map_id'],
									'tickets1' => $r['team1'][$i],
									'tickets2' => $r['team2'][$i]
									));
							}
						}

						$this->update_match_post($match_id);

						wp_redirect(admin_url('admin.php?page=wp-clanwars-matches&add=1'));
						exit();
					} else
						$this->add_notice(__('An error occurred.', 'addict'), 'error');

				break;

			case 'edit':

					extract($this->extract_args(stripslashes_deep($_POST), array(
						'id' => 0,
						'game_id' => 0,
						'title' => '',
						'description' => '',
						'external_url' => '',
						'date' => $this->current_time_fixed('timestamp', 0),
						'team1' => 0,
						'team2' => 0,
						'new_team_title' => '',
						'match_status' => 0,
						'scores' => array()
						)));

					$date = $this->date_array2time_helper($date);

					if(!empty($new_team_title)) {
						$pickteam = $this->quick_pick_team($new_team_title);

						if($pickteam > 0)
							$team2 = $pickteam;
					}

					$this->update_match($id, array(
							'title' => $title,
							'date' => date('Y-m-d H:i:s', $date),
							'team1' => $team1,
							'team2' => $team2,
							'game_id' => $game_id,
							'match_status' => $match_status,
							'description' => $description,
							'external_url' => $external_url
						));

					$rounds_not_in = array();

					foreach($scores as $round_group => $r) {
						for($i = 0; $i < sizeof($r['team1']); $i++) {
							$round_id = $r['round_id'][$i];
							$round_data = array('match_id' => $id,
									'group_n' => abs($round_group),
									'map_id' => $r['map_id'],
									'tickets1' => $r['team1'][$i],
									'tickets2' => $r['team2'][$i]
									);

							if($round_id > 0) {
								$this->update_round($round_id, $round_data);
								$rounds_not_in[] = $round_id;
							} else {
								$new_round = $this->add_round($round_data);
								if($new_round !== false)
									$rounds_not_in[] = $new_round;
							}
						}
					}

					$this->delete_rounds_not_in($id, $rounds_not_in);

					$this->update_match_post($id);

					wp_redirect(admin_url('admin.php?page=wp-clanwars-matches&act=edit&id=' . $id . '&update=1'));
					exit();

				break;
			}
		}
	}

	function on_shortcode($atts) {

		$output = '';

		extract(shortcode_atts(array('per_page' => 20), $atts));

		$per_page = abs($per_page);
		$current_page = get_query_var('page');
		$now = $this->current_time_fixed('timestamp');
		$current_game = isset($_GET['game']) ? $_GET['game'] : false;

		$games = $this->get_game('id=all&orderby=title&order=asc');

		if($current_page < 1)
			$current_page = 1;

		$p = array(
			'limit' => $per_page,
			'order' => 'desc',
			'orderby' => 'date',
			'sum_tickets' => true,
			'game_id' => $current_game,
			'offset' => ($current_page-1) * $per_page
		);

		$matches = $this->get_match($p, false);

		$stat = $this->get_match($p, true);

		$page_links = paginate_links( array(
				'base' => add_query_arg('page', '%#%'),
				'format' => '',
				'prev_text' => __('&laquo;', 'addict'),
				'next_text' => __('&raquo;', 'addict'),
				'total' => $stat['total_pages'],
				'current' => $current_page
		));

		$page_links_text = sprintf( '<span class="displaying-num">' . __( 'Displaying %s&#8211;%s of %s', 'addict' ) . '</span>%s',
				number_format_i18n( (($current_page - 1) * $per_page) + 1 ),
				number_format_i18n( min( $current_page * $per_page, $stat['total_items'] ) ),
				'<span class="total-type-count">' . number_format_i18n( $stat['total_items'] ) . '</span>',
				$page_links
		);



		$output .= '<ul class="clanwar-list clanwarlist-page">';
        $output .= '    <li>
        <ul class="tabs">';

        $obj = new stdClass();
        $obj->id = 0;
        $obj->title = __('All', 'addict');
        $obj->abbr = __('All', 'addict');
        $obj->icon = 0;

        array_unshift($games, $obj);

        for($i = 0; $i < sizeof($games); $i++) :
            $game = $games[$i];
            $link = ($game->id == 0) ? 'all' : 'game-' . $game->id;
         if($i == 0){$class = 'selected';}

        $output .= '<li class="'.$class.'"><a href="#'.$link.'" title="'.esc_attr($game->title).'">'.esc_html($game->abbr).'</a></li>';
         endfor;
      $output .= '</ul>
    </li>';
		// generate table content
		$j=0;
		foreach($matches as $index => $match) {
            if($i % 2 != 0) $alt = ' alt';
			$output .= '<li  data-toggle="tooltip" title="' . esc_attr($match->title) . '" class="clanwar-item '.$alt.' match ' . ($index % 2 == 0 ? 'even' : 'odd') . ' game-'.$match->game_id.' ">';

			// output match status
			$is_upcoming = false;
			$t1 = $match->team1_tickets;
			$t2 = $match->team2_tickets;
			$wld_class = $t1 == $t2 ? 'draw' : ($t1 > $t2 ? 'win' : 'loose');
			$date = mysql2date(get_option('date_format') . ', ' . get_option('time_format'), $match->date);
			$timestamp = mysql2date('U', $match->date);
            global $wpdb;
            $table = array(
                'teams' => 'cw_teams',
                'games' => 'cw_games',
            );
            $table = array_map(create_function('$t', 'global $table_prefix; return $table_prefix . $t; '), $table);
			$team1id = $match->team1;
            $team2id = $match->team2;

            $logo1 = $wpdb->get_results('SELECT logo FROM `' . $table['teams'] . '` WHERE `id`= ' .$team1id. ' '  );
            $logo2 = $wpdb->get_results('SELECT logo FROM `' . $table['teams'] . '` WHERE `id`= ' .$team2id. ' '  );

            $thumb1 = $logo1[0]->logo;
            $img_url1 = wp_get_attachment_url( $thumb1,'full'); //get img URL
            $image1 = aq_resize( $img_url1, 50, 50, true, true, true ); //resize & crop img
            $thumb2 = $logo2[0]->logo;
            $img_url2 = wp_get_attachment_url( $thumb2,'full'); //get img URL
            $image2 = aq_resize( $img_url2, 50, 50, true, true, true ); //resize & crop img

			$is_upcoming = $timestamp > $now;
			$is_playing = ($now > $timestamp && $now < $timestamp + 3600) && ($t1 == 0 && $t2 == 0);

			if($is_upcoming) :
				$output .= '<div class="upcoming">' . __('Upcoming', 'addict') . '</div>';
			elseif($is_playing) :
				$output .= '<div class="playing">' . __('Playing', 'addict') . '</div>';
			else :
				$output .= '<div class="scores ' . $wld_class . '">' . sprintf(__('%d:%d', 'addict'), $t1, $t2) . '</div>';
			endif;

			// output game icon
			$game_icon = wp_get_attachment_url($match->game_icon);

			if($game_icon !== false) {
				$output .= '<img src="' . $game_icon . '" alt="' . esc_attr($match->game_title) . '" class="icon" /> ';
			}
			// teams
			$output .= '<div class="wrap"><a href="' . get_permalink($match->post_id) . '" title="' . esc_attr($match->title) . '">';
            $output .= '<div class="matchimages"><img src="'.$image1.'" class="clan1"><div class="matchpage-versus">'.__("VS", "addict").'</div><img src="'.$image2.'" class="clan2"></div>';
			$team2_title = esc_html($match->team2_title);

			if($match->post_id != 0)
				$team2_title = '<a href="' . get_permalink($match->post_id) . '" title="' . esc_attr($match->title) . '">' . $team2_title . '</a>';

			$output .= '<div class="opponent-team">' .
			$this-> $team2_title .
					'</div>';


			$output .= '<div class="date">' . esc_html($date)  . '</div>';



			$rounds = array();
			$r = $this->get_rounds($match->id);
            $i= 0;
			foreach($r as $v) {
				if(isset($rounds[$v->group_n]))
					continue;

				$image = wp_get_attachment_image_src($v->screenshot);
?>
<script type="text/javascript">
 jQuery(document).ready(function() {
  jQuery("#mapspopover<?php echo $i;echo $j; ?>").popover({
      placement : 'bottom',
      trigger: 'hover', //placement of the popover. also can use top, bottom, left or right
      title : '<div><?php echo esc_attr($v->title); ?></div>', //this is the top title bar of the popover. add some basic css
      html: 'true', //needed to show html of course
      content : '<img src="<?php echo esc_attr($image[0]); ?>" />' //this is the content of the html box. add the image here or anything you want really.
});
});
</script>
<?php

				if(!empty($image)) {
					$rounds[$v->group_n] =
					 '<a id="mapspopover'.$i.$j.'" href="' . esc_attr($image[0]) . '#' . $image[1] . 'x' . $image[2] . '" title="' . esc_attr($v->title) . '">' . esc_html($v->title) . '</a>';
				} else{

            		$rounds[$v->group_n] = $v->title;}
                $i++;
			}

			if(!empty($rounds)) {
				$maplist = implode(', ', array_values($rounds));
				$output .= '</a><div class="maplist">' . $maplist . '</div>';
			}

			$output .= '</div>';

			$output .= '</li>';
        $j++;
		}

		$output .= '</ul>';

		$output .= '<div class="wp-clanwars-pagination clearfix">' .$page_links_text . '</div>';

		return $output;
	}

	function on_manage_matches()
	{
		$act = isset($_GET['act']) ? $_GET['act'] : '';
		$current_page = isset($_GET['paged']) ? $_GET['paged'] : 1;
		$limit = 10;
		$game_filter = $this->acl_user_can('which_games');

		switch($act) {
			case 'add':
				return $this->on_add_match();
				break;
			case 'edit':
				return $this->on_edit_match();
				break;
		}

		$stat_condition = array(
			'id' => 'all',
			'game_id' => $game_filter,
			'limit' => $limit
		);

		$condition = array(
			'id' => 'all', 'game_id' => $game_filter, 'sum_tickets' => true,
			'orderby' => 'date', 'order' => 'desc',
			'limit' => $limit, 'offset' => ($limit * ($current_page-1))
		);

		$matches = $this->get_match($condition);
		$stat = $this->get_match($stat_condition, true);

		$page_links = paginate_links( array(
				'base' => add_query_arg('paged', '%#%'),
				'format' => '',
				'prev_text' => __('&laquo;', 'addict'),
				'next_text' => __('&raquo;', 'addict'),
				'total' => $stat['total_pages'],
				'current' => $current_page
		));

		$page_links_text = sprintf( '<span class="displaying-num">' . __( 'Displaying %s&#8211;%s of %s', 'addict' ) . '</span>%s',
				number_format_i18n( (($current_page - 1) * $limit) + 1 ),
				number_format_i18n( min( $current_page * $limit, $stat['total_items'] ) ),
				'<span class="total-type-count">' . number_format_i18n( $stat['total_items'] ) . '</span>',
				$page_links
		);

		$table_columns = array('cb' => '<input type="checkbox" />',
					  'title' => __('Title', 'addict'),
					  'game_title' => __('Game', 'addict'),
					  'date' => __('Date', 'addict'),
					  'match_status' => __('Match status', 'addict'),
					  'team1' => __('Team 1', 'addict'),
					  'team2' => __('Team 2', 'addict'),
					  'tickets' => __('Tickets', 'addict'));

		if(isset($_GET['add'])) {
			$this->add_notice(__('Match is successfully added.', 'addict'), 'updated');
		}

		if(isset($_GET['delete'])) {
			$deleted = (int)$_GET['delete'];
			$this->add_notice(sprintf(_n('%d Match deleted.', '%d Matches deleted', $deleted, 'addict'), $deleted), 'updated');
		}

		$this->print_notices();

	?>
		<div class="wrap wp-cw-matches">
			<h2><?php _e('Matches', 'addict'); ?> <a href="<?php echo admin_url('admin.php?page=wp-clanwars-matches&act=add'); ?>" class="add-new-h2"><?php _e('Add New', 'addict'); ?></a></h2>

			<div id="poststuff" class="metabox-holder">

				<div id="post-body">
					<div id="post-body-content" class="has-sidebar-content">

					<form id="wp-clanwars-manageform" action="admin-post.php" method="post">
						<?php wp_nonce_field('wp-clanwars-deletematches'); ?>

						<input type="hidden" name="action" value="wp-clanwars-deletematches" />

						<div class="tablenav">

							<div class="alignleft actions">
								<select name="do_action">
									<option value="" selected="selected"><?php _e('Bulk Actions', 'addict'); ?></option>
									<option value="delete"><?php _e('Delete', 'addict'); ?></option>
								</select>
								<input type="submit" value="<?php _e('Apply', 'addict'); ?>" name="doaction" id="wp-clanwars-doaction" class="button-secondary action" />
							</div>

							<div class="alignright actions" style="display: none;">
								<label class="screen-reader-text" for="games-search-input"><?php _e('Search Teams:', 'addict'); ?></label>
								<input id="games-search-input" name="s" value="<?php echo esc_html($search_title); ?>" type="text" />

								<input id="games-search-submit" value="<?php _e('Search Games', 'addict'); ?>" class="button" type="button" />
							</div>

						<br class="clear" />

						</div>

						<div class="clear"></div>

						<table class="widefat fixed" cellspacing="0">
						<thead>
						<tr>
						<?php $this->print_table_header($table_columns); ?>
						</tr>
						</thead>

						<tfoot>
						<tr>
						<?php $this->print_table_header($table_columns, false); ?>
						</tr>
						</tfoot>

						<tbody>

						<?php foreach($matches as $i => $item) : ?>

							<?php
							// if the match has no title so set default one
							if(empty($item->title))
								$item->title = __('Regular match', 'addict');
							?>

							<tr class="iedit<?php if($i % 2 == 0) echo ' alternate'; ?>">
								<th scope="row" class="check-column"><input type="checkbox" name="delete[]" value="<?php echo $item->id; ?>" /></th>
								<td class="title column-title">
									<a class="row-title" href="<?php echo admin_url('admin.php?page=wp-clanwars-matches&amp;act=edit&amp;id=' . $item->id); ?>" title="<?php echo sprintf(__('Edit &#8220;%s&#8221; Match', 'addict'), esc_attr($item->title)); ?>"><?php echo esc_html($item->title); ?></a><br />
									<div class="row-actions">
										<span class="edit"><a href="<?php echo admin_url('admin.php?page=wp-clanwars-matches&amp;act=edit&amp;id=' . $item->id); ?>"><?php _e('Edit', 'addict'); ?></a></span> | <span class="delete">
												<a href="<?php echo wp_nonce_url('admin-post.php?action=wp-clanwars-deletematches&amp;do_action=delete&amp;delete[]=' . $item->id . '&amp;_wp_http_referer=' . urlencode($_SERVER['REQUEST_URI']), 'wp-clanwars-deletematches'); ?>"><?php _e('Delete', 'addict'); ?></a></span>
									</div>
								</td>
								<td class="game_title column-game_title">
									<?php
									$game_icon = wp_get_attachment_url($item->game_icon);
									if($game_icon !== false) :
									?>
									<img src="<?php echo $game_icon; ?>" alt="<?php echo esc_attr($item->game_title); ?>" class="icon" />
									<?php endif; ?>

									<?php echo esc_html($item->game_title); ?>
								</td>
								<td class="date column-date">
									<?php echo date('d.m.Y H:i', strtotime($item->date)); ?>
								</td>
								<td class="match_status column-match_status">
									<?php
									$n = $item->match_status;

									if(isset($this->match_status[$n]))
										echo $this->match_status[$n];
									?>
								</td>
								<td class="team1 column-team1">
									<?php echo esc_html($item->team1_title); ?>
								</td>
								<td class="team2 column-team2">
									<?php echo esc_html($item->team2_title); ?>
								</td>
								<td class="tickets column-tickets">
									<?php echo sprintf('%s:%s', $item->team1_tickets, $item->team2_tickets); ?>
								</td>
							</tr>

						<?php endforeach; ?>

						</tbody>

						</table>

						<div class="tablenav">

							<div class="tablenav-pages"><?php echo $page_links_text; ?></div>

							<div class="alignleft actions">
							<select name="do_action2">
							<option value="" selected="selected"><?php _e('Bulk Actions', 'addict'); ?></option>
							<option value="delete"><?php _e('Delete', 'addict'); ?></option>
							</select>
							<input type="submit" value="<?php _e('Apply', 'addict'); ?>" name="doaction2" id="wp-clanwars-doaction2" class="button-secondary action" />
							</div>

							<br class="clear" />

						</div>

					</form>

					</div>
				</div>
				<br class="clear"/>

			</div>
		</div>
	<?php
	}

	function on_admin_post_settings() {
		global $wpdb;

		if(!current_user_can('manage_options'))
			wp_die(__('Cheatin&#8217; uh?', 'addict'));

		check_admin_referer('wp-clanwars-settings');

		if(isset($_POST['category']))
			update_option(WP_CLANWARS_CATEGORY, (int)$_POST['category']);

		update_option(WP_CLANWARS_DEFAULTCSS, isset($_POST['enable_default_styles']));

		$url = add_query_arg('saved', 'true', $_POST['_wp_http_referer']);

		wp_redirect($url);
	}

	function on_admin_post_acl() {
		global $wpdb;

		if(!current_user_can('manage_options'))
			wp_die(__('Cheatin&#8217; uh?', 'addict'));

		check_admin_referer('wp-clanwars-acl');

		if(isset($_POST['user'])) {
			$user_id = (int)$_POST['user'];
			$data = array();

			if(isset($_POST['permissions']))
				$data['permissions'] = $_POST['permissions'];

			if(isset($_POST['games']))
				$data['games'] = $_POST['games'];

			$this->acl_update($user_id, $data);
		}

		$url = add_query_arg('saved', 'true', $_POST['_wp_http_referer']);

		wp_redirect($url);
	}

	function on_admin_post_deleteacl() {
		global $wpdb;

		if(!current_user_can('manage_options'))
			wp_die(__('Cheatin&#8217; uh?', 'addict'));

		check_admin_referer('wp-clanwars-deleteacl');

		extract($this->extract_args($_POST, array(
			'doaction' => '', 'doaction2' => '',
			'users' => array()
			)));

		$url = $_POST['_wp_http_referer'];

		if($doaction == 'delete' || $doaction2 == 'delete') {

			$users = array_unique(array_values($users));

			foreach($users as $key => $user_id)
				$this->acl_delete($user_id);

			$url = add_query_arg('saved', 'true', $url);
		}

		wp_redirect($url);
	}

	function on_admin_post_import() {
		if(!current_user_can('manage_options'))
			wp_die(__('Cheatin&#8217; uh?', 'addict'));

		check_admin_referer('wp-clanwars-import');

		extract($this->extract_args($_POST, array('import' => '', 'items' => array())));

		$url = remove_query_arg(array('upload', 'import'), $_POST['_wp_http_referer']);

		switch($import) {
			case 'upload':
				if(isset($_FILES['userfile'])) {
					$file = $_FILES['userfile'];

					if($file['error'] == 0) {
						$content = $this->_get_file_content($file['tmp_name']);

						$result = $this->import_games($content);
						$url = add_query_arg('import', $result, $url);
					} else {
						$url = add_query_arg('upload', 'error', $url);
					}
				}
				break;

			case 'available':

				$available_games = $this->get_available_games();

				foreach($items as $item) {
					if(isset($available_games[$item])) {
						$r = $available_games[$item];

						$content = $this->_get_file_content(trailingslashit(WP_CLANWARS_IMPORTPATH) . $r->package);

						$this->import_games($content);
					}
				}

				$url = add_query_arg('import', true, $url);

				break;
		}

		wp_redirect($url);
	}

	function on_settings() {

		$table_columns = array('cb' => '<input type="checkbox" />',
					  'user_login' => __('User Login', 'addict'),
					  'user_permissions' => __('Permissions', 'addict')
				);

		$games = $this->get_game('id=all');

		$obj = new stdClass();
		$obj->id = 0;
		$obj->title = __('All', 'addict');
		$obj->abbr = __('All', 'addict');
		$obj->icon = 0;

		array_unshift($games, $obj);

	?>
	<div class="wrap wp-cw-settings">

		<h2><?php _e('Settings', 'addict'); ?></h2>

		<?php if(isset($_GET['saved'])) : ?>
		<div class="updated fade"><p><?php _e('Settings saved.', 'addict'); ?></p></div>
		<?php endif; ?>

		<form method="post" action="admin-post.php">
			<?php wp_nonce_field('wp-clanwars-settings'); ?>
			<input type="hidden" name="action" value="wp-clanwars-settings" />

			 <table class="form-table">
				<tr valign="top">
					<th scope="row"><?php _e('Matches Category', 'addict'); ?></th>
					<td>
						<?php

						$selected = get_option(WP_CLANWARS_CATEGORY, -1);

						wp_dropdown_categories(
								array('name' => 'category',
									  'hierarchical' => true,
									  'show_option_none' => __('None', 'addict'),
									  'hide_empty' => 0,
									  'hide_if_empty' => 0,
									  'selected' => $selected)
								);

						?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e('Enable default styles', 'addict'); ?></th>
					<td><input type="checkbox" name="enable_default_styles" value="true"<?php checked(get_option(WP_CLANWARS_DEFAULTCSS), true); ?> /></td>
				</tr>
			 </table>

			<p class="submit">
				<input class="button-secondary" value="<?php _e('Save Changes', 'addict'); ?>" type="submit" />
			</p>

		</form>

		<h2><?php _e('User Access', 'addict'); ?></h2>

		<div id="col-container">

			<div id="col-right">
			<div class="col-wrap">

			<form method="post" action="admin-post.php">
				<?php wp_nonce_field('wp-clanwars-deleteacl'); ?>
				<input type="hidden" name="action" value="wp-clanwars-deleteacl" />

				<div class="tablenav">
					<div class="alignleft actions">
					<select name="doaction">
						<option value="" selected="selected"><?php _e('Actions', 'addict'); ?></option>
						<option value="delete"><?php _e('Delete', 'addict'); ?></option>
					</select>
					<input value="<?php _e('Apply', 'addict'); ?>" class="button-secondary action" type="submit" />
					</div>
					<br class="clear" />
				</div>


				<table class="widefat fixed" cellspacing="0">
					<thead>
					<tr>
						<?php $this->print_table_header($table_columns); ?>
					</tr>
					</thead>

					<tfoot>
					<tr>
						<?php $this->print_table_header($table_columns, false); ?>
					</tr>
					</tfoot>

					<tbody>
						<?php

						$acl = $this->acl_get();

						$keys = array_keys($acl);

						for($i = 0; $i < sizeof($keys); $i++) :

							$user_id = $keys[$i];
							$user_acl = $acl[$user_id];
							$user = get_userdata($user_id);

						?>

						<tr<?php if($i % 2 == 0) : ?> class="alternate"<?php endif; ?>>
							<th class="check-column"><input type="checkbox" class="check" name="users[]" value="<?php echo $user_id; ?>" /></th>
							<td><?php echo $user->user_login; ?></td>
							<td>
								<?php foreach($user_acl['permissions'] as $name => $is_allowed) : ?>
								<ul>
									<li><?php echo $this->acl_keys[$name]; ?>: <?php echo ($is_allowed) ? __('Yes', 'addict') : __('No', 'addict'); ?></li>
								</ul>
								<?php endforeach; ?>

								<?php
									$allowed_games = $this->acl_user_can('which_games', false, $user_id);
									$user_games = $this->get_game(array('id' => $allowed_games, 'orderby' => 'title', 'order' => 'asc'));

									if($allowed_games == 'all') {
										echo __('All', 'addict');
									}
								?>

									<?php foreach($user_games as $game) :

										$game_icon = wp_get_attachment_url($game->icon);

										if($game_icon !== false) {
											echo '<img src="' . $game_icon . '" alt="' . esc_attr($game->title) . '" class="icon" /> ';
										} else {
											echo esc_html(empty($game->abbr) ? $game->title : $game->abbr);
										}

									endforeach; ?>
							</td>
						</tr>

						<?php endfor; ?>
					</tbody>
				</table>

				<div class="tablenav">
					<div class="alignleft actions">
					<select name="doaction2">
						<option value="" selected="selected"><?php _e('Actions', 'addict'); ?></option>
						<option value="delete"><?php _e('Delete', 'addict'); ?></option>
					</select>
					<input value="<?php _e('Apply', 'addict'); ?>" class="button-secondary action" type="submit" />
					</div>
					<br class="clear" />
				</div>

			</form>

			</div></div>

			<div id="col-left">
			<div class="col-wrap">

			<h3><?php _e('Add New User', 'addict'); ?></h3>

			<form class="form-wrap" method="post" action="admin-post.php">
				<?php wp_nonce_field('wp-clanwars-acl'); ?>
				<input type="hidden" name="action" value="wp-clanwars-acl" />

				<div class="form-field">
					<label for="user"><?php _e('User', 'addict'); ?></label>
					<?php wp_dropdown_users('name=user'); ?>
				</div>

				<div class="form-field">
					<label><?php _e('Allow user manage specified games only:', 'addict'); ?></label>
					<ul class="listbox">
						<?php foreach($games as $g) : ?>
						<li><label for="game_<?php echo $g->id; ?>"><input type="checkbox" name="games[]" id="game_<?php echo $g->id; ?>" value="<?php echo $g->id; ?>" /> <?php echo esc_html($g->title); ?></label></li>
						<?php endforeach; ?>
					</ul>

					<p class="description"><?php _e('User can create new games <strong>only if &ldquo;All&rdquo; option is checked.</strong>', 'addict'); ?></p>
				</div>

				<div class="form-field">
					<label><?php _e('Allow user:', 'addict'); ?></label>
					<ul class="listbox">
						<?php foreach($this->acl_keys as $key => $title) : ?>
						<li><label for="<?php echo esc_attr($key); ?>"><input type="checkbox" class="check" name="permissions[<?php echo esc_attr($key); ?>]" value="1" id="<?php echo esc_attr($key); ?>" /> <?php echo $title; ?></label></li>
						<?php endforeach; ?>

					</ul>
				</div>

				<p class="submit">
					<input type="submit" class="button-secondary" value="<?php _e('Add User', 'addict'); ?>" />
				</p>
			</form>

			</div></div>

		</div>

	</div>

	<?php

	}

	function get_available_games() {
		$content = $this->_get_file_content(trailingslashit(WP_CLANWARS_IMPORTPATH) . 'import.json');

		if($content)
			return json_decode($content);

		return false;
	}

	function is_game_installed($title, $abbr = '', $objects = false) {

		if(!is_array($objects))
			$objects = $this->get_game('');

		foreach($objects as $p) {
			if(preg_match('#' . preg_quote($abbr, '#') . '#i', $p->abbr))
				return $p;

			if(preg_match('#' . preg_quote($title, '#') . '#i', $p->title))
				return $p;
		}

		return false;
	}

	function on_import() {

		$import_list = $this->get_available_games();
		$installed_games = $this->get_game('');

		if(isset($_GET['upload']))
			$this->add_notice(__('An upload error occurred while import.', 'addict'), 'error');

		if(isset($_GET['import']))
			$this->add_notice($_GET['import'] ? __('File successfully imported.', 'addict') : __('An error occurred while import.', 'addict'), $_GET['import'] ? 'updated' : 'error');

		echo $this->print_notices();

		?>
		<div class="wrap">
			<div id="icon-tools" class="icon32"><br></div>
			<h2><?php _e('Import games', 'addict'); ?></h2>

			<form id="wp-cw-import" method="post" action="admin-post.php" enctype="multipart/form-data">


				<input type="hidden" name="action" value="wp-clanwars-import" />
				<?php wp_nonce_field('wp-clanwars-import'); ?>


				<p><label for="upload"><input type="radio" name="import" id="upload" value="upload" checked="checked" /> <?php _e('Upload Package (gz file)', 'addict'); ?></label></p>

				<p><input type="file" name="userfile" /></p>

				<?php if(!empty($import_list)) : ?>

				<p><label for="available"><input type="radio" name="import" id="available" value="available" /> <?php _e('Import Available Packages', 'addict'); ?></label></p>

					<ul class="available-games">

					<?php foreach($import_list as $index => $game) :

						$installed = $this->is_game_installed($game->title, $game->abbr, $installed_games);

					?>

						<li>
							<label for="game-<?php echo $index; ?>">
								<input type="checkbox" name="items[]" id="game-<?php echo $index; ?>" value="<?php echo $index; ?>" /> <img src="<?php echo esc_attr(trailingslashit(WP_CLANWARS_IMPORTURL) . $game->icon); ?>" alt="<?php echo esc_attr($game->title); ?>" /> <?php echo esc_html($game->title); ?>
								<?php if($installed !== false) : ?>
								<span class="description"><?php _e('installed', 'addict'); ?></span>
								<?php endif; ?>
							</label>
						</li>

					<?php endforeach; ?>

					</ul>

				<?php endif; ?>

				<p class="submit"><input type="submit" class="button-secondary" value="<?php _e('Import', 'addict'); ?>" /></p>

			</form>

		</div>
		<?php

	}

}

/*
 * Initialization
 */

$wpClanWars = new WP_ClanWars();
add_action('init', array(&$wpClanWars, 'on_init'));


?>