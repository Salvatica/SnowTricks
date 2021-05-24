$('#deleteModal').on('show.bs.modal', function (e) {
    console.log(e.relatedTarget);

    $(this).find('.btn-ok').attr('href', $(e.relatedTarget).data('href'));

    $('.debug-url').html('Delete URL:  <strong>' + $(this).find('.btn-ok').attr('href') + '</strong>');
});
$('.one-figure').hide();


function showMoreFigure(count = 4) {
    $(`.one-figure:hidden:lt(${count})`).fadeIn();
}
showMoreFigure(12)


$('#loadMore').click(function () {
    showMoreFigure()
    let remainingFigures=$('.one-figure:hidden').length;
    if(remainingFigures===0)
    {
        $(this).hide();
    }
});