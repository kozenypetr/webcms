
function saveBox(destination)
{
  request = $.ajax({
      type: "POST",
      url:  $('#windowForm').attr('action'),
      data: $( "#windowForm" ).serialize(),
      statusCode: {
        200: function(data) {
          $('#' + destination).html(data);
          $("#editWindow").modal('hide');
        },
        422: function(data){
          // alert(data.responseText);
          if ($("#tinyMceBox") && tinymce.get('tinyMceBox'))
          {
            tinymce.remove("#tinyMceBox");
          }
          $('#modalContent').html(data.responseText);
        }
      }
  });
}

function savePage()
{
  request = $.ajax({
      type: "POST",
      url:  $('#windowForm').attr('action'),
      data: $( "#windowForm" ).serialize(),
      statusCode: {
        200: function(data) {
          $("#editWindow").modal('hide');
          if (data['result'] === 'REDIRECT')
          {
              window.location.href = data['href'];
          }
        },
        422: function(data){          
          $('#modalContent').html(data.responseText);
        }
      }
  });
}



function saveCol(destination)
{
  request = $.ajax({
      type: "POST",
      url:  $('#windowForm').attr('action'),
      data: $( "#windowForm" ).serialize(),
      statusCode: {
        200: function(data) {
          // upravime tridu editovaneho sloupce
          //$('#' + destination).removeClass(data['oldclass']);
          //$('#' + destination).addClass(data['class']);
          
          $('#' + destination).attr('class', data['class_col']);
          $('#' + destination + ' > div:first-child').attr('class', data['class']);
          
          // aktualni radek
          var row  = $('#' + destination).parent();
          row.attr('id', 'updatedrow');
          // aktualni area
          var area = row.parent();
          
          // ziskame index aktualniho radku v area
          var index = 0;
          var i = 0;
          area.find('.row').each(function() {
             if ($(this).attr('id') === 'updatedrow')
             {
                 index = i;
             }
             i++;
          });
          
          // ODEBRANI RADKU POMOCI REGULARU
          if (data['is_row_start_old'] !== data['is_row_start'] && data['is_row_start'] === 0 && index > 0)
          {
              area.html(area.html().replace(/<\/div>[^<]*<div[^>]*id="updatedrow"[^>]*>/, ''));
          }
          row.removeAttr('id');
          
          
          // PRIDANI RADKU POMOCI REGULARU
          if (data['is_row_start_old'] !== data['is_row_start'] && data['is_row_start'] === 1 && row.find('div:first-child').attr('id') !== destination)
          {
              var re = new RegExp('<div[^>]*id="' + destination + '"');
              area.html(area.html().replace(re, 
              '</div><div class="row ui-sortable" area-id="' + area.attr('area-id') + '"><div id="' + destination + '"'));
          }
          
          
          // jquery sortable pro radky a sloupce
          sortable_row('#' + area.attr('id') + ' .row');
          sortable_col('#' + area.attr('id') + ' .cms-col');
          
          // zavreme editacni okno
          $("#editWindow").modal('hide');
        },
        422: function(data){
        }
      }
  });
}



function sortable_row(selector) {
   $(selector).sortable({ items: ".cms-col", revert: false, cursor: "move", placeholder: 'emptydiv col-md-12', handle: ".drag",
        receive: function(event, ui) {
            var droppable = $( this );
            var area      = droppable.parent();
            var area_id   = area.attr('id');
            var new_item  = $(this).data().uiSortable.currentItem;
            var location  =  droppable.parent().attr('location');
            
            var layout_id = $('#layout-id').text();
            var page_id = $('#page-id').text();
            // var href = $(ui.item).attr('href');
            var ph = $(ui.item).attr('ph');
            
            /*alert(new_item.prev().attr('col-id'));
            alert(droppable.sortable( "serialize" ));
            alert(droppable.parent().attr('location'));
            alert(href); return;*/
            
            // vytvoreni noveho sloupce a umisteni do radku
            $.ajax({
                url: admin_urls['col_new'] + '?area_id=' + area_id + '&layout_id=' + layout_id + '&page_id=' + page_id + '&location=' + location + '&ph=' + ph,
                // url: $(ui.item).attr('href') + '?col_id=' + col_id + '&prev_col=' + new_item.prev().attr('col-id') + '&' + droppable.sortable( "serialize" ),
                cache: false,
                async: false,
                success: function(data) {
                    new_item.replaceWith(data);
                    // alert(droppable.sortable( "serialize", { key: "sort" } ));
                }
            });
            
            // razeni sloupcu
            var sort = Array();
            var i = 0;
            $('#' + area.attr('id') + ' .cms-col').each(function(){
              sort[i] = 'col[' + i + ']=' + $(this).attr('col-id');
              i++;
            });
            
            $.ajax({
              url: admin_urls['col_sort'] + '?area_id=' + area_id + '&layout_id=' + layout_id + '&page_id=' + page_id + '&location=' + location + '&' + sort.join('&'),
              cache: false,
              success: function(data) {
                  
              }
            });
            
        }
  }); 
}


$(document).ready(function() {
    
  $('#editWindow').on('hidden.bs.modal', function (e) {
    if ($('#tinyMceBox') && tinymce.get('tinyMceBox'))
    {
        // alert('test2');
        // OPRAVIT
        tinymce.remove("#tinyMceBox");
    }
  });
    
    
  $('#toggleStructure').click(function(){
     if ($('.cms_layout').hasClass('structure'))
     {
         $('.cms_layout').removeClass('structure');
     }
     else
     {
         $('.cms_layout').addClass('structure');
     }
  }); 
    
  $(document).on('focusin', function(e) {
    if ($(e.target).closest(".mce-window").length) {
        e.stopImmediatePropagation();
    }
  });   
    
    
  $.widget('ui.dialog', $.ui.dialog, {
    _allowInteraction: function(event) {
        return ($('.mce-panel:visible').length > 0);
    }
  });  
    
    
  $( document ).delegate('.window-link', 'click', function(event) {
    event.preventDefault();
    $.ajax({
      type: "POST",
      url: $(this).attr('href'),
      data: 'slug=' + admin_params['slug'],
      cache: false,
      success: function(data){
          $('#modalContent').html(data);
          $("#editWindow").modal('show');
      }
    });
  });
 
  
  $( document ).delegate('.delete-link', 'click', function(event) {
     event.preventDefault();
     var element_id = $(this).attr('element-id');
     if (confirm($(this).attr('title'))) { 
       $.ajax({
         url: $(this).attr('href'),
         cache: false,
         success: function(data){
           $('#' + element_id).remove();
         }
       });
     }
  });
  
  
  $( document ).delegate('#windowForm', 'submit', function(event) {
     event.preventDefault();
     $('.submitWindow').click();
  });  
  
  // odesle formular a zapise vysledek do cile
  $('.submitWindow').on('click', function(event) {
    event.preventDefault();
    var destination = $('#resultDestination').val();
    var cmsAction = $('#cmsAction').val();
    tinyMCE.triggerSave();
    
    
    /*
    $.post( $('#windowForm').attr('action'), $( "#windowForm" ).serialize(), function( data ) {
        alert(statusText);
        $('#' + destination).html(data);
        $("#editWindow").modal('hide');
    });
    */
   
   // alert('test');
   
   if (cmsAction === 'saveBox')
   {
       saveBox(destination);
   }
   // alert(cmsAction);
   if (cmsAction === 'saveCol')
   {
       saveCol(destination);
   }
   
   if (cmsAction === 'savePage')
   {
       savePage();
   }
   
  });
  
  $(function() {
    $( ".draggable-box" ).draggable({ 
     connectToSortable: ".cms-col",
     helper: function()
     {
        return $('<span class="draggable-helper btn btn-danger btn-sm">' + $(this).html() + '</span>');
     },
     revert: "invalid",
     appendTo: '#cms_page', 
     cursor: 'move',
     start: function() {
       // $( ".cms-col" ).sortable({ placeholder: "emptydiv " + $(this).attr('ph') });
       close_toolbar_window_box();
     }
    });
  });
  
  $(function() {
    $( ".draggable-col" ).draggable({
     connectToSortable: ".area .area-row", 
     /*helper: "clone", */
     helper: function()
     {
        return $('<span class="draggable-helper btn btn-danger btn-sm">' + $(this).html() + '</span>');
     },
     revert: "invalid", 
     appendTo: '#cms_page', 
     cursor: 'move',
     start: function() {
        // alert($(this).attr('ph'));
        $( ".area .area-row" ).sortable({ placeholder: "emptydiv " + $(this).attr('ph') });
        close_toolbar_window_col();
     }
    });
  });
  
  /*$( ".area" ).droppable({
     activeClass: "area-active",
     hoverClass: "area-hover",
     accept: "#draggable-col",
     drop: function( event, ui ) {
       // $( this ).addClass( "ui-state-highlight" );
       var area_id   = $( this ).attr('area-id');
       var layout_id = $('#layout-id').text();
       var page_id = $('#page-id').text();
       var droppable = $( this );
       var location = $( this ).attr('location');
       $.ajax({
          url: $(ui.draggable).attr('href') + '?area_id=' + area_id + '&layout_id=' + layout_id + '&page_id=' + page_id + '&location=' + location,
          cache: false,
          success: function(data) {
             droppable.append(data);
          }
       });
     }
  });
    */
   
  
  
  /*
  $( ".cms-col" ).droppable({
     activeClass: "cms-col-active",
     hoverClass: "cms-col-hover",
     accept: "#draggable-box",
     drop: function( event, ui ) {
       // $( this ).addClass( "ui-state-highlight" );
       var col_id = $( this ).attr('col-id');
       var droppable = $( this );
       $.ajax({
          url: $(ui.draggable).attr('href') + '?col_id=' + col_id,
          cache: false,
          success: function(data) {
             droppable.append(data);
          }
       });
     }
  });*/
    
    

  
var isReceive = false;
/*
$('.cms-col').sortable({ items: ".cms-box", connectWith: ".cms-col", revert: false, cursor: "move", placeholder: 'emptydiv', handle: ".drag",
    start: function() {
        isReceive = false;
    },

    stop:  function(event, ui) {
        if (isReceive) return;
        var col_id = $( this ).attr('col-id');
        $.ajax({
            url: admin_urls['col_sort'] + '?col_id=' + col_id + '&' + $(this).sortable( "serialize" ),
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
            var href = $(ui.item).attr('href');
            $.ajax({
                url: $(ui.item).attr('href') + '?col_id=' + col_id + '&previtem=' + new_item.prev().attr('id') + '&' + droppable.sortable( "serialize" ),
                cache: false,
                success: function(data) {
                    new_item.replaceWith(data);
                }
            });
        }
        else
        {
            $.ajax({
                url: admin_urls['col_sort'] + '?col_id=' + col_id + '&' + $(this).sortable( "serialize" ),
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
*/
sortable_row('.area .area-row');
sortable_col('.cms-col');

               
  // $('.cols-holder').sortable({handle: ".drag"});
  // $('.cols-holder').sortable({handle: ".drag", connectWith: ".cols-holder"});
  /*$('.cms-col').sortable();*/
  
  
});


// http://jsfiddle.net/alvianomedia/ME43e/
// http://codepen.io/neagle/pen/fuIqs
