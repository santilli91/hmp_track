<?php

$GLOBALS['proc_ads'] = array();


/*
 * Determine if proc ad should be aquired, return null if not
 */
function hmp_channel_get_proc_ad($size) {
	
	if(!isset($GLOBALS['proc_ads']['request'])) {
		$GLOBALS['proc_ads'] = hmp_track_proc_request();
		//$GLOBALS['proc_ads']['request'] = TRUE;
	}

	$jcode = null;
	
	foreach($GLOBALS['proc_ads']['code'][$size] as $key=>$ad) {
		$jcode = "<script type='text/javascript'>
	    var _up = [
	        ['h', '$ad->h'],
	        ['w', '$ad->w'],
	        ['t', 'i'],
	        ['cid','" . $GLOBALS['proc_ads']['npi'] . "'],
	        ['asid','" . $ad->asid . "']]; </script>
		<script type='text/javascript' src='https://adjs.pswec.com/showad.js'></script><br>";

		hmp_channel_log_proclivity($key,$ad,'display');

		unset($GLOBALS['proc_ads']['code'][$size][$key]);
		
		return $jcode;
	}	
	
	return $jcode;
}

/*
 * Log the proclivity ad view
 */
function hmp_channel_log_proclivity($key,$ad,$request) {
	$url = 'https://65824442e7f541288413ff2de5d70c51.us-east-1.aws.found.io:443';
	$username = 'elastic';
	$password = 'NHBaYyLRNwQLj5lETf6CTCMM';

	$index = 'proclivity_log';
	$doc_type = 'default';
	$port = 443;

    $json_doc = array(
                "cid" => $GLOBALS['proc_ads']['npi'],
                'npi' => $GLOBALS['hmp_track']['npi'],
                "timestamp" => time(),
                "asid" => $ad->asid,
                "h" => $ad->h,
                't' => $ad->w,
                "page" => $_SERVER['REQUEST_URI'],
                "domain" => $_SERVER['HTTP_HOST'],
                'action' => $request
            );
    $json_doc = json_encode($json_doc);

    $baseUri = $url.'/'.$index.'/'.$doc_type;

    $ci = curl_init();
    curl_setopt($ci, CURLOPT_URL, $baseUri);
    curl_setopt($ci, CURLOPT_PORT, $port);
    curl_setopt($ci, CURLOPT_TIMEOUT, 200);
    curl_setopt($ci, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ci, CURLOPT_FORBID_REUSE, 0);
    curl_setopt($ci, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ci, CURLOPT_POSTFIELDS, $json_doc);
    curl_setopt($ci, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt($ci, CURLOPT_USERPWD, $username . ":" . $password);
    $response = curl_exec($ci);
}

/*
 * Make Proclivity Request
 */
function hmp_track_proc_request() {
	if($GLOBALS['hmp_track']['npi'] != '') 
	//if($GLOBALS['hmp_track']['npi'] =  9999999990)
		//&& $_COOKIE['tar_enc_npi'] != '9999999990' 
	 {	
		hmp_channel_log_proclivity('null',$size,'request');
	 	$hmp_track = \Drupal::state()->get('hmp_track');
		$npi =  hash('sha256',$GLOBALS['hmp_track']['npi']);
		//echo $npi; exit;
		$url = $GLOBALS['hmp_track']['config']['proclivity_url'];
		$cid = 'cid=' . $npi;
		//$cid = 'cid=6aba1fe79d358da49a70a3da6c13b03ae8e51dd5cc7d41864517235bd0a7c1b7';
		$pubid = '&pubid=' . $GLOBALS['hmp_track']['config']['proclivity_puid'];
		$siteid = '&siteid=' . $GLOBALS['hmp_track']['config']['proclivity_sid'];

		$pl = $hmp_track['proclivity_reserve'] == ''?'':'&pl=' . $hmp_track['proclivity_reserve'];
		$smap = '&smap=' . '(728x90,1),(300x250,1)';

		$url .= $cid . $pubid . $siteid . $smap . $pl;
		try {
			$response = \Drupal::httpClient()->get($url, array('headers' => array(
				'Content-Type' => 'text/xml; charset=UTF-8',
		    	'Authorization'=>'dee8295dd175b7e0bd5bb9c604191c22d8c5d3f785e30a50ef55b43b00bb80d6530441826ca79b1cd5d9c857662d9f2059a261a4a5a2593e36eb8fdda915a114'
			)));
		  
		  $data = json_decode($response->getBody());
		  $ads = array();

		  foreach($data->ads as $ad) {
			$size = $ad->w . 'x' . $ad->h;
			$ads['code'][$size][] = $ad;
		  }
		$ads['npi'] = $npi;
		$GLOBALS['proc_ads']['request'] = true;
		  return $ads;
		}
		catch (RequestException $e) {
		    return FALSE;
		}
	} 
	return null;
}