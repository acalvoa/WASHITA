
function trimChar(string, charToRemove) {
    while(string.charAt(0)==charToRemove) {
        string = string.substring(1);
    }

    while(string.charAt(string.length-1)==charToRemove) {
        string = string.substring(0,string.length-1);
    }

    return string;
}


function getSanitizedAndRoundedUpNumber(n, decimals){
        var sanitized = n.replace(/\./g, ',').replace(/[^0-9,]/g, '').replace(/,0+$/g, '');

        if(decimals <=0){
            return Math.ceil(sanitized);
        }

        if(sanitized === ""){
            return sanitized;
        }

        var regexWithDecimals = new RegExp("[0-9]+[\,][0-9]{1,"+decimals+"}", 'gi');
        var match = sanitized.match(regexWithDecimals);

        var resArray = (match !== "" && match !== null)? match : sanitized.match(/[0-9]+/g);
        return (resArray != null && resArray.length > 0)? ChileanNumberToJsFloat(resArray[0]): n; 
}

function ChileanNumberToJsFloat(n){
    return parseFloat(n.replace(".","").replace(",","."));
}

function JsFloatToChileanNumber(n){
    return n.toString().replace(".",",").replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

var appMaster = {

    preLoader: function(){
        
        imageSources = []
        $('img').each(function() {
            var sources = $(this).attr('src');
            imageSources.push(sources);
        });
        if($(imageSources).load()){
            $('.pre-loader').fadeOut('slow');
        }
    },

    smoothScroll: function() {
        // Smooth Scrolling
        $('a[href*=#]:not([href=#carousel-example-generic])').click(function() {
            if (location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') && location.hostname == this.hostname) {
                appMaster.scrollToHash(this.hash);
            }
        });
    },
    scrollToHash: function(hashName){
        if(hashName === undefined || !hashName.length){
            return false;
        }
        var target = $(hashName);
        target = target.length ? target : $('[name=' + hashName.slice(1) + ']');
        if (target.length) {
            $('html,body').animate({
                scrollTop: target.offset().top
            }, 1000);
            return false;
        }
    },
    scrollToHashInUrl: function(){
        var hashInLocation = $(location).attr('hash');
        if(hashInLocation.length){
            this.scrollToHash(hashInLocation);
        }  
    },
    reviewsCarousel: function() {
        // Reviews Carousel
        $('.review-filtering').slick({
            slidesToShow: 1,
            slidesToScroll: 1,
            dots: true,
            arrows: false,
            autoplay: true,
            autoplaySpeed: 5000
        });
    },

    screensCarousel: function() {
        // Internet explorer 11 has a problem with iframe inside carousel. The error crashes browser
        // To resolve it at first removing iframe
        var iframeParents = []
        $('.filtering').each(function() {
            $(this).find('iframe').each(function(){
                iframeParents.push({'parent':$(this).parent(), 'iframe':$(this), 'html':$(this).parent().html()});
            });            
        });
        iframeParents.forEach(function(item) {
            item.iframe.detach();
        });
 
        // Screens Carousel
        $('.filtering').slick({
            slidesToShow: 1,
            slidesToScroll: 1,
            dots: false,
            arrows: false
        });
        
        // Returning iframes
        iframeParents.forEach(function(item) {
            item.parent.append(item.html);
        });
        
  

        // $('.js-filter-all').on('click', function() {
        //     $('.filtering').slickUnfilter();
        //     $('.filter a').removeClass('active');
        //     $(this).addClass('active');
        // });


        function initMapsIfNot(){
            var divGoogleMaps = $.find(".google-maps");
            // Init google maps
            if(!divGoogleMaps.length){
                var btns = $('.button-google-maps');
                btns.each(function(index){
                    $(this).prop('disabled', true);
                });
                btns.each(function(index){
                    var btn = $(this);
                    var objectToSlick = $(btn.attr("data-slick-object-class"));          
                    var mapsSrc = btn.attr("data-maps-src");   

                    var newDivGoogleMaps = $($.parseHTML('<div class="google-maps"></div>'));
                    var mapsSrc = btn.attr("data-maps-src");            
                    var iframe = $($.parseHTML('<iframe src="'+mapsSrc+'"></iframe>'));
                    newDivGoogleMaps.append(iframe);
                    objectToSlick.append(newDivGoogleMaps);
                });

                btns.each(function(index){
                    $(this).prop('disabled', false);
                });
            }
        }

        $('.button-google-maps').on('click', function() {
            initMapsIfNot();
            
            var btn = $(this);
            var objectClassToSlick = btn.attr("data-slick-object-class");
            $('.filtering').slickFilter(objectClassToSlick);
            $('.filter a').removeClass('active');
            btn.addClass('active');
        });


    },

    animateScript: function() {
        $('.scrollpoint.sp-effect1').waypoint(function(){$(this).toggleClass('active');$(this).toggleClass('animated fadeInLeft');},{offset:'100%'});
        $('.scrollpoint.sp-effect2').waypoint(function(){$(this).toggleClass('active');$(this).toggleClass('animated fadeInRight');},{offset:'100%'});
        $('.scrollpoint.sp-effect3').waypoint(function(){$(this).toggleClass('active');$(this).toggleClass('animated fadeInDown');},{offset:'100%'});
        $('.scrollpoint.sp-effect4').waypoint(function(){$(this).toggleClass('active');$(this).toggleClass('animated fadeIn');},{offset:'100%'});
        $('.scrollpoint.sp-effect5').waypoint(function(){$(this).toggleClass('active');$(this).toggleClass('animated fadeInUp');},{offset:'100%'});
    },
 
 
    revsliderAuto: function(){
        var docHeight = $(window).height();
        this.revSlider(docHeight, true);
    },
    setHeightForMainPageImage: function(){
        var navbarHeightWithoutPadding = $(".navbar-fixed-top").height()-40;
        var docHeight = $(window).height();
        $('#hand-freeze').attr('height',docHeight-navbarHeightWithoutPadding);  
    },
    
    revSlider: function(height, isFullScreen) {
        var mainSlider = $('.tp-banner').revolution({
            delay: 9000,
            startwidth: 1170,
            startheight: height,
            hideThumbs: 10,
            touchenabled: false,
            fullWidth: "on",
            hideTimerBar: "on",
            fullScreen: (isFullScreen? "on":"off"),
            onHoverStop: "off",
            fullScreenOffsetContainer: ""
        });
       
    },

    scrollMenu: function(){
        var num = 50; //number of pixels before modifying styles

        $(window).bind('scroll', function () {
            if ($(window).scrollTop() > num) {
                $('nav').addClass('scrolled');

            } else {
                $('nav').removeClass('scrolled');
            }
        });
    },
    placeHold: function(){
        // run Placeholdem on all elements with placeholders
        Placeholdem(document.querySelectorAll('[placeholder]'));
    },
    
    support_success: function(message) {
        $('#support_alert_placeholder').html('<div class="alert alert-success"><a class="close" data-dismiss="alert">×</a><span>'+message+'</span></div>')
    },
    support_fail: function(message) {
        $('#support_alert_placeholder').html('<div class="alert alert-danger"><a class="close" data-dismiss="alert">×</a><span>'+message+'</span></div>')
    },
    bindEmailSupport: function(){
        $("#supportForm").submit(function(event) {
            event.preventDefault();
            
             var params = {
                     'email': $("#email").val(),
                     'name': $("#name").val(),
                     'message':$("#message").val()
                     };
            $.post("contact.php", params)
            .done(function(data){
               if(data.success){
                   appMaster.support_success("¡Mensaje Enviado!");
               }
               else{
                   appMaster.support_fail(data.message);
               }
            })
            .fail(function()  {
               appMaster.support_fail("Error. Su mensaje no fue enviado.");
            }); 
        });
    }
}; // AppMaster


var appOrder = {
    initToolTips: function(){
        $('[data-toggle="tooltip"]').tooltip();  
    },

    initDatePicker: function(){
        var pickupMinDateTime = new Date($('#order_datepicker').attr('data-min-datetime'));
        var pickupMomentMinDate = moment(pickupMinDateTime);
        
        function nextNearestPoint(choosenDateAndTime) {
            function isMomentWeekDay(mDate){
                var day = mDate.isoWeekday();
                return day == 6 || day == 7;
            }

            var choosenMoment = moment(choosenDateAndTime);

            // Is morning
            if(choosenMoment.hours() === 8){
                // today evening
                return choosenMoment.startOf('day').add(16, 'hours');
            }
            else //Evening
            {
                var nextWorkingDay = choosenMoment.startOf('day').add(1, 'day');
                while(isMomentWeekDay(nextWorkingDay)){
                    nextWorkingDay = nextWorkingDay.add(1, 'day');
                }
                // morning
                return nextWorkingDay.add(8, 'hours');
            }
      }

     function nextDropOff(choosenDateAndTime){
            var nextPickup = choosenDateAndTime;
            //1.5 day
            for(var i=0; i<3;i++){
                nextPickup = nextNearestPoint(nextPickup);
            }
            return nextPickup;        
        }   
       
        var dppPickup = new DateTimePickerPair("order_datepicker", "pickuptime", pickupMomentMinDate, true);
        $(dppPickup).on('dpp.change', function(e, data){
            $('#pickup_datetime').val(data.asText());
            var minDropOff = nextDropOff(data.from);
            dppDropOff.minDate(minDropOff);
            dppDropOff.setDateAndTime(minDropOff);
        });

        var dropOffMomentMinDate = nextDropOff(pickupMinDateTime.from);
        var dppDropOff = new DateTimePickerPair("dropoff_datepicker", "dropofftime", dropOffMomentMinDate, true);
        $(dppDropOff).on('dpp.change', function(e, data){
            $('#dropoff_datetime').val(data.asText());
        });


        dppDropOff.updateValue();
        dppPickup.updateValue();
       
    },
   
    sanitizeNumberInput: function(){
        $('.numbersOnly').focusout(function () {
            var sanitized = getSanitizedAndRoundedUpNumber(this.value, 0);
            if (this.value != sanitized) {
                this.value = sanitized;
            }
        });

        var decimalsWithHundreds = $('.decimals-with-hundreds');
        decimalsWithHundreds.focusout(function () {
            var sanitized = getSanitizedAndRoundedUpNumber(this.value, 2);

            var minValue = $(this).attr("min");
            if(minValue && sanitized < minValue){
                sanitized = minValue;
            }
            
            if (this.value != sanitized) {
                this.value = sanitized;
            }
        });  

        var decimalsWithTens = $('.decimals-with-tens');
        decimalsWithTens.focusout(function () {
            var sanitized = getSanitizedAndRoundedUpNumber(this.value, 1);

            var minValue = $(this).attr("min");
            if(minValue && sanitized < minValue){
                sanitized = minValue;
            }
            
            if (this.value != sanitized) {
                this.value = sanitized;
            }
        });  
        
        // decimalsWithHundreds.attr("pattern", "[0-9]+([\.][0-9]+)?");
    },
    totalIroningItems: 0,
    onlyIroningItemLines: [],
    dryCleaningItemLines: [],
    recalculatePrice: function(){
        var weight = Number($('#weight').val());
        this.recalculatePriceByWeigth(weight);
    },
    showPrice: function(totalPrice,oneKiloPrice,discountProcent,washTypeText,totalIroningItems,priceForIroningPerItemText){
        $('#total_price').text(totalPrice);
        $('#one_kilo_pack_price').text(oneKiloPrice);
        $('#dicount_procent').text(discountProcent);
        $('.selected_washing_text').text(washTypeText.toUpperCase());

        $('#ironing_item_price').text(priceForIroningPerItemText);
        $('.selected_ironing_items_total').text(totalIroningItems);
        
    },
    priceWithDiscount: 0,
    recalculatePriceByWeigth: function(weight){
       var params = {
                     'kilo': weight,
                     'laundry_option': $('input[name="laundry_option"]:checked').val(),
                     'discount_coupon':$('#discount_coupon').val(),
                     'washitems': $('input[name="washitems[]"]').map(function(){return trimChar($(this).val(), ',')}).get().join(';'),
                     'only_ironing_items': $.map(appOrder.onlyIroningItemLines, function(itemLine){return itemLine.item.Id +','+itemLine.count}).join(';'),
                     'dry_cleaning_items': $.map(appOrder.dryCleaningItemLines, function(itemLine){return itemLine.item.Id +','+itemLine.count}).join(';'),
                     'total_ironing_items': appOrder.totalIroningItems,
                     'email': $('input[name="email"]').val()
                     };

           $.post("process_price_service.php", params)
            .done(function(data){
                //   console.log(data);
                var res = jQuery.parseJSON(data);
                appOrder.priceWithDiscount = res.priceWithDiscount;
                appOrder.showPrice(res.priceWithDiscountText,
                              JsFloatToChileanNumber(res.weight) + " x "+ res.pricePerOneKiloText,
                              res.discountValueText,
                              res.washTypeText,
                               res.totalIroningItems,
                               res.priceForIroningPerItemText); 
                             
               var discountWarningMessage = $('#discountWarningMessage');
               if(res.discountWarningMessage){
                   discountWarningMessage.html(res.discountWarningMessage);
                   discountWarningMessage.show();
               }               
               else{
                   discountWarningMessage.html("");
                   discountWarningMessage.hide();
               }
                
            })
            .fail(function()  {
               appOrder.showPrice('Error','Error','Error','Error','Error'); 
            }); 
        
    },
    
    bindPrice: function(){
       appOrder.recalculatePrice();
        $('#weight').bind("change paste keyup", function(event) {
            event.stopPropagation();
            var sanitizedWeight = getSanitizedAndRoundedUpNumber(this.value,2);
            appOrder.recalculatePriceByWeigth(sanitizedWeight);
        });

        $('#weight').blur(function(event) {
            event.stopPropagation();
            var sanitizedWeight = getSanitizedAndRoundedUpNumber(this.value,2);
            appOrder.recalculatePriceByWeigth(sanitizedWeight);
        });
       
       
       $('#discount_coupon').blur(function() {
            appOrder.recalculatePrice();
       });

       $('input[name="email"]').blur(function() {
            appOrder.recalculatePrice();
       });

      
    },
    

}; //appOrder
