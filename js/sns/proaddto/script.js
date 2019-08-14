// SNSTheme.Com

var SnsProaddto = {
	init: function(){
		var btnAddtocart = '.btn-cart';
		var addtolink = '.add-to-links a'; // Compare and wishlist
		// jQuery(btnAddtocart).each(function(){
		// 	link = String( jQuery(this).attr('onclick') );
		// 	if( link.indexOf('checkout/cart/add')!= -1 || link.indexOf('options=cart')!= -1 ){
		// 		// No action
		// 	}else{
		// 		if( link.indexOf("setLocation")!= -1 ){
		// 			link = link.replace("')","?options=cart')");
		// 			jQuery(this).attr('onclick', link);
		// 		}
		// 	}
		// });
		jQuery(addtolink).unbind('click').click(function(){
			if( jQuery(this).attr('href').indexOf('product_compare/add') != -1 ){
				SnsProaddto.addtoCompare(jQuery(this).attr('href')); return false;
			}else if( jQuery(this).attr('href').indexOf('wishlist/index/add') != -1 ){
				SnsProaddto.addtoWishlist(jQuery(this).attr('href')); return false;
			}
		})

	},
	
	locationAddtocart: function (url){
        if (url.indexOf("?")){
            url = url.split("?")[0];
        }
		url = url.replace("checkout/cart","proaddto/index"); 
        url = SnsProaddto.cleanUrl(url);
		jQuery('#proaddto_loading').show();

		try {
			jQuery.ajax( {
				url : url,
				dataType : 'json',
				success : function(data) {
					jQuery('#proaddto_loading').hide();
         			SnsProaddto.setCartResult(data);
				}
			});
		} catch (e) {
		}

	},

	setCartResult: function (data){
		var blockMinicart = '#sns_header .mini-cart .block-minicart';
		var ajaxcart_timer;
		var ajaxcart_sec;

		if(data.status == 'ERROR'){
			alert(data.message.replace("<br/>",""));
		}else{
			// Mini cart
            if(jQuery(blockMinicart)){
                jQuery(blockMinicart).replaceWith(data.minicart);
            }
            // Cart sidebar
            if(jQuery('#sns_content .block.block-cart')){
                jQuery('#sns_content .block.block-cart').replaceWith(data.cart_sidebar);
            }
            // Top Link
            if(jQuery('#sns_header .links')){
                jQuery('#sns_header .links').replaceWith(data.toplink);
            }
            // Re-add formKey
            jQuery('a, button, form').each(function(){
				var el = jQuery(this);
				jQuery.each(this.attributes, function() {
					if(this.specified) el.attr(this.name, this.value.replace(data.formKey, ENCODEDURL));
				});
			});

			SnsProaddto.initConfirm(data.message);
		}
	},
	addtoCompare: function (url){
	    url = url.replace("catalog/product_compare/add","proaddto/whishlist/compare");
		if (url.indexOf("?")){
            url = url.split("?")[0];
        }
		url = SnsProaddto.cleanUrl(url);
		jQuery('#proaddto_loading').show();

	    jQuery.ajax( {
		    url : url,
		    dataType : 'json',
		    success : function(data) {
			    jQuery('#proaddto_loading').hide();
			    if(data.status == 'ERROR'){
				    SnsProaddto.initConfirm(data.message);
			    }else{
				    //alert(data.message.replace("<br/>",""));
				    if(jQuery('.block-compare').length){
                        jQuery('.block-compare').replaceWith(data.sidebar);
                    }
                    // Re-add formKey
		            jQuery('.block-compare a').each(function(){
						var el = jQuery(this);
						jQuery.each(this.attributes, function() {
							if(this.specified) el.attr(this.name, this.value.replace(data.formKey, ENCODEDURL));
						});
					});
					SnsProaddto.initConfirm(data.message);
			    }
		    }
	    });
    },
    addtoWishlist: function (url){
	    url = url.replace("wishlist/index","proaddto/whishlist");
        if (url.indexOf("?")){
            url = url.split("?")[0];
        }
		url = SnsProaddto.cleanUrl(url);
	    jQuery('#proaddto_loading').show();
	    jQuery.ajax( {
		    url : url,
		    dataType : 'json',
		    success : function(data) {
			    jQuery('#proaddto_loading').hide();
			    if(data.status == 'ERROR'){
				    SnsProaddto.initConfirm(data.message);
			    }else{
                    if(jQuery('#sns_header .links')){
                        jQuery('#sns_header .links').replaceWith(data.toplink);
                    }
                    if(jQuery('.block-wishlist')){
                        jQuery('.block-wishlist').replaceWith(data.sidebar);
                    }
                    SnsProaddto.initConfirm(data.message);
			    }
		    }
	    });
    },
    cleanUrl: function(url){
    	url += 'isAjax/1';
        if(window.location.href.match("https://") && !url.match("https://")){
            url = url.replace("http://", "https://");
        }
        if(window.location.href.match("http://") && !url.match("http://")){
            url = url.replace("https://", "http://");
        }
        return url;
    },
    initConfirm: function(message){
    	jQuery('#proaddto_confirmbox #sns_msg_container #sns_proaddto_msg').html(message);
		jQuery.fancybox.close();
		jQuery('#proaddto_confirmbox').fadeIn(200);
		if( jQuery('#proaddto_confirmbox .timer').length ){
	        second = jQuery('#proaddto_confirmbox .timer').text();
	        var timer = setInterval(function(){
	            jQuery('#proaddto_confirmbox .timer').html(jQuery('#proaddto_confirmbox .timer').text()-1);
	        },1000);
	        var timeout = setTimeout(function(){
	            jQuery('#proaddto_confirmbox').fadeOut(200);
	            clearTimeout(timer);
	            setTimeout(function(){
	                jQuery('#proaddto_confirmbox .timer').html(second);
	            }, 1000);
	        },second*1000);

	        jQuery('#proaddto_confirmbox .btn-close, #continue_shopping').click(function(){
				jQuery('#proaddto_confirmbox').fadeOut(200);
	            clearTimeout(timer); timer = 0;
	            clearTimeout(timeout); timeout = 0;
	            setTimeout(function(){
	                jQuery('#proaddto_confirmbox .timer').html(second);
	            }, 1000);
			}); 
	    }
    }

}


jQuery(document).ready(function($){
	SnsProaddto.init();
	setInterval("SnsProaddto.init()",1000);
	window.setLocation = function(args){
		if( args.indexOf('checkout/cart/add')!== -1 ){
			SnsProaddto.locationAddtocart(args);
		}else if( args.indexOf('?options=cart') ){
			window.location = args;
			//SnsProaddto.displayOptions();
		}
	}
	if( typeof productAddToCartForm != 'undefined' ){
		productAddToCartForm.submit = function(){
			if (this.validator.validate()) {
				jQuery('#proaddto_loading').show();
				var dataf = jQuery('#product_addtocart_form').serialize();
                dataf += '&isAjax=1';
                url = jQuery('#product_addtocart_form').attr('action');
                url = url.replace("checkout/cart","proaddto/index"); 
        		url = SnsProaddto.cleanUrl(url);
				jQuery.ajax({
                        url : url,
                        dataType : 'json',
                        type : 'post',
                        data : dataf,
                        success : function(data) {
                        	jQuery('#proaddto_loading').hide();
         					SnsProaddto.setCartResult(data);
                        }
                });
			}
		}
	}
});