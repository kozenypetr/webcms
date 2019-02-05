  // SLOUPEC
  
  $( document ).delegate('.col-delete-link', 'click', function(event) {
     event.preventDefault();
     var element_id = $(this).attr('element-id');
     if (confirm($(this).attr('title'))) { 
       $.ajax({
         url: $(this).attr('href'),
         cache: false,
         success: function(data) {
           if (data === 'OK')
           {
               $('#' + element_id).remove();
           }
           else
           {
               alert('ERROR');
           }
         }
       });
     }
  });
  
  $(document).delegate('.box-expand', 'click', function(event){
     var box_id = $(this).attr('box-id');
     if ($(this).hasClass('open'))
     {
       $('#box-value-' + box_id).hide();
       $(this).removeClass('open');
       $(this).find('.glyphicon').removeClass('glyphicon-chevron-down').addClass('glyphicon-chevron-up');
     }
     else
     {
       $('#box-value-' + box_id).show();
       $(this).addClass('open');
       $(this).find('.glyphicon').removeClass('glyphicon-chevron-up').addClass('glyphicon-chevron-down');
     }
  });


function sortable_col(selector) {
$(selector).sortable({ items: ".cms-box", connectWith: ".cms-col", revert: false, cursor: "move", placeholder: 'emptydiv', handle: ".drag",
    start: function() {
        isReceive = false;
    },

    stop:  function(event, ui) {
        if (isReceive) return;
        var col_id = $( this ).attr('col-id');
        $.ajax({
            url: admin_urls['col_sort_boxes'] + '?col_id=' + col_id + '&' + $(this).sortable( "serialize" ),
            cache: false,
            success: function(data) {}
        });
    },

    receive: function(event, ui) {
        var col_id = $( this ).attr('col-id');
        var droppable = $( this );
        var new_item = $(this).data().uiSortable.currentItem;
        
        
        if ($(ui.item).hasClass('ui-draggable'))
        { 
            var type = new_item.attr('type');
            var href = admin_urls['box_new']; // $(ui.item).attr('href');
            $.ajax({
                type: "POST",
                url: href + '?col_id=' + col_id + '&type=' + type + '&previtem=' + new_item.prev().attr('id') + '&' + droppable.sortable( "serialize" ),
                data: 'slug=' + admin_params['slug'],
                cache: false,
                success: function(data) {
                    new_item.replaceWith(data);
                }
            });
        }
        else
        {
            $.ajax({
                url: admin_urls['col_sort_boxes'] + '?col_id=' + col_id + '&' + $(this).sortable( "serialize" ),
                cache: false,
                success: function(data) {
                    if (data !== 'OK')
                    {
                        alert('ERROR');
                    }
                }
            });
        }
        isReceive = true;
    }
});
}