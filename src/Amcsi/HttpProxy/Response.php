<?php
class Amcsi_HttpProxy_Response
{

    protected $content;
    protected $headers;

    public function __construct($content, $headers)
    {
        $this->setContentAndHeaders($content, $headers);
    }

    public function setContentAndHeaders($content, $headers)
    {
        $this->content = $content;
        $this->headers = $headers;
    }

    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    public function setHeaders(array $headers)
    {
        $this->headers = $headers;
        return $this;
    }

    public function getContent()
    {
        return (string) $this->content;
    }

    public function getHeaders()
    {
        return (array) $this->headers;
    }

    public function __toString()
    {
        return $this->getContent();
    }
}

