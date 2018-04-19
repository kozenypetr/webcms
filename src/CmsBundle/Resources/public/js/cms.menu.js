var cmsMenu = {
    selectedNodeId: null,
    init: function()
    {
        $('#menu')
            .on('select_node.jstree', this.__selectNode)
            .on("loaded.jstree", function (event, data) {
                $(this).jstree("open_all");
            })
            .jstree({
                "core" : {
                    "check_callback" : true
                },
                "plugins" : [ "contextmenu", "dnd" ]
            });

        $('#json').click(function(){
            // alert($("#menu").jstree(true).get_json('#', { 'flat': true }));
            console.log($("#menu").jstree(true).get_json('#'));
        });

        $('#modal-edit-form').submit(function(){
            $('#form_tree').val(JSON.stringify($("#menu").jstree(true).get_json('#')));
        });

    },

    __selectNode: function (e, data)
    {
        console.log(e);
        console.log(data);
        // alert(data.node.id);

        var nodeId = data.node.id;
        cmsMenu.selectedNodeId = nodeId;
        $('#form_menuitem_title').val(data.node.text);
        $('#form_menuitem_link').val(data.node.li_attr.cmslink);
        $('#form_menuitem_class').val(data.node.li_attr.cmsclass);
        $('#menuitem-form').show();

        $('#menuitem-form #menuitem-save-button').click(function(){
            var li = $('#' + cmsMenu.selectedNodeId);

            $('#menu').jstree('set_text', li, $('#form_menuitem_title').val());
            $('#menu').jstree('get_node', li).li_attr['cmslink'] = $('#form_menuitem_link').val();
            $('#menu').jstree('get_node', li).li_attr['cmsclass'] = $('#form_menuitem_class').val();

            // li.attr('data-desc', req.description); // here i am changing data attribute
            // $('#tree_1').jstree('get_node', li).li_attr['data-desc'] = req.description;

            // li.attr('cmslink', $('#form_menuitem_link').val());
            // li.attr('cmsclass', $('#form_menuitem_class').val());
            // li.find('a').html('<i class="jstree-icon jstree-themeicon" role="presentation"></i>' + $('#form_menuitem_title').val());
            $('#menuitem-form').hide();
        });

        $('#menuitem-form #menuitem-storno-button').click(function(){
            $('#menuitem-form').hide();
        });
    }


}