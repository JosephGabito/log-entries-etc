<?php

namespace LogEntriesEtc\Collectors;

class UAProcessCollector
{
    public const HTTP_OK_STATUSES = array( 200, 201 );

    public function __construct(\DebugBar\StandardDebugBar $debugbar)
    {
        $this->debugbar = $debugbar;

        if (! $this->debugbar->hasCollector('Automator')) {
            $this->debugbar->addCollector(new \DebugBar\DataCollector\MessagesCollector('Automator'));
        }
    }

    public function dispatch()
    {

        add_action('automator_before_configure', array( $this, 'configuration' ), 10);
        add_action('automator_configuration_complete', array( $this, 'configuration_complete'), 10);
        add_action('automator_add_integration', array( $this, 'adding_integration'), 10);
        add_action('automator_add_integration_helpers', array( $this, 'loading_integration_helpers'), 10);
        add_action('automator_add_integration_recipe_parts', array( $this, 'automator_add_integration_recipe_parts'), 10);
        add_filter('automator_integration_files', array($this, 'integration_files'), 10, 2);
        add_action('automator_before_init', array( $this, 'automator_before_init'));
        add_action('automator_before_traits', array( $this, 'automator_before_traits'));
        add_action('automator_after_traits', array( $this, 'automator_after_traits'));
        add_filter('automator_core_files', array($this,'automator_core_files_loading'), 999999, 1);
    }

    public function automator_core_files_loading($class)
    {
        $this->debugbar['Automator']->addMessage($this->getTimeElapsed() . ' -> Core files loading... ', 'expandable-head');
        $this->debugbar['Automator']->addMessage($class, 'expandable');
        return $class;
    }

    public function automator_after_traits()
    {
        $this->debugbar['Automator']->addMessage($this->getTimeElapsed() . ' -> All Traits loaded... >> ' . current_filter());
    }

    public function automator_before_traits()
    {
        $this->debugbar['Automator']->addMessage($this->getTimeElapsed() . ' -> Firing up Traits... >> ' . current_filter());
    }

    public function automator_before_init()
    {
        $this->debugbar['Automator']->addMessage($this->getTimeElapsed() . ' -> Automator before init... >> ' . current_filter());
    }

    public function integration_files($files, $dir_name)
    {
        $this->debugbar['Automator']->addMessage($this->getTimeElapsed() . ' -> Loading ' . $dir_name . ' integration files', 'expandable-head');
        $this->debugbar['Automator']->addMessage($files, 'expandable');
        return $files;
    }

    public function automator_add_integration_recipe_parts()
    {
        $this->debugbar['Automator']->addMessage($this->getTimeElapsed() . ' -> Recipe parts loaded... >> ' . current_filter());
    }

    public function loading_integration_helpers()
    {
        $this->debugbar['Automator']->addMessage($this->getTimeElapsed() . ' -> Loading integration helpers... >> ' . current_filter());
    }

    public function configuration()
    {
        $this->startProfile();
        $this->debugbar['Automator']->addMessage($this->getTimeElapsed() . ' -> Loading integrations, assets, utilities, database, core_automator.. >> ' . current_filter());
    }

    public function adding_integration()
    {
        $this->debugbar['Automator']->addMessage($this->getTimeElapsed() . ' -> Adding integrations... >> ' . current_filter());
    }

    public function configuration_complete()
    {
        $this->debugbar['Automator']->addMessage($this->getTimeElapsed() . ' -> Configuration complete... >> ' . current_filter());
    }

    private function startProfile()
    {
        $this->time_started = hrtime(true);
    }

    private function getTimeElapsed()
    {

        $end = hrtime(true);
        $eta = $end - $this->time_started;

        return round($eta / 1000000, 4) . 'ms';
    }
}
