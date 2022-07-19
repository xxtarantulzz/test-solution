//автовыход через 20 секунд из модального окна
var timer = null;
$('.category-list .btn.btn-danger').click(function(){
    setTimeout(function(){
        if (!$(".modal-footer .time-out").length){
            $(".modal-footer").append("<div class='time-out'>20</div>");
        }else{
            $(".modal-footer .time-out").text(20);
        }

        timer =  setInterval(function(){
            let i = 1*$(".modal-footer .time-out").text();
            i--;
            $(".modal-footer .time-out").text(i);
            if(i === 0) {
                $('.modal-footer .bootstrap-dialog-footer-buttons .btn.btn-default').click();
            }
        }, 1000);
    }, 500);
});

$('body').on('click', '.modal-footer .bootstrap-dialog-footer-buttons .btn.btn-default', function(){
    if(timer !== null){
        clearTimeout(timer);
    }
});