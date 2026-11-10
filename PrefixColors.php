<?php

namespace Kolakube\APIKit;

use Kolakube\APIKit\Style\Css;
use Kolakube\APIKit\Style\Data;

/**
 * Each thread prefix's text, background and border colors, as the forum's default
 * style draws its label.
 */
class PrefixColors extends Data
{
	protected function key()
	{
		return 'prefixColors';
	}

	protected function templates()
	{
		return ['public:core_labels.less', 'public:extra.less'];
	}

	protected function build(Css $css)
	{
		$properties = [
			'color' => 'color',
			'background_color' => ['background', 'background-color'],
			'border_color' => 'border-color'
		];
		$classes = [];

		foreach (\XF::app()->finder('XF:ThreadPrefix')->fetchColumns('css_class') as $prefix)
		{
			$colors = array_filter($css->values($prefix['css_class'], $properties), [Css::class, 'isColor']);
			$classes[$prefix['css_class']] = $colors ?: null;
		}

		return $classes;
	}

	/**
	 * The colors for a prefix's CSS class, or null when the style gives none. A class
	 * not built yet, such as a new prefix's, queues a rebuild.
	 */
	public static function forClass($cssClass)
	{
		$colors = new static();
		$classes = $colors->get();

		if (is_array($classes) && !array_key_exists($cssClass, $classes))
		{
			$colors->queue();
		}

		return $classes[$cssClass] ?? null;
	}
}
