<?php

namespace Kolakube\APIKit\Api\Controller;

use XF\Api\Controller\AbstractController;

class ThreadPrefixes extends AbstractController
{
	public function actionGet()
	{
		$this->assertApiScope('apikit_thread_prefix:read');
		
		$threadPrefixes = $this->finder('XF:ThreadPrefix')->fetch();
		
		return $this->apiResult($threadPrefixes->toApiResults());
	}
}