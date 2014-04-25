<?php

/*
	http://hashcash.org/libs/sh/hashcash-1.00.sh
	https://pthree.org/2011/03/24/hashcash-and-mutt/
*/

namespace TheFox\Hashcash;

use RuntimeException;
use InvalidArgumentException;
use DateTime;

use TheFox\Utilities\Rand;
use TheFox\Utilities\Bin;

class Hashcash{
	
	const DATE_FORMAT = 'ymd';
	const EXPIRATION = 2419200; // 28 days
	const MINT_ATTEMPTS_MAX = 3;
	
	private $version = 1;
	private $bits = 0;
	private $date = '';
	private $resource = '';
	private $extension = '';
	private $salt = '';
	private $suffix = '';
	private $expiration = 0;
	private $attempts = 0;
	private $hash = '';
	
	public function __construct($bits = 20, $resource = ''){
		$this->setBits($bits);
		$this->setDate(date(static::DATE_FORMAT));
		$this->setResource($resource);
		$this->setExpiration(static::EXPIRATION);
	}
	
	public function setVersion($version){
		if($version <= 0){
			throw new RuntimeException('Version 0 not implemented yet.');
		}
		elseif($version > 1){
			throw new RuntimeException('Version '.$version.' not implemented yet.');
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
			throw new InvalidArgumentException('Date "'.$date.'" is not valid.', 1);
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
	
	public function mint(){
		#fwrite(STDOUT, __METHOD__.': '.$this->getBits()."\n");
		$stamp = '';
		
		$rounds = pow(2, $this->getBits());
		$salt = $this->getSalt();
		
		#fwrite(STDOUT, __METHOD__.': '.$this->getBits().', '.$bytes."\n");
		
		if(!$salt){
			$salt = base64_encode(Rand::data(16));
		}
		
		$found = false;
		$attempt = 0;
		$round = 0;
		$attemptSalts = array();
		for($attempt = 0; $attempt < static::MINT_ATTEMPTS_MAX && !$found; $attempt++){
			$attemptSalts[] = $salt;
			$baseStamp = $this->getVersion().':'.$this->getBits().':'.$this->getDate().':'.$this->getResource().':'.$this->getExtension().':'.$salt.':';
			
			#fwrite(STDOUT, __METHOD__.' bits: '.$this->getBits()."\n");
			#fwrite(STDOUT, __METHOD__.' rounds: '.$rounds."\n");
			#fwrite(STDOUT, __METHOD__.' baseStamp: '.$baseStamp."\n");
			
			for($round = 0; $round < $rounds; $round++){
				$testStamp = $baseStamp.$round;
				#$testStamp = $baseStamp.base64_encode($round);
				
				$bits = $this->checkBits(hash('sha1', $testStamp, true));
				$found = $bits >= $this->getBits();
				
				#if($round % 100 == 0 && $bits >= 10 || $found)
				#fwrite(STDOUT, __METHOD__.' round '.$round.' '.sprintf('%.4f', $round / $rounds * 100).' % - '.$bits.'>='.$this->getBits().', '.hash('sha1', $testStamp)."\n");
				
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
			throw new RuntimeException('Could not generate stamp after '.static::MINT_ATTEMPTS_MAX.' attempts, '
				.'each with '.$rounds.' rounds. salts='.join(',', $attemptSalts));
		}
		
		return $stamp;
	}
	
	public function verify($stamp){
		#fwrite(STDOUT, __METHOD__.' stamp: "'.$stamp.'"'."\n");
		
		if(!$stamp){
			throw new InvalidArgumentException('Stamp "'.$stamp.'" is not valid.', 1);
		}
		
		$items = preg_split('/:/', $stamp);
		if(count($items) < 7){
			throw new InvalidArgumentException('Stamp "'.$stamp.'" is not valid.', 2);
		}
		
		$verified = false;
		
		$this->setVersion($items[0]);
		$this->setBits($items[1]);
		$this->setDate($items[2]);
		$this->setResource($items[3]);
		$this->setExtension($items[4]);
		$this->setSalt($items[5]);
		$this->setSuffix($items[6]);
		
		#var_export($this);
		
		$bits = $this->checkBits(hash('sha1', $stamp, true));
		$verified = $bits >= $this->getBits();
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
		
		#fwrite(STDOUT, __METHOD__.' bits: '.$bits.' = '.$this->getBits().', '.(int)$verified."\n");
		
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
					$bits++;
					#fwrite(STDOUT, $bits.' ');
					
					if($char & (1 << $bit)){
						break;
					}
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
	
}
