//автовыход через 20 секунд из модального окна
$('.category-list .btn.btn-danger').click(function(){
    setTimeout(function(){
        if (!$(".modal-footer .time-out").length){
            $(".modal-footer").append("<div class='time-out'>20</div>");
        }else{
            $(".modal-footer .time-out").text(20);
        }

        let timer =  setInterval(function(){
            let i = 1*$(".modal-footer .time-out").text();
            i--;
            $(".modal-footer .time-out").text(i);
            if(i === 0) {
                $('.modal-footer .bootstrap-dialog-footer-buttons .btn.btn-default').click();
                clearTimeout(timer);
            }
        }, 1000);
    }, 500);


});