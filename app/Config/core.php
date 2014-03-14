<?php
/**
 * This is core configuration file.
 *
 * Use it to configure core behavior of Cake.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Config
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * CakePHP Debug Level:
 *
 * Production Mode:
 * 	0: No error messages, errors, or warnings shown. Flash messages redirect.
 *
 * Development Mode:
 * 	1: Errors and warnings shown, model caches refreshed, flash messages halted.
 * 	2: As in 1, but also with full debug messages and SQL output.
 *
 * In production mode, flash messages redirect after a time interval.
 * In development mode, you need to click the flash message to continue.
 */
	Configure::write('debug', 2);
	
/**
 * Set Laser App default configuration values 
 */
	Configure::write('LaserApp.pstoedit_command', 'pstoedit -q -f "gcode: -speed {{SPEED}} -intensity {{POWER}} -noheader -nofooter" {{FILE}}');
	Configure::write('LaserApp.storage_path', APP.'webroot'.DS.'files');
	Configure::write('LaserApp.default_max_cut_feedrate', 1000);
	Configure::write('LaserApp.default_traversal_feedrate', 6000);
	Configure::write('LaserApp.user_secret', 'SECRET');
	Configure::write('LaserApp.user_secret_prompt', 'Enter the secret password');
	Configure::write('LaserApp.user_secret_enabled', true);
	Configure::write('LaserApp.power_scale', 100);
	
	//Read customized values
	
	Configure::load('config');
	
/**
 * Application configuration values.  Shouldn't need to be modified by users.
 */
	define('PDF_PATH', Configure::read('LaserApp.storage_path'));
	$base_url = Configure::read('LaserApp.base_url');
	
	if ((!empty($base_url)) && (!defined('FULL_BASE_URL'))) {
		Configure::write('App.fullBaseUrl', Configure::read('LaserApp.base_url'));
	}
	

	if (class_exists('Redis') && file_exists(APP.'Config'.DS.'resque-config.php')) {
		Configure::load('resque-config');
	}
	
	define('PATH_MOVE_UP', -1);
	define('PATH_MOVE_DOWN', 1);
	
	Configure::write('App.version', '2.0-RC2');
	Configure::write('App.pstoedit_command', 'pstoedit -q -f "gcode: -speed {{SPEED}} -intensity {{POWER}} -noheader -nofooter" {{FILE}}');
	Configure::write('App.max_email_retries', 5);
	Configure::write('App.title', 'GCode Creator');
	Configure::write('App.allowed_file_types', array('application/pdf'));
	Configure::write('App.colors', array(
		'#000000','#FF0000','#00FF00','#0000FF','#FFFF00','#FF00FF','#00FFFF',
		'#800000','#008000','#000080','#808000','#800080','#008080','#C0C0C0',
		'#808080','#9999FF','#993366','#FFFFCC','#CCFFFF','#660066','#FF8080',
	));

/**
 * Configure the Error handler used to handle errors for your application. By default
 * ErrorHandler::handleError() is used. It will display errors using Debugger, when debug > 0
 * and log errors with CakeLog when debug = 0.
 *
 * Options:
 *
 * - `handler` - callback - The callback to handle errors. You can set this to any callable type,
 *   including anonymous functions.
 *   Make sure you add App::uses('MyHandler', 'Error'); when using a custom handler class
 * - `level` - int - The level of errors you are interested in capturing.
 * - `trace` - boolean - Include stack traces for errors in log files.
 *
 * @see ErrorHandler for more information on error handling and configuration.
 */
	Configure::write('Error', array(
		'handler' => 'ErrorHandler::handleError',
		'level' => E_ALL & ~E_DEPRECATED,
		'trace' => true
	));

/**
 * Configure the Exception handler used for uncaught exceptions. By default,
 * ErrorHandler::handleException() is used. It will display a HTML page for the exception, and
 * while debug > 0, framework errors like Missing Controller will be displayed. When debug = 0,
 * framework errors will be coerced into generic HTTP errors.
 *
 * Options:
 *
 * - `handler` - callback - The callback to handle exceptions. You can set this to any callback type,
 *   including anonymous functions.
 *   Make sure you add App::uses('MyHandler', 'Error'); when using a custom handler class
 * - `renderer` - string - The class responsible for rendering uncaught exceptions. If you choose a custom class you
 *   should place the file for that class in app/Lib/Error. This class needs to implement a render method.
 * - `log` - boolean - Should Exceptions be logged?
 *
 * @see ErrorHandler for more information on exception handling and configuration.
 */
	Configure::write('Exception', array(
		'handler' => 'ErrorHandler::handleException',
		'renderer' => 'ExceptionRenderer',
		'log' => true
	));

/**
 * Application wide charset encoding
 */
	Configure::write('App.encoding', 'UTF-8');

/**
 * To configure CakePHP *not* to use mod_rewrite and to
 * use CakePHP pretty URLs, remove these .htaccess
 * files:
 *
 * /.htaccess
 * /app/.htaccess
 * /app/webroot/.htaccess
 *
 * And uncomment the App.baseUrl below:
 */
	//Configure::write('App.baseUrl', env('SCRIPT_NAME'));

/**
 * Uncomment the define below to use CakePHP prefix routes.
 *
 * The value of the define determines the names of the routes
 * and their associated controller actions:
 *
 * Set to an array of prefixes you want to use in your application. Use for
 * admin or other prefixed routes.
 *
 * 	Routing.prefixes = array('admin', 'manager');
 *
 * Enables:
 *	`admin_index()` and `/admin/controller/index`
 *	`manager_index()` and `/manager/controller/index`
 *
 */
Configure::write('Routing.prefixes', array('admin'));

/**
 * Turn off all caching application-wide.
 *
 */
	//Configure::write('Cache.disable', true);

/**
 * Enable cache checking.
 *
 * If set to true, for view caching you must still use the controller
 * public $cacheAction inside your controllers to define caching settings.
 * You can either set it controller-wide by setting public $cacheAction = true,
 * or in each action using $this->cacheAction = true.
 *
 */
	//Configure::write('Cache.check', true);

/**
 * Enable cache view prefixes.
 *
 * If set it will be prepended to the cache name for view file caching. This is
 * helpful if you deploy the same application via multiple subdomains and languages,
 * for instance. Each version can then have its own view cache namespace.
 * Note: The final cache file name will then be `prefix_cachefilename`.
 */
	//Configure::write('Cache.viewPrefix', 'prefix');

/**
 * Defines the default error type when using the log() function. Used for
 * differentiating error logging and debugging. Currently PHP supports LOG_DEBUG.
 */
	define('LOG_ERROR', LOG_ERR);

	Configure::write('Session', array(
		'defaults' => 'database',
		'cookie' => 'LaserApp',
		'handler' => array(
			'model' => 'Session',
		)
	));

/**
 * A random string used in security hashing methods.
 */	Configure::write('Security.salt', '235aaca1f037520e87db22d78019ca85cad3277c');

/**
 * A random numeric string (digits only) used to encrypt/decrypt strings.
 */	Configure::write('Security.cipherSeed', '636536313937623930333266323837');

/**
 * Apply timestamps with the last modified time to static assets (js, css, images).
 * Will append a querystring parameter containing the time the file was modified. This is
 * useful for invalidating browser caches.
 *
 * Set to `true` to apply timestamps when debug > 0. Set to 'force' to always enable
 * timestamping regardless of debug value.
 */
	//Configure::write('Asset.timestamp', true);

/**
 * Compress CSS output by removing comments, whitespace, repeating tags, etc.
 * This requires a/var/cache directory to be writable by the web server for caching.
 * and /vendors/csspp/csspp.php
 *
 * To use, prefix the CSS link URL with '/ccss/' instead of '/css/' or use HtmlHelper::css().
 */
	//Configure::write('Asset.filter.css', 'css.php');

/**
 * Plug in your own custom JavaScript compressor by dropping a script in your webroot to handle the
 * output, and setting the config below to the name of the script.
 *
 * To use, prefix your JavaScript link URLs with '/cjs/' instead of '/js/' or use JavaScriptHelper::link().
 */
	//Configure::write('Asset.filter.js', 'custom_javascript_output_filter.php');

/**
 * Uncomment this line and correct your server timezone to fix
 * any date & time related errors.
 */
	//date_default_timezone_set('UTC');


//If CakeResque is defined, use redis for caching as well..
if (!is_null(Configure::read('CakeResque'))) {
	$engine = 'Redis';
} else {
	$engine = 'File';
}
Cache::config('default', array(
	'engine' => $engine,
	'duration' => 3600,
	'prefix' => 'laser-gcode_cache_',
//	'server' => Configure::read('CakeResque.Redis.host'),
//	'port' => Configure::read('CakeResque.Redis.port'),
));

// In development mode, caches should expire quickly.
$duration = '+999 days';
if (Configure::read('debug') > 0) {
	$duration = '+10 seconds';
}

// Prefix each application on the same server with a different string, to avoid Memcache and APC conflicts.
$prefix = 'laser-gcode-gen_';
/**
 * Configure the cache used for general framework caching. Path information,
 * object listings, and translation cache files are stored with this configuration.
 */
Cache::config('_cake_core_', array(
	'engine' => $engine,
	'prefix' => $prefix . 'cake_core_',
	'path' => CACHE . 'persistent' . DS,
	'serialize' => ($engine === 'File'),
	'duration' => $duration,
	'server' => Configure::read('CakeResque.Redis.host'),
	'port' => Configure::read('CakeResque.Redis.port'),
	
));

/**
 * Configure the cache for model and datasource caches. This cache configuration
 * is used to store schema descriptions, and table listings in connections.
 */
Cache::config('_cake_model_', array(
	'engine' => $engine,
	'prefix' => $prefix . 'cake_model_',
	'path' => CACHE . 'models' . DS,
	'serialize' => ($engine === 'File'),
	'duration' => $duration,
	'server' => Configure::read('CakeResque.Redis.host'),
	'port' => Configure::read('CakeResque.Redis.port'),
	
));
