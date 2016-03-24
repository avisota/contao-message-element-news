<?php

/**
 * Avisota newsletter and mailing system
 * Copyright © 2016 Sven Baumann
 *
 * PHP version 5
 *
 * @copyright  way.vision 2016
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @package    avisota/contao-message-element-article
 * @license    LGPL-3.0+
 * @filesource
 */

namespace Avisota\Contao\Message\Element\News;

use Avisota\Contao\Core\Message\Renderer;
use Avisota\Contao\Entity\MessageContent;
use Avisota\Contao\Message\Core\Event\AvisotaMessageEvents;
use Avisota\Contao\Message\Core\Event\RenderMessageContentEvent;
use Contao\Doctrine\ORM\Entity;
use Contao\Doctrine\ORM\EntityAccessor;
use ContaoCommunityAlliance\Contao\Bindings\ContaoEvents;
use ContaoCommunityAlliance\Contao\Bindings\Events\News\GetNewsEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class DefaultRenderer
 */
class DefaultRenderer implements EventSubscriberInterface
{
    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2'))
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            AvisotaMessageEvents::RENDER_MESSAGE_CONTENT => 'renderContent',
        );
    }

    /**
     * Render a single message content element.
     *
     * @param RenderMessageContentEvent $event
     *
     * @return string
     * @internal param MessageContent $content
     * @internal param RecipientInterface $recipient
     */
    public function renderContent(RenderMessageContentEvent $event)
    {
        global $container;

        $content = $event->getMessageContent();

        if ($content->getType() != 'news' || $event->getRenderedContent()) {
            return;
        }

        /** @var EntityAccessor $entityAccessor */
        $entityAccessor = $container['doctrine.orm.entityAccessor'];

        $contentEventTemplate = $this->findNewsTemplate($content);
        $getNewsEvent = new GetNewsEvent(
            $content->getNewsId(),
            $contentEventTemplate
        );

        /** @var EventDispatcher $eventDispatcher */
        $eventDispatcher = $container['event-dispatcher'];
        $eventDispatcher->dispatch(ContaoEvents::NEWS_GET_NEWS, $getNewsEvent);

        $context         = $entityAccessor->getProperties($content);
        $context['news'] = $getNewsEvent->getNewsHtml();

        $template = new \TwigTemplate('avisota/message/renderer/default/mce_news', 'html');
        $buffer   = $template->parse($context);
        $this->removeMicroTimeTemplate($content, $contentEventTemplate);

        $event->setRenderedContent($buffer);
    }

    protected function findNewsTemplate(MessageContent $content)
    {
        $messageCategory = $content->getMessage()->getCategory();
        $messageTheme    = $messageCategory->getLayout()->getTheme();

        $template = null;
        if ($messageTheme->getTemplateDirectory()
            && file_exists(TL_ROOT . '/templates/' . $messageTheme->getTemplateDirectory() . '/' . $content->getNewsTemplate() . '.html5')
        ) {
            $template = $this->copyTemplateInRootTemplates(
                $messageTheme->getTemplateDirectory() . '/' . $content->getNewsTemplate(),
                '.' . microtime(true)
            );
        }
        if (!$template
            && $messageCategory->getViewOnlinePage() > 0
        ) {
            $viewOnlinePage = \PageModel::findByPk($messageCategory->getViewOnlinePage());

            $pageTheme = null;
            if ($viewOnlinePage) {
                $viewOnlinePage->loadDetails();
                $pageTheme = $viewOnlinePage->getRelated('layout')->getRelated('pid');
            }

            if ($pageTheme
                && file_exists(TL_ROOT . '/' . $pageTheme->templates . '/' . $content->getNewsTemplate() . '.html5')
            ) {
                $source = $pageTheme->templates;
                $chunks = explode('/', $source);
                if (count($chunks) > 1) {
                    if (in_array('templates', array_values($chunks))) {
                        $unset = array_flip($chunks)['templates'];
                        unset($chunks[$unset]);
                    }
                }
                $source = implode('/', $chunks);

                $template = $this->copyTemplateInRootTemplates(
                    $source . '/' . $content->getNewsTemplate(),
                    '.' . microtime(true)
                );
            }
        }

        if (!$template) {
            $template = $content->getNewsTemplate();
        }


        return $template;
    }

    protected function copyTemplateInRootTemplates($source, $destination)
    {
        $sourceFile = new \File('templates/' . $source . '.html5');
        $sourceFile->copyTo('templates/' . $destination . '.html5');

        return $destination;
    }

    protected function removeMicroTimeTemplate(MessageContent $content, $remove)
    {
        if ($content->getNewsTemplate() === $remove) {
            return;
        }

        $removeFile = new \File('templates/' . $remove . '.html5', true);
        if (!$removeFile->exists()) {
            return;
        }

        $removeFile->delete();
    }
}
