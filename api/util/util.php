<?php

use Api\Models\Tbi;
use Api\Models\Tbo;
use Api\Models\Tba;
use Api\Models\Tbs;

function msgHandler($jsonString) {
    $decodedData = json_decode(str_replace(["\r", "\n"], '', $jsonString), true);

    $formattedData = '';
    foreach ($decodedData as $key => $value) {
        $formattedData .= "['$key'] => $value\n";
    }

    return $formattedData;
}
// ---------------------------------------------------------------------------------------

function getColor($type, $value) {
	$color = 'orange';
	
	if ($type == 'bat') { //배터리
		if ($value >= 70) $color = 'green';
		else if ($value <= 40) $color = 'red';
	}
	else if ($type == 'humi') { //수분
		if ($value >= 15 && $value <= 40) $color = 'green';
		else if ($value <= 7) $color = 'red';
	}
	else {
		return '';
	}
	
	return $color;
}


function getOrgList() {
	$tmp = Tbo::orderBy('id', 'ASC');
	if (@auth()->user()->org > 0) $tmp->where('id', auth()->user()->org);
	return optional($tmp->get())->toArray();
}

function getAreaList() {
	$data = [];
	$tmp1 = Tba::where('org', '!=', '')->orderBy('org', 'ASC')->orderBy('id', 'ASC');
	if (@auth()->user()->org > 0) $tmp1->where('org', auth()->user()->org);
	$tmp2 = optional($tmp1->get())->toArray();
	
	foreach ($tmp2 as $v) {
		$data[$v['org']][] = $v;
		unset($v);
	}

	return $data;
}

function getIrrList() {
	$data = [];
	
//	$tmp1 = Irrigation::where('org', '!=', '')->where('area', '!=', '')->orderBy('id', 'ASC');
//	if (@auth()->user()->org > 0) $tmp1->where('org', auth()->user()->org);

	$tmp1 = Tbi::orderBy('name', 'ASC');
	$tmp2 = optional($tmp1->get())->toArray();
	
	foreach ($tmp2 as $v) {
	//	$data[$v['org']][$v['area']][] = $v;
		$data[] = $v;
		unset($v);
	}

	return $data;
}

function getDeviceList() {
	$data = [];
	$tmp1 = Tbs::select('imei','device','org','area')->orderBy('device', 'ASC');
	if (@auth()->user()->org > 0) $tmp1->where('org', auth()->user()->org);
	$tmp2 = optional($tmp1->get())->toArray();
	
	foreach ($tmp2 as $v) {
		$data['all'][] = $v;
		
		if ($v['org'] && $v['area']) $data[$v['org']][$v['area']][] = $v;
		unset($v);
	}
	
	return $data;
}

function getDeviceList2() { //관수 등록된 계측기만 추출.
	$data = [];
	$tmp1 = Tbs::select('imei','device','org','area')->where('irr', '>', '0')->where('irr_channel', '>', '0')->orderBy('device', 'ASC');
	if (@auth()->user()->org > 0) $tmp1->where('org', auth()->user()->org);
	$tmp2 = optional($tmp1->get())->toArray();
	
	foreach ($tmp2 as $v) {
		$data['all'][] = $v;
		
		if ($v['org'] && $v['area']) $data[$v['org']][$v['area']][] = $v;
		unset($v);
	}
	
	return $data;
}


function fileUpload($file) {
	$now = date('YmdHis');
	$random = Str::random(20);
	$allowedExtension = ['jpg','jpeg','png','gif','webp','xls','xlsx','csv','mp4','avi','mkv'];
	$extension = strtolower($file->extension());
	
	if (in_array($extension, $allowedExtension)) {
		preg_match_all('/[ㄱ-ㅎ가-힣0-9a-zA-Z-_\.]+/', $file->getClientOriginalName(), $match);
		
		if (isset($match[0])) $fileName = implode('', array_map('implode', $match));
		else $fileName = $file->getClientOriginalName();
		
		$fileType = $file->getClientMimeType();
		$fileSize = $file->getSize();
		$filePath = str_replace("\\", "/", str_replace(public_path(), '', $file->move(public_path('file'), $now.'_'.$random.'.'.$extension)));
		chmod(public_path().$filePath, 0666);
		
		/*
		//이미지가 1000px 보다 크면 리사이즈
		$filePath2 = public_path().$filePath;
		if (in_array($extension, ['jpg','jpeg','png','gif','webp']) && file_exists($filePath2)) {
			$getSize = get_image_size($filePath2, 1000);
			resize_image($filePath2, $filePath2, $getSize);
		}
		*/
		
		if ($filePath) {
			$f = new App\Models\File([
				'file_name' => $fileName,
				'file_path' => $filePath,
				'file_type' => $fileType,
				'file_size' => $fileSize,
				'ip' => request()->ip(),
			]);
			$f->save();
			
			return $f;
		}
	}
	
	return false;
}

function funcDeviceTimeFormat($date) {
	if (!empty($date) && strlen($date) >= 10) { /* 23/08/10,06:00:37+36 형태의 날짜 형식 변경 */
		$y = substr($date,0,2);
		$m = substr($date,3,2);
		$d = substr($date,6,2);
		$hi = substr($date,9,5);
		return '20'.$y.'-'.$m.'-'.$d.' '.$hi;
	} else return '-';
}

function funcPhoneAutoHyphen($phone) {
	$phone = preg_replace('/[^0-9]/', '', $phone);
	$phone = preg_replace('/(^02|^0505|^0507|^1[0-9]{3}|^0[0-9]{2})([0-9]+)?([0-9]{4})$/', '$1-$2-$3', $phone);
	return str_replace('--', '-', $phone);
}

function funcTimestamp() {
	list($microtime, $timestamp) = explode(' ', microtime());
	return $timestamp.substr($microtime, 2, 3);
}

function funcIrrValve($ip, $channel, $type) { //관수 채널 별 코드
	if ($ip == '0.0.0.0') return true; //테스트 데이터 경우 바로 true 반환.
	
	$code = '';
	$port = 50000;
	$flag = false;
	
	if ($type == 'o' || $type == 'open') {
		if ($channel == 1) $code = '448';
		if ($channel == 2) $code = '64C';
		if ($channel == 3) $code = 'A54';
		if ($channel == 4) $code = '264';
		if ($channel == 5) $code = '284';
		if ($channel == 6) $code = '2C4';
		if ($channel == 7) $code = '244';
		if ($channel == 8) $code = '244';
		if ($channel == 9) $code = '448';
		if ($channel == 10) $code = '64C';
		if ($channel == 11) $code = 'A54';
		if ($channel == 12) $code = '264';
		if ($channel == 13) $code = '284';
		if ($channel == 14) $code = '2C4';
		if ($channel == 15) $code = '244';
		if ($channel == 16) $code = '244';
	}
	else if ($type == 'c' || $type == 'close') {
		if ($channel == 1) $code = '346';
		if ($channel == 2) $code = '448';
		if ($channel == 3) $code = '64C';
		if ($channel == 4) $code = 'A54';
		if ($channel == 5) $code = '264';
		if ($channel == 6) $code = '284';
		if ($channel == 7) $code = '2C4';
		if ($channel == 8) $code = '244';
		if ($channel == 9) $code = '346';
		if ($channel == 10) $code = '448';
		if ($channel == 11) $code = '64C';
		if ($channel == 12) $code = 'A54';
		if ($channel == 13) $code = '264';
		if ($channel == 14) $code = '284';
		if ($channel == 15) $code = '2C4';
		if ($channel == 16) $code = '244';
	}
	
	if (!empty($code)) {
		sleep(1);
		$fp = fopen('/home/tbs/tb_scheduler.log', 'a+');
		
		$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

		//

		if ($socket === false) {
			$error_msg = 'Connection 1 failed. ('.socket_strerror(socket_last_error()).')';
			logWrite($fp, $error_msg);
			socket_close($socket);
			fclose($fp);
			return $error_msg;
		}

		socket_set_option($socket, SOL_SOCKET, SO_RCVTIMEO, array('sec' => 10, 'usec' => 0));
		socket_set_option($socket, SOL_SOCKET, SO_SNDTIMEO, array('sec' => 10, 'usec' => 0));

		$result = @socket_connect($socket, $ip, $port);

		if ($result === false) {
			$error_msg = 'Connection 2 failed. ('.socket_strerror(socket_last_error($socket)).')';
			logWrite($fp, $error_msg);
			socket_close($socket);
			fclose($fp);
			return $error_msg;
		}

		socket_write($socket, $code, strlen($code));
		$input = socket_read($socket, 1024);
		logWrite($fp, 'Result : '.$input);
	//	var_dump($input);
	
	//	if (strlen($input) >= 3) $flag = true;
		if (strpos(strtolower($input), 'ok') !== false) $flag = true;

		socket_close($socket);
		fclose($fp);
	}
	
	return $flag;
}


function logWrite($fp, $msg) {
	fwrite($fp, date("Y-m-d H:i:s")."\t".$msg."\r\n");
}


function funcSendSms($to, $content) {
	$timestamp = funcTimestamp();
	
	$uri = '/sms/v2/services/ncp:sms:kr:/messages';
	$accessKey = '';
	$secretKey = '';
	
	$header = [
		'Content-Type: application/json',
		'x-ncp-apigw-timestamp: '.$timestamp,
		'x-ncp-iam-access-key: '.$accessKey,
		'x-ncp-apigw-signature-v2: '.base64_encode(hash_hmac('sha256', "POST {$uri}\n{$timestamp}\n{$accessKey}", $secretKey, true)),
	];
	$data = [
		'type' => 'SMS',
		'contentType' => 'COMM',
		'from' => '025962200',
		'content' => $content,
		'messages' => [ ['to' => preg_replace('/[^0-9]/', '', $to)] ],
	];

	$nowDate = date('Y-m-d');
	$nowTime = date('H');

	if ($nowTime <= '08') $data['reserveTime'] = $nowDate.' 09:10';

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, 'https://sens.apigw.ntruss.com'.$uri);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$response = curl_exec($ch);
	curl_close($ch);
	return json_decode($response, true);
}


function funcSendLms($to, $content) {
	$timestamp = funcTimestamp();
	
	$uri = '/sms/v2/services/ncp:sms:kr:/messages';
	$accessKey = '';
	$secretKey = '';
	
	$header = [
		'Content-Type: application/json',
		'x-ncp-apigw-timestamp: '.$timestamp,
		'x-ncp-iam-access-key: '.$accessKey,
		'x-ncp-apigw-signature-v2: '.base64_encode(hash_hmac('sha256', "POST {$uri}\n{$timestamp}\n{$accessKey}", $secretKey, true)),
	];
	$data = [
		'type' => 'LMS',
		'contentType' => 'COMM',
		'from' => '025962200',
		'subject' => '알림',
		'content' => $content,
		'messages' => [ ['to' => preg_replace('/[^0-9]/', '', $to)] ],
	];

	$nowDate = date('Y-m-d');
	$nowTime = date('H');

	if ($nowTime <= '08') $data['reserveTime'] = $nowDate.' 09:10';

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, 'https://sens.apigw.ntruss.com'.$uri);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$response = curl_exec($ch);
	curl_close($ch);
	return json_decode($response, true);
}

