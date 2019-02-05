$(document).ready(function (){
        $(".fieldset-links a.scroll").click(function (){
                $('html, body').animate({
                    scrollTop: $($(this).attr('href')).offset().top -60
                }, 1000);
                return false;
        });
});

