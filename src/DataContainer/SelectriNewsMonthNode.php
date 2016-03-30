<?php

/**
 * Avisota newsletter and mailing system
 * Copyright © 2016 Sven Baumann
 *
 * PHP version 5
 *
 * @copyright  way.vision 2016
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @package    avisota/contao-message-element-news
 * @license    LGPL-3.0+
 * @filesource
 */

namespace Avisota\Contao\Message\Element\News\DataContainer;

use ContaoCommunityAlliance\Contao\Bindings\ContaoEvents;
use ContaoCommunityAlliance\Contao\Bindings\Events\Image\GenerateHtmlEvent;
use ContaoCommunityAlliance\UrlBuilder\UrlBuilder;
use Hofff\Contao\Selectri\Model\Node;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Class SelectriNewsMonthNode
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class SelectriNewsMonthNode implements Node
{

    /**
     * @var SelectriNewsData
     */
    protected $data;

    /**
     * @var \DateTime
     */
    protected $date;

    /**
     * @var SelectriNewsEventNode[]
     */
    protected $news;

    /**
     * @var bool
     */
    protected $isSorted = false;

    /**
     * SelectriNewsMonthNode constructor.
     *
     * @param SelectriNewsData $data
     * @param \DateTime $date
     */
    public function __construct(SelectriNewsData $data, \DateTime $date)
    {
        $this->data   = $data;
        $this->date   = $date;
        $this->news = array();
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @return SelectriNewsEventNode[]
     */
    public function getNews()
    {
        return $this->news;
    }

    /**
     * @param SelectriNewsEventNode[] $news
     *
     * @return static
     */
    public function setNews(array $news)
    {
        $this->news = array();
        $this->addNews($news);
        return $this;
    }

    /**
     * @param SelectriNewsEventNode[] $news
     *
     * @return static
     */
    public function addNews(array $news)
    {
        foreach ($news as $new) {
            $this->addNew($new);
        }
        return $this;
    }

    /**
     * @param SelectriNewsEventNode $new
     *
     * @return static
     * @internal param SelectriNewsEventNode $news
     *
     */
    public function addNew(SelectriNewsEventNode $new)
    {
        $this->news[] = $new;
        $new->setMonth($this);
        return $this;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->date->format('Y-m');
    }

    /**
     * @return array
     */
    public function getData()
    {
        return array('date' => $this->date->getTimestamp());
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        /** @var SelectriNewsEventNode $newsNode */
        $newsNode = $this->getNews()[0];
        if (!$newsNode) {
            return $this->date->format('Y F');
        }

        $newsData = $newsNode->getRow();
        $label = $this->date->format(\Config::get('dateFormat'));

        if ($newsData['time']) {
            $label .= ' ' . date('H:i', $newsData['time']);
        }

        $label .= ': ' . $newsData['headline'];
        $label .= ' <span style="color: grey;">[' . \NewsArchiveModel::findByPk($newsData['pid'])->title . ']</span>';
        $label .= $this->getInfo($newsData);

        return $label;
    }

    /**
     * @param $newsData
     *
     * @return string
     */
    protected function getInfo($newsData)
    {
        $info = '<div style="margin-left: 16px; padding-top: 6px">';
        $info .= $this->getEditButton($newsData);
        $info .= $this->getHeaderButton($newsData);
        $info .= $this->getPublishedIcon($newsData);
        $info .= '</div>';

        return $info;
    }

    /**
     * @param $newsData
     *
     * @return string
     */
    protected function getEditButton($newsData)
    {
        $urlBuilder = new UrlBuilder();
        $urlBuilder->setPath('contao/main.php')
            ->setQueryParameter('do', 'news')
            ->setQueryParameter('table', 'tl_content')
            ->setQueryParameter('id', $newsData['id'])
            ->setQueryParameter('popup', 1)
            ->setQueryParameter('rt', \RequestToken::get());

        $button =
            '<a href="' . $urlBuilder->getUrl() . '" ' . $this->getOnClickOpenModalIFrame() . '>'
            . $this->getOperationImage('edit.gif')
            . '</a>';

        return $button;
    }

    /**
     * @param $newsData
     *
     * @return string
     */
    protected function getHeaderButton($newsData)
    {
        $urlBuilder = new UrlBuilder();
        $urlBuilder->setPath('contao/main.php')
            ->setQueryParameter('do', 'news')
            ->setQueryParameter('table', 'tl_news')
            ->setQueryParameter('act', 'edit')
            ->setQueryParameter('id', $newsData['id'])
            ->setQueryParameter('popup', 1)
            ->setQueryParameter('rt', \RequestToken::get());

        $button =
            '<a href="' . $urlBuilder->getUrl() . '" ' . $this->getOnClickOpenModalIFrame() . '>'
            . $this->getOperationImage('header.gif')
            . '</a>';

        return $button;
    }

    /**
     * @param $icon
     *
     * @return string
     */
    protected function getOperationImage($icon)
    {
        global $container;
        /** @var EventDispatcher $eventDispatcher */
        $eventDispatcher = $container['event-dispatcher'];

        /** @var GenerateHtmlEvent $imageEvent */
        $imageEvent = $eventDispatcher->dispatch(
            ContaoEvents::IMAGE_GET_HTML,
            new GenerateHtmlEvent(
                $icon,
                '',
                'style="padding-left: 6px;"'
            )
        );

        return $imageEvent->getHtml();
    }

    /**
     * @param $newsData
     *
     * @return string
     */
    protected function getPublishedIcon($newsData)
    {
        $icon = 'visible.gif';
        if ($newsData['published'] < 1) {
            $icon = 'invisible.gif';
        }

        return $this->getOperationImage($icon);
    }

    /**
     * @return string
     */
    protected function getOnClickOpenModalIFrame()
    {
        return 'onclick="Backend.openModalIframe({\'width\':768,\'url\':this.href});return false"';
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return '';
    }

    /**
     * @param $key
     *
     * @return string
     */
    public function getAdditionalInputName($key)
    {
        $name = $this->data->getWidget()->getAdditionalInputBaseName();
        $name .= '[' . $this->getKey() . ']';
        $name .= '[' . $key . ']';
        return $name;
    }

    /**
     * @return string
     */
    public function getIcon()
    {
        return 'system/modules/news/assets/icon.gif';
    }

    /**
     * @return bool
     */
    public function isSelectable()
    {
        return false;
    }

    /**
     * @return bool
     */
    public function isOpen()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function hasPath()
    {
        return false;
    }

    /**
     * @return \EmptyIterator
     */
    public function getPathIterator()
    {
        return new \EmptyIterator();
    }

    /**
     * @return bool
     */
    public function hasItems()
    {
        return (bool) count($this->news);
    }

    /**
     * @return \EmptyIterator
     */
    public function getItemIterator()
    {
        return new \EmptyIterator();
    }

    /**
     * @return bool
     */
    public function hasSelectableDescendants()
    {
        return true;
    }

    /**
     * @return \ArrayIterator
     */
    public function getChildrenIterator()
    {
        if (!$this->isSorted) {
            usort(
                $this->news,
                function (SelectriNewsEventNode $primary, SelectriNewsEventNode $secondary) {
                    return $primary->getDate()->getTimestamp() - $secondary->getDate()->getTimestamp();
                }
            );
            $this->isSorted = true;
        }

        return new \ArrayIterator($this->news);
    }
}
