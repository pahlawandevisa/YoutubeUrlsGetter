<?php namespace YoutubeUrlsGetter;

class StreamMapParser
{
    private $values = [];

    function __construct($raw)
    {
        $this->values = $this->parseValues($raw);
    }

    public function getUrl()
    {
        if (!array_key_exists('url', $this->values))
        {
            return '';
        }

        return urldecode(urldecode( $this->values['url'].
            $this->copyArg('signature', ['signature', 's', 'sig']).
            $this->copyArg('ratebypass', 'ratebypass').
            $this->copyArg('fallback_host', 'fallback_host')
        ));
    }

    public function getStream()
    {
        $stream = new YoutubeStream();
        $stream->url = $this->getUrl();
        $stream->type = $this->values['type'];
        return $stream;
    }

    private function copyArg($argName, $valueKey)
    {
        if (is_array($valueKey))
        {
            foreach($valueKey as $key)
            {
                $arg = $this->copyArg($argName, $key);
                if ($arg != '')
                {
                    return $arg;
                }
            }
            return;
        }

        if (array_key_exists($valueKey, $this->values))
        {
            return "&$argName=".$this->values[$valueKey];
        }
    }

    function parseValues($raw)
    {
        if ($raw == '')
        {
            return [];
        }

        $pairs = preg_split("/\&/", $raw);
        $result = [];
        
        foreach($pairs as $pairraw)
        {
            $pair = preg_split('/=/', $pairraw);
            $result[ urldecode($pair[0]) ] = count($pair)>1 ? urldecode($pair[1]) : '';
        }
        return $result;
    }

    function __get($p)
    {
        if ($p == 'values')
        {
            return $this->values;
        }
    }
}