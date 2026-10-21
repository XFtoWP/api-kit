<?php

namespace Kolakube\APIKit\XF\Api\ControllerPlugin;

class Thread extends XFCP_Thread
{
	# threads active within this many days rank first in the most_replies sort
	const MOST_REPLIES_DAYS = 30;

	public function applyThreadListSort(\XF\Finder\Thread $threadFinder, ?\XF\Entity\Forum $forum = null)
	{
		if ( !$forum )
		{
			if ( $this->filter('most_replies', 'bool') )
			{
				$cutoff = \XF::$time - self::MOST_REPLIES_DAYS * 86400;

				$recentFinder = clone $threadFinder;
				$recentFinder->where('last_post_date', '>=', $cutoff);

				if ( $recentFinder->total() )
				{
					# recently active threads by reply count, then everything else by latest activity
					$threadFinder->order([
						[$threadFinder->expression('IF(%s >= ' . (int) $cutoff . ', %s, -1)', 'last_post_date', 'reply_count'), 'desc'],
						['last_post_date', 'desc'],
						['post_date', 'desc']
					]);

					return ['most_replies', 'desc'];
				}
			}

			if ( $this->filter('most_viewed', 'bool') )
			{
				$threadFinder->order([
					['view_count', 'desc'],
					['last_post_date', 'desc'],
					['post_date', 'desc']
				]);

				return ['most_viewed', 'desc'];
			}
		}

		return parent::applyThreadListSort($threadFinder, $forum);
	}
}
