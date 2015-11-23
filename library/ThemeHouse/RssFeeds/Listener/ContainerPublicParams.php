<?php

class ThemeHouse_RssFeeds_Listener_ContainerPublicParams extends ThemeHouse_Listener_ContainerParams
{

    public function run()
    {
        $xenOptions = XenForo_Application::get('options');

        if (!$this->getParam('rssFeed')) {
            if ($this->getParam('majorSection') == 'forums') {
                $rssFeed = array(
                    'title' => new XenForo_Phrase('rss_feed_for_x', array(
                        'title' => $xenOptions->boardTitle
                    )),
                    'link' => XenForo_Link::buildPublicLink('forums/-/index.rss')
                );
                $this->setParam('rssFeed', $rssFeed);
            } elseif ($this->getParam('majorSection') == 'resources') {
                $rssFeed = array(
                    'title' => new XenForo_Phrase('th_x_rss_feed_for_y_rssfeeds',
                        array(
                            'feedType' => new XenForo_Phrase('resources'),
                            'title' => $xenOptions->boardTitle,
                        )),
                    'link' => XenForo_Link::buildPublicLink('resources/-/index') . '.rss'
                );
                $this->setParam('rssFeed', $rssFeed);
            }
        }

        return parent::run();
    }

    public static function containerPublicParams(array &$params, XenForo_Dependencies_Abstract $dependencies)
    {
        $params = self::createAndRun('ThemeHouse_RssFeeds_Listener_ContainerPublicParams', $params, $dependencies);
    }
}