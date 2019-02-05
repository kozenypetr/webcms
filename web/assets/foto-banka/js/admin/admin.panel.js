
function open_toolbar_window_col()
{
    $('#toolbar-add-col').removeClass('closed');
    $('#toolbar-button-col .glyphicon').removeClass('glyphicon-chevron-down');
    $('#toolbar-button-col .glyphicon').addClass('glyphicon-chevron-up');
}

function close_toolbar_window_col()
{
    $('#toolbar-add-col').addClass('closed');
    $('#toolbar-button-col .glyphicon').removeClass('glyphicon-chevron-up');
    $('#toolbar-button-col .glyphicon').addClass('glyphicon-chevron-down');
}

function open_toolbar_window_box()
{
    $('#toolbar-add-box').removeClass('closed');
    $('#toolbar-button-box .glyphicon').removeClass('glyphicon-chevron-down');
    $('#toolbar-button-box .glyphicon').addClass('glyphicon-chevron-up');
}

function close_toolbar_window_box()
{
    $('#toolbar-add-box').addClass('closed');
    $('#toolbar-button-box .glyphicon').removeClass('glyphicon-chevron-up');
    $('#toolbar-button-box .glyphicon').addClass('glyphicon-chevron-down');
}

function open_toolbar_pages()
{
    $('#toolbar-pages-list').removeClass('closed');
    $('#toolbar-button-pages .glyphicon').removeClass('glyphicon-chevron-down');
    $('#toolbar-button-pages .glyphicon').addClass('glyphicon-chevron-up');
}

function close_toolbar_pages()
{
    $('#toolbar-pages-list').addClass('closed');
    $('#toolbar-button-pages .glyphicon').removeClass('glyphicon-chevron-up');
    $('#toolbar-button-pages .glyphicon').addClass('glyphicon-chevron-down');
}

$(document).ready(function(){
    $('#toolbar-button-col').click(function(){
        if ($('#toolbar-add-col').hasClass('closed'))
        {
            open_toolbar_window_col();
            close_toolbar_window_box();
            close_toolbar_pages();
        }
        else
        {
            close_toolbar_window_col();
        }
        return false;
    });
    
    $('#toolbar-button-box').click(function(){
        if ($('#toolbar-add-box').hasClass('closed'))
        {
            open_toolbar_window_box();
            close_toolbar_window_col();
            close_toolbar_pages();
        }
        else
        {
            close_toolbar_window_box();
        }
        return false;
    });
    
    $('#toolbar-button-pages').click(function(){
        if ($('#toolbar-pages-list').hasClass('closed'))
        {
            open_toolbar_pages();
            close_toolbar_window_col();
            close_toolbar_window_box();
        }
        else
        {
            close_toolbar_pages();
        }
        return false;
    });
        
});


