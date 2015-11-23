<?php

/**
 *
 * @see XenResource_ControllerPublic_Resource
 */
class ThemeHouse_RssFeeds_Extend_XenResource_ControllerPublic_Resource extends XFCP_ThemeHouse_RssFeeds_Extend_XenResource_ControllerPublic_Resource
{

    public function actionIndex()
    {
        if ($resourceId = $this->_input->filterSingle('resource_id', XenForo_Input::UINT)) {
            return $this->responseReroute(__CLASS__, 'view');
        }
        
        if ($this->_routeMatch->getResponseType() == 'rss') {
            return $this->getGlobalResourceRss();
        }
        
        return parent::actionIndex();
    }

    /**
     * Gets the data for the global resources RSS feed.
     *
     * @return XenForo_ControllerResponse_Abstract
     */
    public function getGlobalResourceRss()
    {
        /* @var $resourceModel XenResource_Model_Resource */
        $resourceModel = $this->_getResourceModel();
        $visitor = XenForo_Visitor::getInstance();
        
        $resourcesPerPage = max(1, XenForo_Application::get('options')->resourcesPerPage);
        
        $resources = $resourceModel->getResources(array(), 
            array(
                'join' => XenResource_Model_Resource::FETCH_DESCRIPTION | XenResource_Model_Resource::FETCH_CATEGORY,
                'limit' => $resourcesPerPage * 3, // to filter
                'order' => 'last_update',
                'direction' => 'desc'
            ));
        $resources = $this->_getResourceModel()->filterUnviewableResources($resources);
        $resources = array_slice($resources, 0, $resourcesPerPage, true);
        
        $resources = $resourceModel->prepareResources($resources);
        
        $viewParams = array(
            'resources' => $resources
        );
        return $this->responseView('ThemeHouse_RssFeeds_ViewPublic_Resource_GlobalRss', '', $viewParams);
    }
}