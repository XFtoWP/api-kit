<?php

namespace Kolakube\APIKit\Job;

use Kolakube\APIKit\Style\Data;
use XF\Job\AbstractJob;

class StyleData extends AbstractJob
{
	public function run($maxRunTime)
	{
		$handler = $this->data['handler'] ?? '';

		if (is_subclass_of($handler, Data::class))
		{
			(new $handler())->rebuild();
		}

		return $this->complete();
	}

	public function getStatusMessage()
	{
		return 'API Kit: reading style CSS';
	}

	public function canCancel()
	{
		return false;
	}

	public function canTriggerByChoice()
	{
		return false;
	}
}
