<?php

namespace App\Http\Controllers;

use Auth;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\DownloadController;
use App\Libraries\PatternLib;
use App\Libraries\ProductHelper;
use App\Libraries\FtpLib;
use App\Libraries\ExcelLib;
use App\Libraries\FileManager;
use Illuminate\Validation\Factory as ValidatorFactory;

use Request;
use Crypt;
use Input;
use Validator;

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
        $this->excelObj = new ExcelLib;
        $this->FileManager=new FileManager();
        $this->download =  new DownloadController();
	}

	public function ftp()
	{
		$data = Request::all();
        $res = array();
        $images = $this->ftpLib->collectImagesFromFtp($data['product']);
        $this->prodConfigs=$this->productHelper->getProductDevConfigs($data['product']);
        foreach($images as $key=>$value)
        {
            foreach($value as $key1=>$value1)
            {
                if(isset($this->prodConfigs['pdc_client_pattern']) && $this->prodConfigs['pdc_client_pattern']!='')
                {   
                    $this->patternLib->pattern=$this->prodConfigs['pdc_client_pattern'];
                    $this->patternLib->subject=$value1;
                    $part=$this->patternLib->stringExtract(1);

                }else{
                    $part = explode("_",$value1);    
                    $part = $part;
                }
                $res[$part] = $key;
            }
        }
        $refs = explode(",",$data['ref']);
        $final = array();
        foreach($refs as $key => $value){
            if(array_key_exists(trim($value),$res)){
                $referenceInfo=$this->productHelper->refernceInfo($value,$data['product'],$this->prodConfigs);
                $final[$value]['value']=$value;
                $final[$value]['pdn']=$referenceInfo['pdn'];
                $final[$value]['ref']=$referenceInfo['ref'];
                $final[$value]['gen']=$referenceInfo['gen'];
                $final[$value]['delivery']=$referenceInfo['delivery'];
                $final[$value]['folder']=$res[$value];
            }
        }
        if(count($final) == 1)
        {
            foreach($final as $final1){
                return redirect('product/ftp/'.Crypt::encrypt($data['product']).'/images/?f='.Crypt::encrypt($final1['folder']).'&r='.Crypt::encrypt($final1['value']) );
            }
        }
        else
        {
            if(!empty($final)):
            foreach($final as $final2)
                $result[] = $final2;
            $header = array('value'=>'Image','pdn'=>'PDN','ref'=>'REF','gen'=>'GEN','delivery'=>'Delivery');
            $final_result[0]['value'] = 'Image';
            $final_result[0]['pdn'] = 'PDN';
            $final_result[0]['ref'] = 'REF';
            $final_result[0]['gen'] = 'GEN';
            $final_result[0]['delivery'] = 'DELIVERY';
            foreach($result as $k=>$v){
                $final_result[$k+1]['value'] = $result[$k]['value'];
                $final_result[$k+1]['pdn'] = ($result[$k]['pdn'] == 1) ? "Yes" : "No";
                $final_result[$k+1]['ref'] = ($result[$k]['ref'] == 1) ? "Yes" : "No";
                $final_result[$k+1]['gen'] = ($result[$k]['gen'] == 1) ? "Yes" : "No";
                $final_result[$k+1]['delivery'] = ($result[$k]['delivery'] == 1) ? "Yes" : "No";
            }
            $fileName = "singlesearch.xlsx";
            $filePath = public_path()."/uploads/products/".$data['product']."/".$fileName ;
            $fname=$this->excelObj->writeExcelSearch($final_result,$filePath);
            return response()->download($fname);
            else:
                return redirect()->back()->with('customError',['The reference does not exist']);
            endif;
        }
		
	}

    public function fileSearch()
    {
        $data = Request::all();
        $options = array('name'=>'file_upload','url'=>'products/'.$data['product'].'/search/');
        $file = Input::file('filesearch');
        $validator = Validator::make(
            [
                'file'      => $file,
                'extension' => strtolower($file->getClientOriginalExtension()),
            ],
            [
                'file'          => 'required',
                'extension'      => 'required|in:xlsx',
            ]
        );
        if($validator->fails())
        {
                return redirect()->back()->withInput()->withErrors($validator->errors());
        }
        $upload=$this->FileManager->simpleUplaod($file,$options);
        $refs=$this->excelObj->readExcel(public_path()."/uploads/".$upload);
        
        $res = array();
        $images = $this->ftpLib->collectImagesFromFtp($data['product']);
        $this->prodConfigs=$this->productHelper->getProductDevConfigs($data['product']);
        foreach($images as $key=>$value)
        {
            foreach($value as $key1=>$value1)
            {
                if(isset($this->prodConfigs['pdc_client_pattern']) && $this->prodConfigs['pdc_client_pattern']!='')
                {   
                    $this->patternLib->pattern=$this->prodConfigs['pdc_client_pattern'];
                    $this->patternLib->subject=$value1;
                    $part=$this->patternLib->stringExtract(1);

                }else{
                    $part = explode("_",$value1);    
                    $part = $part;
                }
                $res[$part] = $key;
            }
        }
        foreach($refs as $key => $value){
            foreach($value as $k1 => $v1){
                if(array_key_exists(trim($v1),$res)){
                    $referenceInfo=$this->productHelper->refernceInfo($v1,$data['product'],$this->prodConfigs);
                    $final[$v1]['value']=$v1;
                    $final[$v1]['pdn']=$referenceInfo['pdn'];
                    $final[$v1]['ref']=$referenceInfo['ref'];
                    $final[$v1]['gen']=$referenceInfo['gen'];
                    $final[$v1]['delivery']=$referenceInfo['delivery'];
                    $final[$v1]['folder']=$res[$v1];
                }
            }
            
        }
        if(!empty($final)):
        foreach($final as $final2)
                $result[] = $final2;
            $final_result[0]['value'] = 'Image';
            $final_result[0]['pdn'] = 'PDN';
            $final_result[0]['ref'] = 'REF';
            $final_result[0]['gen'] = 'GEN';
            $final_result[0]['delivery'] = 'DELIVERY';
            foreach($result as $k=>$v){
                $final_result[$k+1]['value'] = $result[$k]['value'];
                $final_result[$k+1]['pdn'] = ($result[$k]['pdn'] == 1) ? "Yes" : "No";
                $final_result[$k+1]['ref'] = ($result[$k]['ref'] == 1) ? "Yes" : "No";
                $final_result[$k+1]['gen'] = ($result[$k]['gen'] == 1) ? "Yes" : "No";
                $final_result[$k+1]['delivery'] = ($result[$k]['delivery'] == 1) ? "Yes" : "No";
            }
            $fileName = "filesearch.xlsx";
            $filePath = public_path()."/uploads/products/".$data['product']."/".$fileName ;
            $fname=$this->excelObj->writeExcelSearch($final_result,$filePath);
            return response()->download($fname);
        else:
            return redirect()->back()->with('customError',['The file does not consist of valid references']);
        endif;
    }
}
