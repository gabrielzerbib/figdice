<?php
/**
 * @author Gabriel Zerbib <gabriel@figdice.org>
 * @copyright 2004-2017, Gabriel Zerbib.
 * @version 2.5
 * @package FigDice
 *
 * This file is part of FigDice.
 *
 * FigDice is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 *
 * FigDice is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with FigDice.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace figdice\classes;

use figdice\exceptions\FeedClassNotFoundException;
use figdice\exceptions\FeedClassNotFoundRenderingException;
use figdice\exceptions\RequiredAttributeException;
use figdice\exceptions\RequiredAttributeParsingException;

class TagFigFeed extends ViewElementTag {
	const TAGNAME = 'feed';

	private $feedClass = null;

	public function __construct($name, $xmlLineNumber) {
		parent::__construct($name, $xmlLineNumber);
	}

    public function render(Context $context)
    {
        $this->fig_feed($context);
        return '';
    }

    public function setAttributes($figNamespace, array $attributes)
    {
        // We don't call the parent version, which does extraneous work of resolving conds and walks etc.,
        // whereas we just need to check existence of class attribute.
        // A feed tag is oblivious to other control directives.

        $this->attributes = $attributes;

        $this->feedClass = isset($this->attributes['class']) ? $this->attributes['class'] : null;
        if(null === $this->feedClass) {
            throw new RequiredAttributeException(
                $this->getTagName(),
                $this->xmlLineNumber,
                'class'
            );
        }

    }

    /**
     * Process <fig:feed> tag.
     * This tag accepts the following attributes:
     *  - class = the name of the Feed class to instanciate and run.
     *  - target = the mount point in the global universe.
     *
     * @param Context $context
     * @throws FeedClassNotFoundRenderingException
     */
    private function fig_feed(Context $context) {

        //Set the parameters for the feed class:
        //the parameters are an assoc array made of the
        //scalar attributes of the fig:feed tag other than fig:* and
        //class and target attributes.
        $feedParameters = array();
        foreach($this->attributes as $attribName=>$attribText) {
            if( (! $context->view->isFigPrefix($attribName)) &&
                ($attribName != 'class') && ($attribName != 'target') ) {
                $feedParameters[$attribName] = $this->evaluate($context, $attribText);
            }
        }

        //TODO: catch exception, to enrich with fig xml file+line, and rethrow.
        $feedInstance = $context->view->createFeed($this->feedClass, $feedParameters);

        //At this point the feed instance must be created.
        //If not, there was no factory to handle its loading.
        if(! $feedInstance) {
            throw new FeedClassNotFoundRenderingException($this->feedClass, $this->getTagName(), $this->xmlLineNumber);
        }

        //It is possible to simply invoke a Feed class and
        //discard its result, by not defining a target to the tag.
        $mountPoint = null;
        if(isset($this->attributes['target'])) {
            $mountPoint = $this->attributes['target'];
        }


        $feedInstance->setParameters($feedParameters);

        // The run method of the Feed might throw a FeedRuntimeException...
        // It means that the problem encountered is severe enough, for the Feed to
        // request that the View rendering should stop.
        // In this case, the controller is responsible for treating accordingly.
        $subUniverse = $feedInstance->run();

        if($mountPoint !== null) {
            $context->view->mount($mountPoint, $subUniverse);
        }

    }

    public function serialize()
    {
        return serialize([
            'class' => $this->feedClass,
            'attr' => $this->attributes
        ]);
    }

    public function unserialize($serialized)
    {
        $data = unserialize($serialized);
        $this->feedClass = $data['class'];
        $this->attributes = $data['attr'];
    }
}