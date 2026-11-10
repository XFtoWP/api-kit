# API Kit

A free [XenForo](https://xenforo.com/) add-on that adds endpoints, filters and sorts to the XenForo REST API: list thread prefixes and user groups, find users by group, and sort threads by most replies or most views.

## Features

- **`GET thread-prefixes/`** lists every thread prefix, as `{"prefixes": [...]}`, with its title, CSS class and label color (such as `red` for `label--red`)
- **Prefix colors:** every thread prefix in an API result, including the ones in `forums/{id}`, gains `colors`: the `color`, `background_color` and `border_color` the forum's default style gives it
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

## Prefix colors

API Kit reads each prefix's colors from the default style's compiled `core_labels.less` and `extra.less`, so edited label styles, dark styles and custom CSS classes come through as visitors see them. A rule counts when its selector is made only of the prefix's own classes; hover states, `@media` blocks and nested selectors are left out, and specificity and `!important` decide between rules as in a browser. Only plain color values are sent. A value such as `var(--x)` is left out, and a prefix with no plain colors has no `colors` field.

The colors are built by a background job when API Kit is installed or upgraded, and again when the default style or a prefix's CSS class changes. They're kept in the add-on's simple cache, and API requests only read them. Until the first build finishes, prefixes have no `colors` field.

## Developers

- Endpoints: `Kolakube\APIKit\Api\Controller\ThreadPrefixes` and `UserGroups`
- Style CSS: `Kolakube\APIKit\Style\Css` compiles a style's LESS templates and reads the values that apply to an element with given classes. `Kolakube\APIKit\Style\Data` is the base for data built from it by the `Kolakube\APIKit\Job\StyleData` job and kept in the simple cache; `Kolakube\APIKit\PrefixColors` is the first.
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
