<?php namespace App\Libraries;

use Auth;
use request;
use Carbon\Carbon;
use File;


use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Response;
use App\Services\UploadsManager;
 /**
 * class patternLib used to regex related operations 
 */
 class PatternLib  
 {
 	public $pattern;
 	public $subject;
 	public $matches;
 	public $flags;
 	public $offset;

 	/**
 	 * Function stringExtract
 	 *
 	 * @param int $group 
 	 * @return
 	 */		
 	public function stringExtract($group)
 	{	
 		//echo $this->pattern."<br>";
 		//echo $this->subject."<br>";
 		if (preg_match($this->pattern,$this->subject,$this->matches,$this->flags,$this->offset)) {
	  		//print_r($this->matches);exit;
	  		return $this->matches[$group];
		}
 	}
 	//(?!([A-Za-z]*)\-){2}[A-za-z0-9]{5}
 	/**
 	 * Function multiMatch
 	 *
 	 * @param
 	 * @return
 	 */		
 	public function stringReplace()
 	{
 		
 	}


 	
 }