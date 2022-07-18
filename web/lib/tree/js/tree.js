$(function () {
    var treeView = $('#treeViewCategory');
    var formName = treeView.data('formName');
    var csrf = $('#_csrf').val();


    $("#treeViewCategory ol li>div").droppable({
        greedy: true,
        hoverClass: "drop-hover",
        drop: function (event, ui) {
            if(!$(this).hasClass('active')){
                ui.draggable.css('visibility', 'hidden');
            }
        }
    });

    $(document).on('click', '.collapse-list-btn', function () {
        $(this).find('i').toggleClass('fa-arrow-up').toggleClass('fa-arrow-down');
    });

    treeView.find('> ol').nestedSortable({
        items: '.category-list-item',
        helper: 'clone',
        handle: '.drag-btn',
        toleranceElement: '> div',
        isTree: true,
        update: function (e, obj) {
            var url = obj.item.data('updateUrl');
            var data = {_csrf: csrf};
            var parent = obj.item.parents('li');
            data[formName] = {
                sort_order: 0,
                parent_id: parent.length ? parent.data('id') : null
            };

            obj.item.parent().children('li').each(function(){
                var url = $(this).attr('data-update-url');
                data[formName].sort_order++;
                $(this).attr({'data-sort': data[formName].sort_order});

                $.ajax({
                    url: url,
                    method: 'post',
                    data: data
                });
            });

            updateCollapseButtons();
        }
    }).disableSelection();

    function updateCollapseButtons() {
        var items = treeView.find('li');
        items.each(function () {
            var item = $(this);
            var btn = item.find('> div .collapse-list-btn');
            var list = item.find('> ol');
            if (list.children().length) {
                btn.removeClass('disabled');
                if (list.hasClass('in')) {
                    btn.find('i').removeClass('fa-arrow-down').addClass('fa-arrow-up');
                } else {
                    btn.find('i').removeClass('fa-arrow-up').addClass('fa-arrow-down');
                }
            } else {
                btn.addClass('btn-default');
            }
        });
    }
});