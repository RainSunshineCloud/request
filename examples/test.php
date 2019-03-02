<?php 
include '../../../autoload.php';
use RainSunshineCloud\Request;
use RainSunshineCloud\RequestException;

class Test {
	public static function int($val,$err_message,$other_params)
	{
		return 2343;
	}

	public function float($val,$err_message,$other_params)
	{
		return 23423;
	}
}

try {
	$_POST['a'] = 2;
	//使用方法1
	$request = new Request();
	$res = $request->check('a','int','必须是整数1')->post('a');
	
	//使用方法2
	$res = Request::instance()->check('a','int','必须是整数2')->post(['a','b','c' => '1']);

	//使用方法3
	$res = Request::instance()->check('a','int','必须是整数2')->check('c','float','必须大于4',['min' => 4])->post(['a','b','c' => '4']);
	//使用方法4
	$res = Request::instance()->check('a',['Test','int'],'必须是整数2')->check('c','float','必须大于4',['min' => 4])->post(['a','b','c' => '4']);

	//使用方法5
	$res = Request::instance()->check('a',[new Test(),'float'],'必须是整数2')->check('c','float','必须大于4',['min' => 4])->post(['a','b','c' => '4']);

	//使用方法5
	
	$res = Request::instance()->check('a',function (){return 324;},'必须是整数2')->check('c','float','必须大于4',['min' => 4])->post(['a','b','c' => '4']);

	var_dump($res);
} catch (RequestException $e) {
	echo $e->getMessage();
}