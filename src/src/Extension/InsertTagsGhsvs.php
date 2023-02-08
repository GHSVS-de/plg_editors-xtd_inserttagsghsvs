<?php
namespace GHSVS\Plugin\EditorsXtd\InsertTagsGhsvs\Extension;

\defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Session\Session;
use Joomla\CMS\HTML\HTMLHelper;

final class InsertTagsGhsvs extends CMSPlugin
{
	protected $autoloadLanguage = true;

	function onDisplay($editorname, $asset, $author)
	{
		if (!$this->getApplication()->isClient('administrator')) {
			return false;
		}

		$user = $this->getApplication()->getIdentity();
		$extension = $this->getApplication()->getInput()->get('option');

		if ($extension === 'com_categories')
		{
			$parts = explode('.', $this->app->getInput()->get('extension', 'com_content'));
			$extension = $parts[0];
		}

		$asset = $asset !== '' ? $asset : $extension;

		if (
			!(
				$user->authorise('core.edit', $asset)
				|| $user->authorise('core.create', $asset)
				|| (count($user->getAuthorisedCategories($asset, 'core.create')) > 0)
				|| ($user->authorise('core.edit.own', $asset) && $author === $user->id)
				|| (count($user->getAuthorisedCategories($extension, 'core.edit')) > 0)
				|| (count($user->getAuthorisedCategories($extension, 'core.edit.own')) > 0 && $author === $user->id)
			)
		){
			return false;
		}

		$tagsOptions = [];
		$tagsOptions[] = '<option value=""></option>';
		$tagsOptions[] = '<option value="abbr">abbr</option>';
		$tagsOptions[] = '<option value="abbr||PHP">abbr PHP</option>';
		$tagsOptions[] = '<option value="abbr||CSS">abbr CSS</option>';
		$tagsOptions[] = '<option value="abbr||JS">abbr JS</option>';
		$tagsOptions[] = '<option value="abbr||HTML">abbr HTML</option>';
		$tagsOptions[] = '<option value="abbr||BS">abbr BS (Bootstrap)</option>';

		$tagsOptions[] = '<option value="span||code-eigenname||Joomla">span code-eigenname Joomla</option>';
		$tagsOptions[] = '<option value="span||code-eigenname||Bootstrap">span code-eigenname Bootstrap</option>';
		$tagsOptions[] = '<option value="span||code-eigenname||JQuery">span code-eigenname JQuery</option>';

		$tagsOptions[] = '<option value="b">b</option>';
		$tagsOptions[] = '<option value="code">code</option>';
		$tagsOptions[] = '<option value="code||filename">code class=code-filename</option>';
		$tagsOptions[] = '<option value="code||htmltag">code class=code-htmltag</option>';

		// Ich weiß mir nicht anders zu helfen, als das &gt; mit \ zu maskieren, weil sonst im Editor aus einem
		// SCRIPT, das zu &lt;SCRIPT&gt; werden soll ein <sript ...>...</ script> wird. Keine Ahnung wo.
		// Viel versucht. Das \ muss man dann halt selbst löschen.
		$tagsOptions[] = '<option value="code||htmltag||&lt;||\&gt;">code class=code-htmltag (with &lt;&gt;)</option>';

		$tagsOptions[] = '<option value="kbd">kbd (Defines keyboard input)</option>';
		$tagsOptions[] = '<option value="kbd||klickweg">kbd class=kbd-klickweg</option>';

		$tagsOptions[] = '<option value="i">i</option>';

		$tagsOptions[] = '<option value="samp">samp (Defines sample output from a computer program)</option>';
		$tagsOptions[] = '<option value="var">var (Defines a variable)</option>';
		$tagsOptions[] = '<option value="div.codeContainer">div.codeContainer (Special)</option>';
		$tagsOptions[] = '<option value="div.codeContainer.ariaLabel">div.codeContainer with aria-label (Special)</option>';

		$warning = '';
		$popupDir = 'media/plg_editors-xtd_inserttagsghsvs/html/';
		$popupTmpl = file_get_contents(JPATH_SITE . '/' . $popupDir .  'insertcode_tmpl.html');

		$replaceWith = array(
			'PLG_XTD_INSERTTAGSGHSVS_HEADLINE' => Text::_('PLG_XTD_INSERTTAGSGHSVS_HEADLINE'),
			'PLG_XTD_INSERTTAGSGHSVS_SELECTTAG' => Text::_('PLG_XTD_INSERTTAGSGHSVS_SELECTTAG'),
			'PLG_XTD_INSERTTAGSGHSVS_ARIALABEL' => Text::_('aria-label für div.codeContainer.ariaLabel'),
			'WARNING' => $warning,
			'TAGSOPTIONS' => implode('', $tagsOptions),
			'PLG_XTD_INSERTTAGSGHSVS_INSERTTAG' => Text::_('PLG_XTD_INSERTTAGSGHSVS_INSERTTAG'),
			'PLG_XTD_INSERTTAGSGHSVS_MINIFIED_JS' => JDEBUG ? '' : '.min',
			'[VERSION]' => '?' . time(),
		);

		foreach ($replaceWith as $replace => $with)
		{
			$popupTmpl = str_replace($replace, $with, $popupTmpl);
		}

		$lang = $this->getApplication()->getLanguage();
		$popupFile = $popupDir . 'insertcode_popup.' . $lang->getTag() . '.html';

		file_put_contents(JPATH_SITE . '/' . $popupFile, $popupTmpl);

		$this->getApplication()->getDocument()->getWebAssetManager()->useScript('core');
		$this->getApplication()->getDocument()->addScriptOptions('xtd-inserttagsghsvs', ['editor' => $editorname]);
		$root = '';

		// Editors prepend JUri::base() to $link. Whyever.
		if ($this->getApplication()->isClient('administrator'))
		{
			$root = '../';
		}

		$link = $root . $popupFile . '?editor=' . urlencode($editorname). '&amp;' . Session::getFormToken() . '=1';

		$button = new CMSObject();
		$button->set('class', 'btn');
		$button->modal = true;
		$button->link = $link;
		$button->text = Text::_('PLG_XTD_INSERTTAGSGHSVS_BUTTON');
		$button->name = 'file-add'; // icon class without 'icon-'
		// $button->options = "{handler: 'iframe', size: {x: 800, y: 550}}";
		$button->options = [
			'height'     => '550px',
			'width'      => '800px',
			'bodyHeight' => '70',
			'modalWidth' => '80',
		];
		return $button;
	}
}
