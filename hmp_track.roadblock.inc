<?php
$GLOBALS['hmp_track']['roadblock'] = null;
/** Get ad code **/
function hmp_track_get_adcode($zid,$size) {
	$ID = isset($GLOBALS['hmp_track']['roadblock'])?$GLOBALS['hmp_track']['roadblock']:hmp_track_get_raodblock();
}

/** Get Roadblock Info **/
function hmp_track_get_raodblock() {
	$aid = null;
	$aid = hmp_track_get_roadblock_uri();
	if($aid) {
		$GLOBALS['hmp_track']['roadblock'] = hmp_track_get_roadblockID($aid);
	} else {
		$hmp_track = \Drupal::state()->get('hmp_track');
		$GLOBALS['hmp_track']['roadblock'] = $hmp_track['advertserve_rosID'];
		$aid = $hmp_track['advertserve_rosID'];
	}
	return $aid;
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
	$ID = \Drupal::database()->select('hmp_track_ads','a')->fields('a',['id'])->condition('a.ad_id',$aid,'=')->execute()->fetchField();
	if($ID)
		return $ID;
	return null;
}