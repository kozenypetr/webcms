function detectmob() {
    if( navigator.userAgent.match(/Android/i)
        || navigator.userAgent.match(/webOS/i)
        || navigator.userAgent.match(/iPhone/i)
        || navigator.userAgent.match(/iPad/i)
        || navigator.userAgent.match(/iPod/i)
        || navigator.userAgent.match(/BlackBerry/i)
        || navigator.userAgent.match(/Windows Phone/i)
    ){
        return true;
    }
    else {
        return false;
    }
}

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


    if (detectmob())
    {
        $('.bxslider').bxSlider({
            auto: true,
            slideWidth: 5000,
            responsive: true,
            minSlides: 1,
            maxSlides: 1,
            slideMargin: 0,
            pager: false,
            moveSlides: 1,
            autoHover: true,
            pause: 4000,
            captions: true
        });
    }
    else {
        $('.bxslider').bxSlider({
            auto: true,
            slideWidth: 5000,
            responsive: true,
            minSlides: 4,
            maxSlides: 4,
            slideMargin: 5,
            pager: false,
            moveSlides: 1,
            autoHover: true,
            pause: 4000,
            captions: true
        });
    }


    if ($('.photogallery1').length)
    {
      $(".photogallery1").fancybox();
    }

    if ($('.photogallery2').length)
    {
        $(".photogallery2").fancybox();
    }

    if ($('.photogallery3').length)
    {
        $(".photogallery3").fancybox();
    }

    // https://owlcarousel2.github.io/OwlCarousel2/docs/api-options.html

});