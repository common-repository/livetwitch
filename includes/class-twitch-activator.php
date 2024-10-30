<?php

/**
 * Fired during plugin activation
 *
 * @since      0.0.1
 *
 * @package    LiveTwitch
 * @subpackage LiveTwitch/includes
 * @author     Jerry Isaksson <jerry@allofesports.com>
 */

class LiveTwitch_Activator {

	/**
	 * Create and register Custom Post Type & Custom Fields.
	 *
	 *
	 * @since    0.0.1
	 */
	public $CustomPostType = 'Twitch';
	public $CustomPostTypeRegistrationInfo = null;
	
	function __construct($init = true){
		// This was also added to my constructor
		// inittialize $customPostTypeRegistrationInfo and $taxonomiesRegistrationInfo
		if ($init) $this->init();
		
	}
	
	public function init() {

		$labels = array(
			'name'                  => _x( 'Twitch', 'Post Type General Name', 'twitch' ),
			'singular_name'         => _x( 'Twitch', 'Post Type Singular Name', 'twitch' ),
		);
		$args = array(
			'label'                 => __( 'Twitch', 'twitch' ),
			'description'           => __( 'Post Type Description', 'twitch' ),
			'labels'                => $labels,
			'hierarchical'          => false,
			'public'                => true,
			'show_ui'               => true,
			'show_in_menu'          => true,
			'menu_position'         => 20,
			'show_in_admin_bar'     => true,
			'show_in_nav_menus'     => true,
			'rewrite'				=> false,
			'can_export'            => true,
			'has_archive'           => false,
			'exclude_from_search'   => true,
			'publicly_queryable'    => false,
			'menu_icon' => 'dashicons-businessman',
			'supports' => [
				'title',
				'custom-fields',
			],
			'capability_type'       => 'post',
		);
		$this->CustomPostTypeRegistrationInfo = $args;
		$this->LiveTwitch_CustomFields();
		//$this->registerCustomLogMenuPage();
	}

	private function LiveTwitch_CustomFields() {
		//Might need at some point
	}

}
