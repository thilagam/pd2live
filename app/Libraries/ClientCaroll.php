<?php namespace App\Libraries;

use Auth;
use request;
use Carbon\Carbon;
use File;
use DB;

use App\CepUploads; 
use App\CepDownloads;
use App\CepDownloadLogs;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Response;
use App\Services\UploadsManager;

/* Caroll Models */		
use App\ClientModels\Caroll\CepClientCarollPdns;
use App\ClientModels\Caroll\CepClientCarollRefs;
use App\ClientModels\Caroll\CepClientCarollGens;

/**
* Caroll class used for Processing lahale client logic
*
*/
class ClientCaroll
{
	public $pdnModel;
	public $refModel;
	public $genModel;


	/**
	 * Function __Construct
	 *
	 * @param
	 * @return
	 */		
	public function __construct()
	{
		$this->pdnModel=new CepClientCarollPdns();
		$this->refModel=new CepClientCarollRefs();
		$this->genModel=new CepClientCarollGens();		
	
	}	
  /**
	 * Function referenceFileUploads
	 * insert new references to the DB no duplicate
	 * @param array $data
     * @param array $options
	 * @return
	 */
	public function referenceFileUploads($data,$options,$col=0)
	{
      //$options['model'] = '\CepClientKorbenCchRefs';
		$model = "\App\ClientModels".$options['model'];
		$fields = with(new $model)->getFillable();
		$dataNew = array();
		unset($data[0]); // Unset Header
	    foreach($data as $key=>$dt){
	    	if($dt[$col]!=''){
		 		$dataNew[] = array(
									$fields[0]=>$dt[$col],
									$fields[1]=>json_encode($dt),
									$fields[2]=>$options['upload_id']
								  );
		 	}
		 }
		return ($model::insertIgnore($dataNew) ? true : false);
    }
  
    /**
	 * Function pdnFileUploads
	 * insert new references to the DB no duplicate
	 * @param array $data
	 * @param array $options
	 * @return
	 */
	public function pdnFileUploads($data,$options,$col=0)
	{
      //$options['model'] = '\CepClientKorbenCchRefs';
			$model = "\App\ClientModels".$options['model'];
			$fields = with(new $model)->getFillable();
			$dataNew = array();
			unset($data[0]); // Unset Header
		    foreach($data as $key=>$dt){
		    	if($dt[$col]!=''){
			 		$dataNew[] = array(
										$fields[0]=>$dt[$col],
										$fields[1]=>json_encode($dt),
										$fields[2]=>$options['upload_id']
									  );
		    	}
			 }
			return ($model::insertIgnore($dataNew) ? true : false);
    }
  

	/**
	 * Function writerFileUploads
	 * insert new references to the DB no duplicate
	 * @param array $data, $option, $col(reference column), $url(sheet column 1 or 2 mostly)
   * @param array $options
	 * @return
	 */
	public function writerFileUploads($data,$options,$col=0,$url=2)
	{
      //$options['model'] = '\CepClientKorbenCchRefs';
			$model = "\App\ClientModels".$options['model'];
			$fields = with(new $model)->getFillable();
			//print_r ($fields); exit;
			$dataNew = array();
			unset($data[0]);  // Unset Header
		  foreach($data as $key=>$dt){
			 		$dataNew[] = array(
									$fields[0]=>$dt[$col],
									$fields[1]=>$dt[$url],
									$fields[2]=>0,
									$fields[3]=>json_encode($dt),
									$fields[4]=>$options['upload_id']
									);
			 }
			return ($model::insertIgnore($dataNew) ? true : false);
    }
    
    /**
	 * Function uniqueReferenceFileDownloads
	 * insert new references to the DB no duplicate
	 * @param array $data
   * @param array $options
	 * @return
	 */
	public function uniqueReferenceFileDownloads($item)
	{
        
    }

	/**
	 * @Sheel-Script
	 * Function ftpImageCheck -> Type
	 * check image on ftp and return url or None
	 * @param $product_id
     * @param $reference_id
	 * @return
	 */
	public function ftpImageCheck($product_id,$reference_id)
	{
		$cmd = "find ".public_path()."/uploads/products/".$product_id."/ftp/ -iname ".$reference_id."\* | wc -l";
		return shell_exec($cmd);
	}


	/**
	 * Function uniquePdnReferences
	 *
	 * @param
	 * @return
	 */		
	public function uniquePdnReferences($refInfo=false,$refData=false)
	{
		$globalParentsData=array();
		$globalParentsDataFile=array();
		$globalParentsXlsFile=array();
		$globalData=array();
		$fields="*";
		$pdnData=array();
		if($refInfo && $refData)
		{
			$pdnData=CepClientCarollPdns::select('caroll_pdn_reference','caroll_pdn_data','caroll_pdn_upload_id')
									->with('uploads')
									->where('caroll_pdn_status','=',1) 
								  	->get()->toArray();
		}
		elseif($refInfo && !$refData)
		{
			$pdnData=CepClientCarollPdns::select('caroll_pdn_reference','caroll_pdn_upload_id')
									->with('uploads')
									->where('caroll_pdn_status','=',1) 
								  	->get()->toArray();
		}
		elseif(!$refInfo && $refData)
		{
			$pdnData=CepClientCarollPdns::select('caroll_pdn_reference','caroll_pdn_data')
									->where('caroll_pdn_status','=',1) 
								  	->get()->toArray();
		}
		else
		{
			$pdnData=CepClientCarollPdns::select('caroll_pdn_reference')
									->where('caroll_pdn_status','=',1) 
								  	->get()->toArray();
		}

		if(!empty($pdnData)){
			foreach ($pdnData as $key => $value) {
				$global_parents_data[]=$value['caroll_pdn_reference'];
				$global_parents_data_file[$value['caroll_pdn_reference']]=(!empty($value['uploads']))?$value['uploads']['upload_original_name']."_".$value['uploads']['upload_date']:'';
				$globalData[$value['caroll_pdn_reference']]=(isset($value['caroll_pdn_data']))? json_decode($value['caroll_pdn_data']):'';
			}
		}
		return array($global_parents_data,$global_parents_data_file,$globalData);
	}

	/**
	 * Function uniqueRefReferences
	 *
	 * @param
	 * @return
	 */		
	public function uniqueRefReferences($refInfo=false,$refData=false)
	{
		$globalParentsData=array();
		$globalParentsDataFile=array();
		$globalParentsXlsFile=array();
		$globalData=array();
		$fields="*";
		$referenceData=array();
		if($refInfo && $refData)
		{
			$referenceData=CepClientCarollRefs::select('caroll_ref_reference','caroll_ref_data','caroll_ref_upload_id')
									->with('uploads')
									->where('caroll_ref_status','=',1) 
								  	->get()->toArray();
		}
		elseif($refInfo && !$refData)
		{
			$referenceData=CepClientCarollRefs::select('caroll_ref_reference','caroll_ref_upload_id')
									->with('uploads')
									->where('caroll_pdn_status','=',1) 
								  	->get()->toArray();
		}
		elseif(!$refInfo && $refData)
		{
			$referenceData=CepClientCarollRefs::select('caroll_ref_reference','caroll_ref_data')
									->where('caroll_ref_status','=',1) 
								  	->get()->toArray();
		}
		else
		{
			$referenceData=CepClientCarollRefs::select('caroll_ref_reference')
									->where('caroll_ref_status','=',1) 
								  	->get()->toArray();
		}

		if(!empty($referenceData)){
			foreach ($referenceData as $key => $value) {
				$global_parents_data[]=$value['caroll_ref_reference'];
				$global_parents_data_file[$value['caroll_ref_reference']]=(!empty($value['uploads']))?$value['uploads']['upload_original_name']."_".$value['uploads']['upload_date']:'';
				$globalData[$value['caroll_ref_reference']]=(isset($value['caroll_ref_data']))? json_decode($value['caroll_ref_data']):'';
			}
		}
		//echo "<pre>"; print_r(array($global_parents_data,$global_parents_data_file,$globalData));exit;
		return array($global_parents_data,$global_parents_data_file,$globalData);
	}

	/**
	 * Function uniqueGenReferences
	 *
	 * @param
	 * @return
	 */		
	public function uniqueGenReferences($info=false)
	{
		$globalParentsData=array();
		$globalParentsDataFile=array();
		$globalParentsXlsFile=array();
		$globalData=array();
		$fields="*";
		$referenceData=array();
		if($refInfo && $refData)
		{
			$genData=CepClientCarollGens::select('caroll_gen_reference','caroll_gen_data','caroll_gen_upload_id')
									->with('downloads')
									->where('caroll_gen_status','=',1) 
								  	->get()->toArray();
		}
		elseif($refInfo && !$refData)
		{
			$genData=CepClientCarollGens::select('caroll_gen_reference','caroll_gen_upload_id')
									->with('downloads')
									->where('caroll_gen_status','=',1) 
								  	->get()->toArray();
		}
		elseif(!$refInfo && $refData)
		{
			$genData=CepClientCarollGens::select('caroll_gen_reference','caroll_gen_data')
									->where('caroll_gen_status','=',1) 
								  	->get()->toArray();
		}
		else
		{
			$genData=CepClientCarollGens::select('caroll_gen_reference')
									->where('caroll_gen_status','=',1) 
								  	->get()->toArray();
		}

		if(!empty($genData)){
			foreach ($genData as $key => $value) 
			{
				$global_parents_data[]=$value['caroll_gen_reference'];
				$global_parents_data_file[$value['caroll_gen_reference']]=(!empty($value['uploads']))?$value['uploads']['download_name']."_".$value['uploads']['download_date']:'';
				$globalData[$value['caroll_gen_reference']]=(isset($value['caroll_gen_data']))? json_decode($value['caroll_gen_data']):'';
			}
		}
		//echo "<pre>"; print_r(array($global_parents_data,$global_parents_data_file,$globalData));exit;
		return array($global_parents_data,$global_parents_data_file,$globalData);
	}
}
