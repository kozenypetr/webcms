var treeDocument = {
    init: function()
    {
        // ionicializace stromu jstree
        $('#page-tree')
            .on('select_node.jstree', this.__selectNode)
            .on('move_node.jstree', this.__moveNode)
            .jstree({
                "core" : {
                    "check_callback" : true
                },
                "plugins" : [ "types", "dnd" ],
                "types" : {
                    "document" : {
                        "valid_children" : []
                    },
                    "document1" : {
                        "valid_children" : ["document", "document1"]
                    }
                },
            });

        // otevreni vsech stranek
        $('#page-tree').jstree('open_all');

        dnd.document();
    },

    __selectNode: function(e, data)
    {
        $(location).attr('href', '/' + data.node.a_attr.href);
    },
    /**
     *
     * @param e Udalost
     * @param data Data udalosti
     * @private
     */
    __moveNode: function(e, data)
    {
        var parentId = data.parent;
        var parent = $('#' + parentId);

        var position = data.position;
        var documentId = data.node.data.documentId;

        var url = Routing.generate('cms_document_move', { 'id': documentId, 'parent_id': parent.data('document-id') });

        return cms.ajax(url, 'POST', treeDocument.__moveNodeSuccess, { position: position });
    },

    __moveNodeSuccess: function(data)
    {
        dnd.document();
    }

}