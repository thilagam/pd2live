<?php namespace App\Http\Controllers\Client;

use App\Http\Requests;
use App\Http\Controllers\Controller;
 
use Illuminate\Http\Request;

/* Models */		
use App\CepProducts;
use App\CepProductUsers;
use App\CepProductFtp;
use App\CepProductConfigurations;
use App\CepItemConfigurations;
use App\CepProductMailingList;
use App\CepItems;
use App\CepUploads;
use App\ClientModels\Caroll\CepClientCarollPdns;
use App\ClientModels\Caroll\CepClientCarollRefs;
use App\ClientModels\Caroll\CepClientCarollGens;

/* FACADES */		
use DB;
use Validator;
use Auth;
use Input;  
//use Request;
use Crypt;
use Config;
use FTP;
use Log;
use File;
use Storage;

/* Services */		
use App\Services\UploadsManager;

/* Libraries */		
use App\Libraries\FileManager; 
use App\Libraries\ActivityMainLib;
use App\Libraries\CheckAccessLib;
use App\Libraries\ProductHelper;
use App\Libraries\ClientCaroll;
use App\Libraries\ExcelLib;
use App\Libraries\FtpLib;


class CarollController extends Controller 
{
	  private $user_id;

    public $permissions;
	  public $configs;
	  public $dictionary;

	  private $manager;
  	private $activityObject;
  	private $checkaccess;
  	private $productHelper;
  	private $ClientCaroll;
    public $pdnColLength=8;
    public $pdnKey=0;
    public $refKey=0;


  	/**
     * Instantiate a new UserController instance.
     */
    public function __construct(\Illuminate\Http\Request $request)
    {	
    	$this->middleware('auth');
    	$this->user_id = Auth::id();

    	$this->permit=$request->attributes->get('permit');
    	$this->configs=$request->attributes->get('configs');
    	$this->dictionary=$request->attributes->get('dictionary');

      /* Lib Init */    
    	$this->activityObject = new ActivityMainLib;
    	$this->checkaccess = new CheckAccessLib;
    	$this->productHelper = new ProductHelper;

    	$this->ClientCaroll = new ClientCaroll;
      $this->excelLib = new ExcelLib();
      $this->FtpLib=new FtpLib();
      /* Model Init */      
      $this->pdnModel=new CepClientCarollPdns();
      $this->refModel=new CepClientCarollRefs();
      $this->genModel=new CepClientCarollGens();  


    	
    }

    /**
     * Function pdn
     *
     * @param
     * @return
     */		
    public function pdn($product,$id)
    {	
    	$error=false;

		    /* Get Upload Data */		
		    $uploadData=CepUploads::where('upload_id',$id)->where('upload_status',1)->first();

        /* Read File */       
        $url=public_path()."/uploads/".$uploadData['upload_url'];
        $data = $this->excelLib->readExcelUniqueValue($url, $this->pdnKey);
        
        $prodConfigs['pdn']=CepProductConfigurations::where('pconf_type','=','pdn')
                     ->where('pconf_product_id','=',$product)
                     ->first();
      //  echo "<pre>"; print_r($prodConfigs);    exit;                 
        /* Upload Pdn References */        
        $options=array();
        $options['upload_id'] = $id;
        $options['model'] = '\Caroll\CepClientCarollPdns';
        if($this->ClientCaroll->pdnFileUploads($data,$options,$prodConfigs['pdn']['pconf_reference_id']-1)){
            /* Check if upload success and change upload status */  
            $uploadData->upload_verification_status=1;
            $uploadData->upload_verification_by=$this->user_id;
            $uploadData->upload_verification_msg='success';
            $uploadData->save();      
        }else{
            $error=true;
        }
		
        if(!$error){
            return redirect("product/gen/".$product)->with('success', $this->dictionary->msg_pdn_process_success);
        }else{
            return redirect("product/gen/".$product)->with('customError', $this->dictionary->msg_pdn_process_fail);
        }
    }
    /* For Reference  */        
    //$pdns=$this->ClientCaroll->uniquePdnReferences(true,true);
    //$pdns=$this->ClientCaroll->uniqueRefReferences(true,true);

    /**
     * Function ref
     *
     * @param
     * @return
     */        
    public function ref($product,$id)
    {
        $error=false;

        /* Get Upload Data */       
        $uploadData=CepUploads::where('upload_id',$id)->where('upload_status',1)->first();

        /* Read File */       
        $url=public_path()."/uploads/".$uploadData['upload_url'];
        $data = $this->excelLib->readExcelUniqueValue($url, $this->refKey);
        
        $prodConfigs['ref']=CepProductConfigurations::where('pconf_type','=','ref')
                     ->where('pconf_product_id','=',$product)
                     ->first();
        /* Upload Pdn References */        
        $options=array();
        $options['upload_id'] = $id;
        $options['model'] = '\Caroll\CepClientCarollRefs';
        if($this->ClientCaroll->referenceFileUploads($data,$options,$prodConfigs['ref']['pconf_reference_id']-1)){
            /* Check if upload success and change upload status */  
            $uploadData->upload_verification_status=1;
            $uploadData->upload_verification_by=$this->user_id;
            $uploadData->upload_verification_msg='success';
            $uploadData->save();      
        }else{
            $error=true;
        }
        //rturn ($error)?false:true;
        if(!$error){
            return redirect("product/gen/".$product)->with('success', $this->dictionary->msg_ref_process_success);
        }else{
            return redirect("product/gen/".$product)->with('customError', $this->dictionary->msg_ref_process_fail);
        }
    }

    /**
     * Function gen
     *
     * @param
     * @return
     */        
    public function gen($product,$folder)
    {
        //echo "PRD:".$product."<br>";
        //echo "FOLDER:".$folder."<br>";
        //$model = $this->genModel;
        $fields = $this->genModel->getFillable();
        //echo "<pre>"; print_r($fields);exit;
        $imageList=$this->FtpLib->collectImagesFromFtp($product,$folder);
        $this->prodConfigs=$this->productHelper->getProductDevConfigs($product);
        $references=$this->FtpLib->getFolderReferences($imageList[$folder],$this->prodConfigs);
        // echo "<pre>"; print_r($references);exit;
        $oldGen=$this->productHelper->getFolderGenCount($folder,$product,$this->prodConfigs);
       // echo "<pre>"; print_r($oldGen[0]);
        /* Single query to check if reference present in PDN , REF and not in GEN */    
        $genData = DB::table('cep_client_caroll_pdns')
                    ->select('caroll_pdn_reference as ref' ,
                             'caroll_pdn_data as pdn_data' ,
                             'caroll_ref_data as ref_data'
                             )
                    ->leftjoin('cep_client_caroll_refs', 'caroll_ref_reference', '=', 'caroll_pdn_reference')
                    ->whereIn('caroll_pdn_reference',$references)
                    ->whereNotIn('caroll_pdn_reference', function($q){
                          $q->select('caroll_gen_reference')->from('cep_client_caroll_gens');
                      })
                    ->where('caroll_ref_status', '=', 1)
                    ->where('caroll_pdn_status', '=', 1)

                    ->orderBy('caroll_pdn_reference', 'asc')
                    ->groupBy('caroll_pdn_reference')
                    ->get();
       // echo "<pre>"; print_r($genData);exit;
        $newGen=array();   
        $insertArray=array(); 
        $index=2;        
        foreach ($genData as $key => $value) {
          //echo "<pre>"; print_r($value);
          $refData=json_decode($value->ref_data);
          //echo "<pre>"; print_r($refData);
          $refData=(array) $refData;
          $newRefData=array();
          foreach ($refData as $key2 => $value2) {
            $newRefData[$key2]=$value2;
          }
          $refData=$newRefData;
         // exit;
          //  echo "<pre>"; print_r($refData);
         // echo $refData[0];
         // exit;
          $pdnData=json_decode($value->pdn_data);
          $pdnData=(array) $pdnData;
          $newPdnData=array();
          foreach ($pdnData as $key2 => $value2) {
            $newPdnData[$key2]=$value2;
          }
          $pdnData=$newPdnData;
         // echo "<pre>"; print_r($pdnData); exit;
          $len="LEN(D".$index.")";
          $temp=array(
                  url('/product/ftp/'.Crypt::encrypt($product).'/images/?f='.Crypt::encrypt($folder).'&r='.Crypt::encrypt($value->ref)),
                  '',
                  (isset($refData[14]))?$refData[14]:'',
                  '',
                  $len,
                  (isset($pdnData[2]))?$pdnData[1]:'',
                  (isset($pdnData[2]))?$pdnData[2]:'',
                  (isset($pdnData[3]))?$pdnData[3]:'',
                  (isset($pdnData[4]))?$pdnData[4]:'',
                  (isset($pdnData[5]))?$pdnData[5]:'',
                  (isset($pdnData[6]))?$pdnData[6]:'',
                  (isset($pdnData[7]))?$pdnData[7]:'',
                  
                  (isset($refData[3]))?$refData[3]:'',
                  (isset($refData[4]))?$refData[4]:'',
                  (isset($refData[6]))?$refData[6]:'',
                  (isset($refData[7]))?$refData[7]:'',
                  (isset($refData[8]))?$refData[8]:'',
                  (isset($refData[9]))?$refData[9]:'',
                  (isset($refData[10]))?$refData[10]:'',
                  (isset($refData[11]))?$refData[11]:'',
                  (isset($refData[12]))?$refData[12]:'',
                  (isset($refData[13]))?$refData[13]:'',
                  (isset($refData[15]))?$refData[15]:''
                  );

          //echo "<pre>"; print_r($comb);exit;
          $newGen[]=$temp;
          $insertArray[]=array(
                    $fields[1]=>$value->ref,
                    $fields[2]=>(isset($refData[15]))?$refData[15]:'',
                    $fields[3]=>url('/product/ftp/'.Crypt::encrypt($product).'/images/?f='.Crypt::encrypt($folder).'&r='.Crypt::encrypt($value->ref)),
                    $fields[4]=>$folder,
                    $fields[5]=>'',
                    $fields[6]=>json_encode($temp),
                    $fields[7]=>''
                    );
          $index++;

        }
        //echo "<pre>"; print_r($insertArray);exit;
        //rturn ($error)?false:true;
        if($this->genModel->insertIgnore($insertArray) ? true : false){
            return redirect("product/gen/".$product)->with('success', $this->dictionary->msg_gen_process_success);
        }else{
            return redirect("product/gen/".$product)->with('customError', $this->dictionary->msg_gen_process_fail);
        }
    }

}