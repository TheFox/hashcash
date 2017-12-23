<?php

/*
	http://hashcash.org/libs/sh/hashcash-1.00.sh
	https://pthree.org/2011/03/24/hashcash-and-mutt/
*/

namespace TheFox\Pow;

use DateTime;
use RuntimeException;
use InvalidArgumentException;
use TheFox\Utilities\Rand;

class Hashcash
{
    const NAME = 'Hashcash';
    const VERSION = '1.9.0-dev.2';
    const DATE_FORMAT = 'ymd';
    const DATE_FORMAT10 = 'ymdHi';
    const DATE_FORMAT12 = 'ymdHis';
    const EXPIRATION = 2419200; // 28 days
    const MINT_ATTEMPTS_MAX = 10;

    /**
     * @var int
     */
    private $version = 1;

    /**
     * @var int
     */
    private $bits;

    /**
     * @var string
     */
    private $date;

    /**
     * @var string
     */
    private $resource;

    /**
     * @var string
     */
    private $extension = '';

    /**
     * @var string
     */
    private $salt = '';

    /**
     * @var string
     */
    private $suffix = '';

    /**
     * @var int
     */
    private $expiration = 0;

    /**
     * @var int
     */
    private $attempts = 0;

    /**
     * @var string
     */
    private $hash = '';

    /**
     * @var int
     */
    private $mintAttemptsMax;

    /**
     * @var string
     */
    private $stamp = '';

    /**
     * @param int $bits
     * @param string $resource
     */
    public function __construct(int $bits = 20, string $resource = '')
    {
        $this->setBits($bits);
        $this->setDate(date(static::DATE_FORMAT));
        $this->setResource($resource);
        $this->setExpiration(static::EXPIRATION);
        $this->setMintAttemptsMax(static::MINT_ATTEMPTS_MAX);
    }

    /**
     * @param int $version
     * @return $this
     */
    public function setVersion(int $version) : Hashcash
    {
        if ($version <= 0) {
            throw new RuntimeException('Version 0 not implemented yet.', 1);
        } elseif ($version > 1) {
            throw new RuntimeException(
                'Version ' . $version . ' not implemented yet.', 2);
        }

        $this->version = $version;

        return $this;
    }

    /**
     * @return int
     */
    public function getVersion(): int
    {
        return $this->version;
    }

    /**
     * @param int $bits
     * @return $this
     */
    public function setBits(int $bits) : Hashcash
    {
        $this->bits = $bits;

        return $this;
    }

    /**
     * @return int
     */
    public function getBits(): int
    {
        return $this->bits;
    }

    /**
     * @param string $date
     * @return $this
     */
    public function setDate(string $date) : Hashcash
    {
        $dateLen = strlen($date);
        if ($dateLen != 6 && $dateLen != 10 && $dateLen != 12) {
            throw new InvalidArgumentException('Date "' . $date . '" is not valid.', 1);
        }

        $this->date = $date;

        return $this;
    }

    /**
     * @return string
     */
    public function getDate(): string
    {
        return $this->date;
    }

    /**
     * @param string $resource
     * @return $this
     */
    public function setResource(string $resource) : Hashcash
    {
        $this->resource = $resource;

        return $this;
    }

    /**
     * @return string
     */
    public function getResource(): string
    {
        return $this->resource;
    }

    /**
     * @param string $extension
     * @return $this
     */
    public function setExtension(string $extension) : Hashcash
    {
        $this->extension = $extension;

        return $this;
    }

    /**
     * @return string
     */
    public function getExtension(): string
    {
        return $this->extension;
    }

    /**
     * @param string $salt
     * @return $this
     */
    public function setSalt(string $salt) : Hashcash
    {
        $this->salt = $salt;

        return $this;
    }

    /**
     * @return string
     */
    public function getSalt(): string
    {
        return $this->salt;
    }

    /**
     * @param string $suffix
     * @return $this
     */
    public function setSuffix(string $suffix) : Hashcash
    {
        $this->suffix = $suffix;

        return $this;
    }

    /**
     * @return string
     */
    public function getSuffix(): string
    {
        return $this->suffix;
    }

    /**
     * @param int $expiration
     * @return $this
     */
    public function setExpiration(int $expiration) : Hashcash
    {
        $this->expiration = $expiration;

        return $this;
    }

    /**
     * @return int
     */
    public function getExpiration(): int
    {
        return $this->expiration;
    }

    /**
     * @param int $attempts
     * @return $this
     */
    public function setAttempts(int $attempts) : Hashcash
    {
        $this->attempts = $attempts;

        return $this;
    }

    /**
     * @return int
     */
    public function getAttempts(): int
    {
        return $this->attempts;
    }

    /**
     * @param string $hash
     * @return $this
     */
    public function setHash(string $hash) : Hashcash
    {
        $this->hash = $hash;

        return $this;
    }

    /**
     * @return string
     */
    public function getHash(): string
    {
        return $this->hash;
    }

    /**
     * @param int $mintAttemptsMax
     * @return $this
     */
    public function setMintAttemptsMax(int $mintAttemptsMax) : Hashcash
    {
        $this->mintAttemptsMax = $mintAttemptsMax;

        return $this;
    }

    /**
     * @return int
     */
    public function getMintAttemptsMax(): int
    {
        return $this->mintAttemptsMax;
    }

    /**
     * @param string $stamp
     * @return $this
     */
    public function setStamp(string $stamp) : Hashcash
    {
        $this->stamp = $stamp;

        return $this;
    }

    /**
     * @return string
     */
    public function getStamp(): string
    {
        if (!$this->stamp) {
            $stamp = $this->getVersion() . ':' . $this->getBits();
            $stamp .= ':' . $this->getDate();
            $stamp .= ':' . $this->getResource() . ':' . $this->getExtension();
            $stamp .= ':' . $this->getSalt() . ':' . $this->getSuffix();

            $this->stamp = $stamp;
        }
        return $this->stamp;
    }

    /**
     * @return string
     */
    public function mint(): string
    {
        //$stamp = '';

        $rounds = pow(2, $this->getBits());
        $bytes = (int)($this->getBits() / 8 + (8 - ($this->getBits() % 8)) / 8);

        $salt = $this->getSalt();
        if (!$salt) {
            $salt = base64_encode(Rand::data(16));
        }

        $baseStamp = $this->getVersion() . ':' . $this->getBits();
        $baseStamp .= ':' . $this->getDate();
        $baseStamp .= ':' . $this->getResource() . ':' . $this->getExtension() . ':';

        $found = false;
        $round = 0;
        $testStamp = '';
        //$bits = 0;
        $attemptSalts = [];
        $attempt = 0;
        for (; ($attempt < $this->getMintAttemptsMax() || !$this->getMintAttemptsMax()) && !$found; $attempt++) {
            $attemptSalts[] = $salt;
            $attemptStamp = $baseStamp . $salt . ':';

            for ($round = 0; $round < $rounds; $round++) {
                $testStamp = $attemptStamp . $round;

                $hash = hash('sha1', $testStamp, true);
                $data = substr($hash, 0, $bytes);
                $found = $this->checkBitsFast($data, $bytes, $this->getBits());

                if ($found) {
                    break;
                }
            }

            if (!$found) {
                $salt = base64_encode(Rand::data(16));
            }
        }

        if ($found) {
            $stamp = $testStamp;
            $this->setSuffix((string)$round);
            $this->setSalt($salt);
            $this->setAttempts($attempt);
            $this->setHash(hash('sha1', $stamp));
        } else {
            $msg = 'Could not generate stamp after ' . $attempt . ' attempts, ';
            $msg .= 'each with ' . $rounds . ' rounds. ';
            $msg .= 'bits=' . $this->getBits() . ', ';
            $msg .= 'date=' . $this->getDate() . ', ';
            $msg .= 'resource=' . $this->getResource() . ', ';
            $msg .= 'salts=' . join(',', $attemptSalts);
            throw new RuntimeException($msg);
        }

        $this->setStamp($stamp);
        return $stamp;
    }

    /**
     * @return array
     */
    public function mintAll(): array
    {
        $stamps = [];

        $bits = $this->getBits();
        $rounds = pow(2, $bits);
        $bytes = (int)($bits / 8 + (8 - ($bits % 8)) / 8);

        $salt = $this->getSalt();
        /*if (!$salt) {
            $salt = base64_encode(Rand::data(16));
        }*/

        $baseStamp = $this->getVersion() . ':' . $bits;
        $baseStamp .= ':' . $this->getDate();
        $baseStamp .= ':' . $this->getResource() . ':' . $this->getExtension() . ':' . $salt . ':';

        for ($round = 0; $round < $rounds; $round++) {
            $testStamp = $baseStamp . $round;
            $hash = hash('sha1', $testStamp, true);
            $data = substr($hash, 0, $bytes);
            $found = $this->checkBitsFast($data, $bytes, $bits);

            if ($found) {
                $stamps[] = $testStamp;
            }
        }

        return $stamps;
    }

    /**
     * @param string $stamp
     * @return $this
     */
    public function parseStamp(string $stamp) : Hashcash
    {
        if (!$stamp) {
            throw new InvalidArgumentException('Stamp "' . $stamp . '" is not valid.', 1);
        }

        $items = preg_split('/:/', $stamp);
        if (count($items) < 7) {
            throw new InvalidArgumentException('Stamp "' . $stamp . '" is not valid.', 2);
        }

        $this->setVersion($items[0]);
        $this->setBits($items[1]);
        $this->setDate($items[2]);
        $this->setResource($items[3]);
        $this->setExtension($items[4]);
        $this->setSalt($items[5]);
        $this->setSuffix($items[6]);

        return $this;
    }

    /**
     * @param string|null $stamp
     * @return boolean
     */
    public function verify(string $stamp = null): bool
    {
        if ($stamp === null) {
            $stamp = $this->getStamp();
        } else {
            $this->parseStamp($stamp);
        }

        $bytes = (int)($this->getBits() / 8 + (8 - ($this->getBits() % 8)) / 8);
        $hash = hash('sha1', $stamp, true);
        $data = substr($hash, 0, $bytes);
        $verified = $this->checkBitsFast($data, $bytes, $this->getBits());

        if ($verified && $this->getExpiration()) {
            $dateLen = strlen($this->getDate());
            $year = '';
            $month = '';
            $day = '';
            $hour = '00';
            $minute = '00';
            $second = '00';

            switch ($dateLen) {
                case 12:
                    $second = substr($this->getDate(), 10, 2);
                case 10:
                    $hour = substr($this->getDate(), 6, 2);
                    $minute = substr($this->getDate(), 8, 2);
                case 6:
                    $year = substr($this->getDate(), 0, 2);
                    $month = substr($this->getDate(), 2, 2);
                    $day = substr($this->getDate(), 4, 2);
            }

            $date = new DateTime($year . '-' . $month . '-' . $day . ' ' . $hour . ':' . $minute . ':' . $second);
            $now = new DateTime('now');

            if ($date->getTimestamp() < $now->getTimestamp() - $this->getExpiration()) {
                $verified = false;
            }
        }

        return $verified;
    }

    /**
     * @param string $data
     * @return int
     */
    private function checkBitsSlow(string $data): int
    {
        $bits = 0;

        $dataLen = strlen($data);
        for ($charn = 0; $charn < $dataLen; $charn++) {
            $char = ord($data[$charn]);

            if ($char) {
                for ($bit = 7; $bit >= 0; $bit--) {
                    if ($char & (1 << $bit)) {
                        break;
                    }
                    $bits++;
                }
                break;
            } else {
                $bits += 8;
            }
        }

        return $bits;
    }

    /**
     * @param string $data
     * @param int $bytes
     * @param int $bits
     * @return bool
     */
    private function checkBitsFast(string $data, int $bytes, int $bits): bool
    {
        $last = $bytes - 1;

        if (substr($data, 0, $last) == str_repeat("\x00", $last) &&
            ord(substr($data, -1)) >> ($bytes * 8 - $bits) == 0
        ) {
            return true;
        }

        return false;
    }
}
