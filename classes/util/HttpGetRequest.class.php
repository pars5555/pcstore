<?php

class HttpGetRequest{
	
	private $toFile;
	private $reqHeaders;
	private $reqCookies;
	private $reqParams;
	private $timeout;
	private $isSecure;
	private $userName;
	private $password;

	private $resHeaders;
	private $resCookies;
	private $body;
		
	public function __construct($toFile = false){
		$this->toFile = $toFile;;
		$this->reqHeaders=array();
		$this->reqCookies=array();
	 	$this->timeout=null;
	 	$this->isSecure=false;
	 	$this->resHeaders=null;
	 	$this->resCookies=null;
	 	$this->body=null;
		
	}
	public function setToFile($toFile){
	
		$this->toFile = $toFile;	
	}
	
	public function setHeaders($headers){
		$this->reqHeaders=$headers;
	}
	
	public function setCookies($cookies){
		$this->reqCookies=$cookies;
	}
	
	public function setParams($params){
		$this->reqParams=$params;
	}
	
	public function setTimeout($timeout){
		$this->timeout=$timeout;
	}
	
	public function useSSL($userName, $password){
		$this->isSecure=true;
		$this->userName=$userName;
		$this->password=$password;
	}
	
/*@desc for initiating http get request
*@access public
*@param url of requesting host
*@return true if all is OK, false if thereare errors
*/
	
	public function request($url){
		$curl = curl_init();
		$headers=$this->reqHeaders;

		foreach($this->reqCookies as $key => $value){
			$headers["Cookie"]="$key=$value";
		}
		
		$reqHeaders=array();
		foreach($headers as $key => $value){
			$reqHeaders[]="$key: $value";
		}

		curl_setopt($curl, CURLOPT_HTTPHEADER, $reqHeaders);
		curl_setopt($curl, CURLOPT_HEADER, TRUE);

		// set URL
		if($this->reqParams && count($this->reqParams)>0){
			$url.="?";
			foreach($this->reqParams as $key => $value){
				$url.=urlencode($key)."=".urlencode($value)."&";
			}		
		}
		curl_setopt($curl, CURLOPT_URL, $url);
		
		if($this->isSecure){
			curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			curl_setopt($curl, CURLOPT_USERPWD, $this->userName.":".$this->password);  
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
		}

		if(!$this->toFile){
			// Write result to variable
			
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
			
		}
		else{
			
			//write result to the file
			$fp = fopen($this->toFile, 'w');
			curl_setopt($curl, CURLOPT_HEADER, 0);
			curl_setopt($curl, CURLOPT_FILE, $fp);
			
		}
	
		// Set timeout
		if ($this->timeout){
			curl_setopt($curl, CURLOPT_TIMEOUT, $this->Timeout);
		}

		
		// write content in $doc
		$response = curl_exec($curl);
		

		// close connection
		curl_close($curl);
		
		if($this->toFile){
			fclose($fp);
			return $response;
		}
		
		// Get HTTP Status code from the response
		$status_code = array ();
		preg_match('/\d{3}/', $response, $status_code);
				
		// Check the HTTP Status code
		if($status_code[0]==200){
			$bodyIndex=strpos($response, "\n\r\n");
			
			//  Setting response body
			$this->body=substr($response, $bodyIndex+3);
			$head=substr($response, 0, $bodyIndex-1);
			$headers=array();
			preg_match_all("|\n.+|", $head, $headers);
			
			//  Setting response headers
			for($i=0; $i<count($headers[0]); $i++){
				$headerChuncks=array();
				preg_match('/(.+?):\s*(.+)/', trim($headers[0][$i]), $headerChuncks);
				$this->resHeaders[$headerChuncks[1]]=$headerChuncks[2];
				
			// Setting cookies
				if($headerChuncks[1]=="Set-Cookie"){
					$cookieIndex=strpos($headerChuncks[2], "=");
					$cookieLastIndex=strpos(substr($headerChuncks[2], $cookieIndex+1), ";");
					$this->resCookies[substr($headerChuncks[2], 0, $cookieIndex)]=substr($headerChuncks[2], $cookieIndex+1, $cookieLastIndex);
				}
			}
			return true;
		}		
		
		return false;		
	}
	
	public function getHeaders(){
		return $this->resHeaders;
	}
	
	public function getCookies(){
		return $this->resCookies;
	}
	
	public function getBody(){
		return $this->body;
	}
	
		
}
?>