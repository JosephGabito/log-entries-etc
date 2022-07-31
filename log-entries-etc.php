<?php
/**
 * Plugin Name: Log Entries Etcetera
 * Plugin URI:  https://josephwp.com
 * Description: A WordPress wrapper for PHP Debug Bar. Useful when troubleshooting.
 * Version:     1.0.0
 * Author:      Joseph G.
 * Author URI:  https://josephwp.com
 * License:     GPL2+
 * Text Domain: log-entries-etc
 * Domain Path: /languages/
 *
 * @package LogEntriesEtc
 */
namespace LogEntriesEtc;

use LogEntriesEtc\Events\Emitter\DebugContainer;
use LogEntriesEtc\Collectors\HTTPRequestCollector;

require_once __DIR__ . '/vendor/autoload.php';

$container = new \DI\Container();

$debugEmitter = $container->get( DebugContainer::class );
$debugEmitter->emit();

// Subscribe to http api debug
add_action( 'http_api_debug', array( $container->get( HTTPRequestCollector::class ), 'collect' ), 10, 5 );
