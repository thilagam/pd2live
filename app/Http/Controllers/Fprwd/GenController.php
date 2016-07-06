<?php namespace App\Http\Controllers\Fprwd;

use App\Http\Requests;
use App\Http\Controllers\Controller;

//use Illuminate\Http\Request;
use Request;
use App\CepProductConfigurations;
use App\CepItemConfigurations;
use App\CepUploads;
use App\CepItems;
use DB;
use Auth;
use Validator;
use Input;

/* Services */
use App\Services\UploadsManager;

/* Libraries */
use App\Libraries\FileManager;
use App\Libraries\CheckAccessLib;
use App\Libraries\ProductHelper;
use App\Libraries\EMailer;
use App\Libraries\ActivityMainLib;
use App\Libraries\ExcelLib;
use App\Libraries\FtpLib;

class GenController extends Controller {

	public $prodConfigs;
	 /**
     * Instantiate a new UserController instance.
     */
    public function __construct(\Illuminate\Http\Request $request)
    {
    	$this->middleware('auth');

    	$this->permit=$request->attributes->get('permit');
    	$this->configs=$request->attributes->get('configs');
    	$this->dictionary=$request->attributes->get('dictionary');

    	$this->manager=new UploadsManager;
    	$this->activityObject = new ActivityMainLib;
    	$this->checkaccess = new CheckAccessLib;
    	$this->productHelper = new ProductHelper;

    	$this->emailActivity = new EMailer;
        $this->customactivity = new ActivityMainLib;

        $this->excellibobj = new ExcelLib;
        $this->FileManager=new FileManager();
        $this->FtpLib=new FtpLib();



    }

    /**
	 * Function pdn
	 *
	 * @param
	 * @return
	 */
	public function gen($id)
	{
		$gen=array();
		$alphaRange=array_combine(range('1','26'),range('A','Z'));
		
		$imageList=$this->FtpLib->collectImagesFromFtp($id);
		//echo "<pre>"; print_r($imageList);

		$itemConf= CepItemConfigurations::where('iconf_product_id',$id)
                     ->where('iconf_item_id','link_gen')
                     ->where('iconf_name','genType')
                     ->select('*')
                     ->first()->toArray();
        $itemType=$itemConf['iconf_value'];

        $itemConf= CepItemConfigurations::where('iconf_product_id',$id)
                     ->where('iconf_item_id','link_gen')
                     ->where('iconf_name','genRoute')
                     ->select('*')
                     ->first()->toArray();
        $itemRoute=$itemConf['iconf_value'];

        $itemConf= CepItemConfigurations::where('iconf_product_id',$id)
                     ->where('iconf_item_id','link_gen')
                     ->where('iconf_name','genDownload')
                     ->select('*')
                     ->first()->toArray();
        $itemDownload=$itemConf['iconf_value'];

        $this->prodConfigs=$this->productHelper->getProductDevConfigs($id);
        
       // echo "<pre>"; print_r($this->prodConfigs);
        
		foreach ($imageList as $key => $value) {
			//echo "<pre>"; print_r($value);exit;
			$folder=array();
			$folder['name']=$key;
			$folder['url']='product/ftp/'.$id.'?f='.$key;
			$folder['route']=$itemRoute.'/'.$id.'/'.$key;
			$folder['download']=$itemDownload.'/'.$id.'/'.$key;
			$folder['images']=count($value);
			$folder['total_ref']=count($this->FtpLib->getFolderReferences($value,$this->prodConfigs));
			$folderGenCounts=$this->productHelper->getFolderGenCount($key,$id,$this->prodConfigs);
			//echo "<pre>"; print_r($folderGenCounts);
			$folder['new']=$folderGenCounts[1];
			$folder['gen']=$folderGenCounts[0];
			$gen[]=(object) $folder;

		}
        		//echo "<pre>"; print_r($gen);exit;
       
		//echo "<pre>"; print_r($prodConfigs);        exit;					
        return view("product.gen",compact('gen','itemType','alphaRange'));
	}

	/**
	 * Function download
	 *
	 * @param
	 * @return
	 */		
	public function download($product,$folder,$type)
	{	$error=false;
		$genData=array();


		if($folder=='' || $product=='' || $type==''){
			$error=true;
		}else{
			//echo $product." ".$folder." ".$type;
			$this->prodConfigs=$this->productHelper->getProductDevConfigs($product);
			$model = "\App\ClientModels\\".$this->prodConfigs['pdc_client_gen_model'];
			$folderField=with(new $model)->folder;
			$reference=with(new $model)->reference;
			$genStatusField=with(new $model)->genStatus;
			$downloadField=with(new $model)->download;
			$dataField=with(new $model)->dataField;
			//echo $folder;exit;
			//echo "<pre>"; print_r($this->prodConfigs);exit;
			$imageList=$this->FtpLib->collectImagesFromFtp($product);
			$images=$this->FtpLib->getFolderReferences($imageList[$folder],$this->prodConfigs);
			//echo "<pre>"; print_r($images);exit;
			if($type=='all'){
				$genData=$model::with('downloads')->where($folderField,'=',$folder)->get()->toArray();
			}elseif($type=='new'){
				$genData=$model::where($folderField,'=',$folder)->where($genStatusField,'=',0)->where($downloadField,'=','')->get()->toArray();
			}else{
				$error=true;
			}
			//echo "<pre>"; print_r($genData);
			$updateIds=array();
			if(!$error && !empty($genData)){
				$new=array();
				$dowblon=array();
				$na=array();
				foreach ($genData as $key => $value) {
					if($value[$downloadField]==0 ){
						$new[]=json_decode($value[$dataField]);
						$updateIds[]=$value[$reference];
					}else{
						$dData=json_decode($value[$dataField]);
						//echo "<pre>"; print_r($value);
						$dData[0]=(!empty($value['downloads']))?"DOUBLON_".$value['downloads']['download_name']:'';
						//echo "<pre>"; print_r($dData);exit;
						$dowblon[]=$dData;
					}
					
					$pos = array_search($value[$reference], $images);
					unset($images[$pos]);
				}
				
				if(!empty($images)){
					foreach ($images as $key => $value) 
					{
						$temp=array(
							'NA',
							$value
						);
						$na[]=$temp;
					}
				}
				/* Get Headers  */
				$itemConf= CepItemConfigurations::where('iconf_product_id',$product)
                     ->where('iconf_item_id','link_gen')
                     ->select('*')
                     ->get()->toArray();	
                    // echo "<pre>"; print_r($itemConf);exit;
                $genConf=array();     
                foreach ($itemConf as $key => $value) {
                	$genConf[$value['iconf_name']]=$value['iconf_value'];
                }
                array_unshift($new,json_decode($genConf['genHeader1']));
                array_unshift($dowblon,json_decode($genConf['genHeader2']));
                array_unshift($na,json_decode($genConf['genHeader3']));
                //echo "<pre>"; print_r($itemConf);     	
				//echo "<pre>"; print_r(array($new,$dowblon,$na));exit;
                /* Generate File */		
               // $WriterInfo=$this->productHelper->checkwriterAvailable($product,true);
      			//echo "<pre>"; print_r(array($new,$dowblon,$na));exit;
                $fileName = $genConf['genName']."_".uniqid() ."_".date('y-m-d').".xlsx" ;
    	  		$filePath = public_path()."/uploads/products/".$product."/gen/".$fileName ;
	    		$this->excellibobj->writeMultiExcel(array($new,$dowblon,$na),array('new','doub','na'),$filePath);
	    		$path='products/'.$product."/gen/".$fileName;	
	    		//exit;
      			/* Download entry Prepaarations and Entry*/
			  	$productInfo=$this->productHelper->checkProductExists($product);
			  	$genInfo=$this->productHelper->checkGenAvailable($product,true);
      			//echo "<pre>"; print_r($genInfo);
			  	$dwdoptions=array(
			  				'name'=>basename($fileName),
			  				'type'=>'writer',
			  				'client'=>$productInfo->client_id,
			  				'product'=>$product,
			  				'item'=>$genInfo['pconf_item_id'],
			  				'path'=>$path,
			  				'description'=>'gen file generated'
			  			);
			  	
			  	if($this->FileManager->downloadInitiated($dwdoptions)){
			  		$dwd=$this->FileManager->getDwdId(basename($fileName),$genInfo['pconf_item_id']);
			  		$model::whereIn($reference,$updateIds)
			  				->update(
			  						array(
			  							$genStatusField=>1,
			  							$downloadField=>$dwd['download_id']
			  						 )
			  						);
			  		return redirect("download/".$dwd['download_id']);

			  	}	
			  		
			}

		}

		//echo "<pre>"; print_r($images);exit;
		if($error){
		 return redirect("product/gen/".$product)->with('customError', $this->dictionary->msg_gen_process_fail);
		}else{

		}
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		//
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

}
