<?php

/*
	http://hashcash.org/libs/sh/hashcash-1.00.sh
	https://pthree.org/2011/03/24/hashcash-and-mutt/
*/

namespace TheFox\Pow;

use RuntimeException;
use InvalidArgumentException;
use DateTime;

use TheFox\Utilities\Rand;
use TheFox\Utilities\Bin;

class Hashcash{
	
	const DATE_FORMAT = 'ymd';
	const DATE_FORMAT10 = 'ymdHi';
	const DATE_FORMAT12 = 'ymdHis';
	const EXPIRATION = 2419200; // 28 days
	const MINT_ATTEMPTS_MAX = 10;
	
	private $version = 1;
	private $bits;
	private $date;
	private $resource;
	private $extension = '';
	private $salt = '';
	private $suffix = '';
	private $expiration = 0;
	private $attempts = 0;
	private $hash = '';
	private $mintAttemptsMax;
	private $stamp = '';
	
	public function __construct($bits = 20, $resource = ''){
		$this->setBits($bits);
		$this->setDate(date(static::DATE_FORMAT));
		$this->setResource($resource);
		$this->setExpiration(static::EXPIRATION);
		$this->setMintAttemptsMax(static::MINT_ATTEMPTS_MAX);
	}
	
	public function setVersion($version){
		if($version <= 0){
			throw new RuntimeException('Version 0 not implemented yet.');
		}
		elseif($version > 1){
			throw new RuntimeException(
				'Version '.$version.' not implemented yet.');
		}
		
		$this->version = (int)$version;
	}
	
	public function getVersion(){
		return (int)$this->version;
	}
	
	public function setBits($bits){
		$this->bits = (int)$bits;
	}
	
	public function getBits(){
		return (int)$this->bits;
	}
	
	public function setDate($date){
		$dateLen = strlen($date);
		if($dateLen != 6 && $dateLen != 10 && $dateLen != 12){
			throw new InvalidArgumentException(
				'Date "'.$date.'" is not valid.', 1);
		}
		
		$this->date = $date;
	}
	
	public function getDate(){
		return $this->date;
	}
	
	public function setResource($resource){
		$this->resource = $resource;
	}
	
	public function getResource(){
		return $this->resource;
	}
	
	public function setExtension($extension){
		$this->extension = $extension;
	}
	
	public function getExtension(){
		return $this->extension;
	}
	
	public function setSalt($salt){
		$this->salt = $salt;
	}
	
	public function getSalt(){
		return $this->salt;
	}
	
	public function setSuffix($suffix){
		$this->suffix = $suffix;
	}
	
	public function getSuffix(){
		return $this->suffix;
	}
	
	public function setExpiration($expiration){
		$this->expiration = $expiration;
	}
	
	public function getExpiration(){
		return $this->expiration;
	}
	
	public function setAttempts($attempts){
		$this->attempts = $attempts;
	}
	
	public function getAttempts(){
		return $this->attempts;
	}
	
	public function setHash($hash){
		$this->hash = $hash;
	}
	
	public function getHash(){
		return $this->hash;
	}
	
	public function setMintAttemptsMax($mintAttemptsMax){
		$this->mintAttemptsMax = (int)$mintAttemptsMax;
	}
	
	public function getMintAttemptsMax(){
		return (int)$this->mintAttemptsMax;
	}
	
	public function setStamp($stamp){
		$this->stamp = $stamp;
	}
	
	public function getStamp(){
		if(!$this->stamp){
			$stamp = $this->getVersion().':'.$this->getBits();
			$stamp .= ':'.$this->getDate();
			$stamp .= ':'.$this->getResource().':'.$this->getExtension();
			$stamp .= ':'.$this->getSalt().':'.$this->getSuffix();
			
			$this->stamp = $stamp;
		}
		return $this->stamp;
	}
	
	public function mint(){
		#fwrite(STDOUT, __METHOD__.': '.$this->getBits()."\n");
		$stamp = '';
		
		$rounds = pow(2, $this->getBits());
		$bytes = $this->getBits() / 8 + (8 - ($this->getBits() % 8)) / 8;
		
		$salt = $this->getSalt();
		if(!$salt){
			$salt = base64_encode(Rand::data(16));
		}
		
		$baseStamp = $this->getVersion().':'.$this->getBits();
		$baseStamp .= ':'.$this->getDate();
		$baseStamp .= ':'.$this->getResource().':'.$this->getExtension().':';
		
		$found = false;
		$round = 0;
		$testStamp = '';
		$bits = 0;
		$attemptSalts = array();
		$attempt = 0;
		for(; ($attempt < $this->getMintAttemptsMax() || !$this->getMintAttemptsMax()) && !$found; $attempt++){
			$attemptSalts[] = $salt;
			$attemptStamp = $baseStamp.$salt.':';
			
			#fwrite(STDOUT, 'attempt: '.$attempt.'/'.$this->getMintAttemptsMax()."\n");
			#fwrite(STDOUT, "\t".' bits: '.$this->getBits()."\n");
			#fwrite(STDOUT, "\t".' rounds: '.$rounds."\n");
			#fwrite(STDOUT, "\t".' attemptStamp: '.$attemptStamp."\n");
			
			for($round = 0; $round < $rounds; $round++){
				$testStamp = $attemptStamp.$round;
				#$testStamp = $attemptStamp.base64_encode($round);
				
				#$bits = $this->checkBits(hash('sha1', $testStamp, true));
				#$found = $bits >= $this->getBits();
				$found = $this->checkBitsFast(
					substr(hash('sha1', $testStamp, true), 0, $bytes), $bytes, $this->getBits());
				
				#$percent = sprintf('%.4f', $round / $rounds * 100).' %';
				#$hash = hash('sha1', $testStamp);
				#if($round % 100 == 0 && $bits >= 10 || $found)
				#fwrite(STDOUT, ' round '.$round.' '.$percent.' - '.$bits.'>='.$this->getBits().', '.$hash."\n");
				#if($round % 100 == 0 || $found)
				#fwrite(STDOUT, ' round '.$round.' '.$percent.' - '.$hash."\n");
				
				if($found){
					#Bin::debugData(hash('sha1', $testStamp, true));
					
					break;
				}
			}
			
			if(!$found){
				$salt = base64_encode(Rand::data(16));
			}
		}
		
		if($found){
			$stamp = $testStamp;
			$this->setSuffix($round);
			$this->setSalt($salt);
			$this->setAttempts($attempt);
			$this->setHash(hash('sha1', $stamp));
		}
		else{
			$msg = 'Could not generate stamp after '.$attempt.' attempts, ';
			$msg .= 'each with '.$rounds.' rounds. ';
			$msg .= 'bits='.$this->getBits().', ';
			$msg .= 'date='.$this->getDate().', ';
			$msg .= 'resource='.$this->getResource().', ';
			$msg .= 'salts='.join(',', $attemptSalts);
			throw new RuntimeException($msg);
		}
		
		$this->setStamp($stamp);
		return $stamp;
	}
	
	public function mintAll(){
		#fwrite(STDOUT, __METHOD__.': '.$this->getBits()."\n");
		$stamps = array();
		
		$rounds = pow(2, $this->getBits());
		$bytes = $this->getBits() / 8 + (8 - ($this->getBits() % 8)) / 8;
		$salt = $this->getSalt();
		
		$baseStamp = $this->getVersion().':'.$this->getBits();
		$baseStamp .= ':'.$this->getDate();
		$baseStamp .= ':'.$this->getResource().':'.$this->getExtension().':'.$salt.':';
		
		#fwrite(STDOUT, __METHOD__.': '.$this->getBits().', '.$bytes."\n");
		
		if(!$salt){
			$salt = base64_encode(Rand::data(16));
		}
		
		#fwrite(STDOUT, 'bits: '.$this->getBits()."\n");
		#fwrite(STDOUT, "\t".' rounds: '.$rounds."\n");
		#fwrite(STDOUT, "\t".' baseStamp: '.$baseStamp."\n");
		
		for($round = 0; $round < $rounds; $round++){
			$testStamp = $baseStamp.$round;
			$found = $this->checkBitsFast(substr(hash('sha1', $testStamp, true), 0, $bytes), $bytes, $this->getBits());
			
			#$percent = sprintf('%.4f', $round / $rounds * 100).' %';
			#if($round % 1000000 == 0 || $found)
			#if($found)
			#fwrite(STDOUT, "\t".' round '.$round.' '.$percent.' - '.hash('sha1', $testStamp)."\n");
			
			if($found){
				$stamps[] = $testStamp;
			}
		}
		
		return $stamps;
	}
	
	public function parseStamp($stamp){
		if(!$stamp){
			throw new InvalidArgumentException('Stamp "'.$stamp.'" is not valid.', 1);
		}
		
		$items = preg_split('/:/', $stamp);
		if(count($items) < 7){
			throw new InvalidArgumentException('Stamp "'.$stamp.'" is not valid.', 2);
		}
		
		$this->setVersion($items[0]);
		$this->setBits($items[1]);
		$this->setDate($items[2]);
		$this->setResource($items[3]);
		$this->setExtension($items[4]);
		$this->setSalt($items[5]);
		$this->setSuffix($items[6]);
	}
	
	public function verify($stamp = null){
		if($stamp === null){
			$stamp = $this->getStamp();
		}
		else{
			$this->parseStamp($stamp);
		}
		
		$verified = false;
		
		$bytes = $this->getBits() / 8 + (8 - ($this->getBits() % 8)) / 8;
		$verified = $this->checkBitsFast(substr(hash('sha1', $stamp, true), 0, $bytes), $bytes, $this->getBits());
		
		if($verified && $this->getExpiration()){
			$dateLen = strlen($this->getDate());
			$year = '';
			$month = '';
			$day = '';
			$hour = '00';
			$minute = '00';
			$second = '00';
			
			switch($dateLen){
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
			
			#fwrite(STDOUT, __METHOD__.' date: '.$year.', '.$month.', '.$day.' - '.$hour.':'.$minute.':'.$second."\n");
			
			$date = new DateTime($year.'-'.$month.'-'.$day.' '.$hour.':'.$minute.':'.$second);
			$now = new DateTime('now');
			
			#var_export($date);
			#var_export($now);
			
			if($date->getTimestamp() < $now->getTimestamp() - $this->getExpiration()){
				$verified = false;
			}
			
			#fwrite(STDOUT, __METHOD__.' date: '.$date->getTimestamp()."\n");
			#fwrite(STDOUT, __METHOD__.' now:  '.$now->getTimestamp()."\n");
			#fwrite(STDOUT, __METHOD__.' exp:  '.($now->getTimestamp() - $this->getExpiration()).' '.(int)$verified."\n");
		}
		
		#fwrite(STDOUT, __METHOD__.' bits: '.$bits.'>='.$this->getBits().', '.(int)$verified."\n");
		
		return $verified;
	}
	
	private function checkBits($data){
		#fwrite(STDOUT, __METHOD__.''."\n");
		$bits = 0;
		
		#fwrite(STDOUT, "\n");
		#Bin::debugData($data);
		
		$dataLen = strlen($data);
		for($charn = 0; $charn < $dataLen; $charn++){
			$char = ord($data[$charn]);
			
			#fwrite(STDOUT, "charn $charn: ".( sprintf('%d', $char) )."\n");
			#fwrite(STDOUT, "\t\t ");
			
			if($char){
				for($bit = 7; $bit >= 0; $bit--){
					#fwrite(STDOUT, $bits.' ');
					
					if($char & (1 << $bit)){
						break;
					}
					
					$bits++;
				}
				#fwrite(STDOUT, ' - '.$bits.' ');
				#fwrite(STDOUT, "\n");
				break;
			}
			else{
				$bits += 8;
				
				/*for($bit = 7; $bit >= 0; $bit--){
					$bits++;
					#fwrite(STDOUT, '{'.$bits.'} ');
				}
				*/
				
				#fwrite(STDOUT, ' - '.$bits.' ');
				#fwrite(STDOUT, "\n");
			}
		}
		
		#fwrite(STDOUT, __METHOD__.' end: '.$bits."\n");
		
		return $bits;
	}
	
	private function checkBitsFast($data, $bytes, $bits){
		#fwrite(STDOUT, __METHOD__.': '.$bytes.', '.$bits.''."\n");
		
		$last = $bytes - 1;
		
		if(substr($data, 0, $last) == str_repeat("\x00", $last) && 
			ord(substr($data, -1)) >> ($bytes * 8 - $bits) == 0 ){
			return true;
		}
		
		return false;
	}
	
}
