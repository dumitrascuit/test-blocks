<?php
/**
 * Plugin Name: Test Blocks
 * Description: Plugin to add custom content
 * Version: 0.0.1
 * Author: MADL
 * Author URI:
 * Requires at least: 5.8.4
 * Requires PHP: 7.4
 * Text Domain: madl
 */

use TestBlocks\TestBlocks;

//  Exit if accessed directly.
defined('ABSPATH') || exit;

define('TEST_BLOCKS_DIR', plugin_dir_path(__FILE__));
define('TEST_BLOCKS_URL', plugin_dir_url(__FILE__));
define('TEST_BLOCKS_DEBUG_ENABLED', defined('WP_DEBUG') && WP_DEBUG);
define('TEST_BLOCKS_DEBUG_LOG_ENABLED', defined('WP_DEBUG_LOG') && WP_DEBUG_LOG);

require TEST_BLOCKS_DIR . 'vendor/autoload.php';

add_action('plugins_loaded', function () {
    TestBlocks::getInstance()->setInstances();
}, 20);
