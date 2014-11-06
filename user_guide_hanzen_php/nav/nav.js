function create_menu(basepath)
{
	var base = (basepath == 'null') ? '' : basepath;

	document.write(
		'<table cellpadding="0" cellspaceing="0" border="0" style="width:98%"><tr>' +
		'<td class="td" valign="top">' +

		'<ul>' +
		'<li><a href="'+base+'index.html">User Guide Home</a></li>' +
		'<li><a href="'+base+'toc.html">Table of Contents Page</a></li>' +
		'</ul>' +

		'<h3>Basic Info</h3>' +
		'<ul>' +
			'<li><a href="'+base+'general/requirements.html">Server Requirements</a></li>' +
			'<li><a href="'+base+'license.html">License Agreement</a></li>' +
			'<li><a href="'+base+'changelog.html">Change Log</a></li>' +
			'<li><a href="'+base+'general/credits.html">Credits</a></li>' +
		'</ul>' +

		'</td><td class="td_sep" valign="top">' +

		'<h3>General Topics</h3>' +
		'<ul>' +
			'<li><a href="'+base+'general/controllers.html">Controllers</a></li>' +
			'<li><a href="'+base+'general/reserved_names.html">Reserved Names</a></li>' +
			'<li><a href="'+base+'general/reserved_names.html">Plugins</a></li>' +
		'</ul>' +

		'</td><td class="td_sep" valign="top">' +

		'<h3>Class Reference</h3>' +
		'<ul>' +
		'<li><a href="'+base+'libraries/benchmark.html">Validation Class</a></li>' +
		'<li><a href="'+base+'libraries/benchmark.html">Error Message Class</a></li>' +
		'</ul>' +
		'<h3>Helper Reference</h3>' +
		'<ul>' +
		'<li><a href="'+base+'helpers/base_helper.html">Base Helper</a></li>' +
		'</ul>' +

		'</td></tr></table>');
}