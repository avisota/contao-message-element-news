<?php

/**
 * Avisota newsletter and mailing system
 * Copyright (C) 2013 Tristan Lins
 *
 * PHP version 5
 *
 * @copyright  bit3 UG 2013
 * @author     Tristan Lins <tristan.lins@bit3.de>
 * @package    avisota/contao-message-element-article
 * @license    LGPL-3.0+
 * @filesource
 */


namespace Avisota\Contao\Message\Element\News;

use Avisota\Contao\Core\Message\Renderer;
use Avisota\Contao\Entity\MessageContent;
use Avisota\Contao\Message\Core\Event\AvisotaMessageEvents;
use Avisota\Contao\Message\Core\Event\RenderMessageContentEvent;
use Avisota\Recipient\RecipientInterface;
use Contao\Doctrine\ORM\Entity;
use Contao\Doctrine\ORM\EntityAccessor;
use ContaoCommunityAlliance\Contao\Bindings\ContaoEvents;
use ContaoCommunityAlliance\Contao\Bindings\Events\Controller\GetArticleEvent;
use ContaoCommunityAlliance\Contao\Bindings\Events\News\GetNewsEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;


/**
 * Class DefaultRenderer
 */
class DefaultRenderer implements EventSubscriberInterface
{
	/**
	 * {@inheritdoc}
	 */
	static public function getSubscribedEvents()
	{
		return array(
			AvisotaMessageEvents::RENDER_MESSAGE_CONTENT => 'renderContent',
		);
	}

	/**
	 * Render a single message content element.
	 *
	 * @param MessageContent     $content
	 * @param RecipientInterface $recipient
	 *
	 * @return string
	 */
	public function renderContent(RenderMessageContentEvent $event)
	{
		$content = $event->getMessageContent();

		if ($content->getType() != 'news' || $event->getRenderedContent()) {
			return;
		}

		/** @var EntityAccessor $entityAccessor */
		$entityAccessor = $GLOBALS['container']['doctrine.orm.entityAccessor'];

		$getNewsEvent = new GetNewsEvent(
			$content->getNewsId(),
			$content->getNewsTemplate()
		);

		/** @var EventDispatcher $eventDispatcher */
		$eventDispatcher = $GLOBALS['container']['event-dispatcher'];
		$eventDispatcher->dispatch(ContaoEvents::NEWS_GET_NEWS, $getNewsEvent);

		$context = $entityAccessor->getProperties($content);
		$context['news'] = $getNewsEvent->getNewsHtml();

		$template = new \TwigTemplate('avisota/message/renderer/default/mce_news', 'html');
		$buffer   = $template->parse($context);

		$event->setRenderedContent($buffer);
	}
}
