<?php
/*
 * http://ge.tt 类，作者 mail@YISI.us
 *
 * 官方文档https://open.ge.tt/1/doc/
 */
class GettApi{	
	public $url='https://open.ge.tt';

/*
 *  Authentication ----------------------------
 */
	//获取accesstoken
	public function GetAccesstoken(){  //填入你的配置，到这里创建app：http://ge.tt/developers/create
		$fields = '{
		"apikey":"<YOUR APIKEY>",
		"email":"<YOUR@MAI.LE>",
		"password":"<YOURPASSWORD>"
		}';
		$url = $this->url.'/1/users/login';
		$data = $this->POST($url,$fields);
		$data = json_decode($data);//返回的data里同时带有refreshtoken，可以直接用它获取accesstoken，但是有时间限制，详见官方文档
		return  $data->accesstoken;	
	}		
	//获取用户基本信息
	public function GetInfo($accesstoken){
		$url = $this->url.'/1/users/me?accesstoken='.$accesstoken;
		$data = file_get_contents($url);
		$data = json_decode($data);
		return $data;
	}
	
/*
 *  Shares ----------------------------
 */
 
	//获取用户的分享
	public function GetShares($accesstoken){
		$url = $this->url.'/1/shares?accesstoken='.$accesstoken;
		$data = file_get_contents($url);
		$data = json_decode($data);
		return $data;
	}
	//创建一个分享
	public function CreateShare($accesstoken,$title){
		$fields['title'] = $title;
		$fields = json_encode($fields);
		$url = $this->url.'/1/shares/create?accesstoken='.$accesstoken;
		$data = $this->POST($url,$fields);
		$data = json_decode($data);
		return $data;
	}
	//获取某个分享内的所有文件信息,注意sharename不是title，而是类似这种随机字符串4g4Jksk
	public function GetShareInfo($sharename){
		$url = $this->url.'/1/shares/'.$sharename;
		$data = file_get_contents($url);
		$data = json_decode($data);
		return $data;
	}
	//更新一个share。官方说：目前，你只能更新title  2013/7/4
	public function UpdateShare($accesstoken,$sharename,$newtitle){
		$fields['title'] = $newtitle;
		$fields = json_encode($fields);
		$url = $this->url.'/1/shares/'.$sharename.'/update?accesstoken='.$accesstoken;
		$data = $this->POST($url,$fields);
		$data = json_decode($data);
		return $data;
	}
	//删除一个share和其中所有的文件
	public function DeleteShare($accesstoken,$sharename){
		$url = $this->url.'/1/shares/'.$sharename.'/destroy?accesstoken='.$accesstoken;
		$data = $this->POST($url,$fields='');
		$data = json_decode($data);
		return $data;
	}
	
/*
 *  Files ----------------------------
 */	
	//创建一个文件到指定的share里。注意filename最好带上后缀，如myfile.txt
	public function CreateFile($accesstoken,$sharename,$filename){
		$fields['filename'] = $filename;
		$fields = json_encode($fields);
		$url = $this->url.'/1/files/'.$sharename.'/create?accesstoken='.$accesstoken;
		$data = $this->POST($url,$fields);
		$data = json_decode($data);
		return $data;//注意保存$data里的posturl，用来上传文件
	}
	//获取某个share里特定文件的状态  $fileid = 1,2,3 ...
	public function GetFileInfo($sharename,$fileid){
		$url = $this->url.'/1/files/'.$sharename.'/'.$fileid;
		$data = file_get_contents($url);
		$data = json_decode($data);
		return $data;
	}
	//获取某个share里特定文件的posturl和puturl  $fileid = 1,2,3 ...
	public function GetFileUploadUrl($accesstoken,$sharename,$fileid){
		$url = $this->url.'/1/files/'.$sharename.'/'.$fileid.'/upload?accesstoken='.$accesstoken;
		$data = file_get_contents($url);
		$data = json_decode($data);
		return $data;
	}
	//彻底删除一个文件
	public function DeleteFile($accesstoken,$sharename,$fileid){
		$url = $this->url.'/1/files/'.$sharename.'/'.$fileid.'/destroy?accesstoken='.$accesstoken;
		$data = $this->POST($url,$fields='');
		$data = json_decode($data);
		return $data;//注意保存$data里的posturl，用来上传文件
	}
	//重定向到某个文件的下载链接（Will redirect to the binary content of the file）
	public function GetFileBlob($sharename,$fileid){
		$url = $this->url.'/1/files/'.$sharename.'/'.$fileid.'/blob';
		$data = file_get_contents($url);
		$data = json_decode($data);
		return $data;
	}
	//重定向到某张图片的缩略图下载链接（Will redirect to a thumbnail of the binary file. Currently only available for images.）
	public function GetFileThumb($sharename,$fileid){
		$url = $this->url.'/1/files/'.$sharename.'/'.$fileid.'/blob/thumb';
		$data = file_get_contents($url);
		$data = json_decode($data);
		return $data;
	}
	
	
/*	
 *  因为上传文件用得比较多，专门封装一个方法
 */
	public function UPLOAD($filename,$file,$sharename,$accesstoken){
	$data = $this->CreateFile($accesstoken,$sharename,$filename);
	$posturl = $data->upload->posturl;
	$args['file'] = '@' . $file;
	$result = $this->POST($posturl,$args);
	return $result;//返回'computer says yes'，说明上传成功
	}
	

	
	
	
	public function POST($url,$fields){
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS,$fields);
	$data = curl_exec($ch);//运行curl
	curl_close($ch);
	return $data;
	}
	
	
}
