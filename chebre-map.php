<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://chebre.net
 * @since             1.0.0
 * @package           Chebre_Map
 *
 * @wordpress-plugin
 * Plugin Name:       Chebre Map
 * Plugin URI:        http://chebre.net
 * Description:       Plugin ida ne hau kria atu facilita hatama dados koordenadas ba pontos turistikus, ou qualker uso nebe bele representa ho koordenadas(latitude e longitude),
 * Version:           1.0.0
 * Author:            Helder Chebre
 * Author URI:        http://chebre.net
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       chebre-map
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'CHEBRE_MAP_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-chebre-map-activator.php
 */
function activate_chebre_map() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-chebre-map-activator.php';
	Chebre_Map_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-chebre-map-deactivator.php
 */
function deactivate_chebre_map() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-chebre-map-deactivator.php';
	Chebre_Map_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_chebre_map' );
register_deactivation_hook( __FILE__, 'deactivate_chebre_map' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-chebre-map.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_chebre_map() {

	$plugin = new Chebre_Map();
	$plugin->run();

}
run_chebre_map();
