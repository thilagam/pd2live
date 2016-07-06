<?php namespace App\ClientModels\Caroll;

use Illuminate\Database\Eloquent\Model;

class CepClientCarollPdns extends Model {

	//Init
	protected $table = "cep_client_caroll_pdns";
	protected $primaryKey = "caroll_pdn_id";
	protected $fillable = ['caroll_pdn_reference', 'caroll_pdn_data', 'caroll_pdn_upload_id'];
    protected $guarded = ['caroll_pdn_id', 'caroll_pdn_status'];
    public $reference = 'caroll_pdn_reference';
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

	/**  
	*  The One to one relationship to Uploads table 
	*
	* 
	*/		
	public function uploads()
	{
		return $this->hasOne('App\CepUploads','upload_id','cep_pdn_upload_id');
	}

}

  
