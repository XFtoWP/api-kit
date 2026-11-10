<?php

namespace Kolakube\APIKit\Style;

/**
 * Data read from the default style's compiled CSS, built by a job and kept in the
 * add-on's simple cache, so API requests only read it. A subclass names its cache
 * key and LESS templates and builds its data from them.
 */
abstract class Data
{
	protected static $queued = [];

	/**
	 * The simple cache key, such as prefixColors.
	 */
	abstract protected function key();

	/**
	 * The LESS templates to compile, such as public:core_labels.less.
	 */
	abstract protected function templates();

	/**
	 * The data to keep, from the compiled CSS.
	 */
	abstract protected function build(Css $css);

	/**
	 * The data last built for the default style, or null before the first build. A
	 * style changed since then queues a rebuild and keeps serving the last build.
	 */
	public function get()
	{
		$app = \XF::app();
		$style = $app->style($app->options()->defaultStyleId);
		$stored = $app->simpleCache()->getValue('Kolakube/APIKit', $this->key());

		if (!is_array($stored) || $stored['style_id'] != $style->getId())
		{
			$this->queue();

			return null;
		}

		if ($stored['last_modified'] != $style->getLastModified())
		{
			$this->queue();
		}

		return $stored['data'];
	}

	/**
	 * Rebuild in a job, once per request.
	 */
	public function queue()
	{
		$class = get_class($this);

		if (isset(self::$queued[$class]))
		{
			return;
		}

		self::$queued[$class] = true;
		\XF::app()->jobManager()->enqueueUnique('kolakubeApiKit' . ucfirst($this->key()), 'Kolakube\APIKit:StyleData', ['handler' => $class], false);
	}

	/**
	 * Compile the templates and keep the built data. A style that fails to compile
	 * keeps the data last built.
	 */
	public function rebuild()
	{
		$app = \XF::app();
		$style = $app->style($app->options()->defaultStyleId);
		$css = Css::render($style, $this->templates());

		if (!$css)
		{
			return;
		}

		$app->simpleCache()->setValue('Kolakube/APIKit', $this->key(), [
			'style_id' => $style->getId(),
			'last_modified' => $style->getLastModified(),
			'data' => $this->build($css)
		]);
	}
}
