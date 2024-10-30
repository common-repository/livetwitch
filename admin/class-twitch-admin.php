<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @since      0.0.1
 *
 * @package    LiveTwitch
 * @subpackage Twitch/includes
 * @author     Jerry Isaksson <jerry@allofesports.com>
 */
class Twitch_Admin {

	/**
	 * 
	 * Create action for admin menu page and setting init
	 *
	 * @since    0.0.1
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		add_action( 'admin_menu', [$this, 'twitch_add_admin_menu'] );
		add_action( 'admin_init', [$this, 'twitch_settings_init'] );
	}

	/**
	 * 
	 * Adds LiveTwitch -link to Settings menu
	 *
	 * @since    0.0.1
	 */
	public function twitch_add_admin_menu(  ) { 
		add_options_page( 'LiveTwitch', 'LiveTwitch', 'manage_options', 'twitch_live', [$this, 'twitch_options_page'] );
	
	}
	
	/**
	 * 
	 * Register settings (Twitch API Key, Offline text). API key is used to fetch data from Twitch 
	 * and Offline text will be displayed when there are no live streamers.
	 *
	 * @since    0.0.1
	 */
	public function twitch_settings_init(  ) { 
	
		register_setting( 'twitch_plugin_settings', 'twitch_settings' );
		
		//SECTIONS
		add_settings_section(
			'twitch_plugin_setting_section', 
			__( 'Settings' ), 
			[$this, 'twitch_settings_section_callback'], 
			'twitch_plugin_settings'
		);

		add_settings_section(
			'twitch_plugin_style_section', 
			__( 'Style' ), 
			[$this, 'twitch_settings_section_callback'], 
			'twitch_plugin_settings'
		);
	
		//FIELDS
		add_settings_field( 
			'twitch_apikey', 
			__( 'API key', 'twitch' ), 
			[$this, 'twitch_apikey_render'], 
			'twitch_plugin_settings', 
			'twitch_plugin_setting_section' 
		);
		add_settings_field( 
			'twitch_offlinetext', 
			__( 'Offline text', 'twitch' ), 
			[$this, 'twitch_offlinetext_render'], 
			'twitch_plugin_settings', 
			'twitch_plugin_setting_section' 
		);
		add_settings_field( 
			'twitch_style', 
			__( 'Dark' ), 
			[$this, 'twitch_style_render'], 
			'twitch_plugin_settings', 
			'twitch_plugin_style_section' 
		);	
	
	}
	
	/**
	 * 
	 * Render API key -settings field.
	 *
	 * @since    0.0.1
	 */
	public function twitch_apikey_render(  ) { 
	
		$options = get_option( 'twitch_settings' );
		?>
		<input type='text' name='twitch_settings[twitch_apikey]' size="32" value='<?php echo $options['twitch_apikey']; ?>'>
		<p>You need to have Twitch Developer <b>API key</b>, to fetch the necessery data from Twitch</p>
		<?php
	
	}
	
	/**
	 * 
	 * Render Offline text -settings field.
	 *
	 * @since    0.0.1
	 */
	public function twitch_offlinetext_render(  ) { 
	
		$options = get_option( 'twitch_settings' );
		?>
		<textarea name='twitch_settings[twitch_offlinetext]' cols=35 rows=3><?php echo $options['twitch_offlinetext']; ?></textarea>
		<p>This text will be displayed, when there aren't any live streams to list. (Default: No live streams.)</p>
		<?php
	
	}	
		
	/*
	public function twitch_streams_render(  ) { 
	
		$options = get_option( 'twitch_settings' );
		?>
		<textarea name='twitch_settings[twitch_streams]' cols=35 rows=3><?php echo $options['twitch_streams']; ?></textarea>
		<p>Add the streams that you want to display/follow seperated by comma (Example: riotgames,eslone,shourd).</p>
		<?php
	
	}*/

	/**
	 * 
	 * Change between dark and light style.
	 *
	 * @since    0.0.1
	 */
	public function twitch_style_render(  ) { 
	
		$options = get_option( 'twitch_settings' );
		$checked = NULL;
		if(isset($options['twitch_style'])){
			$checked =  $options['twitch_style'];
		}
		?>
		<input type="checkbox" value="1" <?php checked( $checked, 1 ); ?> name='twitch_settings[twitch_style]' size="32">
		Check for darker display theme.
		<?php
	
	}

	public function twitch_settings_section_callback(  ) { 
		//echo "Header of the settings section";

	}

	/**
	 * 
	 * Construct the LiveTwitch options page
	 *
	 * @since    0.0.1
	 */
	public function twitch_options_page() { 

		?>
		<form action='options.php' method='post'>
	
			<h1>LiveTwitch</h1>
	
			<?php
			settings_fields( 'twitch_plugin_settings' );
			do_settings_sections( 'twitch_plugin_settings' );
			submit_button();
			?>
	
		</form>
		<?php
	
	}
	public function enqueue_styles() {

	}
	public function enqueue_scripts() {

	}

}
