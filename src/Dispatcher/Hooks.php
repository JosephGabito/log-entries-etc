<?php
namespace LogEntriesEtc\Dispatcher;

class Hooks {


	public function __construct( \DebugBar\StandardDebugBar $debugbar, \LogEntriesEtc\Config $config ) {

		$this->debugbar = $debugbar;

		$this->config = $config;

		add_action( 'init', array( $this, 'registerDebugger' ) );

		add_action( 'wp_head', array( $this, 'debugHeaders' ) );
		add_action( 'admin_head', array( $this, 'debugHeaders' ) );

		add_action( 'wp_footer', array( $this, 'render' ) );
		add_action( 'admin_footer', array( $this, 'render' ) );

	}

	public function registerDebugger() {

		$this->debugbarJSRenderer = $this->debugbar->getJavascriptRenderer();

		$this->debugbarJSRenderer->setBaseUrl( $this->config->getBaseURL() . '../vendor/maximebf/debugbar/src/DebugBar/Resources/' );

	}

	public function debugHeaders() {

		echo $this->debugbarJSRenderer->renderHead();

	}

	public function render() {
		$this->debugbar['messages']->addMessage( apply_filters( 'LogEntriesEtc\Message', 'Debugger' ) );

		echo $this->debugbarJSRenderer->render();

	}

}
