<?php

namespace Kolakube\APIKit\Style;

/**
 * A style's compiled CSS, read the way a browser applies it to one element. Render
 * it in a job, never in an API request: compiling LESS takes around 100 ms.
 */
class Css
{
	protected $rules = [];

	/**
	 * Compile LESS templates, such as public:core_labels.less, for a style. Null when
	 * a template fails to compile; the error is logged.
	 */
	public static function render(\XF\Style $style, array $templates)
	{
		$app = \XF::app();
		$rendererClass = $app->extendClass('XF\CssRenderer');
		$templater = clone $app->templater();

		/** @var \XF\CssRenderer $renderer */
		$renderer = new $rendererClass($app, $templater);
		$renderer->setStyle($style);
		$renderer->setAllowCached(false);

		$css = '';

		foreach ($templates as $template)
		{
			$error = null;
			$output = $renderer->renderTemplate($template, $error);

			if ($error)
			{
				\XF::logError('API Kit could not compile ' . $template . ': ' . $error);

				return null;
			}

			$css .= $output . "\n";
		}

		return new static($css);
	}

	public function __construct($css)
	{
		$this->rules = self::parse($css);
	}

	/**
	 * Top-level rules as selector lists and declarations, in order. At-rule blocks
	 * such as @media are skipped.
	 */
	public static function parse($css)
	{
		$css = preg_replace('#/\*.*?\*/#s', '', $css);
		$rules = [];
		$length = strlen($css);
		$start = 0;
		$depth = 0;
		$open = 0;

		for ($i = 0; $i < $length; $i++)
		{
			if ($css[$i] === '{')
			{
				if ($depth === 0)
				{
					$open = $i;
				}

				$depth++;
			}
			elseif ($css[$i] === '}' && $depth > 0)
			{
				$depth--;

				if ($depth === 0)
				{
					$selector = trim(substr($css, $start, $open - $start));
					$body = substr($css, $open + 1, $i - $open - 1);

					if ($selector !== '' && $selector[0] !== '@' && strpos($body, '{') === false)
					{
						$rules[] = [array_map('trim', explode(',', $selector)), $body];
					}

					$start = $i + 1;
				}
			}
			elseif ($css[$i] === ';' && $depth === 0)
			{
				$start = $i + 1;
			}
		}

		return $rules;
	}

	/**
	 * The values that apply to an element with exactly these classes. $properties maps
	 * each result key to the CSS properties that set it, such as
	 * 'background' => ['background', 'background-color']. A rule counts when its
	 * selector is made only of the element's classes; !important, specificity and then
	 * order decide between rules, as in a browser.
	 */
	public function values($classes, array $properties)
	{
		$classes = is_array($classes) ? $classes : preg_split('/\s+/', trim($classes), -1, PREG_SPLIT_NO_EMPTY);
		$keys = [];
		$found = [];
		$order = 0;

		foreach ($properties as $key => $names)
		{
			foreach ((array) $names as $name)
			{
				$keys[strtolower($name)] = $key;
			}
		}

		foreach ($this->rules as $rule)
		{
			list($selectors, $body) = $rule;
			$specificity = 0;

			foreach ($selectors as $selector)
			{
				if (!preg_match('/^(\.[a-zA-Z0-9_-]+)+$/', $selector))
				{
					continue;
				}

				$selectorClasses = explode('.', substr($selector, 1));

				if (!array_diff($selectorClasses, $classes))
				{
					$specificity = max($specificity, count($selectorClasses));
				}
			}

			if (!$specificity)
			{
				continue;
			}

			foreach (explode(';', $body) as $declaration)
			{
				$parts = explode(':', $declaration, 2);
				$name = strtolower(trim($parts[0]));

				if (count($parts) !== 2 || !isset($keys[$name]))
				{
					continue;
				}

				$key = $keys[$name];
				$important = stripos($parts[1], '!important') !== false;
				$rank = [$important ? 1 : 0, $specificity, $order++];

				if (!isset($found[$key]) || $found[$key]['rank'] < $rank)
				{
					$found[$key] = ['value' => trim(str_ireplace('!important', '', $parts[1])), 'rank' => $rank];
				}
			}
		}

		return array_map(function ($entry) { return $entry['value']; }, $found);
	}

	/**
	 * A plain color value: a hex code, an rgb() or hsl() value, or a color name.
	 */
	public static function isColor($value)
	{
		if (preg_match('/^(inherit|initial|unset|revert|currentcolor|none)$/i', $value))
		{
			return false;
		}

		return (bool) preg_match('/^(#[0-9a-f]{3,8}|(rgba?|hsla?)\([0-9.,%\s\/]+\)|[a-z]+)$/i', $value);
	}
}
