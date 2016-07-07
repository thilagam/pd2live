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
        $images = $this->ftpLib->collectImagesFromFtp($data['product']);
        $this->prodConfigs=$this->productHelper->getProductDevConfigs($data['product']);
        $ref = array();
        foreach($images as $images1){
            foreach($images1 as $images2){
                $string = explode("-",$images2);
                if(isset($string[2])):
                    array_push($ref,substr($string[2],0,5));
                endif;
            }
        }
        $refs = explode(",",$data['ref']);
        $res = array();
        foreach($refs as $key=>$value){
            if(in_array($value,$ref)){
                $referenceInfo=$this->productHelper->refernceInfo($value,$data['product'],$this->prodConfigs);
                $res[$value]['value']=$value;
                $res[$value]['pdn']=$referenceInfo['pdn'];
                $res[$value]['ref']=$referenceInfo['ref'];
                $res[$value]['gen']=$referenceInfo['gen'];
                $res[$value]['delivery']=$referenceInfo['delivery'];
            }
        }
        echo "<pre>";print_r($res);
		
	}
}
