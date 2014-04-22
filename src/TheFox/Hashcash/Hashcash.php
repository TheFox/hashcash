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

class Hashcash{
	
	const DATE_FORMAT = 'ymd';
	const EXPIRATION = 2419200 // 28 days
	
	private $version = 1;
	private $bits = 0;
	private $date = '';
	private $resource = '';
	private $extension = '';
	private $salt = '';
	private $suffix = '';
	private $expiration = 0;
	
	public function __construct($bits = 20, $resource = ''){
		$this->setBits($bits);
		$this->setDate(date(static::DATE_FORMAT));
		$this->setResource($resource);
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
	
	public function mint(){
		fwrite(STDOUT, __METHOD__.': '.$this->getBits()."\n");
		$stamp = '';
		
		$rounds = pow(2, $this->getBits());
		
		if(!$this->getSalt()){
			$this->setSalt(base64_encode(Rand::data(16)));
		}
		
		$baseStamp = $this->getVersion().':'.$this->getBits().':'.$this->getDate().':'.$this->getResource().':'.$this->getExtension().':'.$this->getSalt().':';
		
		#fwrite(STDOUT, __METHOD__.' bits: '.$this->getBits()."\n");
		#fwrite(STDOUT, __METHOD__.' rounds: '.$rounds."\n");
		#fwrite(STDOUT, __METHOD__.' baseStamp: '.$baseStamp."\n");
		
		for($round = 0; $round < $rounds; $round++){
			$testStamp = $baseStamp.$round;
			#$testStamp = $baseStamp.base64_encode($round);
			
			$bits = $this->checkBits(hash('sha1', $testStamp, true));
			$ok = $bits >= $this->getBits();
			
			#if($round % 2000 == 0)
			#if($round % 50 == 0 || $ok || $bits >= 10)
			if($round % 50 == 0 && $bits >= 10)
			fwrite(STDOUT, __METHOD__.' round: '.$round.': '.$testStamp.', '.hash('sha1', $testStamp).', '.$bits."\n");
			
			if($ok){
				fwrite(STDOUT, __METHOD__.' round: '.$round.': '.$testStamp.', '.hash('sha1', $testStamp).', '.$bits."\n");
				
				$stamp = $testStamp;
				$this->setSuffix($round);
				break;
			}
			#if($round >= 5) break;
		}
		
		return $stamp;
	}
	
	public function verify($stamp){
		fwrite(STDOUT, __METHOD__.' stamp: '.$stamp."\n");
		
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
		
		fwrite(STDOUT, __METHOD__.' bits: '.$bits.' = '.$this->getBits().', '.(int)$verified."\n");
		
		return $verified;
	}
	
	private function checkBits($data){
		#fwrite(STDOUT, __METHOD__.''."\n");
		$bits = 1;
		
		$dataLen = strlen($data);
		for($charn = 0; $charn < $dataLen; $charn++){
			$char = ord($data[$charn]);
			
			#fwrite(STDOUT, "charn $charn: ".( sprintf('%d', $char) )."\n"."\t\t ");
			
			if($char){
				for($bit = 7; $bit >= 0; $bit--){
					if($char & (1 << $bit)){
						break;
					}
					#fwrite(STDOUT, $bits.' ');
					$bits++;
				}
				#fwrite(STDOUT, "\n");
				break;
			}
			else{
				$bits += 8;
				#fwrite(STDOUT, "\n");
			}
		}
		
		return $bits;
	}
	
}
