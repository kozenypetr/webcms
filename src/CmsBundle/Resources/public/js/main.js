$( function() {

/*
    $('.slick').slick({
        infinite: true,
        slidesToShow: 3,
        slidesToScroll: 3
    });
*/

    $('.bxslider > div').on('click', function(e){
        alert('click');
    });

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