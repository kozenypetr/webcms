/**
 * Number.prototype.format(n, x, s, c)
 *
 * @param integer n: length of decimal
 * @param integer x: length of whole part
 * @param mixed   s: sections delimiter
 * @param mixed   c: decimal delimiter
 */
Number.prototype.format = function(n, x, s, c) {
    var re = '\\d(?=(\\d{' + (x || 3) + '})+' + (n > 0 ? '\\D' : '$') + ')',
        num = this.toFixed(Math.max(0, ~~n));

    return (c ? num.replace('.', c) : num).replace(new RegExp(re, 'g'), '$&' + (s || ','));
};


var calculator = {

    box: null,
    option1: 'bungalov',
    option2: 'standard',
    area: 100,


    init: function() {
        calculator.initEvent();
        calculator.updatePrice();
    },

    initEvent: function()
    {
        $('.option-1').click(calculator.clickOption1);
        $('.option-2').click(calculator.clickOption2);
        $('#house-area').keyup(calculator.changeArea);
        $('.example-button').click(calculator.showExample);
        $('.contact-link').click(function(e){
            e.preventDefault();
            $('html, body').animate({
                scrollTop: parseInt($("#calculator-contact-form").offset().top)
            }, 1000);
        });
        // $(cart.eventSelector + '.click').click(cart.processEvent);
    },

    clickOption1: function()
    {
        var value = $(this).data('value');
        $('.option-1').removeClass('selected');
        $(this).addClass('selected');
        calculator.option1 = value;
        calculator.updatePrice();

    },

    clickOption2: function()
    {
        var value = $(this).data('value');
        $('.option-2').removeClass('selected');
        $(this).addClass('selected');
        calculator.option2 = value;
        calculator.updatePrice();
    },

    changeArea: function()
    {
        var value = $(this).val();
        calculator.area = parseInt(value);
        calculator.updatePrice();
    },

    showExample: function()
    {
        var area = $(this).data('area');
        var type = $(this).data('type');

        calculator.area = parseInt(area);
        $('#house-area').val(area);
        $('.option-1[data-value="' + type + '"]').click();

        calculator.updatePrice();

        $('html, body').animate({
            scrollTop: parseInt($("#calc-title").offset().top)
        }, 1000);
    },

    updatePrice: function()
    {
        console.log(calculator);
        var pricePerMeter = parseInt($('#' + calculator.option1 + '-' + calculator.option2).val());
        var price = parseFloat(pricePerMeter * calculator.area);
        if (calculator.area > 0)
        {
            $('#calculator-price').html(price.format(0, 0, ' ', ',') + ' Kč s DPH');
        }
        else
        {
            $('#calculator-price').html("Zadejte plochu od 30 do 250 m<sup>2</sup>");
        }

        $('.box-option-desc-item').hide();
        $('.desc-' + calculator.option2).show();

        var message = 'Prosím o více informací k domu - ' + calculator.option1 + ' o velikosti ' + calculator.area + ' m2 v rozsahu dodávky ' + calculator.option2;


        $('#form_message').html(message);
    }

};


$( function() {

/*
    $('.slick').slick({
        infinite: true,
        slidesToShow: 3,
        slidesToScroll: 3
    });
*/

    $('.bxslider > div').on('click', function(e){
        // alert('click');
    });

    var slides = 4;

    if ($(window).width() < 700)
    {
        slides = 1;
    }


    $('.bxslider').bxSlider({
        auto: true,
        slideWidth: 5000,
        responsive: true,
        minSlides: slides,
        maxSlides: slides,
        slideMargin: 5,
        pager: false,
        moveSlides: 1,
        autoHover: true,
        pause: 4000,
        captions: true
    });



    if ($('.photogallery1').length)
    {
      $('.photogallery1').fancybox();
    }

    if ($('.photogallery2').length)
    {
        $(".photogallery2").fancybox();
    }

    if ($('.photogallery3').length)
    {
        $(".photogallery3").fancybox();
    }

    if ($('.news-gallery-image').length)
    {
        $(".news-gallery-image").fancybox();
    }

    // https://owlcarousel2.github.io/OwlCarousel2/docs/api-options.html


    $( '.contact-form' ).each(function() {
        $( this ).validate({ onfocusout: function(element) { $(element).valid(); } });
    });


    calculator.init();
});