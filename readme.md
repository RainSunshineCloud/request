### 这是一个接收请求参数的封装类
#### 说明

1. 支持post get serv(获取$_SERVER) patch delete put 请求
2. 支持json格式的payload请求方式 (通过setDataType 设置数据来源)
3. 自带类型转换（如 int 会自动帮忙转为 int)
4. 支持链式操作，
5. 支持验证其他数据源，通过 data($data)
6. 支持默认值操作
7. 支持闭包验证
8. 支持自定义的类验证
9. 采用异常抛出的方式，返回错误信息
10. 灵活的第三方参数传递
11. 支持单例

#### 基本用法
```

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
	$res = Request::instance()->check('a',['test','int'],'必须是整数2')->check('c','float','必须大于4',['min' => 4])->post(['a','b','c' => '4']);

	//使用方法5
	$res = Request::instance()->check('a',[new Test(),'int'],'必须是整数2')->check('c','float','必须大于4',['min' => 4])->post(['a','b','c' => '4']);

	var_dump($res);
} catch (RequestException $e) {
	echo $e->getMessage();
}
```