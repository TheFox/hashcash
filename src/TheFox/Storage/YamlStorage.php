<?php

namespace TheFox\Storage;

use Symfony\Component\Yaml\Yaml;

class YamlStorage
{
    /**
     * @var string
     */
    private $datadirBasePath;

    /**
     * @var string
     */
    private $filePath;

    /**
     * @var array
     */
    public $data = [];

    /**
     * @var bool
     */
    public $dataChanged = false;

    /**
     * @var bool
     */
    private $isLoaded = false;

    /**
     * YamlStorage constructor.
     * @param null $filePath
     */
    public function __construct($filePath = null)
    {
        if ($filePath !== null) {
            $this->setFilePath($filePath);
        }
    }

    /**
     * @return bool
     */
    public function save()
    {
        $rv = false;

        if ($this->dataChanged) {
            if ($this->getFilePath()) {
                $rv = file_put_contents($this->getFilePath(), Yaml::dump($this->data));
            }
            if ($rv) {
                $this->setDataChanged(false);
            }
        }

        return $rv;
    }

    /**
     * @return bool|null
     */
    public function load()
    {
        if ($this->getFilePath()) {
            if (file_exists($this->getFilePath())) {
                $this->data = Yaml::parse($this->getFilePath());
                return $this->isLoaded(true);
            }
        }

        return false;
    }

    /**
     * @param null $isLoaded
     * @return bool|null
     */
    public function isLoaded($isLoaded = null)
    {
        if ($isLoaded !== null) {
            $this->isLoaded = $isLoaded;
        }

        return $this->isLoaded;
    }

    /**
     * @param $filePath
     */
    public function setFilePath($filePath)
    {
        $this->filePath = $filePath;
    }

    /**
     * @return null
     */
    public function getFilePath()
    {
        return $this->filePath;
    }

    /**
     * @param $datadirBasePath
     */
    public function setDatadirBasePath($datadirBasePath)
    {
        $this->datadirBasePath = $datadirBasePath;
    }

    /**
     * @return null
     */
    public function getDatadirBasePath()
    {
        if ($this->datadirBasePath) {
            return $this->datadirBasePath;
        }

        return null;
    }

    /**
     * @param bool $changed
     */
    public function setDataChanged($changed = true)
    {
        $this->dataChanged = $changed;
    }

    /**
     * @return bool
     */
    public function getDataChanged()
    {
        return $this->dataChanged;
    }
}
