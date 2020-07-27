<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              unitycode.tech
 * @since             1.0.0
 * @package           Integracao_Bao
 *
 * @wordpress-plugin
 * Plugin Name:       Integracao BAO
 * Plugin URI:        https://github.com/joanezandrades/integracao-bao
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            jnz93
 * Author URI:        unitycode.tech
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       integracao-bao
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
define( 'INTEGRACAO_BAO_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-integracao-bao-activator.php
 */
function activate_integracao_bao() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-integracao-bao-activator.php';
	Integracao_Bao_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-integracao-bao-deactivator.php
 */
function deactivate_integracao_bao() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-integracao-bao-deactivator.php';
	Integracao_Bao_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_integracao_bao' );
register_deactivation_hook( __FILE__, 'deactivate_integracao_bao' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-integracao-bao.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_integracao_bao() {

	$plugin = new Integracao_Bao();
	$plugin->run();

}
run_integracao_bao();
