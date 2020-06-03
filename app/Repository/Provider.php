<?php

namespace App\Repository;


use App\Exception\InvalidArgumentException;

abstract class Provider extends FileReader
{

    private const PROPERTIES_FILE = __DIR__ . '\..\..\properties.conf';

    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $providingMethod;

    /**
     * @param string $urlProp
     * @param string $methodProp
     */
    public function __construct(string $urlProp, string $methodProp)
    {
        $this->url = $this->getProp($urlProp);
        $this->providingMethod = $this->getProp($methodProp);
    }

    /**
     * @param string $path
     *
     * @return bool|string
     */
    function getRemoteFileContents(string $path = '')
    {
        return @file_get_contents($this->getUrl() . $path);
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
    private function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @return string
     */
    function getProvidingMethod(): string
    {
        return $this->providingMethod;
    }
}