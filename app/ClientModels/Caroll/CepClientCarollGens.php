<?php namespace App\ClientModels\Caroll;

use Illuminate\Database\Eloquent\Model;

class CepClientCarollGens extends Model {

	//Init
	public $table = "cep_client_caroll_gens";
	public $primaryKey = 'caroll_gen_id';

	protected $fillable=[
						  'caroll_gen_id',
						  'caroll_gen_reference',
						  'caroll_gen_sasid',
						  'caroll_gen_file_url',
						  'caroll_gen_ref_folder',
						  'caroll_gen_image_count',
						  'caroll_gen_data',
						  'caroll_gen_download_id',
						  'caroll_gen_delivery_status',
						  'caroll_gen_status'
						];
	protected $guarded = [
						  'caroll_gen_id', 
						  'caroll_gen_reference'
						 ];
    
	public $reference = 'caroll_gen_reference';	
	public $genStatus = 'caroll_gen_delivery_status';	
	public $folder = 'caroll_gen_ref_folder';	
	public $download = 'caroll_gen_download_id';
	public $dataField = 'caroll_gen_data';

	public $timestamps=false;


	/**  
	*  The One to one relationship to Downloads table 
	*
	* 
	*/		
	public function downloads()
	{
		return $this->hasOne('App\CepDownloads','download_id','caroll_gen_download_id');
	}	


}
