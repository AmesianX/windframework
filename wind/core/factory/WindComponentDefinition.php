<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-12-31
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

L::import('WIND:core.factory.WindClassDefinition');
/**
 * 组件定义
 * 
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$ 
 * @package 
 */
class WindComponentDefinition extends WindClassDefinition {

	const CONFIG = 'config';

	const RESOURCE = 'resource';

	const CONFIGCACHE = 'wind_components_config';

	const PROXY = 'proxy';

	/**
	 * 类代理对象定义
	 *
	 * @var string
	 */
	protected $proxy = '';

	protected $config = array();

	/* (non-PHPdoc)
	 * @see WindClassDefinition::createInstance()
	 */
	protected function createInstance($factory, $args = array()) {
		$instance = parent::createInstance($factory, $args);
		if (!($instance instanceof WindComponentModule)) return $instance;
		$windConfig = null;
		if (isset($this->config['resource']) && ($resource = $this->config['resource'])) {
			L::import('WIND:core.config.parser.WindConfigParser');
			$windConfig = new WindConfig($resource, new WindConfigParser(), $this->getAlias(), self::CONFIGCACHE);
		} else {
			$windConfig = new WindConfig($this->config);
		}
		$instance->setConfig($windConfig);
		$this->setProxyForClass($instance, $factory);
		return $instance;
	}

	/**
	 * 为类设置代理
	 * 
	 * @param WindModule $instance
	 * @param WindFactory $factory
	 */
	protected function setProxyForClass($instance, $factory) {
		if (!$instance instanceof WindModule) return;
		$proxyClass = L::import($this->getProxy());
		if (!class_exists($proxyClass)) return;
		
		$proxyClass = new $proxyClass();
		if (isset($factory->request)) $proxyClass->setAttribute('request', $factory->request);
		if (isset($factory->response)) $proxyClass->setAttribute('response', $factory->response);
		if (isset($factory->application)) $proxyClass->setAttribute('application', $factory->application);
		if ($proxyClass instanceof WindClassProxy) $instance->setClassProxy($proxyClass);
	}

	/* (non-PHPdoc)
	 * @see WindClassDefinition::init()
	 */
	protected function init($classDefinition) {
		parent::init($classDefinition);
		if (isset($classDefinition[self::CONFIG])) {
			$this->config = $classDefinition[self::CONFIG];
		}
		if (isset($classDefinition[self::PROXY])) {
			$this->setProxy($classDefinition[self::PROXY]);
		}
	}

	/**
	 * @return the $proxy
	 */
	public function getProxy() {
		return $this->proxy;
	}

	/**
	 * @param string $proxy
	 */
	public function setProxy($proxy) {
		$this->proxy = $proxy;
	}

}