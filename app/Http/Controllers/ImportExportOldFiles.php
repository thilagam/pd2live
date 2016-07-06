<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

//use Illuminate\Http\Request;

use App\Libraries\ClientKorben;
use App\Libraries\ClientCaroll;
use App\Libraries\ExcelLib;

use Request;

class ImportExportOldFiles extends Controller {
	
	public $refDb;
	public $pdnDb;
	public $writerDb;
	
	public $refKey;
	public $pdnKey;
	public $writerKey;
	
	public $uploadId;
	
	public $clientLibrary;
	


	public function __construct(\Illuminate\Http\Request $request)
	{
		
		$this->middleware('auth');
		
		$this->refDb = '\Caroll\CepClientCarollRefs'; // change models as per clients
		$this->pdnDb =  ''; // change models as per clients
		$this->writerDb =  ''; // change models as per clients 
		
		$this->refKey = 0; // change column key as per clients
		$this->pdnKey = 0; // change column key as per clients
		$this->writerKey = 0; // change column key as per clients
		
		$this->uploadId = "12345"; // Single Id to upload all the Old files
		
		$this->clientLibrary = "";  // Different Client have different Number of Columns and So data will be saved in different manner
		
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		//
		return view('import_export.index');
		
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
		$this->index();
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//
		$this->index();
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
		$this->index();
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
		$this->index();
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
		$this->index();
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
		$this->index();
	}
	
	/**
	 * Update Reference File for client Dev to Db
	 *
	 * @param  int  $id :- product_id
	 * @return Response
	 */
	
	public function refImport($id)
	{
      $excel_lib = new ExcelLib();
			$folder_listing = array();
			$dir = public_path()."/uploads/products/".$id."/ref/old/"; //exit;
			if ($handle = opendir($dir)) {
					while (false !== ($entry = readdir($handle))) {
							if ($entry != "." && $entry != "..") {
									$folder_listing[] = $dir.$entry;
							}
					}
					closedir($handle);
			}

			echo "<div style='height:150px;overflow:scroll;'><pre>"; print_r ($folder_listing); echo "</div>";//exit;

			$input_f = 0;
			if(Request::all()){
				$input_f_a = Request::only('f','op');
				$op = $input_f_a['op'];
				$input_f = $input_f_a['f'];
				$data = array();
				//echo $folder_listing[$input_f]; exit;
        if($input_f < sizeof($folder_listing)){
				$data = $excel_lib->readExcelUniqueValue($folder_listing[$input_f],$this->refKey);
				//echo "<pre>"; print_r ($data); exit;

				echo "<h2>Data of ".intval($input_f+2)." File</h2><p>".$folder_listing[$input_f]."</p><table border='1'><tr>";
				foreach($data as $k=>$dt){
					foreach($dt as $row){
							echo "<td>".$row."</td>";
				  }
					echo "</tr>";
					if($k==3)
						break;
				}
				echo "</table><br />";
			  }

        if($op == 'y'){
					$client = new $this->clientLibrary;
					$options['upload_id'] = $this->uploadId;
					$options['model'] = $this->refDb;
					$client->referenceFileUploads($data,$options,$this->refKey);
				}
				echo "Imported Data of ".intval($input_f+1)." files among ".count($folder_listing)."<br />";
				echo "<a href='?f=".intval($input_f+1)."&op=y'>next</a><br />";
				echo "<a href='?f=".intval($input_f+2)."&op=n'>skip</a>";

			}else{

        $data = $excel_lib->readExcelUniqueValue($folder_listing[0],$this->refKey);
        //echo "<pre>"; print_r ($data); exit;
				echo "<h2>Data of 1st File</h2><p>".$folder_listing[0]."</p><table border='1'><tr>";
				foreach($data as $k=>$dt){
					foreach($dt as $row){
							echo "<td>".$row."</td>";
					}
					echo "</tr>";
					if($k==6)
						break;
				}
				echo "</table><br />";
				echo "<a href='?f=".intval(0)."&op=y'>Begin</a>";
			}
	}
	
	
	/**
	 * Update PDN File for client Dev to Db
	 *
	 * @param  int  $id :- product_id
	 * @return Response
	 */
	
	public function pdnImport($id)
	{
      $excel_lib = new ExcelLib();
			$folder_listing = array();
			$dir = public_path()."/uploads/products/".$id."/pdn/old/"; //exit;
			if ($handle = opendir($dir)) {
					while (false !== ($entry = readdir($handle))) {
							if ($entry != "." && $entry != "..") {
									$folder_listing[] = $dir.$entry;
							}
					}
					closedir($handle);
			}

			echo "<div style='height:150px;overflow:scroll;'><pre>"; print_r ($folder_listing); echo "</div>";//exit;

			$input_f = 0;
			if(Request::all()){
				$input_f_a = Request::only('f','op');
				$op = $input_f_a['op'];
				$input_f = $input_f_a['f'];
				$data = array();
				//echo $folder_listing[$input_f]; exit;
        if($input_f < sizeof($folder_listing)){
				$data = $excel_lib->readExcelUniqueValue($folder_listing[$input_f], $this->pdnKey);
				//echo "<pre>"; print_r ($data); exit;

				echo "<h2>Data of ".intval($input_f+2)." File</h2><p>".$folder_listing[$input_f]."</p><table border='1'><tr>";
				foreach($data as $k=>$dt){
					foreach($dt as $row){
							echo "<td>".$row."</td>";
				  }
					echo "</tr>";
					if($k==3)
						break;
				}
				echo "</table><br />";
			  }

        if($op == 'y'){
					$client = new $this->clientLibrary;
					$options['upload_id'] = $this->uploadId;
					$options['model'] = $this->pdnDb;
					$client->pdnFileUploads($data,$options,$this->pdnKey);
				}
				echo "Imported Data of ".intval($input_f+1)." files among ".count($folder_listing)."<br />";
				echo "<a href='?f=".intval($input_f+1)."&op=y'>next</a><br />";
				echo "<a href='?f=".intval($input_f+2)."&op=n'>skip</a>";

			}else{

        $data = $excel_lib->readExcelUniqueValue($folder_listing[0],$this->pdnKey);
        //echo "<pre>"; print_r ($data); exit;
				echo "<h2>Data of 1st File</h2><p>".$folder_listing[0]."</p><table border='1'><tr>";
				foreach($data as $k=>$dt){
					foreach($dt as $row){
							echo "<td>".$row."</td>";
					}
					echo "</tr>";
					if($k==6)
						break;
				}
				echo "</table><br />";
				echo "<a href='?f=".intval(0)."&op=y'>Begin</a>";
			}
	}
	
	/**
	 * Update Writer File for client Dev to Db
	 *
	 * @param  int  $id :- product_id
	 * @return Response
	 */

	public function writerImport($id)
	{
			$excel_lib = new ExcelLib();
			$folder_listing = array();
			$dir = public_path()."/uploads/products/".$id."/writer/old/"; //exit;
			if ($handle = opendir($dir)) {
					while (false !== ($entry = readdir($handle))) {
							if ($entry != "." && $entry != "..") {
									$folder_listing[] = $dir.$entry;
							}
					}
					closedir($handle);
			}

			echo "<div style='height:150px;overflow:scroll;'><pre>"; print_r ($folder_listing); echo "</div>";//exit;

			$input_f = 0;
			if(Request::all()){
				$input_f_a = Request::only('f','op');
				$op = $input_f_a['op'];
				$input_f = $input_f_a['f'];
				$data = array();
				//echo $folder_listing[$input_f]; exit;
				if($input_f < sizeof($folder_listing)){
				$data = $excel_lib->readExcelUniqueValue($folder_listing[$input_f],$this->refWriter);
				//echo "<pre>"; print_r ($data); exit;

				echo "<h2>Data of ".intval($input_f+2)." File</h2><p>".$folder_listing[$input_f]."</p><table border='1'><tr>";
				foreach($data as $k=>$dt){
					foreach($dt as $row){
							echo "<td>".$row."</td>";
					}
					echo "</tr>";
					if($k==3)
						break;
				}
				echo "</table><br />";
				}

				if($op == 'y'){
					$client = new $this->clientLibrary;
					$options['upload_id'] = $this->uploadId;
					$options['model'] = $this->writerDb;
					$client->writerFileUploads($data,$options,$this->refWriter);
				}
				echo "Imported Data of ".intval($input_f+1)." files among ".count($folder_listing)."<br />";
				echo "<a href='?f=".intval($input_f+1)."&op=y'>next</a><br />";
				echo "<a href='?f=".intval($input_f+2)."&op=n'>skip</a>";

			}else{

				$data = $excel_lib->readExcelUniqueValue($folder_listing[0],$this->refWriter);
				echo "<h2>Data of 1st File</h2><p>".$folder_listing[0]."</p><table border='1'><tr>";
				foreach($data as $k=>$dt){
					foreach($dt as $row){
							echo "<td>".$row."</td>";
					}
					echo "</tr>"; 
					if($k==6)
						break;
				}
				echo "</table><br />";
				echo "<a href='?f=".intval(0)."&op=y'>Begin</a>";
			}
	}
	
	public function exportUniqueReferences(){
		    $option['model']= "\App\ClientModels".$this->refDb;
		    $statusanddata = with(new $option['model'])->getStatusAndData();
		    $data =  $option['model']::where($statusanddata['status'],1)->get()->toArray();
		    $final_data = array();
		    foreach($data as $dt){
			$final_data[] = json_decode($dt[$statusanddata['data']],true);
		    }

			

		    echo "<pre>"; print_r ($final_data); exit;
	}	

}
