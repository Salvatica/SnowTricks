function showMoreFigure(count = 4) {
    $(`.one-figure:hidden:lt(${count})`).fadeIn();
}

showMoreFigure(12)


$("#loadMore").click(function () {
    showMoreFigure()
    let remainingFigures = $(".one-figure:hidden").length;
    if (remainingFigures === 0) {
        $(this).hide();
    }
});
