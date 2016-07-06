<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

//use Illuminate\Http\Request;
use Input;
use Response;
use View;
use App\User;
use App\CepUserPlus;
use Crypt;
use DB;
use File;
use Cookie;
use Config;
use FTP;
use Mail;
use Storage;

use App\Libraries\FileManager;
use App\Libraries\EMailer;

use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Cell;

use App\Services\UploadsManager;

use App\Libraries\ProductHelper;
use App\Libraries\ClientKorben;
use App\Libraries\ClientCaroll;
use App\Libraries\ExcelLib;

use Request;


class ErrandController extends Controller {

	protected $manager;
	public $permit;
	public $configs;
	public $excelobj;
	// /public $FileManager;

	public function __construct(\Illuminate\Http\Request $request)
	{
		$this->manager = new UploadsManager;
		$this->permit=$request->attributes->get('permit');
    	$this->configs=$request->attributes->get('configs');
    	//$this->FileManager = new FileManager($manager);
	}

	/**
	 * Function newProductLine
	 *
	 * @param
	 * @return
	 */
	public function newProductLine($id)
	{
		//$input = Input::all();
		/* Get Bo Users to Select incharge  */
        $bousers=User::with('userPlus')
						->where('group_id','=','243211')
						->get();
		$bouserData=array('0'=>'');
		foreach ($bousers as $key => $value) {
			$bouserData[$value->id]=($value['user_plus']['up_first_name']!='') ? ucfirst($value['user_plus']['up_first_name'])." ".ucfirst($value['userPlus']['up_last_name']): $value['name'] ;
		}
		return view('snippets.addProductLine',compact('id','bouserData'))->render();

	}

	/**
	 * Function addMailingListHelper
	 *
	 * @param
	 * @return
	 */
	public function addMailingListHelper($id)
	{
		return view('snippets.addMailingList',compact('id'))->render();
	}

	/**
	 * Function userImageUpload
	 *
	 * @param
	 * @return
	 */
	public function userImageUpload()
	{
		$file=Input::file('file');
		$name="usr_".uniqid();
		$options =array (
	 				'name'=>$name,
					'url'=> 'images/',
				 );
		$FileManager=new FileManager();
		$upload=$FileManager->simpleUplaod($file,$options);
		echo $upload;
	}


	/**
	 * Function test
	 *
	 * @param
	 * @return
	 */
	public function test()
	{

		$head=array(	'url',
				'DOUBLON',
				'DESCRIPTIF COURT',
				'Desc long',
				'Min. signs',
				'Référence',
				'Couleur Référence',
				'couleur',
				'Composition doublure',
				'Bénéfices et ressenti produit',
				'ganse',
				'Longueur produits(longueur pantalons, jupes, robes, manteaux et doudounes, vestes et blousons, gilet longs) ou dimensions sacs, hauteur talon chaussure',
				'COLORIS',
				'REFCOL',
				'DEPARTEMENT 2',
				'COMPOSITION',
				'MATIERE',
				'FAMILLE',
				'SOUS FAMILLE',
				'LONGUEUR ',
				'FORME FORME',	
				'MANCHES',
				'COL',
				'SAS ID'
			);
		echo json_encode($head);

		echo json_encode(array('URL','Reference'));


		//return view("test/index");
		//$em = new EMailer;

		//echo $em->getTemplateCode(20);


		/*Config::set('ftp.connections.newftp', array(
           'host'   => 't052.x1.fr',
           'username' => 'morgan_ep',
           'password'   => 'n$2RLR',
           'passive'   => false,
		));

		//echo config;

		$listing = FTP::connection("newftp")->getDirListing();
		//print_r ($listing);

		$listing1 = FTP::connection("newftp")->getDirListingDetailed();
		echo "<pre>"; print_r ($listing1);

		foreach($listing1 as $key=>$l1)
		    echo $key; */

		/* QRY TO GET PRODUCT USERS  */
		// $id=1515082812194318;
		// $result=DB::table('cep_product_users1')
		// 	 ->leftjoin('cep_groups', 'cep_groups.group_id', '=', 'cep_product_users.puser_group_id')
		// 	 ->leftjoin('cep_user_plus', 'cep_user_plus.up_user_id', '=', 'cep_product_users.puser_user_id')
		// 	 ->leftjoin('cep_products', 'cep_products.prod_id', '=', 'cep_product_users.puser_product_id')
	 //    ->whereIn('puser_product_id', function($query) use ($id)
	 //    {
	 //        $query->select('puser_product_id')
	 //              ->from('cep_product_users')
	 //              ->whereRaw('cep_product_users.puser_user_id = '.$id)
	 //              ->groupBy('puser_product_id');
	 //    })
	 //    ->groupBy('puser_product_id')
	 //    ->get();
	 //    echo "<pre>"; print_r($result);
		// /$folder = $request->get($this->configs->uploads_path);
		// echo $this->configs->uploads_path;
		// $folder='test';
		// //$result = $this->manager->createDirectory($folder);
  //   	$data = $this->manager->folderInfo($this->configs->uploads_path."/test");
  //   	echo "<pre>"; print_r($data);

		//$cookie = Cookie::forever('DEFAULT_LANGUAGE', 'en');
		//echo public_path().$this->configs->uploads_path."/".$this->configs->uploads_products_path."lahalle5";
		//$this->manager->createDirectory($this->configs->uploads_products_path."lahalle5");
		//chmod(public_path().$this->configs->uploads_path."/".$this->configs->uploads_products_path."lahalle5", 0777);


		//$allUsers = User::paginate(5);

		//echo $allUsers;

		//return view('test',compact('data','allUsers'));

		//echo $value = Crypt::encrypt("products/454329/pdn/5677dcd6a6620.xls");
		//echo "\n";
		//echo Crypt::decrypt($value);
		//\App:: make ('Excel');


       /*$excel = new PHPExcel;
       $file =public_path()."/uploads/test/Compo_MC_AH15_20151013.xlsx";

       $objPHPExcel = PHPExcel_IOFactory::load($file);
       $cell_collection = $objPHPExcel->getActiveSheet()->getCellCollection();
       foreach ($cell_collection as $cell) {
         $column = $objPHPExcel->getActiveSheet()->getCell($cell)->getColumn();
         $colNumber = PHPExcel_Cell::columnIndexFromString($column);
         $row = $objPHPExcel->getActiveSheet()->getCell($cell)->getRow();
         $data_value = $objPHPExcel->getActiveSheet()->getCell($cell)->getValue();
         $arr_data[$row][$column] = $data_value;
	 		}
     print_r ($arr_data);*/

		/*$excel_lib = new ExcelLib();
		$data = $excel_lib->readExcelUniqueValue(public_path()."/uploads/test/Compo_MC_AH15_20151013.xlsx");
		//echo "<pre>"; print_r ($data); exit;
		$client = new ClientKorben;

		$options['upload_id'] = 12345;
		$options['model'] = "CepClientKorbenCchRefs";
		echo $client->referenceFileUploads($data,$options);*/

	}

	/**
	 * Function testPost
	 *
	 * @param
	 * @return
	 */
	public function testPost()
	{
		echo "<pre>"; print_r($_POST);
		echo "<pre>"; print_r($_FILES);
		// $file=Input::file('file');
		// $name=uniqid();
		// $options =array (
	 // 				'name'=>$name,
		// 			'description'=> 'some info',
		// 			'url'=> 'test/',
		// 			'client'=> '',
		// 			'product'=> '',
		// 			'item'=> ''
		// 		 );
		// $FileManager=new FileManager();
		// $upload=$FileManager->upload($file,$options);
		// if($upload){
		// 	echo "SUCCESS";
		// }else{
		// 	echo "FAIL";
		// }
	}

	
}
