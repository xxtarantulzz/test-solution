var $dynamic_grid_filter_columns = $('#dynamic_grid_filter_columns');

function start() {
    setTimeout(function () {
        var i = 0;
        $('.dynagrid-config-form .sortable-visible li[role="option"]').each(function () {
            $dynamic_grid_filter_columns.next().children('ul').append('<li class="list-group-item"><div class="material-text pull-left">' + $(this).text() + '</div><div class="material-switch pull-right"><input id="attr' + $(this).attr('id') + '" data-id="' + $(this).attr('id') + '" type="checkbox" checked/><label for="attr' + $(this).attr('id') + '" class="label-primary"></label></div><div class="clearfix"></div></li>');
            i++;
        });

        $('.dynagrid-config-form .sortable-hidden li[role="option"]').each(function () {
            $dynamic_grid_filter_columns.next().children('ul').append('<li class="list-group-item"><div class="material-text pull-left">' + $(this).text() + '</div><div class="material-switch pull-right"><input id="attr' + $(this).attr('id') + '" data-id="' + $(this).attr('id') + '" type="checkbox"/><label for="attr' + $(this).attr('id') + '" class="label-primary"></label></div><div class="clearfix"></div></li>');
            i++;
        });

        if (i === 0) {
            start();
        } else {
            $(".dynamic_grid_filter_columns ul").sortable({delay: 200, opacity: 0.7, axis: 'y'}).disableSelection();

            var pageY = 0;
            $('.dynamic_grid_filter_columns div.form ul').on('mousedown', 'li.list-group-item', function (event) {
                if (event.target.nodeName === 'LI') {
                    pageY = event.pageY;
                }
            });
            $('.dynamic_grid_filter_columns div.form ul').on('mouseup', 'li.list-group-item', function (event) {
                if (event.target.nodeName === 'LI' && Math.abs(pageY - event.pageY) < 20) {
                    $('.material-switch label', this).click();
                }
            });
        }
    }, 100);
}

start();

$('body').click(function (e) {
    if (e.target.nodeName !== 'BUTTON') {
        if ($dynamic_grid_filter_columns.hasClass('active')) {
            if ($(e.target).parents('.dynamic_grid_filter_columns').length === 0) {
                $dynamic_grid_filter_columns.next().hide();
                $dynamic_grid_filter_columns.removeClass('active');
            }
        }
    } else {
        if ($(e.target).attr('id') === 'dynamic_grid_filter_columns') {
            if ($dynamic_grid_filter_columns.hasClass('active')) {
                $dynamic_grid_filter_columns.next().hide();
                $dynamic_grid_filter_columns.removeClass('active');
            } else {
                $dynamic_grid_filter_columns.next().show();
                $dynamic_grid_filter_columns.addClass('active');
            }
        } else {
            if ($dynamic_grid_filter_columns.hasClass('active')) {
                if (e.target.nodeName === 'BUTTON' && $(e.target).attr('id') !== 'dynamic_grid_filter_columns') {
                    if ($(e.target).attr('id') === 'dynamic_grid_filter_columns_save') {
                        var visibleKeys = '';
                        $('.dynamic_grid_filter_columns input:checked').each(function () {
                            if (visibleKeys !== '') {
                                visibleKeys += ',';
                            }

                            visibleKeys += $(this).attr('data-id');
                        });

                        if (visibleKeys === '') {
                            visibleKeys = '00000000';
                        }

                        $('.dynagrid-config-form input[name="visibleKeys"]').val(visibleKeys);
                        $('.dynagrid-config-form form').submit();
                    } else {
                        $dynamic_grid_filter_columns.next().hide();
                        $dynamic_grid_filter_columns.removeClass('active');
                    }
                }
            }
        }
    }
});