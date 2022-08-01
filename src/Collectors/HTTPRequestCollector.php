<?php

namespace LogEntriesEtc\Collectors;

class HTTPRequestCollector
{
    public const HTTP_OK_STATUSES = array( 200, 201 );

    public function __construct(\DebugBar\StandardDebugBar $debugbar)
    {
        $this->debugbar = $debugbar;

        if (! $this->debugbar->hasCollector('HTTP_Requests')) {
            $this->debugbar->addCollector(new \DebugBar\DataCollector\MessagesCollector('HTTP_Requests'));
        }
    }

    public function dispatch()
    {
        add_filter('pre_http_request', array( $this, 'startProfile' ), 10, 3);
        add_action('http_api_debug', array( $this, 'collect' ), 10, 5);
    }

    public function startProfile($preempt, $parsed_args, $url)
    {
        $domain = str_ireplace('www.', '', parse_url($url, PHP_URL_HOST));
        $this->time_started = hrtime(true);
        $this->debugbar['time']->startMeasure('http_requests', $domain, parse_url($url, PHP_URL_PATH));

        return $preempt;
    }

    public function collect($response, $requests, $class, $parsed_args, $url)
    {

        $this->collectStatusCodes($response, $url, $parsed_args);

        $this->collectRequestsArgs($parsed_args, $url);

        $this->collectResponseBody($response);

        $this->collectResponseHeaders($response);

        $this->collectHttpRequest($response, $requests, $class, $parsed_args, $url);

        $this->debugbar['time']->stopMeasure('http_requests');
    }

    public function collectResponseHeaders($response)
    {
        $this->debugbar['HTTP_Requests']->addMessage(
            array(
                'Response:Header' => wp_remote_retrieve_headers($response),
            ),
            'http-response-header'
        );
    }

    public function collectStatusCodes($response, $url, $parsed_args)
    {

        $status_code = wp_remote_retrieve_response_code($response);

        if (empty($status_code)) {
            $this->debugbar['HTTP_Requests']->addMessage($parsed_args['method'] . ' ' . $this->getTimeElapsed() . ' ' . $url, 'http-request-url');
            return $this;
        }

        if (! in_array($status_code, self::HTTP_OK_STATUSES, true)) {
            $this->debugbar['HTTP_Requests']->addMessage($status_code . ' ' . $this->getTimeElapsed() . $parsed_args['method'] . ' ' . $url, 'http-request-url-error');
            return $this;
        }

        $this->debugbar['HTTP_Requests']->addMessage($status_code . ' ' . $this->getTimeElapsed() . ' ' . $parsed_args['method'] . ' ' . $url, 'http-request-url');

        return $this;
    }

    public function collectResponseBody($response)
    {

        $status_code = wp_remote_retrieve_response_code($response);

        // Only add response body if status code is not empty.
        if (! empty($status_code)) {
            $this->debugbar['HTTP_Requests']->addMessage(
                array(
                    'Response:Body' => wp_remote_retrieve_body($response),
                ),
                'http-response-body'
            );
        }

        return $this;
    }

    public function collectRequestsArgs($parsed_args, $url)
    {
        $this->debugbar['HTTP_Requests']->addMessage(
            array(
                'Request:Args' => $parsed_args,
            ),
            'http-response-args'
        );
    }

    public function collectHttpRequest($response, $requests, $class, $parsed_args, $url)
    {
        $this->debugbar['HTTP_Requests']->addMessage(
            array(
                'Summary' => array(
                    'Url'       => $url,
                    'Class'     => $class,
                    'Arguments' => $parsed_args,
                    'Response'  => $response,
                    'Request'   => $requests,
                ),
            ),
            'http-request-summary'
        );
    }

    private function getTimeElapsed()
    {

        $end = hrtime(true);
        $eta = $end - $this->time_started;

        return round($eta / 1000000, 2) . 'ms';
    }
}
