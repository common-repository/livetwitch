<?php

/**
 * Fired during plugin deactivation
 *
 * @link       http://localhost
 * @since      0.0.1
 *
 * @package    LiveTwitch
 * @subpackage LiveTwitch/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      0.0.1
 * @package    LiveTwitch
 * @subpackage LiveTwitch/includes
 * @author     test <tes@sti>
 */
class LiveTwitch_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    0.0.1
	 */
	public static function deactivate() {
		add_action('init','twitch_unregister_post_type');
	}

	public static function twitch_unregister_post_type(){
		unregister_post_type( 'twitch' );
	}

}
