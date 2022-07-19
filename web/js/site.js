//автовыход через 20 секунд из модального окна
var timer = null;
$('.category-list .btn.btn-danger').click(function () {
    setTimeout(function () {
        if (!$(".modal-footer .time-out").length) {
            $(".modal-footer").append("<div class='time-out'>20</div>");
        } else {
            $(".modal-footer .time-out").text(20);
        }

        timer = setInterval(function () {
            let i = 1 * $(".modal-footer .time-out").text();
            i--;
            $(".modal-footer .time-out").text(i);
            if (i === 0) {
                $('.modal-footer .bootstrap-dialog-footer-buttons .btn.btn-default').click();
            }
        }, 1000);
    }, 500);
});

$('body').on('click', '.modal-footer .bootstrap-dialog-footer-buttons .btn.btn-default', function () {
    if (timer !== null) {
        clearTimeout(timer);
    }
});


//поменять название категории без перезагрузки
$('.category-list-item span.category-name').click(function () {
    let name = prompt('Введіть нову назву?', $(this).text());
    $(this).text(name);

    let id = $(this).data('id');
    $.post("/category/ajax-change-name", {id: id, name: name});
});