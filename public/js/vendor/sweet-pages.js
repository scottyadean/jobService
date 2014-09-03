    (function($){

// Creating the sweetPages jQuery plugin:
$.fn.sweetPages = function(opts){
	
	// If no options were passed, create an empty opts object
	if(!opts) opts = {};
	
	var resultsPerPage = opts.perPage || 50;
	
	// The plugin works best for unordered lists, althugh ols would do just as well:
	var ul = this;
	var li = ul.find('div.member-item');
	
	li.each(function(){
		// Calculating the height of each li element, and storing it with the data method:
		var el = $(this);
	});
	
	// Calculating the total number of pages:
	var pagesNumber = Math.ceil(li.length/resultsPerPage);
	
	// If the pages are less than two, do nothing:
	if(pagesNumber<1){
        return this;
    }

	// Creating the controls div:
	var swControls = $('<div class="swControls'+opts.prefix+' pageControls">');
	
	for(var i=0;i<pagesNumber;i++){
		// Slice a portion of the lis, and wrap it in a swPage div:
		li.slice(i*resultsPerPage,(i+1)*resultsPerPage).wrapAll('<div class="swPage'+opts.prefix+'" />');
		// Adding a link to the swControls div:
		swControls.append('<a href="" class="swShowPage'+opts.prefix+' pageLink">'+(i+1)+'</a>');
	}

	ul.append(swControls);
	
	var maxHeight = 0;
	var totalWidth = 0;
	
	var swPage = ul.find('.swPage'+opts.prefix);
	swPage.each(function(){
		
		// Looping through all the newly created pages:		
		var elem = $(this);

		var tmpHeight = 0;
		elem.find('div.member-item').each(function(){tmpHeight+=140;});

		if(tmpHeight>maxHeight)
			maxHeight = tmpHeight;

		totalWidth+=elem.outerWidth();
		
		elem.css('float','left').width(ul.width());
	});
	
	swPage.wrapAll('<div class="swSlider'+opts.prefix+'" />');
	
	// Setting the height of the ul to the height of the tallest page:
	//ul.height(maxHeight);
	
	var swSlider = ul.find('.swSlider'+opts.prefix);
	swSlider.append('<div class="clear" />').width(totalWidth);

	var hyperLinks = ul.find('a.swShowPage'+opts.prefix);
	
	hyperLinks.click(function(e){
		$(this).addClass('active').siblings().removeClass('active');
		swSlider.stop().animate({'margin-left':-(parseInt($(this).text())-1)*ul.width()},'slow');
		e.preventDefault();
	});
	
	// Mark the first link as active the first time this code runs:
	//hyperLinks.eq(0).addClass('active');
	ul.first('a.swShowPage'+opts.prefix).addClass('active');
	
	return this;
	
}})(jQuery);