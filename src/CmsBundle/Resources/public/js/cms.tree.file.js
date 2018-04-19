var treeFile = {
    init: function()
    {
        $('#file-tree')
            .on('loaded.jstree', this.__afterLoad)
            .on('after_open.jstree', this.__afterLoad)
            .jstree({
                'core': {
                    'data': {
                        'url': Routing.generate('cms_file_node'),
                        'data': function (node) {
                            return {'id': node.id};
                        }
                    }
                }
        });



    },

    __afterLoad: function() {
        dnd.file();

        $("#file-tree .jstree-anchor").on("mouseenter", function(event) {
            event.stopImmediatePropagation();

            $('#image-preview').html('<img src="/images/loading.gif" width="80" />');
            $('#image-preview').show();

            var url = Routing.generate('cms_image_preview', { 'file': encodeURI($(this).attr('id')) });

            cms.ajax(url, 'GET', treeFile.__imagePreview);
        });

        $("#file-tree .jstree-anchor").on("mouseleave", function(event) {
            $('#image-preview').hide();
        });
    },

    __imagePreview: function(data)
    {
        if (data.status == 'noimage')
        {
            $('#image-preview').hide();
        }

        if (data.status == 'image')
        {
            $('#image-preview').html('<img width="400" src="' + data.path + '" alt="' + data.path + '" />');
        }
    }
}