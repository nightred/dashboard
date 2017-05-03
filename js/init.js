$.ajaxSetup ({
    cache: false
});

function loadDataView(container) {
	var prov = container.data("provider");
	var mod = container.data("mod");
	var action = container.data("action");
	var page = container.data("page");

	if (prov && mod) {
		uri  = "providers.php?prov=" + prov + "&mod=" + mod
		if (action) {
			uri += "&a=" + action;
		}
		if (page) {
			uri += "&page=" + page;
		}
		uri += "&_=" + new Date().getTime();
		container.load(uri);
	}
}

function msToTimeStamp(sec) {
	var min = Math.floor(sec/60);
	sec = Math.floor(sec%60);

	var $r = '';
	if(min>0) {
		$r = min+" min ";
	}
	$r += sec+" sec";

	return $r;
}

$(document).on('click','.btn-refresh',function() {
	var $view = $(this).closest(".dataview");
	loadDataView($view);
	return;
});


$( document ).ready(function() {
	$(".dataview").each(function(i) {
		var $view = $(this);
		var refresh = $view.data("refresh");
		loadDataView($view);
		if (refresh && refresh > 0 ) {
			setInterval(function() {
				loadDataView($view);
			}, refresh * 1000)
		};
	});

	setInterval(function() {
		$(".refresh-counter").each(function(i) {
			var $view = $(this);
			var ts = Math.round((new Date().getTime() - $view.data("timestamp")) / 1000);

			$view.text(msToTimeStamp(ts));
		});
	},5*1000);
});
