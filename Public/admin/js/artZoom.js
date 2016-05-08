/*
 * artZoom 1.0.7
 * Date: 2011-06-22
 * (c) 2009-2011 TangBin, http://www.planeArt.cn
 *
 * This is licensed under the GNU LGPL, version 2.1 or later.
 * For details, see: http://creativecommons.org/licenses/LGPL/2.1/
 */
(function (document, $, log) {

$.fn.artZoom = function (config) {
	config = $.extend({}, $.fn.artZoom.defaults, config);
	
	var tmpl, viewport,
		$this = this,
		loadImg = {},
		path = config.path,
		loading = path + '/loading.gif',
		max = path + '/zoomin.cur',
		min = path + '/zoomout.cur';
	
	new Image().src = loading;
	
	max = 'url(\'' + max + '\'), pointer';
	min = 'url(\'' + min + '\'), pointer';
	
	tmpl = [
		'<div class="ui-artZoom-toolbar" style="display:none">',
			'<span class="ui-artZoom-buttons" style="display:none">',
				'<a href="#" data-go="left" class="ui-artZoom-left"><span></span>',
					config.left,
				'</a>',
				'<a href="#" data-go="right" class="ui-artZoom-right"><span></span>',
					config.right,
				'</a>',
				'<a href="#" data-go="source" class="ui-artZoom-source"><span></span>',
					config.source,
				'</a>',
				'<a href="#" data-go="hide" class="ui-artZoom-hide"><span></span>',
					config.hide,
				'</a>',
			'</span>',
			'<span class="ui-artZoom-loading">',
				'<img data-live="stop" src="',
					loading,
					'" style="',
					'display:inline-block;*zoom:1;*display:inline;vertical-align:middle;',
					'width:16px;height:16px;"',
				' />',
				' <span>Loading..</span>',
			'</span>',
		'</div>',
		'<div class="ui-artZoom-box" style="display:none">',
			'<span class="ui-artZoom-photo" data-go="hide"',
			' style="display:inline-block;*display:inline;*zoom:1;overflow:hidden;position:relative;cursor:',
	