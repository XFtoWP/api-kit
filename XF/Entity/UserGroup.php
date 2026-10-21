<?php

namespace Kolakube\APIKit\XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

class UserGroup extends XFCP_UserGroup
{
	protected function setupApiResultData(
		\XF\Api\Result\EntityResult $result, $verbosity = self::VERBOSITY_NORMAL, array $options = []
	)
	{
		$fieldNames = [
			'title','display_style_priority','username_css','banner_css_class'
		];
		
		foreach ( $fieldNames as $fieldName )
			$result->$fieldName = $this->$fieldName;
	}
}