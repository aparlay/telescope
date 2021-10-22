$('.skin_score_lable').click(function(){
    $('.skin_score_lable').removeClass('active');
    $(this).addClass('active');
});

$('.awesomeness_score_label').click(function(){
    $('.awesomeness_score_label').removeClass('active');
    $(this).addClass('active');
});

$(window).load(function(){
    setTimeout(function(){
        $('.alert').hide();
    }, 500);
});