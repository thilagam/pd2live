<?php namespace App\Libraries;

use Auth;
use request;
use Carbon\Carbon;
use File;
use DB;
use Redirect;

use App\CepProducts;
use App\CepProductFtp;
use App\CepProductConfigs;
use App\CepProductItems;
use App\CepProductUsers;
use App\CepProductConfigurations; 
use App\CepDeveloperConfigurations; 
use App\CepItemConfigurations;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Response;

/**
* Lahalle class used for Processing lahale client logic
*
*/
class ProductHelper
{

	public $user_id;


	public function __construct()
	{
		$this->user_id = Auth::id();
	}


	/**
	 * Function checkProductExists
	 *
	 * @param
	 * @return
	 */
	public function checkProductExists($id)
	{
		/* Get Product details   */
		$product = DB::table('cep_products')
                    ->select('prod_id as id' ,
                    		 'id as client_id' ,
                    		 'prod_name as name',
                             'up_company_name as company',
                             'prod_description as description',
                             'cep_products.created_at as create_date',
                             'prod_revert_time',
                             'prod_status'
                            )
                    ->leftjoin('cep_product_users', 'puser_product_id', '=', 'prod_id')
                    ->leftjoin('users', 'puser_user_id', '=', 'id')
                  	->leftjoin('cep_user_plus', 'up_user_id', '=', 'puser_user_id')
                    ->where('users.status', '=', 1)
                    ->where('prod_id','=',$id)
                    ->first();

        if(is_null($product))
        {
        	return false;
        }else
        {
        	return $product;
        }
	}

	/**
	 * Function checkPdnAvailable
	 *
	 * @param
	 * @return
	 */
	public function checkPdnAvailable($id,$data=false)
	{
		$pdn=CepProductConfigurations::where('pconf_type','=','pdn')
										 ->where('pconf_product_id','=',$id)
										 ->where('pconf_status','=',1)
										 ->first();
		 if(is_null($pdn))
        {
        	return false;
        }else
        {
        	return ($data)? $pdn : true;
        }
	}

	/**
	 * Function checkRefAvailable
	 *
	 * @param
	 * @return
	 */
	public function checkRefAvailable($id,$data=false)
	{
		$ref=CepProductConfigurations::where('pconf_type','=','ref')
										 ->where('pconf_product_id','=',$id)
										 ->where('pconf_status','=',1)
										 ->first();
		if(is_null($ref))
        {
        	return false;
        }else
        {

        	return ($data)? $ref : true;
        }
	}

	/**
	 * Function checkDeliveryAvailable
	 *
	 * @param
	 * @return
	 */
	public function checkDeliveryAvailable($id,$data=false)
	{
		$writer=CepProductConfigurations::where('pconf_type','=','writer')
										 ->where('pconf_product_id','=',$id)
										 ->where('pconf_status','=',1)
										 ->first();
		 if(is_null($writer))
        {
        	return false;
        }else
        {
        	return ($data)? $writer : true;
        }
	}

	/**
	 * Function checkGenAvailable
	 *
	 * @param
	 * @return
	 */
	public function checkGenAvailable($id,$data=false)
	{
		$gen=CepProductConfigurations::where('pconf_type','=','gen')
										 ->where('pconf_product_id','=',$id)
										 ->first();
		 if(is_null($gen))
        {
        	return false;
        }else
        {
        	return ($data)? $gen : true;
        }
	}

	/**
	 * Function checkwriterAvailable
	 *
	 * @param
	 * @return
	 */
	public function checkwriterAvailable($id,$data=false)
	{
		$writer=CepProductConfigurations::where('pconf_type','=','writer')
										 ->where('pconf_product_id','=',$id)
										 ->first();
		if(is_null($writer))
        {
        	return false;
        }else
        {
        	return ($data)? $writer : true;
        }
	}

	/**
	 * Function getProductFtpInfo
	 *
	 * @param
	 * @return
	 */
	public function getProductFtpInfo($id,$data=false)
	{
		$ftp=CepProductFtp::where('ftp_product_id','=',$id)
							->where('ftp_status','=',1)
							->first();
		if(is_null($ftp))
        {
        	return false;
        }else
        {
        	return ($data)? $ftp : true;
        }
	}

	/**
	 * Function getClientProductUsers
	 * Get Users Related to product based on Group Codes
	 * @param integer $id , product id
	 * @param array $userTypes , List of client related group codes array('BO','CL','CM','ANy Other Group code')
	 * @return array $users array of users
	 */
	public function getClientProductUsers($id,$userTypes=array())
	{
		$users= DB::table('cep_products')
                    ->select('prod_id as id' ,
                    		 'id as user_id' ,
                    		 'up_first_name',
                    		 'up_last_name',
                             'name'
                            )
                    ->leftjoin('cep_product_users', 'puser_product_id', '=', 'prod_id')
                    ->leftjoin('users', 'puser_user_id', '=', 'id')
                  	->leftjoin('cep_user_plus', 'up_user_id', '=', 'puser_user_id')
                  	->leftjoin('cep_groups', 'cep_groups.group_id', '=', 'puser_group_id')
                    ->where('users.status', '=', 1)
                    ->where('prod_id','=',$id)
                    ->whereIn('group_code',$userTypes)
                    ->get();
		$usersIds=array();
		foreach ($users as $key => $value) {
			$usersIds[$value->user_id]=($value->up_first_name!='' && $value->up_last_name!='')? $value->up_first_name." ".$value->up_last_name: $value->name;
		}
		return $usersIds;
	}

	/**
	* Function getProductCount
	* Get Product Count based on user id
	* @param integer $id user id
	* @return array product count if > 1 else product id
	*/
	public function getUserProductCount($id)
	{
		$products = CepProductUsers::where('puser_user_id',$id)
																->where('puser_status',1)
																->select('puser_product_id')
																->get()
																->toArray();
		return ($products);
	}

	/**
	 * Function getProductDevConfigs
	 *
	 * @param
	 * @return
	 */		
	public function getProductDevConfigs($id)
	{
		$devconf=CepDeveloperConfigurations::where('dconf_product_id',$id)
											 ->where('dconf_status',1)
											 ->get()
											 ->toArray();
		$conf=array();
		foreach ($devconf as $key => $value) {
			$conf[$value['dconf_name']]=$value['dconf_value'];
		}
		return $conf;
	}

	/**
	 * Function getItemConfigs
	 *
	 * @param
	 * @return
	 */		
	public function getItemDevConfigs($id,$type)
	{
		$itemConf=CepItemConfigurations::where('iconf_product_id',$id)
					->where('iconf_item_id',$type)
                    ->where('iconf_status',1)
                    ->get()
                    ->toArray();
        $conf=array();
		foreach ($itemConf as $key => $value) {
			$conf[$value['iconf_name']]=$value['iconf_value'];
		}
		return $conf;

	}

	/**
	 * Function goDirect
	 * function Represents on fly Generation of the Writer file
	 * This function will reroute request to clientController
	 *
	 * @param
	 * @return
	 */		
	public function goDirect($uploadData,$devConfigs,$itemConfigs)
	{
		$redirect='';
		$productId=$uploadData['upload_product_id'];
		$itemId=$uploadData['upload_item_id'];
		if($uploadData->upload_type=='ref' && $itemConfigs['genRoute']!='')
		{
			$redirect=url($itemConfigs['genRoute']."/".$productId."/".$uploadData['upload_id']);
			return array($redirect,'success','');
		}
		else
		{
			return array('back','customError',$this->dictionary->msg_product_configurations_error);
		}
	}

	/**
	 * Function goIndirect
	 * Function Represent Generation type where reference file uploaded at one place and the generations 
	 * done based on folder 
	 * 
	 * @param
	 * @return
	 */		
	public function goIndirect($uploadData,$devConfigs,$itemConfigs)
	{
		$redirect='';
		$productId=$uploadData['upload_product_id'];
		$itemId=$uploadData['upload_item_id'];
		if($uploadData->upload_type=='ref' && $itemConfigs['genRoute']!='')
		{
			$redirect=url($itemConfigs['genRoute']."/".$productId."/".$uploadData['upload_id']);
			return array($redirect,'success','');
		}
		else
		{
			return array('back','customError',$this->dictionary->msg_product_configurations_error);
		}
	}

	/**
	 * Function goHybrid
	 * Function used when generation is depend on 2 or more Different file ( PDN & REF ) and 
	 * the Writer files generated based on Folder 
	 *
	 * @param
	 * @return
	 */		
	public function goHybrid($uploadData,$devConfigs,$itemConfigs)
	{
		//echo "<pre>"; print_r($uploadData);
		//echo "<pre>"; print_r($itemConfigs);
		//echo "<pre>"; print_r($devConfigs);exit;
		$redirect='';
		$productId=$uploadData['upload_product_id'];
		$itemId=$uploadData['upload_item_id'];
		if($uploadData->upload_type=='ref' && $itemConfigs['genRoute']!='')
		{
			$redirect=url($itemConfigs['genRoute']."/".$productId."/".$uploadData['upload_id']);
			return array($redirect,'success','');
		}
		else if($uploadData->upload_type=='pdn' && $itemConfigs['genRoute']!='')
		{
			$redirect=url($itemConfigs['genRoute']."/".$productId."/".$uploadData['upload_id']);
			return array($redirect,'success','');
		}
		else
		{
			return array('back','customError',$this->dictionary->msg_product_configurations_error);
		}
	}

	/**
	 * Function goCustom
	 * Here in this function the generations are totally different and need to specify or Reroute Request 
	 * from here
	 * @param
	 * @return
	 */		
	public function goCustom($uploadData,$devConfigs,$itemConfigs)
	{
		
	}

	/**
	 * Function referncenInfo
	 * Get information about product reference
	 * @param
	 * @return
	 */		
	public function refernceInfo($ref,$id,$configs)
	{	
		//echo $this->user_id;
		//echo "<pre>"; print_r($configs);
		$info=array();
		if(isset($configs['pdc_client_type']) && $this->user_id!='')
		{
			$product=$this->checkProductExists($id);
			//echo "<pre>"; print_r($product);
			switch ($configs['pdc_client_type']) {
				case 'direct':
						 	$info=array(
									'ref'=>$this->checkReferenceRef($ref,$product,$configs),
									'gen'=>$this->checkReferenceGen($ref,$product,$configs),
									'delivery'=>$this->checkReferenceDelivery($ref,$product,$configs)
								   );
					break;
				case 'indirect':
							$info=array(
									'ref'=>$this->checkReferenceRef($ref,$product,$configs),
									'gen'=>$this->checkReferenceGen($ref,$product,$configs),
									'delivery'=>$this->checkReferenceDelivery($ref,$product,$configs)
								   );
					break;
				case 'hybrid':
							$info=array(
									'pdn'=>$this->checkReferencePdn($ref,$product,$configs),
									'ref'=>$this->checkReferenceRef($ref,$product,$configs),
									'gen'=>$this->checkReferenceGen($ref,$product,$configs),
									'delivery'=>$this->checkReferenceDelivery($ref,$product,$configs)
								   );
					break;
				case 'custom':
							$info=array();
					break;
				
				default:
							$info=array();
					break;
			}
			//echo "<pre>"; print_r($info);exit;
			return $info;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Function checkReferencePdn
	 *
	 * @param
	 * @return
	 */		
	public function checkReferencePdn($ref,$product,$configs)
	{
		if($ref=='' || empty($product) || !isset($configs['pdc_client_pdn_model'])){
			return false;
		}else{
			$model = "\App\ClientModels\\".$configs['pdc_client_pdn_model'];
			$referenceField=with(new $model)->reference;
			//echo $referenceField;exit;
			$pdnData=$model::where($referenceField,'=',$ref)->get()->toArray();
			//echo "<pre>"; print_r($pdnData);exit;
			if(!empty($pdnData)){
				return true;
			}else{
				return false;
			}
		}
	}

	/**
	 * Function checkReferenceRef
	 *
	 * @param
	 * @return
	 */		
	public function checkReferenceRef($ref,$product,$configs)
	{
		if($ref=='' || empty($product) || !isset($configs['pdc_client_ref_model'])){
			return false;
		}else{
			$model = "\App\ClientModels\\".$configs['pdc_client_ref_model'];
			$referenceField=with(new $model)->reference;
			//echo $referenceField;exit;
			$refData=$model::where($referenceField,'=',$ref)->get()->toArray();
			//echo "<pre>"; print_r($refData);exit;
			if(!empty($refData)){
				return true;
			}else{
				return false;
			}
		}
	}

	/**
	 * Function checkReferenceWriter
	 *
	 * @param
	 * @return
	 */		
	public function checkReferenceGen($ref,$product,$configs)
	{	//echo "<pre>"; print_r($configs);exit;
		if($ref=='' || empty($product) || !isset($configs['pdc_client_gen_model'])){
			return false;
		}else{
			$model = "\App\ClientModels\\".$configs['pdc_client_gen_model'];
			$referenceField=with(new $model)->reference;
			//echo $referenceField;exit;
			$genData=$model::where($referenceField,'=',$ref)->get()->toArray();
			//echo "<pre>"; print_r($genData);exit;
			if(!empty($genData)){
				return true;
			}else{
				return false;
			}
		}
	}

	/**
	 * Function checkReferenceDelivery
	 *
	 * @param
	 * @return
	 */		
	public function checkReferenceDelivery($ref,$product,$configs)
	{
		if($ref=='' || empty($product) || !isset($configs['pdc_client_gen_model'])){
			return false;
		}else{
			$model = "\App\ClientModels\\".$configs['pdc_client_gen_model'];
			$referenceField=with(new $model)->reference;
			$genStatusField=with(new $model)->genStatus;
			//echo $referenceField;exit;
			$genData=$model::where($referenceField,'=',$ref)->where($genStatusField,'=',1)->get()->toArray();
			if(!empty($genData)){
				return true;
			}else{
				return false;
			}
		}
	}

	/**
	 * Function getFolderGenCount
	 *
	 * @param
	 * @return
	 */		
	public function getFolderGenCount($folder,$product,$configs)
	{	$count=0;
		$countNew=0;
		if($folder=='' || empty($product) || !isset($configs['pdc_client_gen_model'])){
			return false;
		}else{
			$model = "\App\ClientModels\\".$configs['pdc_client_gen_model'];
			$folderField=with(new $model)->folder;
			$genStatusField=with(new $model)->genStatus;
			$downloadField=with(new $model)->download;
			//echo $referenceField;exit;
			$genData=$model::where($folderField,'=',$folder)->get()->toArray();
			$count=count($genData);
			$genDataNew=$model::where($folderField,'=',$folder)->where($genStatusField,'=',0)->where($downloadField,'=','')->get()->toArray();
			$countNew=count($genDataNew);
		}

		return array($count,$countNew,$genData);
	}

	
}
