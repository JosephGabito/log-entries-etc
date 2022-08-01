<?php
/**
 * Plugin Name: Log Entries Etcetera
 * Plugin URI:  https://josephwp.com
 * Description: A WordPress wrapper for PHP Debug Bar. Useful when troubleshooting.
 * Version:     1.0.0
 * Requires at least: 4.6
 * Tested up to: 4.8
 * Requires PHP: 7.4.0
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
use LogEntriesEtc\Collectors\UAProcessCollector;

if ( version_compare(phpversion(), '7.4', '<') ) {
    return;
}

require_once __DIR__ . '/vendor/autoload.php';

$container = new \DI\Container();

// WordPress wrapper for phpdebug.
$debugEmitter = $container->get( DebugContainer::class );
$debugEmitter->emit();

// Http Request Data Collectors.
$httpRequestCollector = $container->get( HTTPRequestCollector::class );
$httpRequestCollector->dispatch();

// UncannyAutomator Process Data Collector.
$UAProcessCollector = $container->get( UAProcessCollector::class );
$UAProcessCollector->dispatch();
