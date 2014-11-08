<?
class http {
//----------------------------------------------------------------
	var $str_response_text;
	var $arr_headers;
	var $str_content;
//----------------------------------------------------------------
	function http ($arg_str_url="", $arg_arr_request_headers=array()) {
		if ($arg_str_url) {
			$this->request($arg_str_url, $arg_arr_request_headers);
		}
	}
//----------------------------------------------------------------
	function request ($arg_str_url, $arg_arr_request_headers=array()) {
		// reset properties
		$this->str_response_text = "";
		$this->arr_headers = array();
		$this->str_content = "";
		
		$arr_req_headers = array();
		if (is_array($arg_arr_request_headers)) {
			// reformat request headers
			while (list($k, $v) = each($arg_arr_request_headers)) {
				$arr_req_headers[] = $k . ": " . $v;
			}
		}
		
		// make curl request
		$ch = curl_init($arg_str_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_PORT, 80);
		if ($arr_req_headers) curl_setopt($ch, CURLOPT_HTTPHEADER, $arr_req_headers);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		$this->str_response_text = curl_exec($ch);
		curl_close($ch);



		// get headers
		$arr_text = split("\n", $this->str_response_text);
		$bool_headers_begun = false;
		while (list(, $line) = each($arr_text)) {
			$line = trim($line);
			if ($bool_headers_begun == false) {
				if (strpos($line, 'HTTP/1.1 200 OK') !== false) {
					$bool_headers_begun = true;
				}
			} else {	// if the headers began
				if ($line == "") break;
				// these should be headers
				$int_colon_position = strpos($line, ": ");
				$this->arr_headers[substr($line, 0, $int_colon_position)] = substr($line, $int_colon_position + 2);
			}
		}
		
		// get content
		while (list(, $line) = each($arr_text)) {
			$this->str_content .= $line . "\n";
		}
	}
//----------------------------------------------------------------
	function get_all_headers () {
		return $this->arr_headers;
	}
//----------------------------------------------------------------
	function get_header ($arg_str_header_name) {
		return $this->arr_headers[$arg_str_header_name];
	}
//----------------------------------------------------------------
	function get_content () {
		return $this->str_content;
	}
//----------------------------------------------------------------
}
?>
