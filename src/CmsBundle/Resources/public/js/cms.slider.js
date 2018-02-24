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
                obj.find(".handle").html('<span class="glyphicon glyphicon-chevron-right"></span>');
                obj.addClass("left-shadow-overlay");
            },
            hidden: function(obj){
                obj.find(".handle").html('<span class="glyphicon glyphicon-chevron-left"></span>');
                obj.removeClass("left-shadow-overlay");
            }
        });
    }
}