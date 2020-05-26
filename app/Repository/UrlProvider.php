<?php

namespace App\Repository;


use App\Exception\InvalidArgumentException;

abstract class UrlProvider extends FileReader
{

    private const PROPERTIES_FILE = __DIR__ . '\..\..\properties.conf';

    /**
     * @var string
     */
    private $url;

    /**
     * @param string $urlProp
     */
    function __construct($urlProp)
    {
        $this->url = $this->getProp($urlProp);
    }

    /**
     * @param string $key
     *
     * @throws InvalidArgumentException
     *
     * @return string
     */
    private function getProp(string $key): string
    {
        foreach ($this->getFileByLines(self::PROPERTIES_FILE) as $line) {
            $keyValue = explode('=', $line);
            if ($keyValue[0] === $key) {
                return $keyValue[1];
            }
        }
        throw new InvalidArgumentException("Property `$key` not found in " . self::PROPERTIES_FILE);
    }

    /**
     * @return string
     */
    protected function getUrl(): string
    {
        return $this->url;
    }
}