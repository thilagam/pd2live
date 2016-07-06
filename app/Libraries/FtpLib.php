<?php namespace App\Libraries;

use Redirect;

use App\Libraries\PatternLib;
/**
* 
*/

class FtpLib
{
	public function __construct(){
			$this->patternLib= new PatternLib;
	}
	
	
	/* collectImagesFromFtp function 
    * 
    */

  function collectImagesFromFtp($product_id,$folder='all') {
	
		$folder_array = array();
			
		foreach(file(public_path()."/uploads/products/".$product_id."/".$product_id."_ftp.txt") as $key=>$line) {
			if($key > 0){
				$folderImages = explode(":",$line);
				$folderNameOnly = str_replace(public_path()."/uploads/products/".$product_id."/ftp/","",$folderImages[0]);
				if(strcmp($folder, "all") == 0){
					$folder_array[$folderNameOnly] = explode(", ",str_replace("[","",str_replace("]","",$folderImages[1])));
					//$folder_array[$folderNameOnly] = $folderImages[1];
				}
				if(strpos($line, $folder)  !== false){
					$folder_array[$folderNameOnly] = explode(", ",str_replace("[","",str_replace("]","",$folderImages[1])));
					//$folder_array[$folderNameOnly] = $folderImages[1];
					break;
				}
		    }
		}    	
		
		//echo "<pre>"; print_r ($folder_array);
		//exit;
		return $folder_array;
  }

	/**
	* Function getFolderReferences
	*
	* @param
	* @return
	*/		
	public function getFolderReferences($images,$prodConfigs)
	{	
		
		$imgArray=array();
		if(!empty($images) && !empty($prodConfigs))
		{
			foreach ($images as $key => $value) {
				//echo $value;exit;
				$this->patternLib->pattern=$prodConfigs['pdc_client_pattern'];

    			$this->patternLib->subject=trim($value,"'");
    			//echo $this
    			$part=$this->patternLib->stringExtract(1);
				if(!in_array($part, $imgArray)){
					$imgArray[]=$part;
				}		
			}	
		}
		return $imgArray;
	}
	
}
?>
