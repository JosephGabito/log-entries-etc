<?php

namespace LogEntriesEtc\Collectors;

class HTTPRequestCollector
{
    public const HTTP_OK_STATUSES = array( 200, 201 );

    public function __construct(\DebugBar\StandardDebugBar $debugbar)
    {
        $this->debugbar = $debugbar;
    }

    public function dispatch()
    {

        add_action('http_api_debug', array( $this, 'collect' ), 10, 5);
        add_filter('pre_http_request', array( $this, 'startProfile' ), 10, 2);
    }

    public function startProfile($parsed_args, $url)
    {
        $this->debugbar['time']->startMeasure('http_requests', 'HTTP Requests');
    }

    public function collect($response, $requests, $class, $parsed_args, $url)
    {

        if (! $this->debugbar->hasCollector('HTTP_Requests')) {
            $this->debugbar->addCollector(new \DebugBar\DataCollector\MessagesCollector('HTTP_Requests'));
        }

        $status_code = wp_remote_retrieve_response_code($response);

        if (! in_array($status_code, self::HTTP_OK_STATUSES, true)) {
            $this->debugbar['HTTP_Requests']->error($url);
            $this->debugbar['HTTP_Requests']->error('Status code: ' . $status_code);
        } else {
            $this->debugbar['HTTP_Requests']->info($url);
            $this->debugbar['HTTP_Requests']->info('Status code: ' . $status_code);
        }

        $this->debugbar['HTTP_Requests']->info(
            array(
                'Request' => $parsed_args,
            )
        );

        $this->debugbar['HTTP_Requests']->info(
            array(
                'Response:Body' => wp_remote_retrieve_body($response),
            )
        );

        $this->debugbar['HTTP_Requests']->info(
            array(
                'Response:Header' => wp_remote_retrieve_headers($response),
            )
        );

        $this->debugbar['HTTP_Requests']->addMessage(
            array(
                'WPHTTP:Debug:' . $class => array(
                    'Response'  => $response,
                    'Request'   => $requests,
                    'Class'     => $class,
                    'Arguments' => $parsed_args,
                    'Url'       => $url,
                ),
            )
        );

        $this->debugbar['HTTP_Requests']->info('End~');
        $this->debugbar['time']->stopMeasure('http_requests');
    }
}
