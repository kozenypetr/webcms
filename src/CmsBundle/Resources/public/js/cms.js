var cmsBox = {

    boxInEdit: null,

    addBox: function(selector)
    {
        this.__addEvents(selector);
        this.__addContextMenu(selector);
    },

    __addEvents: function(selector)
    {

        $(selector + ' .box-toolbar .fancybox').fancybox({
            modal: false,
            afterLoad: this.__afterLoadForm
        });

        $(selector + ' .add-widget').click(this.__addWidget)
    },

    __addWidget: function(e)
    {
        e.preventDefault();

        var box_id = $(this).data('box-id');

        $.ajax({
            type: "GET",
            url: $(this).attr('href'),
            statusCode: {
                200: function(data) {
                    $('#box-' + box_id + ' .box-inner').append(data.widgetHtml);
                    // pridame widget do boxu
                    cmsWidget.initWidget('#widget-' + data.id);
                    // zobrazime editaci boxu
                    cmsWidget.openEditDialog(data.id);
                }
            }
        });
    },


    __addWidgetDrag: function(e)
    {
      e.preventDefault();

      var box_id = $(this).data('box-id');

      $.ajax({
        type: "GET",
        url: $(this).attr('href'),
        statusCode: {
          200: function(data) {
            $('#box-' + box_id + ' .box-inner').append(data.widgetHtml);
            // pridame widget do boxu
            cmsWidget.initWidget('#widget-' + data.id);
            // zobrazime editaci boxu
            cmsWidget.openEditDialog(data.id);
          }
        }
      });
    },


    __addContextMenu: function(selector)
    {
        $.contextMenu({
            selector: selector + ' .context-menu',
            trigger: 'left',
            callback: function(key, options) {
                var m = "clicked: " + key + ' id=' + $(this).data('widget');
                window.console && console.log(m) || alert(m);
            },
            items: {
                "edit": {name: "Edit", icon: "edit", callback: cmsBox.__edit},
                "cut": {name: "Cut", icon: "cut"},
                copy: {name: "Copy", icon: "copy"},
                "paste": {name: "Paste", icon: "paste"},
                "sep1": "---------",
                "add-box": {name: "Přidat box", icon: "fa-plus"},
                "quit": {name: "Quit", icon: function(){
                    return 'context-menu-icon context-menu-icon-quit';
                }}
            }
        });
    },

    __afterLoadForm: function(instance, current)
    {
        cmsBox.boxInEdit = current.opts.$orig;
        $('#box-edit-form').submit(cmsBox.__submitBoxEditForm);
    },


    __submitBoxEditForm: function(e)
    {
        e.preventDefault();

        $.ajax({
            type: "POST",
            url: $(this).attr('action'),
            data: $(this).serialize(), // serializes the form's elements.
            success: function(data)
            {
                var id = cmsBox.boxInEdit.data('box-id');
                $('.box-' + id).attr('class', 'box container box-' + id + ' ' +  data.class);
            }
        });

    }
}

var cmsModal = {

}

var cmsWidget = {

    widgetInEdit: null,

    initWidget: function(selector)
    {
        this.__addEvents(selector);
        this.__addContextMenu(selector);
    },

    openEditDialog: function(id)
    {
        $('#widget-' + id + ' .widget-edit').click();
    },

    __addEvents: function(selector)
    {
        $(selector + ' .widget-edit').click(this.__loadForm);
        $('#modal-save-button').click(function(){
            $('.modal-body form').submit();
        });
        /*$(selector + ' .fancybox').fancybox({
            modal: false,
            afterLoad: this.__afterLoadForm
        });*/
    },

    __loadForm: function(e)
    {
        e.preventDefault();

        cmsWidget.widgetInEdit = $(this);

        $.ajax({
            type: "GET",
            url: $(this).attr('href'),
            statusCode: {
                200: function(data) {
                    var modal = $('#cms-modal');
                    modal.find('.modal-body').html(data);
                    $('#widget-edit-form').submit(cmsWidget.__submitBoxEditForm);
                    $('#cms-modal-label').text('Editace obsahu');

                    tinymce.init({
                      selector: '#widget-edit-form textarea.tiny',
                      themes: "modern",
                      height : "200"
                    });

                    modal.modal();

                    modal.on('hidden.bs.modal', function () {
                      if ($("textarea.tiny").length/* && tinymce.get('form_html')*/)
                      {
                        tinymce.remove("textarea.tiny");
                      }
                    })
                }
            }
        });
    },

    __addContextMenu: function(selector)
    {
        $.contextMenu({
            selector: selector + ' .context-menu',
            trigger: 'left',
            callback: function(key, options) {
                var m = "clicked: " + key + ' id=' + $(this).data('widget');
                window.console && console.log(m) || alert(m);
            },
            items: {
                "edit": {name: "Edit", icon: "edit", callback: cmsBox.__edit},
                "cut": {name: "Cut", icon: "cut"},
                copy: {name: "Copy", icon: "copy"},
                "paste": {name: "Paste", icon: "paste"},
                "sep1": "---------",
                "add-box": {name: "Přidat box", icon: "fa-plus"},
                "quit": {name: "Quit", icon: function(){
                    return 'context-menu-icon context-menu-icon-quit';
                }}
            }
        });
    },

    __afterLoadForm: function(instance, current)
    {
        cmsWidget.widgetInEdit = current.opts.$orig;
        $('#widget-edit-form').submit(cmsWidget.__submitBoxEditForm);


    },

    __submitBoxEditForm: function(e)
    {
        e.preventDefault();

        tinyMCE.triggerSave();

        $.ajax({
            type: "POST",
            url: $(this).attr('action'),
            data: $(this).serialize(), // serializes the form's elements.
            statusCode: {
                200: function(data) {
                    var id = cmsWidget.widgetInEdit.data('widget-id');
                    // $('.box-' + id).attr('class', 'box container box-' + id + ' ' +  data.class);
                    // aktualizace tridy widgetu
                    $('#widget-' + id).attr('class', 'widget widget-' + id + ' ' +  data.class);
                    // ulozeni obsahu
                    $('#widget-' + id + ' .widget-content').html(data.html);
                    // nacteni obsahu widgetu
                    // $.fancybox.close('all');
                    $('#cms-modal').modal('hide');


                },
                400: function(data) {
                    $('#widget-fom-box').replaceWith(data.responseText);
                    $('#widget-edit-form').submit(cmsWidget.__submitBoxEditForm);
                }
            }
        });

    }

}

var cms = {
    init: function()
    {
        cms.__addEvents();
        // cmsBox.addBox('.box');
        cmsWidget.initWidget('.widget');
        // cmsContextMenu.init();
    },

    __addEvents: function()
    {
        $('.edit-mode-toggle').click(cms.__toggleEditModeEvent);
    },

    __toggleEditModeEvent: function()
    {
        $('body').toggleClass('edit-mode');
    },
}

$( document ).ready( cms.init );

/*
$( document ).ready(function() {
  interact('.box-template').draggable();

  interact('.drag-here').dropzone({
    accept: '.box-template',
  });
});
*/

$( function() {

  $( '.region .row' ).sortable({
    items: ".widget",
    revert: false,
    cursor: "move",
    placeholder: 'emptydiv col-md-12',
    handle: ".widget-toolbar .drag",
    receive: function(event, ui) {
        // zde prijde pridani widgetu - ajax + vlozeni widgetu do DOMu
        // var new_item  = $(this).data().uiSortable.currentItem;

        /*
        console.log(event);
        console.log(ui);
        console.log($( this ).context.childNodes);

        console.log($( this ).context.childNodes.length);
        for (var i = 0; i < $( this ).context.childNodes.length; i++)
        {
            if ($( this ).context.childNodes[i]['nodeName'] == 'SPAN')
            {

            }
        }*/

        // console.log(new_item.next().attr('id'));
        //console.log(ui.item);
        //console.log($('#box-drag').prev());
        //console.log($('#box-drag').next().data('widget-id'));
        // console.log(ui);
        // ui.helper.replaceWith('<div id="widget-1" data-widget-id="1" class="widget col-md-12"><div class="widget-content"><h1>Nadpis stránky sadas</h1></div></div>');
        var $helper = ui.helper;
        var $widget  = $helper.data('widget');
        // console.log(ui.helper.parent());
        // console.log(event.target.parentElement.data('region'));

        // predame informace - region, sort, document_id,
        $.ajax({
          type: 'POST',
          data: {
            document_id: adminParam['document_id'],
            region: $(this).parent().data('region'),
            region_type: $(this).parent().data('type'),
            prev: $('#box-drag').prev().data('widget-id'),
            next: $('#box-drag').next().data('widget-id'),
            widget: $widget
          },
          // url: admin_urls['col_new'] + '?area_id=' + area_id + '&layout_id=' + layout_id + '&page_id=' + page_id + '&location=' + location + '&ph=' + ph,
          // url: $(ui.item).attr('href') + '?col_id=' + col_id + '&prev_col=' + new_item.prev().attr('col-id') + '&' + droppable.sortable( "serialize" ),
          url: adminUrl['widget_add'],
          cache: false,
          async: false,
          success: function(data) {
            // alert(data);
            // new_item.replaceWith(data);
            // alert(droppable.sortable( "serialize", { key: "sort" } ));
            $helper.replaceWith(data.widgetHtml);

            cmsWidget.initWidget('#widget-' + data.id);
          }
        });

    }
  });


  $( ".box-template" ).draggable({
    connectToSortable: ".region .row",
    revert: "invalid",
    cursor: 'move',
    helper: function()
    {
      return $('<span id="box-drag" data-widget="' + $(this).data('widget')  + '" class="draggable-helper btn btn-danger btn-sm">' + $(this).html() + '</span>');
    },
  });


});


