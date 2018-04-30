var dnd = {

    init: function()
    {
        this.file();
        this.document();
    },

    file: function()
    {
        $( "#file-tree .jstree-anchor" ).draggable({
            helper: 'clone',
            start: function() {
                $("#slider-right").slideReveal("hide");
            }
        });

        $( ".widget-image-receiver .landing" ).droppable({
            accept: "#file-tree .jstree-anchor",
            drop: function( event, ui ) {
                var file = ui.draggable.attr('id');
                // pridani obrazku k widgetu - posleme ID obrazku
                var url = Routing.generate('cms_widget_add_image', { 'id': event.target.dataset.widgetId});
                // pridani obrazku k widgetu
                cms.ajax(url, 'PUT', cmsWidget.__addImage, { 'file': file });
            }
        });

        $( ".dropimage" ).droppable({
            accept: "#file-tree .jstree-anchor",
            drop: function( event, ui ) {
                var file = ui.draggable.attr('id');
                file = file.replace("_anchor", "");
                file = '/data' + file;
                $(this).val(file);
            }
        });
    },

    document: function()
    {
        $( "#page-tree .jstree-anchor .fa-link" ).draggable({
            helper: 'clone',
            start: function() {
                $("#slider-right").slideReveal("hide");
            }
        });

        $( ".droplink" ).droppable({
            accept: "#page-tree .jstree-anchor .fa-link",
            drop: function( event, ui ) {
                var document = ui.draggable.attr('id');
                event.target.value = '[' + ui.draggable.data('document-name') + ':' + ui.draggable.data('document-id') + ']';
                // pridani obrazku k widgetu - posleme ID obrazku
                // var url = Routing.generate('cms_widget_add_image', { 'id': event.target.dataset.widgetId});
                // cms.ajax(url, 'PUT', cmsWidget.__addImage, { 'file': file });
                console.log(event);
                console.log(ui);
            }
        });
    }

}




/*
helper: function(event, ui)
{
    var $clone =  ui.find('span.drag').clone();
    $clone.addClass('sortable-helper');
    var width = ui.find('span.drag').outerWidth();
    $clone.width(width);
    return $clone.get(0);
},*/