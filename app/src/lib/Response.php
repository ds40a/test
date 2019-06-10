<?php

namespace Test\lib;

/**
 * Class Response
 */
class Response extends ContainerAware
{
    protected $content;

    protected $responseCode;

    /**
     * Response constructor.
     *
     * @param string $content
     * @param int    $responseCode
     */
    public function __construct($content = 'OK!', $responseCode = 200)
    {
        $this->content = $content;
        $this->responseCode = $responseCode;
    }

    public function send()
    {
        echo $this->content;
    }
}
