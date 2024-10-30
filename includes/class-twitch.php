<?php
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-twitch-activator.php';
/**
 * The core plugin class.
 *
 * @since      0.0.1
 * @package    LiveTwitch
 */
class LiveTwitch {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    0.0.1
	 * @access   protected
	 * @var      LiveTwitch_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    0.0.1
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name = "LiveTwitch";

	/**
	 * The current version of the plugin.
	 *
	 * @since    0.0.1
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	public $LiveTwitch_Activator;
	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    0.0.1
	 */
	public function __construct() {
		if ( defined( 'PLUGIN_NAME_VERSION' ) ) {
			$this->version = PLUGIN_NAME_VERSION;
		} else {
			$this->version = '0.0.3';
		}
		$this->activator = new LiveTwitch_Activator();
		$this->plugin_name = 'LiveTwitch';
		$this->plugin_slug = 'livetwitch';
		error_log($this->plugin_name);
	}

	public function init(){
		$this->RegisterCustomPostType();
		$this->load_dependencies();
		$cron = new TwitchCron();
		$cron->registerCron();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		add_shortcode( 'livetwitch',  [$this, 'LiveTwitch_shortcode']);
		$this->loader->run();
	}

	private function RegisterCustomPostType() {
		add_action('init', [$this, 'CreateCustomPostType']);
	}

	public function CreateCustomPostType() {
		register_post_type($this->activator->CustomPostType, $this->activator->CustomPostTypeRegistrationInfo);
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - LiveTwitch_Loader. Orchestrates the hooks of the plugin.
	 * - Twitch_i18n. Defines internationalization functionality.
	 * - Twitch_Admin. Defines all hooks for the admin area.
	 * - Twitch_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    0.0.1
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-twitch-loader.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-twitch-widget.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-twitch-cron.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-twitch-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-twitch-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-twitch-public.php';

		$this->loader = new LiveTwitch_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Twitch_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    0.0.1
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Twitch_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    0.0.1
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Twitch_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    0.0.1
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Twitch_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    0.0.1
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     0.0.1
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	public function get_plugin_slug() {
		return $this->plugin_slug;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     0.0.1
	 * @return    LiveTwitch_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     0.0.1
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Create shortcode that takes 2 variables (count and singular).
	 *
	 * @since     0.0.1
     * @var       string    $atts TODO.
	 */
	public function LiveTwitch_shortcode( $atts ) {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-twitch-widget.php';

		$output = "";
		$atts = shortcode_atts(
			array(
				'count' => '',
				'singular' => '',
				'orderby' => 'viewers',
				'order'	=> 'DESC',
				'singletext' => '',
			),
			$atts,
			'LiveTwitch'
		);

		// return 
		if(!empty($atts['singular']) || strlen($atts['singular']) > 0 ){

			$singletext = false;

            $viewers = $game = $link = $image = $stream_title = "";
			$streams = get_posts(
							array(
								'title'      => $atts['singular'], // by post title
								'post_type'  => 'twitch', // post type of your preference
								'meta_query' => 
									array(
										'key'   => 'type',   
										'value' => 'live',
									)
							)
						);
			$streams = $streams[0];
            $cf = get_post_custom($streams->ID);

            if(isset($cf['viewer_count'])){
				$viewers = $cf['viewer_count'][0];
				if(!empty($atts['singletext'])){
					$singletext = true;
				}
            }
            if(isset($cf['game_id'])){
                $game = $cf['game_id'][0];
            }
            if(isset($cf['thumbnail_url'])){
                $image = $cf['thumbnail_url'][0];
                $image = str_replace('{width}', '250', $image);
                $image = str_replace('{height}', '150', $image);
            }
            if(isset($cf['title'])){
                $stream_title = $cf['title'][0];
            }

            $stream = [
                'title'         => get_the_title($streams->ID),
                'stream_title'  => $stream_title,
                'viewers'       => $viewers,
                'game'          => $game,
                'image'         => $image,
			];
			wp_reset_postdata();
			
		}else{
			if($atts['count'] > 0){
				$count = $atts['count'];
			}else {
				$count = 5;
			}
			$streams = LiveTwitchWidget::LiveTwitch_GetStreams($count, $atts['orderby'], $atts['order']);
		}
		ob_start();
		if( is_array($streams) ){
            foreach($streams as $stream){
				$stream = array_map(array('LiveTwitchWidget','LiveTwitch_DisplayString'), $stream);
				if($stream['viewers'] != "N/A"){
					echo $this->display_stream($stream);
				}
				
            }
		}elseif ($singletext) {
			if($stream['viewers'] != "N/A"){
				echo '<span class="livetwitch is-live">Live</span>';
			}
		}else {
			$stream = array_map(array('LiveTwitchWidget','LiveTwitch_DisplayString'), $stream);
			if($stream['viewers'] != "N/A"){
				echo $this->display_stream($stream);
			}
		}
		
		
		return ob_get_clean();
	
	}

    /**
	 * Add filter for Twitch game name.
	 *
	 * @since    0.0.3
	 * @access   public
	 * @var      string    $game    Game name.
	 */
	static function livetwitch_gamedisplay($game) {
		return apply_filters('livetwitch_gamedisplay_filter', $game);
	}

	static public function display_stream($stream){

		$options = get_option( 'twitch_settings' );
		$class = "";

		if(isset($options['twitch_style'])){
			if($options['twitch_style']){
				$class = " dark";
			}
		}

		$st_length = strlen($stream['stream_title']);
		$extra_class = "";
		if($st_length > 30) {
			$extra_class = " rolling-text";
			if($st_length <= 60) {
				$extra_class .= "-60";
			}elseif($st_length <= 100 && $st_length > 60) {
				$extra_class .= "-100";
			}elseif($st_length <= 200 && $st_length > 100) {
				$extra_class .= "-200";
			}
		}

		ob_start();
		?>
		<div class="mx-auto twitch-card<?php echo $class; ?>">
			<a target="_blank" class="twitch-link" href="https://www.twitch.tv/<?php echo $stream['title']; ?>">
				<div class="thumbnail">
					<img class='img-responsive' src='<?php echo $stream['image'];?>'>
					<p class="streamer"><?php echo $stream['title'];?></p>
					<p class="viewers"><svg class="views" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 21 21"><g transform="matrix(.02146 0 0 .02146 1 1)"><path d="m466.07 161.53c-205.6 0-382.8 121.2-464.2 296.1-2.5 5.3-2.5 11.5 0 16.9 81.4 174.9 258.6 296.1 464.2 296.1 205.6 0 382.8-121.2 464.2-296.1 2.5-5.3 2.5-11.5 0-16.9-81.4-174.9-258.6-296.1-464.2-296.1m0 514.7c-116.1 0-210.1-94.1-210.1-210.1 0-116.1 94.1-210.1 210.1-210.1 116.1 0 210.1 94.1 210.1 210.1 0 116-94.1 210.1-210.1 210.1"/><circle cx="466.08" cy="466.02" r="134.5"/></g></svg><?php echo $stream['viewers'];?></p>
					<div class="stream-title<?php echo $extra_class; ?>">
						<p><?php echo $stream['stream_title']; ?></p>
					</div>
				</div>
				<div class="information">
					<p class="game">
						<?php 
						echo LiveTwitch::livetwitch_gamedisplay($stream['game']);
						?>
					</p>
				</div>
			</a>
		</div>
		<?php
		return ob_get_clean();

	}

	static public function uninstall() {
		if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
			exit;
		}
		if (!function_exists('add_action'))	{
			header('Status: 403 Forbidden');
			header('HTTP/1.1 403 Forbidden');
			exit();
		}
		if (!current_user_can('manage_options'))	{
			header('Status: 403 Forbidden');
			header('HTTP/1.1 403 Forbidden');
			exit();
		}
		if (!defined('WP_UNINSTALL_PLUGIN')) exit();

		wp_clear_scheduled_hook('twitch_live_cron');
		add_action('init','twitch_unregister_post_type');
		unregister_post_type( 'twitch' );
		delete_option('twitch_plugin_settings');
		delete_site_option('twitch_plugin_settings');
	}

}
