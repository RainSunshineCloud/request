<?php 
namespace RainSunshineCloud;

class Request
{
	protected $validate_func = [];
	protected $validate = null;
	protected static $data_type = 1;
	protected static $self = null;

	/**
	 * 获取get传参
	 * @param  array  $params [description]
	 * @return [type]         [description]
	 */
	public function get( $params = [])
	{
		$data = $this->getData($_GET,$params);
		return $this->validate($data);
	}

	/**
	 * 自己传入data验证
	 * @param  array  $data [description]
	 * @return [type]       [description]
	 */
	public function data(array $data,$params = [])
	{
		$data = $this->getData($data,$params);
		return $this->validate($data);
	}

	/**
	 * 获取put传参
	 * @param  array  $params [description]
	 * @return [type]         [description]
	 */
	public function put( $params = [] )
	{
		$method = $this->serv('REQUEST_METHOD');
		$data = [];
		if ($method['REQUEST_METHOD'] == 'PUT') {
			switch (self::$data_type) {
				case 1://原生
					parse_str(file_get_contents('php://input'), $data);
					break;
				case 2://json
					$data = $this->getByJson();
					break;
			}
		}
		$data = $this->getData($data,$params);
		return $this->validate($data);
	}

	/**
	 * 获取delete参数
	 * @param  array  $params [description]
	 * @return [type]         [description]
	 */
	public function delete( $params = [] )
	{
		$method = $this->serv('REQUEST_METHOD');
		$data = [];
		if ($method['REQUEST_METHOD'] == 'DELETE') {
			switch (self::$data_type) {
				case 1://原生
					parse_str(file_get_contents('php://input'), $data);
					break;
				case 2://json
					$data = $this->getByJson();
					break;
			}
		}
		$data = $this->getData($data,$params);
		return $this->validate($data);
	}

	/**
	 * 获取delete参数
	 * @param  array  $params [description]
	 * @return [type]         [description]
	 */
	public function patch( $params = [] )
	{
		$method = $this->serv('REQUEST_METHOD');
		$data = [];

		if ($method['REQUEST_METHOD'] == 'PATCH') {

			switch (self::$data_type) {
				case 1://原生
					parse_str(file_get_contents('php://input'), $data);
					break;
				case 2://json
					$data = $this->getByJson();
					break;
			}
		}

		$data = $this->getData($data,$params);
		return $this->validate($data);
	}

	/**
	 * 获取请求头信息
	 * @return [type] [description]
	 */
	public function serv($params = [])
	{
		$data = $this->getData($_SERVER,$params);
		return $this->validate($data);
	}

	/**
	 * post 请求
	 * @param  array  $params [description]
	 * @return [type]         [description]
	 */
	public function post( $params = [])
	{
		switch (self::$data_type) {
			case 1: //原生
				$data = $_POST;
				break;
			case 2: //json
				$data = $this->getByJson();
				break;
		}


		$data = $this->getData($data,$params);
		return $this->validate($data);
	}

	/**
	 * 检查
	 * @param  string $params_name  [参数]
	 * @param  [type] $check_method [检查方法]
	 * @param  string $err_message  [错误信息]
	 * @param  array  $other_params [其他值]
	 * @return 
	 */
	public function check(string $params_name,$check_method ,string $err_message = 'invalid params', array $other_params = [])
	{
		$type = $this->checkMethodType($check_method);

		if (!isset($this->validate_func[$params_name])) {
			$this->validate_func[$params_name] = [];
		} 

		array_push($this->validate_func[$params_name],[
			'check_method' 	=> $check_method,
			'error_message' => $err_message,
			'type'			=> $type,
			'other_params' 	=> $other_params,
		]);

		return $this;
	}


	/**
	 * 通过json获取值
	 * @return [type] [description]
	 */
	protected function getByJson()
	{
		$data = file_get_contents('php://input');
		$data = json_decode($data,true);
		if ($data === false) {
			throw new RequestException('类型错误');
		}
		return $data;
	}

	/**
	 * 验证器
	 * @param  [type] $data [description]
	 * @return [type]       [description]
	 */
	protected function validate(&$data)
	{
		foreach ($this->validate_func as $params_name => $methods_arr) {
			foreach ($methods_arr as $methods) {
				if (!isset($data[$params_name])) {
					throw new RequestException($methods['error_message']);
				}

				switch ($methods['type']) {
					case 1:
						$data[$params_name] = call_user_func($methods['check_method'],$data[$params_name],$methods['error_message'],$methods['other_params']);
						break;
					case 2:
						$data[$params_name] = call_user_func([$this->validate,$methods['check_method']],$data[$params_name],$methods['error_message'],$methods['other_params']);
						break;
					case 4:
						$data[$params_name] = call_user_func($methods['check_method'],$data[$params_name],$methods['error_message'],$methods['other_params']);
						break;
				}
			}
		}

		$this->validate_func = [];
		return $data;
	}

	/**
	 * 赠礼数据源
	 * @param  [type] $input  [输入值]
	 * @param  [type] $params [整理参数]
	 * @return [type]         [description]
	 */
	protected function getData($input,$params)
	{
		if (!$params) {
			return $input;
		}

		if (is_string($params)) {
			if (isset($input[$params])) return [$params => $input[$params]];
			else return [$params => null];
		} 

		if (is_array($params)) {
			$data = [];
			foreach ($params as $k => $v) {
				switch (true) {
					case is_int($k) && isset($input[$v]):
						$data[$v] = $input[$v];
						break;
					case is_int($k) && !isset($input[$v]):
						$data[$v] = null;
						break;
					case is_string($k) && isset($input[$k]):
						$data[$k] = $input[$k];
						break;
					case is_string($k) && !isset($input[$k]):
						$data[$k] = $v;
						break;
					default:
						throw new RequestException('无法支持该使用方法');
				}
			}
			return $data;
		}
			
		throw new RequestException('无法支持该使用方法');
	}


	/**
	 * 类型
	 * @param  [type] $method [方法]
	 * @return [type]         [description]
	 */
	protected function checkMethodType ($method)
	{

		if (is_array($method)) {
			return 1;
		} else if (is_string($method) && method_exists('RainSunshineCloud\Validate',$method)) {
			$this->instanceValidate();
			return 2;
		} else if (is_object($method) && $method instanceof \Closure) {
			return 4;
		} else {
			throw new RequestException('不支持该类型的验证方法',100);
		}
	}

	/**
	 * 单例instance
	 * @return [type] [description]
	 */
	protected function instanceValidate()
	{
		if (is_null($this->validate)) {
			$this->validate = new Validate();
		}
	}

	/**
	 * 设置请求类型
	 */
	public static function setDataType($type = 1)
	{
		self::$data_type = $type;
	}

	/**
	 * 单例
	 * @return [type] [description]
	 */
	public static function instance()
	{
		if (self::$self) {
			return self::$self;
		}

		$class = get_called_class();
		return new $class();
	}

}

/**
 * 异常类
 */
class RequestException extends \Exception 
{

}
