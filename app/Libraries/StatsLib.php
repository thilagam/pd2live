<?php namespace App\Libraries;

use Redirect;

use App\CepProductFtp;
use App\CepDownloads;
use App\CepUploads;

use App\Libraries\FtpLib;

class StatsLib
{

	public function __construct(){
		$this->ftpLib = new FtpLib();
	}

	/*
		To get the count of images 

	*/
	function getImage($id){
		$imgCount = array();
        $ftp_check = CepProductFtp::where('ftp_product_id',$id)->get();
        $ftp_check = $ftp_check->toArray();
        if(!empty($ftp_check))
        {
            $imgs=$this->ftpLib->collectImagesFromFtp($id);
            foreach($imgs  as $key=>$value){
                foreach($value as $k=>$v){
                    $imgCount[] = $v;
                }
            }
        }
        return count($imgCount);
	}


	function getUpload($id)
    {
        return CepUploads::where('upload_product_id',$id)->count();
    }

    function getFileGen($id)
    {
        return CepDownloads::where('download_product_id',$id)->count();
    }
}

?>