<?php

class Conf_Env {

    const ACTION = 'action';
    const DEFAULTREQUEST = 'Index';
	/**
	 * 现在的配置
	 * 
	 * @var array
	 */
	private static $conf = array();
	
	/**
	 * 指定一个默认的配置文件路径
	 * 
	 * @param file
	 * @return bool 加载成功返回true。文件不存在或者返回值不为array返回false，并触发一个warning日志
	 */
	public static function setConfFile($conf_file_path) {
		if (!file_exists($conf_file_path)) {
			return false;
		}
		
		$conf = include_once($conf_file_path);
		if (!is_array($conf)) {
			return false;
		}
		
		self::$conf = $conf;
		return true;
	}
	
	/**
	 * @param string $key
	 * @param string $default
	 */
	public static function get($key, $default=null) {
		return isset(self::$conf[$key]) ? self::$conf[$key] : $default;
	}
}
