<?php

namespace TheFox\Utilities;

class Bin{
	
	public static function debugData($data){
		fwrite(STDOUT, "ddd xx    b b b b  b b b b\n");
		fwrite(STDOUT, "--- --    ----------------\n");
		
		$dataLen = strlen($data);
		for($pos = 0; $pos < $dataLen; $pos++){
			$char = $data[$pos];
			$ascii = ord($char);
			
			fwrite(STDOUT, sprintf("%3d %02x    %d %d %d %d  %d %d %d %d\n", $ascii, $ascii,
				($ascii & (1 << 7) ) > 0 ,
				($ascii & (1 << 6) ) > 0 ,
				($ascii & (1 << 5) ) > 0 ,
				($ascii & (1 << 4) ) > 0 ,
				($ascii & (1 << 3) ) > 0 ,
				($ascii & (1 << 2) ) > 0 ,
				($ascii & (1 << 1) ) > 0 ,
				($ascii & (1 << 0) ) > 0 
				));
		}
	}
}
