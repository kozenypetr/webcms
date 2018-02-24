var treeFile = {
    init: function()
    {
        $('#file-tree')
            .on('loaded.jstree', dnd.file)
            .on('after_open.jstree', dnd.file)
            .jstree({
                'core': {
                    'data': {
                        'url': Routing.generate('cms_file_node'),
                        'data': function (node) {

                            return {'id': node.id};
                        }
                    },
                    'themes' : {
                        'responsive' : false,
                        'stripes' : true
                    }
                }
            });
    }
}