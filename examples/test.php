<?php 
include '../../../../autoload.php';
use RainSunshineCloud\Request;
use RainSunshineCloud\RequestException;

try {
	$_POST['a'] = 2;
	//使用方法1
	$request = new Request();
	$res = $request->check('a','int','必须是整数1')->post('a');
	
	//使用方法2
	$res = Request::instance()->check('a','int','必须是整数2')->post(['a','b','c' => '1']);

	//使用方法3
	$res = Request::instance()->check('a','int','必须是整数2')->check('c','float','必须大于4',['min' => 4])->post(['a','b','c' => '4']);
	var_dump($res);
} catch (RequestException $e) {
	echo $e->getMessage();
}
