<?php

abstract class ThemeHouse_Listener_ContainerParams
{

	protected $_params = null;

	/**
	 *
	 * @var XenForo_Dependencies_Abstract
	 */
	protected $_dependencies = null;

	/**
	 * Standard approach to caching other model objects for the lifetime of the
	 * model.
	 *
	 * @var array
	 */
	protected $_modelCache = array();

	/**
	 *
	 * @param array $params
	 * @param XenForo_Dependencies_Abstract $dependencies
	 */
	public function __construct(array &$params, XenForo_Dependencies_Abstract $dependencies)
	{
		$this->_params = $params;
		$this->_dependencies = $dependencies;
	}

	/**
	 * This only works on PHP 5.3+, so method should be overridden for now.
	 */
	public static function containerPublicParams(array &$params, XenForo_Dependencies_Abstract $dependencies)
	{
		if (function_exists('get_called_class')) {
			$className = get_called_class();
		} else {
			$className = get_class();
		}

		$params = self::createAndRun($className, $params, $dependencies);
	}

	/**
	 * This only works on PHP 5.3+, so method should be overridden for now.
	 */
	public static function containerAdminParams(array &$params, XenForo_Dependencies_Abstract $dependencies)
	{
		if (function_exists('get_called_class')) {
			$className = get_called_class();
		} else {
			$className = get_class();
		}

		$params = self::createAndRun($className, $params, $dependencies);
	}

	/**
	 *
	 * @return string
	 */
	public function run()
	{
		$this->_run();

		return $this->_params;
	}

	/**
	 * Method designed to be overridden by child classes to add run behaviours.
	 */
	protected function _run()
	{
		// TODO: remove returned value as no longer required
		return $this->_params;
	}

	/**
	 * Factory method to get the named container params listener.
	 * The class must exist or be autoloadable or an exception will be thrown.
	 *
	 * @param string Class to load
	 * @param array $params
	 * @param XenForo_Dependencies_Abstract $dependencies
	 *
	 * @return ThemeHouse_Listener_ContainerPublicParams
	 */
	public static function create($class, array &$params, XenForo_Dependencies_Abstract $dependencies)
	{
		$createClass = XenForo_Application::resolveDynamicClass($class, 'listener_th');
		if (!$createClass) {
			throw new XenForo_Exception("Invalid listener '$class' specified");
		}

		return new $createClass($params, $dependencies);
	}

	/**
	 *
	 * @param string $class
	 * @param array $params
	 * @param XenForo_Dependencies_Abstract $dependencies
	 * @return array
	 */
	public static function createAndRun($class, array &$params, XenForo_Dependencies_Abstract $dependencies)
	{
		$createClass = self::create($class, $params, $dependencies);

		if (XenForo_Application::debugMode()) {
			return $createClass->run();
		}
		try {
			return $createClass->run();
		} catch (Exception $e) {
			return $this->_params;
		}
	}

	/**
	 *
	 * @param string $param
	 * @param mixed $value
	 */
	public function setParam($param, $value)
	{
		$this->_params[$param] = $value;
	}

	/**
	 *
	 * @param string $param
	 * @return mixed
	 */
	public function getParam($param)
	{
		if (!isset($this->_params[$param])) {
			return null;
		}

		return $this->_params[$param];
	}

	/**
	 * Gets the specified model object from the cache.
	 * If it does not exist,
	 * it will be instantiated.
	 *
	 * @param string $class Name of the class to load
	 *
	 * @return XenForo_Model
	 */
	public function getModelFromCache($class)
	{
		if (!isset($this->_modelCache[$class])) {
			$this->_modelCache[$class] = XenForo_Model::create($class);
		}

		return $this->_modelCache[$class];
	}
}