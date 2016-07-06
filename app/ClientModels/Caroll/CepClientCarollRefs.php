<?php namespace App\ClientModels\Caroll;

use Illuminate\Database\Eloquent\Model;

class CepClientCarollRefs extends Model {

	//Init
	protected $table = "cep_client_caroll_refs";
	protected $primaryKey = "caroll_ref_id";
	protected $fillable = ['caroll_ref_reference', 'caroll_ref_data', 'caroll_ref_upload_id'];
	protected $guarded = ['caroll_ref_id', 'caroll_ref_status'];
	public $reference = 'caroll_ref_reference';
	public $timestamps = false;

	/**
	* Get the fillable attributes for the model.
	*
	* @return array
	*/
	public function getFillable()
	{
			return $this->fillable;
	}
	
	public function getStatusAndData()
	{
			return array('status'=>$this->guarded[1],'data'=>$this->fillable[1]);
	}

	/**  
	*  The One to one relationship to Uploads table 
	*
	* 
	*/		
	public function uploads()
	{
		return $this->hasOne('App\CepUploads','upload_id','cep_ref_upload_id');
	}	

}
