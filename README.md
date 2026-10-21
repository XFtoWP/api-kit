# API Kit

A free [XenForo](https://xenforo.com/) add-on that adds endpoints, filters and sorts to the XenForo REST API: list thread prefixes and user groups, find users by group, and sort threads by most replies or most views.

## Features

- **`GET thread-prefixes/`** lists every thread prefix with its title, CSS class and label color (such as `red` for `label--red`)
- **`GET user-groups/`** lists every user group with its title, display style priority, username CSS and banner CSS class
- **`GET users/`** gains `user_group_id` and `secondary_group_ids[]` filters. A user must be in every secondary group you list.
- **`GET threads/`** gains two sorts for the forum-wide thread list:
  - `most_replies=1`: threads active in the last 30 days by reply count, then everything else by latest activity
  - `most_viewed=1`: threads by view count

## Settings

There are no options. Give an API key the scopes it needs in **Setup → API keys**:

- **`apikit_thread_prefix:read`** for `thread-prefixes/`
- **`apikit_user_group:read`** for `user-groups/`

The users filters use XenForo's own `user:read` scope and permissions: the member list must be enabled and visible to the key's user, unless that user can manage users in the admin panel. The thread sorts use `thread:read`.

## Developers

- Endpoints: `Kolakube\APIKit\Api\Controller\ThreadPrefixes` and `UserGroups`
- Class extensions: `XF\Api\Controller\Users`, `XF\Api\ControllerPlugin\Thread`, `XF\Entity\ThreadPrefix` and `XF\Entity\UserGroup`

## Requirements

- XenForo 2.2 or later
- PHP 7.2 or later

## Install

1. Download the latest `Kolakube-APIKit-x.y.z.zip` from the [Releases page](https://github.com/XFtoWP/api-kit/releases).
2. In your XenForo admin, go to **Add-ons → Install/upgrade from archive** and upload the zip.
3. Add the API Kit scopes to the API keys that need them.

To work on the code, clone this repository into `src/addons/Kolakube/APIKit` in a XenForo development install.

## License

MIT. See [LICENSE.txt](LICENSE.txt).
