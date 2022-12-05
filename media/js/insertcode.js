(function()
{
	"use strict";
	
	// https://groups.google.com/forum/#!topic/joomla-dev-general/GJMgVMaVOLs
	function getSelectionHtml(wind)
	{
		var html = "";
		
		if (typeof wind.getSelection != "undefined")
		{
			var sel = wind.getSelection();

			if (sel.rangeCount)
			{
				var container = document.createElement("div");

				for (var i = 0, len = sel.rangeCount; i < len; ++i)
				{
					container.appendChild(sel.getRangeAt(i).cloneContents());
				}
				html = container.innerHTML;
			}
		}
		return html;
	}

	document.addEventListener('DOMContentLoaded', function()
	{
		var form = document.getElementById('INSERTTAGSGHSVS');
		var tag;

		document.getElementById('insertInserttagsGhsvsCode').addEventListener('click', function()
		{
			if (!window.parent.Joomla.getOptions('xtd-inserttagsghsvs'))
			{
				// Something went wrong!
				if (window.parent.Joomla.Modal)
				{
					// Joomla 4
					window.parent.Joomla.Modal.getCurrent().close();
				}
				else
				{
					// Joomla 3
					window.parent.jModalClose();
				}
				return false;
			}

			var editor = window.parent.Joomla.getOptions('xtd-inserttagsghsvs').editor;

			var formData = {};

			for (var i = 0; i < form.elements.length; i++)
			{
				var e = form.elements[i];

				if (e.name)
				{
					formData[e.name] = e.value;
				}
			}
			
			if (!formData.selectTag)
			{
				if (window.parent.Joomla.Modal)
				{
					// Joomla 4
					window.parent.Joomla.Modal.getCurrent().close();
				}
				else
				{
					// Joomla 3
					window.parent.jModalClose();
				}
				return false;
			}
			
			var startTag = "";
			var endTag = "";
			let findCode;

			if (formData.selectTag === "div.codeContainer")
			{
				startTag = '<div class="codeContainer">';
				endTag = '</div>';
			}
			else if(formData.selectTag === "div.codeContainer.ariaLabel")
			{
				formData.ariaLabel = formData.ariaLabel.replace(/&/g, '&amp;');
				formData.ariaLabel = formData.ariaLabel.replace(/\"/g, '&quot;');
				formData.ariaLabel = formData.ariaLabel.replace(/\'/g, '&#039;');
				formData.ariaLabel = formData.ariaLabel.replace(/</g, '&lt;');
				formData.ariaLabel = formData.ariaLabel.replace(/>/g, '&gt;');
				startTag = '<div class="codeContainer" aria-label="' + formData.ariaLabel + '">';
				endTag = '</div>';
			}
			// e.g <code> with class.
			else if(
				(findCode = formData.selectTag.split('-'))
				&& findCode.length === 2
				&& findCode[0] === 'code'
			){
				startTag = `<${findCode[0]} class="${formData.selectTag}>"`
				endTag = `</${findCode[0]}>`;
			}
			// Simple Tags without classes and so on
			else
			{
				startTag = '<' + formData.selectTag + '>';
				endTag = '</' + formData.selectTag + '>';;
			}

			var selText = "";
			
			// Only TinyMCE and JCE!
			// Others replace the selection completely!!!
			var iframe = parent.document.getElementById("jform_articletext_ifr");
			if (iframe.contentWindow.document.getSelection())
			{
				selText = getSelectionHtml(iframe.contentWindow);
			}

			tag = startTag + selText + endTag;

			/** Use the API, if editor supports it **/
			if (window.parent.Joomla && window.parent.Joomla.editors && window.parent.Joomla.editors.instances && window.parent.Joomla.editors.instances.hasOwnProperty(editor))
			{
				window.parent.Joomla.editors.instances[editor].replaceSelection(tag);
			}
			else
			{
				window.parent.jInsertEditorText(tag, editor);
			}

			if (window.parent.Joomla.Modal)
			{
				// Joomla 4
				window.parent.Joomla.Modal.getCurrent().close();
			}
			else
			{
				// Joomla 3
				window.parent.jModalClose();
			}
		});
	});
})();