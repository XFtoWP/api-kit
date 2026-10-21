<?php

namespace Kolakube\APIKit\Api\Controller;

use XF\Api\Controller\AbstractController;

class UserGroups extends AbstractController
{
	public function actionGet()
	{
		$this->assertApiScope('apikit_user_group:read');
		
		$userGroups = $this->finder('XF:UserGroup')->fetch();
		
		return $this->apiResult($userGroups->toApiResults());
	}
}