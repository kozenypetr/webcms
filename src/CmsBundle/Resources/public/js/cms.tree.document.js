var treeDocument = {
    init: function()
    {
        // ionicializace stromu jstree
        $('#page-tree')
            .on('select_node.jstree', this.__selectNode)
            .jstree();

        // otevreni vsech stranek
        $('#page-tree').jstree('open_all');

        // inicializace 
        this.__initDrag();
    },

    __selectNode: function(e, data)
    {
        $(location).attr('href', '/' + data.node.a_attr.href);
    },

    __initDrag: function(e, data)
    {
        $( "#page-tree .jstree-anchor" ).draggable({
            helper: 'clone',
            start: function() {
                $("#slider-right").slideReveal("hide");
            }
        });
    }
}