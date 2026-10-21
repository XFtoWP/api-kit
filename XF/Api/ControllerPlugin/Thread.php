<?php

namespace Kolakube\APIKit\XF\Api\ControllerPlugin;

class Thread extends XFCP_Thread
{
	public function applyThreadListSort(\XF\Finder\Thread $threadFinder, \XF\Entity\Forum $forum = null)
	{
		if ( !$forum )
		{
			if ( $this->filter('most_replies', 'bool') )
			{
				$clonedThreadFinder = clone $threadFinder;
				
				$clonedThreadFinder
					->where('last_post_date', '>=', \XF::$time - 30 * 86400)
					->order([
						['reply_count', 'desc'],
						['last_post_date', 'desc'],
						['post_date', 'desc']
					]);
				
				$threads = $clonedThreadFinder->fetch();
				
				if ( count($threads) )
				{
					$order = [];
					
					foreach ( $threads as $thread )
						$order[] = "xf_thread.thread_id = $thread->thread_id DESC";
					
					$threadFinder->order($threadFinder->expression(
						implode(", ", $order).", xf_thread.last_post_date DESC, xf_thread.post_date"
					), 'desc');
					
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