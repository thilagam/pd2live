<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class CepproductMailingList extends Model {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'cep_product_mailing_list';
	protected $primaryKey = 'ml_id';
	//
	protected $fillable = ['ml_id','ml_product_id','ml_email','ml_status'];
	

}
