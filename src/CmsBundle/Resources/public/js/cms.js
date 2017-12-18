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
                    cmsWidget.addWidget('#widget-' + data.id);
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

    addWidget: function(selector)
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
                      selector: '#widget-edit-form textarea',
                      themes: "modern",
                      height : "350"
                    });

                    modal.modal();

                    modal.on('hidden.bs.modal', function () {
                      if ($("#form_html").length && tinymce.get('form_html'))
                      {
                        tinymce.remove("#form_html");
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
        cmsBox.addBox('.box');
        cmsWidget.addWidget('.widget');
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

$( document ).ready(function() {
  interact('.box-template').draggable();

  interact('.drag-here').dropzone({
    accept: '.box-template',
  });
});

$( function() {

  $( '.region .row' ).sortable({ items: ".widget", revert: false, cursor: "move", placeholder: 'emptydiv col-md-12', handle: ".widget-toolbar .drag"});

  $( ".box-template" ).draggable({
    connectToSortable: ".region .row",
    revert: "invalid",
    cursor: 'move'
  });


});


