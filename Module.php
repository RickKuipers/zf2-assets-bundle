<?php
namespace Neilime\AssetsBundle;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
class Module implements AutoloaderProviderInterface{
	
	/**
	 * @var \Zend\ModuleManager\ModuleManagerInterface
	 */
	private $moduleManager;
	
	/**
	 * Init module
	 * @param \Zend\ModuleManager\ModuleManagerInterface $oManager
	 * @return void
	 */
	public function init(\Zend\ModuleManager\ModuleManagerInterface $oManager){
		$this->moduleManager = $oManager;
	}
	
	/**
	 * @param \Zend\EventManager\EventInterface $oEvent
	 */
	public function onBootstrap(\Zend\EventManager\EventInterface $oEvent){
		$oServiceManager = $oEvent->getApplication()->getServiceManager();
		if($oServiceManager->get('ViewRenderer') instanceof \Zend\View\Renderer\PhpRenderer)$oEvent->getApplication()->getEventManager()->attach('render', array($this, 'renderAssets'), 32);
	}
	
	/**
	 * @param \Zend\Mvc\MvcEvent $oEvent
	 */
	public function renderAssets(\Zend\Mvc\MvcEvent $oEvent){
		/* @var $oRouter \Zend\Mvc\Router\RouteMatch */
		$oRouter = $oEvent->getRouteMatch();
		if($oRouter instanceof \Zend\Mvc\Router\RouteMatch){
			$oEvent->getApplication()->getServiceManager()->get('NeilimeAssetBundleService')
			->setControllerName($oRouter->getParam('controller'))
			->setActionName($oRouter->getParam('action'))
			->setRenderer($oEvent->getApplication()->getServiceManager()->get('ViewRenderer'))
			->renderAssets(array_keys($this->moduleManager->getLoadedModules()));
		}
	}

	/**
	 * @see \Zend\ModuleManager\Feature\AutoloaderProviderInterface::getAutoloaderConfig()
	 * @return array
	 */
	public function getAutoloaderConfig(){
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(__NAMESPACE__ => __DIR__ . '/src/' . str_replace('\\', '/' , __NAMESPACE__))
            ),
        );
    }

    /**
     * @return array
     */
    public function getConfig(){
        return include __DIR__ . '/config/module.config.php';
    }
}