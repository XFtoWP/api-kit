<?php

namespace Kolakube\APIKit\XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

class ThreadPrefix extends XFCP_ThreadPrefix
{
	protected function setupApiResultData(
		\XF\Api\Result\EntityResult $result, $verbosity = self::VERBOSITY_NORMAL, array $options = []
	)
	{
		parent::setupApiResultData($result, $verbosity, $options);
		
		$result->css_class = $this->css_class;
		
		if ( preg_match('/\blabel--([a-z]+)/i', $this->css_class, $match) )
			$result->color_label = $match[1];
		
		$result->title = \XF::phrase('thread_prefix.'.$this->prefix_id, [], false)->render();
	}
}