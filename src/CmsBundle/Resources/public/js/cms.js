var cmsWidget = {

    widgetInEdit: null,
    regionInEdit: null,

    initWidget: function(selector)
    {
        this.__addEvents(selector);
        // this.__addContextMenu(selector);

        $('.region-edit').click(this.__loadRegionEditForm);
    },

    openEditDialog: function(id)
    {
        $('#widget-' + id + ' .widget-edit').click();
    },

    __addEvents: function(selector)
    {
        // editace widgetu - nacteni formulare
        $(selector + ' .widget-edit').click(this.__loadEditForm);

        // mazani widgetu
        $(selector + ' .widget-delete').click(this.__delete);



        // udalost tlacitka pro ulozeni, ktere neni soucasti formulare
        $('#modal-save-button').click(function(){
            $('.modal-body form').submit();
        });

    },

    /**
     * Metoda pro smazani widgetu
     * @param e Objekt udalosti
     * @private
     */
    __delete: function(e)
    {
        e.preventDefault();

        // potvrzeni mazani widgetu vyskakovacim oknem
        if (!confirm('Opravdu chcete smazat widget?')) { return false };

        // ID widgetu z parametru data-widget-id odkazu widget-delete
        var widgetId = $(this).data('widget-id');

        /* odeslani pozadavku na serveru */
        cms.ajax($(this).attr('href'), 'DELETE', $('#widget-' + widgetId).remove());
    },

    /**
     * Nacteni formulare pro editaci widgetu
     * @param e
     * @private
     */
    __loadEditForm: function(e)
    {
        e.preventDefault();
        // ulozime widget, ktery se edituje
        cmsWidget.widgetInEdit = $(this);
        // nacteme formular pro editaci widgetu
        cms.ajax($(this).attr('href'), 'GET', cmsWidget.__loadEditFormSuccess);
    },

    /**
     * Uspesne nacteni formulare pro editaci widgetu = zobrazeni modal okna
     * @param data
     * @private
     */
    __loadEditFormSuccess: function(data)
    {
        cmsModal.show(data, 'Editace obsahu', cmsWidget.__submitWidgetEditForm);
    },

    /**
     * Nacteni formulare pro editaci widgetu
     * @param e
     * @private
     */
    __loadRegionEditForm: function(e)
    {
        e.preventDefault();
        // ulozime widget, ktery se edituje
        cmsWidget.regionInEdit = $(this);
        // nacteme formular pro editaci widgetu
        cms.ajax($(this).attr('href'), 'GET', cmsWidget.__loadRegionEditFormSuccess);
    },

    /**
     * Uspesne nacteni formulare pro editaci widgetu = zobrazeni modal okna
     * @param data
     * @private
     */
    __loadRegionEditFormSuccess: function(data)
    {
        cmsModal.show(data, 'Editace regionu', cmsWidget.__submitRegionEditForm);
    },

    /*
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
    */
    /*
    __afterLoadForm: function(instance, current)
    {
        cmsWidget.widgetInEdit = current.opts.$orig;
        $('#widget-edit-form').submit(cmsWidget.__submitBoxEditForm);
    },*/

    __submitWidgetEditForm: function(e)
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
                    $('#modal-form-box').replaceWith(data.responseText);
                    $('#modal-edit-form').submit(cmsWidget.__submitRegionEditForm);
                }
            }
        });

    },

    __submitRegionEditForm: function(e)
    {
        e.preventDefault();

        $.ajax({
            type: "POST",
            url: $(this).attr('action'),
            data: $(this).serialize(), // serializes the form's elements.
            statusCode: {
                200: function(data) {
                    // alert(data);
                    $('#' + data.region).attr('class', data.class);
                    $('#cms-modal').modal('hide');
                    /*
                    var id = cmsWidget.widgetInEdit.data('widget-id');
                    // $('.box-' + id).attr('class', 'box container box-' + id + ' ' +  data.class);
                    // aktualizace tridy widgetu
                    $('#widget-' + id).attr('class', 'widget widget-' + id + ' ' +  data.class);
                    // ulozeni obsahu
                    $('#widget-' + id + ' .widget-content').html(data.html);
                    // nacteni obsahu widgetu
                    // $.fancybox.close('all');
                    $('#cms-modal').modal('hide');
                    */
                },
                400: function(data) {
                    $('#modal-fom-box').replaceWith(data.responseText);
                    $('#widget-edit-form').submit(cmsWidget.__submitRegionEditForm);
                }
            }
        });
    }

}

var cmsModal = {
    show: function(data, title, submitHandler) {
        // ziskame element modalu
        var modal = $('#cms-modal');
        // zobrazime formular
        modal.find('.modal-body').html(data);
        // pridame osetreni udalosti pro odeslani
        $('#modal-edit-form').submit(submitHandler);
        // nastavime nadpis modalu
        $('#cms-modal-label').text(title);

        // inicializace tiny
        tinymce.init({
            selector: '#modal-edit-form textarea.tiny',
            themes: "modern",
            plugins: [
                "advlist autolink lists link image charmap print preview anchor",
                "searchreplace visualblocks code fullscreen",
                "insertdatetime media table contextmenu paste",
            ],
            height : "150",
            entity_encoding : "raw"
        });

        $(document).on('focusin', function(e) {
            if ($(event.target).closest(".mce-window").length) {
                e.stopImmediatePropagation();
            }
        });

        // zobrazeni okna
        modal.modal();
        // udalost pri uzavreni okna
        modal.on('hidden.bs.modal', function () {
            if ($("#modal-edit-form textarea.tiny").length/* && tinymce.get('form_html')*/)
            {
                tinymce.remove("textarea.tiny");
            }
        })
    }
}

var cms = {
    init: function () {
        cms.__addEvents();
        // cmsBox.addBox('.box');
        cmsWidget.initWidget('.widget');
        // cmsContextMenu.init();
        tinyMCE.baseURL = '/assets/vendors/tinymce';
    },

    __addEvents: function () {
        $('.edit-mode-toggle').click(cms.__toggleEditModeEvent);
    },

    __toggleEditModeEvent: function () {
        $.cookie('cms_editmod', !$('body').hasClass('edit-mode'));
        $('body').toggleClass('edit-mode');
    },

    /**
     * Odeslani AJAX
     * @param url
     * @param method
     * @param successHandler metoda, ktera navratovy kod 200
     * @param data Data pro odeslani na server
     */
    ajax: function (url, method, successHandler, data){
        // nastaveni pozadavku
        var $result = true;
        var settings = {
            type: method,
            url:  url,
            data: data,
            statusCode: {
                // ok - spustime funci pro uspesnem navratu
                200: successHandler,
                // neexistujici stranka
                404: function(data) { $result = false; alert('Stránka neexistuje') },
                // chyba pri ukladani
                500: function(data) { $result = false;  alert('Chyba na serveru') },
                // neopravneny pristup
                403: function(data) { $result = false;  alert('Nejste přihlášeni') }
            } // statusCode
        }

        // odeslani pozadavku metodou AJAX
        $.ajax(settings);

        return $result;
    }
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
        connectWith: '.region .row',
        items: ".widget",
        revert: false,
        cursor: "move",
        placeholder: 'emptydiv col-md-12',
        handle: ".widget-toolbar .drag",
        update: function(event, ui) {
            if (!ui.item.hasClass('widget'))
            {
                return true;
            }

            var $widget = $('#' + ui.item.attr('id'));

            var params = {};
            params['document_id'] = adminParam['document_id'];
            params['region']      = $widget.parent().parent().data('region');
            params['widget_id']   = $widget.data('widget-id');
            params['prev']        = $widget.prev().data('widget-id');
            params['next']        = $widget.next().data('widget-id');

            return cms.ajax(adminUrl['widget_sort'], 'POST', null, { parameters: JSON.stringify(params) });
        },
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

            // predame informace - id dokumentu, region, typ regionu, id predchoziho a nasledujiciho widgetu a typ widget
            var params = {};
            params['document_id'] = adminParam['document_id'];
            params['region']      = $(this).parent().data('region');
            params['prev']        = $('#box-drag').prev().data('widget-id');
            params['next']        = $('#box-drag').next().data('widget-id');
            params['widget']      = $widget;

            $.ajax({
              type: 'PUT',
              data: {
                parameters: JSON.stringify(params)
              },
              // url: admin_urls['col_new'] + '?area_id=' + area_id + '&layout_id=' + layout_id + '&page_id=' + page_id + '&location=' + location + '&ph=' + ph,
              // url: $(ui.item).attr('href') + '?col_id=' + col_id + '&prev_col=' + new_item.prev().attr('col-id') + '&' + droppable.sortable( "serialize" ),
              url: adminUrl['widget_add'],
              cache: false,
              // async: false,
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


