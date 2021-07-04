$(".anchor-scroll").anchorScroll({
    scrollSpeed: 800, // scroll speed
    offsetTop: 0, // offset for fixed top bars (defaults to 0)
    onScroll: function () {
        // callback on scroll start
    },
    scrollEnd: function () {
        // callback on scroll end
    }
});

//Permet d"afficher" le bouton "flèche du haut" en dessous de 800px
$(document).scroll(function () {
    var y = $(this).scrollTop();
    if (y > 800) {
        $(".floatnav-bottom").fadeIn();
    } else {
        $(".floatnav-bottom").fadeOut();
    }
});

