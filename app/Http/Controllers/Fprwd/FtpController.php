<?php namespace App\Http\Controllers\Fprwd;

use Auth;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Libraries\PatternLib;
use App\Libraries\ProductHelper;
use App\Libraries\FtpLib;

//use Illuminate\Http\Request;
use Request;
use Crypt;

class FtpController extends Controller {

	public $user_id;
 	/**
 	* Instantiate a new UserController instance.
 	*/
    public function __construct(\Illuminate\Http\Request $request)
    {	
    	/* 
    	 * Load only if User is Logged in 
		 * No need to Validate Login here we want free image URLS
    	 */		
    	$this->user_id = Auth::id();
    	if($this->user_id){
    		$this->permit=$request->attributes->get('permit');
    	}
    	$this->configs=$request->attributes->get('configs');
    	$this->dictionary=$request->attributes->get('dictionary');
    	$this->productHelper = new ProductHelper;
    	$this->patternLib= new PatternLib;
    	$this->prodConfigs=array();
    	
    	$this->ftpLib = new FtpLib();
    	
    }
	/**
	 * Function ftp
	 *
	 * @param
	 * @return
	 */		
    public function viewReference($id){
		
        $product_id = $id;
		$reference_listing = array();
		$folder_listing = array();
		$this->prodConfigs=$this->productHelper->getProductDevConfigs($id);
        $dir = public_path()."/uploads/products/".$id."/ftp/";
		if ($handle = opendir($dir)) {
 		   while (false !== ($entry = readdir($handle))) {
        		if ($entry != "." && $entry != "..") {
            		$folder_listing[] = $entry;
        		}
    		}
    		closedir($handle);
		}
		//exit;

       if(count($folder_listing) > 0){
		$data = Request::all();
		$folder_show = "";
		if(empty($data))
			$folder_show = $folder_listing[0];
		else
			$folder_show = $data['f'];

		$dir = public_path()."/uploads/products/".$id."/ftp/".$folder_show."/";
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
	   } 

		//echo "<pre>"; print_r($reference_listing); exit;
		return view("product.reference", compact('folder_listing','reference_listing','product_id','folder_show'));
	}


	/**
	 * Function ftp
	 *
	 * @param
	 * @return
	 */		

	public function viewReferenceImage($id){

		$product_id = "";
		$folder_show = "";
		$images_show = "";

//		try{
		$product_id = Crypt::decrypt($id);
		$data = Request::all();
		$folder_show = "";
		if(empty($data))
			return redirect('accessDenied');
		else{
			$folder_show = Crypt::decrypt($data['f']);
			$images_show = Crypt::decrypt($data['r']);
		}
//		}catch(DecryptException $e){ return redirect('accessDenied');  } 	

		$reference_listing = array();
		$dir = public_path()."/uploads/products/".$product_id."/ftp/".$folder_show."/";		//exit;

		/* Get Dev configs */		
		$this->prodConfigs=$this->productHelper->getProductDevConfigs($product_id);
      	$referenceInfo=$this->productHelper->refernceInfo($images_show,$product_id,$this->prodConfigs);
		try{
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
            			$part = $part[0];
            		}
					//$part = explode("_",$entry);
					//echo $part." ".$images_show."<br />";exit;
            		if($part==$images_show){
            			$reference_listing1['ref_path'] = "/uploads/products/".$product_id."/ftp/".$folder_show."/".$entry;
            			$reference_listing1['ref_id'] = $entry;
            			$reference_listing[] = $reference_listing1;
            		}	
        		}
    		}
    		closedir($handle);
    	 }
    	}catch(Exception $e){ return redirect('accessDenied');  } 

		//print_r ($reference_listing); exit;
    	if(empty($reference_listing))
			return redirect('404'); // Stop hacking and stop entrying wrong data and redirect to 404 page.

			return view("product.reference_image",compact('product_id','reference_listing','images_show','product_id','referenceInfo'));
	}

	/**
	 * Function ftp
	 *
	 * @param
	 * @return
	 */		
	public function ftpImageView($id)
	{	
			$product_id = $id;
			$data = Request::all();
			$folder_show = "";
			$images_show = "";
			if(empty($data))
				return redirect('accessDenied');
			else{
				$folder_show = $data['f'];
				$images_show = $data['r'];
			}
			//exit;
			return redirect('/product/ftp/'.Crypt::encrypt($product_id).'/images/?f='.Crypt::encrypt($folder_show).'&r='.Crypt::encrypt($images_show));
	}
	

}
