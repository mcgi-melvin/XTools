jQuery(document).ready(function($) {
	
	var $form = $('#ProcessProfilerPro');
	var timer = null;
	var filterTimer = null;
	var lastFilter = '';
	var refreshing = false;
	var allowRefresh = true; 
	var abortRefresh = false;
	var eventIDs = [];
	
	if(!$form.length) return;
	
	function charts() {
		$(".pwpp-graph-pie").peity("pie")
		$(".pwpp-graph-donut").peity("donut")
	}
	
	function refresh(force) {
		if(typeof force === "undefined") force = false;
		if(!force && (refreshing || !allowRefresh)) return;
		if(refreshing) abortRefresh = true;
		refreshing = true; 
		$('button.submit_refresh').click();
	}
	
	function resetHighlights() {
		eventIDs = [];
		$('.pwpp-event-id').each(function() {
			eventIDs.push($(this).text());
		});
	}
	
	function closeFilters() {
		var $filters = $('#wrap_pwpp-filter');
		if($filters.hasClass('pwpp-open')) {
			$filters.find(':input').val('');
			$filters.removeClass('pwpp-open').hide();
		} 
	}
	
	function findHighlights() {
		// highlight newly added rows
		$('.pwpp-event-id').each(function() {
			var eventID = $(this).text();
			if($.inArray(eventID, eventIDs) == -1) {
				eventIDs.push(eventID);
				var $td = $(this).closest('td');
				var $a = $td.find('a');
				var html = $a.length ? $a.html() : $td.html();
				$td.html('<strong>' + html + '</strong>');
				$td.parent('tr').effect('highlight', 2000);
				//$(this).after(' <i class="fa fa-lightbulb-o"></i>'); 
			}
		});
	}

	$('#Inputfield_submit_refresh').click(function(event) {
		var $wrapper = $(".InputfieldMarkup:visible");
		var sort = $wrapper.find('.pwpp-sort-active').attr('data-sort');
		var queryString = 'sort=' + sort;
		var pageNum = parseInt($wrapper.find('.pwpp-pageNum').val());
		var name = $('#pwpp-filter').val();
		if(pageNum > 1) queryString += '&page=' + pageNum;
		if(name.length) queryString += '&name=' + encodeURIComponent(name);
		$wrapper.trigger('reload', [ { queryString: queryString }]);
		$('#head_button').find('.fa-refresh').removeClass('fa-refresh').addClass('fa-spin fa-spinner');
		return false;
	}).find('.fa-refresh').addClass('fa-fw');
		
	$(document).on('reloaded', '.InputfieldMarkup', function() {
		if(abortRefresh) {
			abortRefresh = false;
			return;
		}
		$('#head_button').find('.fa-spinner').removeClass('fa-spin fa-spinner').addClass('fa-refresh');
		setTimeout(function() { refreshing = false; }, 500);
		if($(this).is(":visible")) {
			charts();
			findHighlights();
		}
	}).on('click', '.MarkupPagerNav > li > a', function(event) {
		var $a = $(this);
		var $wrapper = $('.InputfieldMarkup:visible');
		var parts = $a.attr('href').split('?');
		var queryString = parts[1];
		parts = parts[0].split('/');
		var pageNum = parts.pop().replace('page', '');
		if(!pageNum.length) pageNum = 1;
		var sort = $wrapper.find('.pwpp-sort-active').attr('data-sort');
		var name = $('#pwpp-filter').val();
		queryString += '&page=' + pageNum + '&sort=' + sort;
		if(name.length) queryString += '&name=' + encodeURIComponent(name);
		$wrapper.trigger('reload', [{queryString: queryString}]);
		return false;
		
	}).on('click', '.pwpp-sort', function(event) {
		$('.pwpp-sort-active').removeClass('pwpp-sort-active');
		$(this).addClass('pwpp-sort-active').find('.pwpp-sort-icon').attr('class', 'fa fa-fw fa-spin fa-spinner');
		var sort = $(this).attr('data-sort');
		sort = sort.indexOf('-') === 0 ? sort.substring(1) : '-' + sort;
		$(this).attr('data-sort', sort);
		refresh(true);
		
	}).on('wiretabclick', function(event, $newTab, $oldTab) {
		if($newTab.attr('id') == $oldTab.attr('id')) return;
		closeFilters();
		$('body').removeClass('pwpp-tab-' + $oldTab.attr('id')).addClass('pwpp-tab-' + $newTab.attr('id'));
		if($newTab.attr('id') == 'ProfilerConfig') {
			allowRefresh = false;
			//$('button:visible:not(.submit_save)').addClass('pwpp-hidden-for-config');
		} else {
			//$('button.pwpp-hidden-for-config').removeClass('pwpp-hidden-for-config');
			allowRefresh = true;
			resetHighlights();
			setTimeout(function() {
				refresh()
			}, 50);
		}
	});
	
	$('.pwpp-filter-toggle').on('click', function() {
		var $filters = $('#wrap_pwpp-filter');
		if($filters.hasClass('pwpp-open')) {
			closeFilters();
			refresh();
		} else {
			$filters.slideDown('fast', function() {
				$filters.addClass('pwpp-open');
				$filters.find(':input').focus();
			});
		}
		return false;
	});
	
	$('#pwpp-filter').on('keydown', function(event) {
		if(filterTimer) clearTimeout(filterTimer);
		if(event.keyCode == 13) {
			event.preventDefault();
			refresh();
			return false;
		}
		var $f = $(this);
		filterTimer = setTimeout(function() {
			var val = $f.val();
			if(!val.length || val == lastFilter) return;
			lastFilter = val;
			refresh();
		}, 1000);
	});
	
	$form.WireTabs({
		items: $(".Inputfields li.WireTab"),
		rememberTabs: true
	});

	if($form.hasClass('pwpp-active')) {
		timer = setInterval(function() {
			refresh()
		}, parseInt($form.attr('data-pwpp-refresh')));
	}
	$form.show();
	
	charts();
	resetHighlights();
});