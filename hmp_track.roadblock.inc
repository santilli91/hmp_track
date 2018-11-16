<?php
$GLOBALS['hmp_track']['roadblock'] = null;
/** Get ad code **/
function hmp_track_get_adcode($zid,$type,$size = null) {
	$token = isset($GLOBALS['hmp_track']['roadblock'])?$GLOBALS['hmp_track']['roadblock']:hmp_track_get_roadblock();

	/** Return Proper Ad code based on TYPE parameter **/
	if($type == 'html') {
			$ad_code = "<div id='avp_html_ad_$zid'>";

			$ad_code .= "<script type=\"text/javascript\">
		document.write('<scr'+'ipt src=\"https://hmpads.advertserve.com/servlet/view/banner/javascript/zone?zid=$zid&pid=0&custom1=$token&crawler=true&align=center&closeable=false&polite=false&spacing=10&bgcolor=transparent&resolution='+(window.innerWidth||screen.width)+'x'+(window.innerHeight||screen.height)+'&random='+Math.floor(89999999*Math.random()+10000000)+'&millis='+new Date().getTime()+'&referrer='+encodeURIComponent((window!=top&&window.location.ancestorOrigins)?window.location.ancestorOrigins[window.location.ancestorOrigins.length-1]:document.location)+'\" type=\"text/javascript\"></scr'+'ipt>');
		</script>";
			$ad_code .= "</div>";
	}
	else {
		$id = "avp_zid_" . $zid . $type;
		$ad_code = "<div id='$id'><script type='text/javascript'>_avp.push({tagid:'$id',alias:'/',type:'banner',zid:$zid,pid:0,custom1:'$token'});</script></div>";
	}
    return $ad_code;
}

/** Get Roadblock Info **/
function hmp_track_get_roadblock() {
	$token = null;

	if(function_exists('hmp_track_roadblock_pre')) 
		$token = hmp_track_roadblock_pre();

	if(!$token) {
		$aid = hmp_track_get_roadblock_uri();
		if($aid) 
			$token = hmp_track_get_roadblockID($aid);
	}

	if(!$token)
		if(function_exists('hmp_track_roadblock_post'))
			$token = hmp_track_roadblock_post();

	if($token) {
		$GLOBALS['hmp_track']['roadblock'] = $token;
	} else {
		$hmp_track = \Drupal::state()->get('hmp_track');
		$GLOBALS['hmp_track']['roadblock'] = $hmp_track['advertserve_rosID'];
		$token = $hmp_track['advertserve_rosID'];
	}
	return $token;
}

/** Get Roadblock By URI **/
function hmp_track_get_roadblock_uri() {
	$uri = \Drupal::request()->getRequestUri();
	$query = \Drupal::database()->select('hmp_track_ads_uri','u');
	$query->fields('u',['aid']);
	$query->condition('u.uri',"$uri",'=');
	$aid = $query->execute()->fetchField();
	if($aid)
		return $aid;
	return null;
}

/** Get Roadblock ID by AID **/
function hmp_track_get_roadblockID($aid) {
	$ID = \Drupal::database()->select('hmp_track_ads','a')->fields('a',['ad_id'])->condition('a.id',$aid,'=')->execute()->fetchField();
	if($ID)
		return $ID;
	return null;
}