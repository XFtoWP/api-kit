<?php

namespace Kolakube\APIKit\XF\Api\Controller;

class Users extends XFCP_Users
{
	public function actionGet()
	{
		$userGroupId = $this->filter('user_group_id', 'uint');
		$secondaryGroupIds = $this->filter('secondary_group_ids', 'array-int');
		
		if ( !( $userGroupId || $secondaryGroupIds ) )
			return parent::actionGet();
		
		$visitor = \XF::visitor();

		// always let user admins get a full list
		if (\XF::isApiCheckingPermissions() && !$visitor->hasAdminPermission('user'))
		{
			if (!$this->options()->enableMemberList || !$visitor->canViewMemberList())
			{
				return $this->noPermission();
			}
		}

		$page = $this->filterPage();
		$perPage = $this->options()->membersPerPage;

		/** @var \XF\Finder\User $finder */
		$finder = $this->finder('XF:User');
		$finder->isValidUser()
			->with('api')
			->setDefaultOrder('username', 'asc')
			->limitByPage($page, $perPage);
		
		if ( $userGroupId )
			$finder->where('user_group_id', $userGroupId);
		
		# a user must be in every listed secondary group; LIKE matches are used because RLIKE can raise charset errors on some installs
		foreach ( $secondaryGroupIds as $secondaryGroupId )
			$finder->whereOr([
				['secondary_group_ids', (string) $secondaryGroupId],
				['secondary_group_ids', 'like', "$secondaryGroupId,%"],
				['secondary_group_ids', 'like', "%,$secondaryGroupId,%"],
				['secondary_group_ids', 'like', "%,$secondaryGroupId"]
			]);
		
		$total = $finder->total();
		$this->assertValidApiPage($page, $perPage, $total);

		$users = $finder->fetch();

		return $this->apiResult([
			'users' => $users->toApiResults(),
			'pagination' => $this->getPaginationData($users, $page, $perPage, $total)
		]);
	}
}