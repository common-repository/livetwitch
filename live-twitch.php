<?php

/**
 *
 * @link              http://allofesports.com
 * @since             0.0.1
 * @package           LiveTwitch
 *
 * @wordpress-plugin
 * Plugin Name:       LiveTwitch
 * Plugin URI:        http://allofesports.com/livetwitch
 * Description:       With this plugin you can display specified streams that are live on Twitch.tv
 * Version:           0.0.3
 * Author:            Jerry Isaksson
 * Author URI:        http://allofesports.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       LiveTwitch
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'LiveTwitch', '0.0.5' );
/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/class-twitch.php';

$twitch = new LiveTwitch;
$twitch->init();

