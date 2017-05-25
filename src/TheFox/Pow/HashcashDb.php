<?php

namespace TheFox\Pow;

use TheFox\Storage\YamlStorage;

class HashcashDb extends YamlStorage
{
    /**
     * @var int
     */
    private $hashcashsId = 0;

    /**
     * @var array
     */
    private $hashcashs = array();

    /**
     * HashcashDb constructor.
     * @param null $filePath
     */
    public function __construct($filePath = null)
    {
        parent::__construct($filePath);

        $this->data['timeCreated'] = time();
    }

    /**
     * @return bool|int
     */
    public function save()
    {
        $this->data['hashcashs'] = array();
        foreach ($this->hashcashs as $hashcashId => $hashcash) {
            $hashcashAr = array();
            $hashcashAr['id'] = $hashcashId;
            $hashcashAr['stamp'] = $hashcash->getStamp();

            if ($hashcash->verify()) {
                $this->data['hashcashs'][$hashcashId] = $hashcashAr;
            }
        }

        $rv = parent::save();
        unset($this->data['hashcashs']);

        return $rv;
    }

    /**
     * @return bool
     */
    public function load()
    {
        if (parent::load()) {
            if (isset($this->data['hashcashs']) && $this->data['hashcashs']) {
                foreach ($this->data['hashcashs'] as $hashcashId => $hashcashAr) {
                    $this->hashcashsId = $hashcashId;

                    $hashcash = new Hashcash();
                    if ($hashcash->verify($hashcashAr['stamp'])) {
                        $this->hashcashs[$hashcashId] = $hashcash;
                    }
                }
            }
            unset($this->data['hashcashs']);

            return true;
        }

        return false;
    }

    /**
     * @param Hashcash $hashcash
     * @return bool
     */
    public function hasDoublespend(Hashcash $hashcash)
    {
        return in_array($hashcash, $this->hashcashs);
    }

    /**
     * @param Hashcash $hashcash
     * @return bool
     */
    public function addHashcash(Hashcash $hashcash)
    {
        if (!$this->hasDoublespend($hashcash)) {
            $this->hashcashsId++;
            $this->hashcashs[$this->hashcashsId] = $hashcash;
            $this->setDataChanged(true);

            return true;
        }

        return false;
    }

    /**
     * @return array
     */
    public function getHashcashs()
    {
        return $this->hashcashs;
    }
}
