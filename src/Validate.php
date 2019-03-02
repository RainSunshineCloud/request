<?php
namespace RainSunshineCloud;

class Validate
{
	/**
	 * 转化为整数
	 * @param  [type] $value   [description]
	 * @param  [type] $message [description]
	 * @return [type]          [description]
	 */
	public function int($value,$message,$other = [])
	{
		$other = array_merge(['min' => 0, 'max' => 0],$other);
		if (is_numeric($value) && is_int($value + 0)) {

			$value =  intval($value);
			if ($value < $other['min'] ) {
				throw new RequestException($message);
			} 

			if ($value > $other['max'] && $other['max'] != 0) {
				throw new RequestException($message);
			}

			return $value;
		}


		throw new RequestException($message);
	}

	/**
	 * 必须
	 * @param  [type] $value   [description]
	 * @param  [type] $message [description]
	 * @return [type]          [description]
	 */
	public function require($value,$message)
	{
		if (!isset($value) || (is_string($value) && trim($value) == '')) {
			throw new RequestException($message);
		} 

		return $value;
	}

	/**
	 * 判断是否是字符串
	 * @param  [type] $value   [description]
	 * @param  [type] $message [description]
	 * @param  [type] $other   [description]
	 * @return [type]          [description]
	 */
	public function string($value,$message,$other)
	{
		$other = array_merge(['min' => 0, 'max' => 0, 'trim' => true],$other);

		if (!is_string($value)) {
			throw new RequestException($message);
		} 

		if ($other['trim']) {
			$value = trim($value);
		}
		
		$str_len = mb_strlen($value);

		if ($str_len < $other['min'] ) {
			throw new RequestException($message);
		} 

		if ($str_len > $other['max'] && $other['max'] != 0) {
			throw new RequestException($message);
		}

		return $value;
	}

	/**
	 * 去除空格
	 * @param  [type] $value [description]
	 * @return [type]        [description]
	 */
	public function trim($value,$message)
	{
		if (is_string($value)) {
			return  trim($value);
		}
		
		return $value;
	}

	/**
	 * 手机号
	 * @param  [type] $value   [description]
	 * @param  [type] $message [description]
	 * @return [type]          [description]
	 */
	public function moble($value,$message)
	{
		if (preg_match("/^1[345678]{1}\d{9}$/",$value)) {
			return $value;
		}

		throw new RequestException($message);
	}

	/**
	 * ip
	 * @param  [type] $value   [description]
	 * @param  [type] $message [description]
	 * @return [type]          [description]
	 */
	public function ip($value,$message)
	{
		if (filter_var($value, FILTER_VALIDATE_IP)) {
			return $value;
		}

		throw new RequestException($message);
	}

	/**
	 * 浮点数
	 * @param  [type] $value   [description]
	 * @param  [type] $message [description]
	 * @param  array  $other   [description]
	 * @return [type]          [description]
	 */
	public function float($value,$message,$other = [])
	{
		$other = array_merge(['min' => 0, 'max' => 0],$other);
		if (is_numeric($value)) {

			$value = floatval($value);
			if ($value < $other['min'] ) {
				throw new RequestException($message);
			}

			if ($value > $other['max'] && $other['max'] != 0) {
				throw new RequestException($message);
			}

			return $value;
		}

		throw new RequestException($message);
	}

	/**
	 * 小数点判断
	 * @param  [type] $value   [description]
	 * @param  [type] $message [description]
	 * @param  array  $other   [description]
	 * @return [type]          [description]
	 */
	public function diget( $value, $message, $other = ['min' => 0,'max' => 2])
	{
		$preg = sprintf('/^[0-9]+(\.[0-9]{%d,%d})?$/',$other['min'],$other['max']);
		if (preg_match($preg,$value)) {
			return $value;
		}

		throw new RequestException($message);
	}

	/**
	 * 判断是否是字符串
	 * @param  [type] $value   [description]
	 * @param  [type] $message [description]
	 * @return [type]          [description]
	 */
	public function array($value,$message)
	{
		if (is_array($value)) {
			return $value;
		}

		throw new RequestException($message);
	}

	/**
	 * 判断并转化json
	 * @param  [type] $value   [description]
	 * @param  [type] $message [description]
	 * @param  array  $other   [description]
	 * @return [type]          [description]
	 */
	public function json($value,$message, $other = [])
	{
		if (json_encode($value) && empty($other['get_json'])) {
			return $value;
		}

		throw new RequestException($message);
	}

	/**
	 * 判断是否是url
	 * @param  [type] $value   [description]
	 * @param  [type] $message [description]
	 * @param  array  $other   [description]
	 * @return [type]          [description]
	 */
	public function url($value,$message,$other = [])
	{
		if (filter_var($value,FILTER_VALIDATE_URL,FILTER_FLAG_PATH_REQUIRED) ) {
			return $value;
		}

		throw new RequestException($message);
	}

	/**
	 * email验证
	 * @param  [type] $value   [值]
	 * @param  [type] $message [信息]
	 * @param  [type] $other   [其他参数]
	 * @return [type]          [description]
	 */
	public function email($value, $message, $other = [])
	{
		if (filter_var($value, FILTER_VALIDATE_EMAIL) ) {
			return $value;
		}

		throw new RequestException($message);
	}

}

