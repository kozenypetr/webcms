


var sliderFile = {
    init: function()
    {
        $("#slider-file").slideReveal({
            trigger: $("#slider-file .handle"),
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







var cmsDocument = {
    init: function()
    {
        $('.document-new').click(this.__loadCreateForm);
        $('.document-edit').click(this.__loadEditForm);
        $('.document-delete').click(this.__delete);
    },


    __delete: function(e)
    {
        e.preventDefault();
        if (confirm('Opravdu chcete smazat dokument?'))
        {
            cms.ajax($(this).attr('href'), 'DELETE', cmsDocument.__deleteCallback);
        }
    },

    __deleteCallback: function(data)
    {
        // console.log(data);
        $(location).attr('href', '/');
    },

    /**
     * Nacteni formulare pro pridani stranky
     * @param e
     * @private
     */
    __loadCreateForm: function(e)
    {
        e.preventDefault();
        // nacteme formular pro editaci widgetu
        cms.ajax($(this).attr('href'), 'GET', cmsDocument.__loadCreateFormSuccess);
    },

    /**
     * Nacteni formulare pro editaci stranky
     * @param e
     * @private
     */
    __loadEditForm: function(e)
    {
        e.preventDefault();
        // nacteme formular pro editaci widgetu
        cms.ajax($(this).attr('href'), 'GET', cmsDocument.__loadEditFormSuccess);
    },

    /**
     * Uspesne nacteni formulare pro editaci widgetu = zobrazeni modal okna
     * @param data
     * @private
     */
    __loadCreateFormSuccess: function(data)
    {
        cmsModal.show(data, 'Nová stránka', cmsDocument.__submitCreateForm);
    },

    /**
     * Uspesne nacteni formulare pro editaci stanky = zobrazeni modal okna
     * @param data
     * @private
     */
    __loadEditFormSuccess: function(data)
    {
        cmsModal.show(data, 'Editace stránky', cmsDocument.__submitEditForm);
    },


    __submitCreateForm: function(e)
    {
        e.preventDefault();

        $.ajax({
            type: "POST",
            url: $(this).attr('action'),
            data: $(this).serialize(), // serializes the form's elements.
            statusCode: {
                200: function(data) {
                    // $('#' + data.region).attr('class', data.class);
                    $('#cms-modal').modal('hide');

                    $(location).attr('href', '/' + data.url);
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
                    $('#modal-edit-form').submit(cmsDocument.__submitCreateForm);
                }
            }
        });
    },

    __submitEditForm: function(e)
    {
        e.preventDefault();

        $.ajax({
            type: "POST",
            url: $(this).attr('action'),
            data: $(this).serialize(), // serializes the form's elements.
            statusCode: {
                200: function(data) {
                    $('#cms-modal').modal('hide');
                    //if (adminParam['url'] != data.url)
                    //{
                        $(location).attr('href', '/' +  data.url);
                    //}
                },
                400: function(data) {
                    $('#modal-fom-box').replaceWith(data.responseText);
                    $('#modal-edit-form').submit(cmsDocument.__submitCreateForm);
                }
            }
        });
    }
}

var cmsRegion = {

    widgetAddAfter: null,

    init: function()
    {
        // pridani widgetu do regionu jako prvni
        $('.region .widget-plus-region').click(this.__addWidgetRegion);
    },

    /**
     * Pridani widgetu do regionu
     * @param e
     * @private
     */
    __addWidgetRegion: function(e)
    {
        e.preventDefault();
        // ulozime widget, ktery se edituje
        cmsRegion.regionWidgetAdd = $('#' + $(this).data('region-id')) ;
        // nacteme formular pro editaci widgetu
        cmsModal.show($('#widgetlist').html(), 'Nový widget', null);

        $('.widget-new-item').click(cmsRegion.__addWidgetRegionDone)
    },

    __addWidgetRegionDone: function(e)
    {
        // zavreni modalu
        cmsModal.hide();

        // pozadavek na pridani widget na server
        var params = {};
        params['document_id'] = adminParam['document_id'];
        params['region']      = cmsRegion.regionWidgetAdd.data('region');
        params['prev']        = null
        params['next']        = cmsRegion.regionWidgetAdd.find('.widgetsholder').first().data('widget-id');
        params['widget']      = $(this).data('widget');

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
                // $helper.replaceWith(data.widgetHtml);
                cmsRegion.regionWidgetAdd.find('.widgetsholder').prepend(data.widgetHtml);
                cmsWidget.initWidget('.widget-' + data.id);
            }
        });
    }

}

var cmsWidget = {

    widgetInEdit: null,
    widgetAddAfter: null,
    regionWidgetAdd: null,
    regionInEdit: null,

    initWidget: function(selector)
    {
        this.__addEvents(selector);
        // this.__addContextMenu(selector);

        $('.region-edit').click(this.__loadRegionEditForm);

        dnd.file();
    },

    reloadWidget: function(id)
    {
        var url = Routing.generate('cms_widget_reload', { 'id': id });
        cms.ajax(url, 'GET', cmsWidget.__reloadCallback);
    },

    __reloadCallback: function(data)
    {
        var widgetId = data.id;
        $('.widget-' + widgetId + ' .widget-content').html(data.html);
    },

    openEditDialog: function(id)
    {
        $('.widget-' + id + ' .widget-edit').click();
    },

    __addEvents: function(selector)
    {
        // editace widgetu - nacteni formulare
        $(selector + ' .widget-edit').click(this.__loadEditForm);

        // mazani widgetu
        $(selector + ' .widget-delete').click(this.__delete);

        // pridani podwidgetu
        $(selector + ' .widget-plus').click(this.__addWidget);

        $(selector + ' .widget-copy').click(this.__copyWidget);

        $(selector + ' .widget-paste').click(this.__pasteWidget);

        $(selector + ' .widget-down').click(this.__downWidget);

        $(selector + ' .widget-up').click(this.__upWidget);

        // udalost tlacitka pro ulozeni, ktere neni soucasti formulare
        $('#modal-save-button').click(function(){
            $('.modal-body form').submit();
        });

    },


    __addImage: function(data)
    {
        cmsWidget.reloadWidget(data.id);
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
        cms.ajax($(this).attr('href'), 'DELETE', $('.widget-' + widgetId).remove());
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
        // cmsWidget.__initDropDocument();
        dnd.document();
        dnd.file();
    },

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
                    $('.widget-' + id).attr('class', 'widget widget-' + id + ' ' +  data.class);
                    // ulozeni obsahu
                    $('.widget-' + id + ' .widget-content').html(data.html);
                    // nacteni obsahu widgetu
                    // $.fancybox.close('all');
                    $('#cms-modal').modal('hide');
                },
                400: function(data) {
                    cmsModal.tinyDestroy();
                    $('#modal-form-box').replaceWith(data.responseText);
                    $('#modal-edit-form').submit(cmsWidget.__submitWidgetEditForm);
                    cmsModal.tinyInit();
                }
            }
        });

    },

    __copyWidget: function(e)
    {
        $.cookie('cms_copy_widget_id', $(this).data('widget-id'), { path: '/' });
        alert('Widget byl umístěn do schránky');
        $('.widget-paste').addClass('active');
        $('.widget-paste-region').addClass('active');
    },

    __pasteWidget: function(e)
    {
        var copyWidgetId = $.cookie('cms_copy_widget_id');
        // alert(copyWidgetId);

        var insertWidgetId = $(this).data('widget-id');

        // odeslani kopie widgetu na server
        var params = {};
        params['document_id'] = adminParam['document_id'];
        params['region']      = $(this).closest( '.region' ).data('region');
        params['prev']        = $('.widget-' + insertWidgetId).data('widget-id');
        params['next']        = $('.widget-' + insertWidgetId).next().data('widget-id');

        // $.cookie('cms_copy_widget_id', null);
        $.removeCookie('cms_copy_widget_id');

        $.ajax({
            type: 'POST',
            data: {
                parameters: JSON.stringify(params)
            },
            // url: admin_urls['col_new'] + '?area_id=' + area_id + '&layout_id=' + layout_id + '&page_id=' + page_id + '&location=' + location + '&ph=' + ph,
            // url: $(ui.item).attr('href') + '?col_id=' + col_id + '&prev_col=' + new_item.prev().attr('col-id') + '&' + droppable.sortable( "serialize" ),
            url: Routing.generate('cms_widget_copy', { 'id': copyWidgetId }),
            cache: false,
            // async: false,
            success: function(data) {
                $('.widget-' + insertWidgetId).after(data.widgetHtml);
                cmsWidget.initWidget('.widget-' + data.id);
            }
        });

        $('.widget-paste').removeClass('active');
        $('.widget-paste-region').removeClass('active');

    },


    /**
     * Nacteni formulare pro editaci widgetu
     * @param e
     * @private
     */
    __addWidget: function(e)
    {
        e.preventDefault();
        // ulozime widget, ktery se edituje
        cmsWidget.widgetAddAfter = $('.widget-' + $(this).data('widget-id')) ;
        // nacteme formular pro editaci widgetu
        // cms.ajax($(this).attr('href'), 'GET', cmsWidget.__addWidgetListSuccess);
        cmsModal.show($('#widgetlist').html(), 'Nový widget', null);

        $('.widget-new-item').click(cmsWidget.__addWidgetDone)
    },

    __addWidgetDone: function(e)
    {
        // zavreni modalu
        cmsModal.hide();

        // pozadavek na pridani widget na server
        var params = {};
        params['document_id'] = adminParam['document_id'];
        params['region']      = cmsWidget.widgetAddAfter.closest( '.region' ).data('region');
        params['prev']        = cmsWidget.widgetAddAfter.data('widget-id');
        params['next']        = cmsWidget.widgetAddAfter.next().data('widget-id');
        params['widget']      = $(this).data('widget');

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
                // $helper.replaceWith(data.widgetHtml);
                cmsWidget.widgetAddAfter.after(data.widgetHtml);
                cmsWidget.initWidget('.widget-' + data.id);
            }
        });
    },





    __downWidget: function(e)
    {
        var widgetId = $(this).data('widget-id');
        var $widget = $('.widget-' + widgetId);

        if ($widget.next().length == 0)
        {
            alert('Nelze posunout níž');
            return false;
        }

        $widget.next().after($widget);

        var params = {};
        params['document_id'] = adminParam['document_id'];
        params['region']      = $widget.closest( '.region' ).data('region');
        params['widget_id']   = $widget.data('widget-id');
        params['prev']        = $widget.prev().data('widget-id');
        params['next']        = $widget.next().data('widget-id');

        return cms.ajax(adminUrl['widget_sort'], 'POST', null, { parameters: JSON.stringify(params) });
    },

    __upWidget: function(e)
    {
        var widgetId = $(this).data('widget-id');
        var $widget = $('.widget-' + widgetId);

        if ($widget.prev().length == 0)
        {
            alert('Nelze posunout výš');
            return false;
        }

        $widget.prev().before($widget);

        // console.log($widget.prev());
        //  console.log($widget.next());

        var params = {};
        params['document_id'] = adminParam['document_id'];
        params['region']      = $widget.closest( '.region' ).data('region');
        params['widget_id']   = $widget.data('widget-id');
        params['prev']        = $widget.prev().data('widget-id');
        params['next']        = $widget.next().data('widget-id');

        return cms.ajax(adminUrl['widget_sort'], 'POST', null, { parameters: JSON.stringify(params) });
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

                    $(location).attr('href', '/' + adminParam['url']);
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
                    $('#modal-edit-form').submit(cmsWidget.__submitRegionEditForm);
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

        if (submitHandler)
        {
            $('#modal-save-button').show();
        }
        else
        {
            $('#modal-save-button').hide();
        }

        // nastavime nadpis modalu
        $('#cms-modal-label').text(title);

        this.tinyInit();

        $(document).on('focusin', function(event) {
            if ($(event.target).closest(".mce-window").length) {
                e.stopImmediatePropagation();
            }
        });

        // zobrazeni okna
        modal.modal();
        // udalost pri uzavreni okna
        modal.on('hidden.bs.modal', function () {
            cmsModal.tinyDestroy();
        })
    },

    hide: function()
    {
        $('#cms-modal').modal('hide');
    },

    tinyInit: function()
    {
        // inicializace tiny
        tinymce.init({
            selector: '#modal-edit-form textarea.tiny',
            themes: "modern",
            menubar: false,
            language: 'cs',
            force_br_newlines : false,
            force_p_newlines : true,
            forced_root_block : '',
            convert_urls: false,
            verify_html: false,
            remove_script_host: false,
            codemirror: { indentOnInit:true, path:'/assets/tinymce/plugins/codemirror/codemirror-4.8'},
            plugins: [
                "advlist autolink link image lists charmap print preview hr anchor pagebreak",
                "searchreplace wordcount visualblocks visualchars insertdatetime media nonbreaking spellchecker",
                "table contextmenu directionality emoticons paste textcolor code codemirror codesample table"
            ],
            height : "250",
            entity_encoding : "raw",
            toolbar1: "undo redo | bold italic underline | forecolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent",
            toolbar2: "styleselect |  image | media | link unlink | table | code codesample"
        });
    },

    tinyDestroy: function()
    {
        if ($("#modal-edit-form textarea.tiny").length/* && tinymce.get('form_html')*/)
        {
            tinymce.remove("textarea.tiny");
        }
    }

}

var cms = {
    init: function () {
        cms.__addEvents();
        // cmsBox.addBox('.box');
        cmsWidget.initWidget('.widget');

        cmsRegion.init();

        cmsDocument.init();

        // cmsContextMenu.init();
        tinyMCE.baseURL = '/assets/tinymce';

        treeDocument.init();

        treeFile.init();

       // postranni box se soubory
        slider.init();

        $("#cms-panel img").hide();
        // $( "#widget-dialog" ).dialog();

    },

    __addEvents: function () {
        $('.edit-mode-toggle').click(cms.__toggleEditModeEvent);
        $('.edit-mode-region-toggle').click(cms.__toggleEditModeRegionEvent);
    },

    __toggleEditModeEvent: function () {
        $.cookie('cms_editmod', !$('body').hasClass('edit-mode'));
        $('body').toggleClass('edit-mode');
    },

    __toggleEditModeRegionEvent: function () {
        $.cookie('cms_editmod_region', !$('body').hasClass('edit-mode-region'));
        $('body').toggleClass('edit-mode-region');
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



/*
$( document ).ready(function() {
  interact('.box-template').draggable();

  interact('.drag-here').dropzone({
    accept: '.box-template',
  });
});
*/

$(window).on("load", function(){
    $("#cms-panel img").hide();
})

$( document ).ready( cms.init );



$( function() {

    $( '.region .widgetsholder' ).sortable({
        connectWith: '.region .widgetsholder',
        items: ".widget",
        revert: false,
        cursor: "move",
        cursorAt: {left: 20, top: 10},
        placeholder: 'emptydiv clear',
        handle: ".widget-toolbar .drag",
        helper: function(event, ui)
        {
            var $clone =  ui.find('span.drag').clone();
            $clone.addClass('sortable-helper');
            var width = ui.find('span.drag').outerWidth();
            $clone.width(width);
            return $clone.get(0);
        },
        // presunuti widgetu
        update: function(event, ui) {
            if (!ui.item.hasClass('widget'))
            {
                return true;
            }

            var $widget = $('.widget-' + ui.item.data('widget-id'));

            var params = {};
            params['document_id'] = adminParam['document_id'];
            params['region']      = $widget.closest( '.region' ).data('region');
            params['widget_id']   = $widget.data('widget-id');
            params['prev']        = $widget.prev().data('widget-id');
            params['next']        = $widget.next().data('widget-id');

            return cms.ajax(adminUrl['widget_sort'], 'POST', null, { parameters: JSON.stringify(params) });
        },
        // vlozeni widgetu
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

            if (!$helper.hasClass('draggable-helper'))
            {
                return false;
            }

            var $widget  = $helper.data('widget');
            // console.log(ui.helper.parent());
            // console.log(event.target.parentElement.data('region'));

            // predame informace - id dokumentu, region, typ regionu, id predchoziho a nasledujiciho widgetu a typ widget
            var params = {};
            params['document_id'] = adminParam['document_id'];
            params['region']      = $(this).closest( '.region' ).data('region');
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
                cmsWidget.initWidget('.widget-' + data.id);
              }
            });

        }
    });


  $( "#cms-panel .widget" ).draggable({
    connectToSortable: ".region .widgetsholder",
    revert: "invalid",
    cursor: 'move',
    appendTo: 'body',
    helper: function()
    {
      return $('<span id="box-drag" data-widget="' + $(this).data('widget')  + '" class="draggable-helper">' + $(this).html() + '</span>');
    },
    cursor: "move",
    cursorAt: {left: 20, top: 10},
    start: function (event, ui) {
        $('.widgets').removeClass('open ');
    },
  });

    $(document).bind("ajaxSend", function(){
        $("#cms-panel img").show();
    }).bind("ajaxComplete", function(){
        $("#cms-panel img").hide();
    });

});




