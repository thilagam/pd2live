<?php

namespace App\Http\Controllers;

use Auth;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Libraries\PatternLib;
use App\Libraries\ProductHelper;
use App\Libraries\FtpLib;

use Request;

class SearchController extends Controller
{
	public function __construct(\Illuminate\Http\Request $request)
	{
		$this->user_id = Auth::id();
    	if($this->user_id){
    		$this->permit=$request->attributes->get('permit');
    	}
    	$this->configs=$request->attributes->get('configs');
	$this->productHelper = new ProductHelper;
    	$this->patternLib= new PatternLib;
    	$this->prodConfigs=array();
	$this->ftpLib = new FtpLib();
	}

	public function ftp()
	{
		$data = Request::all();
		//echo "<pre>";print_r($data);
		//$images = $this->ftpLib->collectImagesFromFtp($data['product'],$data['folder']);
		//echo "<pre>";print_r($images);
		$dir = public_path()."/uploads/products/".$data['product']."/ftp/".$data['folder']."/";
		if ($handle = opendir($dir)) {
 		   while (false !== ($entry = readdir($handle))) {
        		if ($entry != "." && $entry != "..") {
            		/* Get Names on Pattern */
            		if(isset($this->prodConfigs['pdc_client_pattern']) && $this->prodConfigs['pdc_client_pattern']!='')
            		{	
            			$this->patternLib->pattern=$this->prodConfigs['pdc_client_pattern'];
            			$this->patternLib->subject=$entry;
            			$part=$this->patternLib->stringExtract(1);

            			
            		}else{
            			$part = explode("_",$entry);	
            			$part = $part;
            		}
            		
            		if(!in_array($part,$reference_listing))	
            			$reference_listing[] = $part;
        		}
    		}
    		closedir($handle);
		}
		echo "<pre>";print_r($reference_listing);
	}
}
