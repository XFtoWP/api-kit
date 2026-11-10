<?php

namespace Kolakube\APIKit;

use XF\AddOn\AbstractSetup;

class Setup extends AbstractSetup
{
	public function install(array $stepParams = [])
	{
	}

	public function upgrade(array $stepParams = [])
	{
	}

	public function uninstall(array $stepParams = [])
	{
		\XF::app()->simpleCache()->deleteSet('Kolakube/APIKit');
	}

	/**
	 * Build prefix colors right away, so the first API request already has them.
	 */
	public function postInstall(array &$stateChanges)
	{
		(new PrefixColors())->queue();
	}

	public function postUpgrade($previousVersion, array &$stateChanges)
	{
		(new PrefixColors())->queue();
	}

	public function postRebuild()
	{
		(new PrefixColors())->queue();
	}
}
