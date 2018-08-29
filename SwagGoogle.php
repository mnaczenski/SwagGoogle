<?php

namespace SwagGoogle;

use Enlight_Controller_Request_Request;
use Enlight_View_Default;
use Shopware\Components\Plugin;

class SwagGoogle extends Plugin
{
    public static function getSubscribedEvents()
    {
        return [
            'Enlight_Controller_Action_PostDispatchSecure_Frontend' => 'onPostDispatch'
        ];
    }

    /**
     * @param \Enlight_Event_EventArgs $args
     */
    public function onPostDispatch(\Enlight_Event_EventArgs $args)
    {
        /** @var Enlight_Controller_Request_Request $request */
        $request = $args->getSubject()->Request();
        /** @var Enlight_View_Default $view */
        $view = $args->getSubject()->View();

        if ($request->isXmlHttpRequest()) {
            return;
        }

        $view->addTemplateDir($this->getPath() . '/Resources/views');

        $config = $this->getConfig();
        
        if (!empty($config['conversion_code'])) {
            $this->handleConversionCode($view, $config);
        }

        if (!empty($config['tracking_code'])) {
            $this->handleTrackingCode($view, $config);
        }
    }

    /**
     * @return array
     */
    private function getConfig()
    {
        $shop = $shop = $this->container->get('shop');
        $configReader = $this->container->get('shopware.plugin.cached_config_reader');

        return $configReader->getByPluginName($this->getName(), $shop);
    }

    /**
     * @param Enlight_View_Default $view
     * @param array                $config
     */
    private function handleConversionCode(Enlight_View_Default $view, array $config)
    {
        $view->assign('GoogleConversionID', $config['conversion_code']);
        $view->assign('GoogleConversionLabel', $config['conversion_label']);
        $view->assign('GoogleConversionLanguage', $this->container->get('locale')->getLanguage());
        $view->assign('GoogleIncludeInHead', $config['include_header']);
    }

    /**
     * @param Enlight_View_Default $view
     * @param array                $config
     */
    private function handleTrackingCode(Enlight_View_Default $view, array $config)
    {
        $view->assign('GoogleTrackingID', $config['tracking_code']);
        $view->assign('GoogleAnonymizeIp', $config['anonymize_ip']);
        $view->assign('GoogleOptOutCookie', $config['include_opt_out_cookie']);
        $view->assign('GoogleTrackingLibrary', $config['trackingLib']);
        $view->assign('GoogleIncludeInHead', $config['include_header']);
    }
}
