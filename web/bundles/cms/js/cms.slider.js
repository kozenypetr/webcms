var slider = {
    init: function()
    {
        $("#slider-right").slideReveal({
            trigger: $("#slider-right .handle"),
            push: false,
            position: "right",
            width: 400,
            speed: 700,
            shown: function(obj){
                obj.find(".handle").html('<i class="fa fa-chevron-right" aria-hidden="true"></i>');
                obj.addClass("left-shadow-overlay");
            },
            hidden: function(obj){
                obj.find(".handle").html('<i class="fa fa-chevron-left" aria-hidden="true"></i>');
                obj.removeClass("left-shadow-overlay");
            }
        });
    }
}