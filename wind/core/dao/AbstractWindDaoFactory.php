<?php

L::import('WIND:core.factory.proxy.WindClassProxy');
L::import('WIND:core.factory.WindComponentFactory');
/**
 * Dao工厂
 * 
 * 职责：
 * 创建DAO实例
 * 数据缓存部署实现
 * 创建数据访问连接对象
 *
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package 
 */
abstract class AbstractWindDaoFactory {

	protected $daoResource = '';

	protected $dbConnections = array();
	
	protected $caches = array();

	/**
	 * Enter description here ...
	 * 
	 * @param string $className
	 */
	public function getDao($className) {
		try {
			$_path = '';
			if (strpos($className, ":") !== false || strpos($className, ".") !== false) {
				$_path = $className;
			} elseif ($this->getDaoResource()) {
				$_path = $this->getDaoResource() . '.' . $className;
			} else {
				$_path = $className;
			}
			$className = L::import($_path);
			
			$daoInstance = WindFactory::createInstance($className);
			$daoInstance->setDbHandler($this->createDbHandler($daoInstance));
			if (!$daoInstance->getIsDataCache()) return $daoInstance;
			$daoInstance->setCacheHandler($this->createCacheHandler($daoInstance));
			$daoInstance->setClassProxy(new WindClassProxy());
			$daoInstance = $daoInstance->getClassProxy();
			$listener = new WindDaoCacheListener($daoInstance);
			foreach ($daoInstance->getCacheMethods() as $classMethod) {
				$daoInstance->registerEventListener($classMethod, $listener);
			}
			return $daoInstance;
		} catch (Exception $exception) {
			throw new WindDaoException($exception->getMessage());
		}
	}

	/**
	 * 获取DBHandler
	 * @param AbstractWindDao $daoObject
	 */
	protected function createDbHandler($daoObject) {
		$_dbClass = $daoObject->getDbClass();
		if (!isset($this->dbConnections[$_dbClass])) {
			$factory = new WindComponentFactory();
			$defintion = $daoObject->getDbDefinition();
			$factory->addClassDefinitions($defintion);
			$this->dbConnections[$_dbClass] = $factory->getInstance($defintion->getAlias());
		}
		return $this->dbConnections[$_dbClass];
	}
	
	protected function createCacheHandler($daoObject){
		$_cacheClass = $daoObject->getCacheClass();
		if (!isset($this->caches[$_cacheClass])) {
			$factory = new WindComponentFactory();
			$defintion = $daoObject->getCacheDefinition();
			$factory->addClassDefinitions($defintion);
			$this->caches[$_cacheClass] = $factory->getInstance($defintion->getAlias());
		}
		return $this->caches[$_cacheClass];
	}

	/**
	 * @return the $daoResource
	 */
	public function getDaoResource() {
		return $this->daoResource;
	}

	/**
	 * @param field_type $daoResource
	 */
	public function setDaoResource($daoResource) {
		$this->daoResource = $daoResource;
	}

	abstract public function getFactory();

}

?>