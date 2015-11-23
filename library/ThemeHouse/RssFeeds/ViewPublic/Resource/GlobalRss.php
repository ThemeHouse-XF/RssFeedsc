<?php

class ThemeHouse_RssFeeds_ViewPublic_Resource_GlobalRss extends XenForo_ViewPublic_Base
{

    public function renderRss()
    {
        $options = XenForo_Application::get('options');
        $title = ($options->boardTitle ? $options->boardTitle : XenForo_Link::buildPublicLink('canonical:index'));
        $description = ($options->boardDescription ? $options->boardDescription : $title);

        $buggyXmlNamespace = (defined('LIBXML_DOTTED_VERSION') && LIBXML_DOTTED_VERSION == '2.6.24');

        $feed = new Zend_Feed_Writer_Feed();
        $feed->setEncoding('utf-8');
        $feed->setTitle($title);
        $feed->setDescription($description);
        $feed->setLink(XenForo_Link::buildPublicLink('canonical:resources'));
        if (!$buggyXmlNamespace) {
            $feed->setFeedLink(XenForo_Link::buildPublicLink('canonical:resources/-/index') . '.rss', 'rss');
        }
        $feed->setDateModified(XenForo_Application::$time);
        $feed->setLastBuildDate(XenForo_Application::$time);
        $feed->setGenerator($title);

        $bbCodeSnippetParser = XenForo_BbCode_Parser::create(
            XenForo_BbCode_Formatter_Base::create('XenForo_BbCode_Formatter_BbCode_AutoLink', false));
        $bbCodeParser = XenForo_BbCode_Parser::create(
            XenForo_BbCode_Formatter_Base::create('Base', array(
                'view' => $this
            )));

        foreach ($this->_params['resources'] as $resource) {
            $entry = $feed->createEntry();
            $entry->setTitle($resource['title'] ? $resource['title'] : $resource['title'] . ' ');
            $entry->setLink(XenForo_Link::buildPublicLink('canonical:resources', $resource));
            $entry->setDateCreated(new Zend_Date($resource['resource_date'], Zend_Date::TIMESTAMP));
            $entry->setDateModified(new Zend_Date($resource['last_update'], Zend_Date::TIMESTAMP));
            if (!empty($resource['description']) && XenForo_Application::getOptions()->discussionRssContentLength) {
                $snippet = $bbCodeSnippetParser->render(
                    XenForo_Helper_String::wholeWordTrim($resource['description'],
                        XenForo_Application::getOptions()->discussionRssContentLength));
                if ($snippet != $resource['description']) {
                    $snippet .= "\n\n[url='" . XenForo_Link::buildPublicLink('canonical:resources', $resource) . "']" .
                         $resource['title'] . '[/url]';
                }
                $content = trim($bbCodeParser->render($snippet));
                if (strlen($content)) {
                    $entry->setContent($content);
                }
            }
            if (!$buggyXmlNamespace) {
                $entry->addAuthor(
                    array(
                        'name' => $resource['username'],
                        'email' => 'invalid@example.com',
                        'uri' => XenForo_Link::buildPublicLink('canonical:members', $resource)
                    ));
                if ($resource['review_count']) {
                    $entry->setCommentCount($resource['review_count']);
                }
            }

            $feed->addEntry($entry);
        }

        return $feed->export('rss');
    }
}