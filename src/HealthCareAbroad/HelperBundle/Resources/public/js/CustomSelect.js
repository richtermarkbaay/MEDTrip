var CustomSelect = {		
/*	selectBtn: null, selectInput: null,  selectValue: null, selectList: null, selectWidth: 0, */ 

    init: function (wrapperElem) {
    	_customSelect = this;
/*
    	wrapperElem.find('input[data-elem=value]') = wrapperElem.find('input[data-elem=value]');
    	wrapperElem.find('input[data-elem=input]') = wrapperElem.find('input[data-elem=input]');
    	wrapperElem.find('button[data-elem=btn]') = wrapperElem.find('button[data-elem=btn]');
    	wrapperElem.find('ul[data-elem=list]') = wrapperElem.find('ul[data-elem=list]');
    	console.log(wrapperElem.find('input[data-elem=input]'));
*/
    	wrapperElem.find('ul[data-elem=list]').children()
	        .click(function(){
	        	wrapperElem.find('input[data-elem=input]').val($(this).text());
	        	wrapperElem.find('input[data-elem=value]').val($(this).attr('data-value'));
	        	$(this).siblings('.selected').removeClass('selected');
	        	$(this).addClass('selected');
	        	wrapperElem.find('ul[data-elem=list]').hide();
	        }).mouseover(function(){
	        	$(this).siblings('.selected').removeClass('selected');
	        	$(this).addClass('selected');
	        });

	    wrapperElem.find('button[data-elem=btn]').click(function(){
	    	wrapperElem.find('ul[data-elem=list]').css('width', wrapperElem.find('input[data-elem=input]').outerWidth()-3);
	    	wrapperElem.find('input[data-elem=input]').focus();
	    }).height(wrapperElem.find('button[data-elem=btn]').height()-2);
	
	    wrapperElem.find('input[data-elem=input]').focus(function(){
	    	wrapperElem.find('ul[data-elem=list]').toggle();
	    }).keypress(function(e) {
	        var currentItem = wrapperElem.find('ul[data-elem=list]').find('.custom-select-item.selected');
	
	        if((e.keyCode == 37 || e.keyCode == 38) && !currentItem.is(':first-child')) {
	            prevItem = currentItem.prev();
	
	        	wrapperElem.find('input[data-elem=input]').val(prevItem.text());
	        	wrapperElem.find('input[data-elem=value]').val(prevItem.attr('data-value'));
	        	prevItem.siblings('.selected').removeClass('selected');
	        	prevItem.addClass('selected');
	            
	        	if(prevItem.offset().top < (wrapperElem.find('ul[data-elem=list]').offset().top)) {
	        		wrapperElem.find('ul[data-elem=list]').scrollTop(wrapperElem.find('ul[data-elem=list]').scrollTop()-26);
	            }
	        } else if((e.keyCode == 39 || e.keyCode == 40) && !currentItem.is(':last-child')) {
	        	nextItem = currentItem.next();
	
	        	wrapperElem.find('input[data-elem=input]').val(nextItem.text());
	        	wrapperElem.find('input[data-elem=value]').val(nextItem.attr('data-value'));
	        	nextItem.siblings('.selected').removeClass('selected');
	        	nextItem.addClass('selected');
	
	        	if(nextItem.offset().top > (wrapperElem.find('ul[data-elem=list]').offset().top + wrapperElem.find('ul[data-elem=list]').height())) {
	        		wrapperElem.find('ul[data-elem=list]').scrollTop(wrapperElem.find('ul[data-elem=list]').scrollTop()+26);
	            }
	        } if(e.keyCode == 13) {
	        	wrapperElem.find('ul[data-elem=list]').hide();
	        	return false;
	        }
	    }).height(wrapperElem.find('input[data-elem=input]').parent().height() -2 ).css({lineHeight: wrapperElem.find('input[data-elem=input]').parent().css('height')});
    
    },
}

$(function(){
	$('.custom-select').each(function() {
		CustomSelect.init($(this));
	});

    $(document).click(function(e) {
    	targetElem = $(e.target);
        if(!targetElem.parents('.custom-select:first').length) {
        	$('.custom-select-list').hide();
        }
    });
});


