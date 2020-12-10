$(document).on("mouseover", ".slide-toggle",function () {
    $(".slide-menu").removeClass("animate__animated animate__slideInRight");
    $(this).find(".slide-menu").addClass("animate__animated animate__slideInRight")
});

$(document).on("mouseleave", ".slide-toggle",function () {
    $(".slide-menu").removeClass("animate__animated animate__slideInRight");
});

$(document).on("click", ".list-group-facet-item-fid", function () {
    $('#filterModal').modal('hide');
    $('body').removeClass('modal-open');
    $('.modal-backdrop').remove();

});


$(document).on("click", ".more-button", function () {
    $(this).siblings().prop("hidden", false);
    $(this).prop("hidden", true)
});

$(document).on("click", ".less-button", function () {
    var siblings = $(this).siblings();
    for (i=0; i < siblings.length; i++ ) {
        if (i>4){
            $(siblings[i]).prop("hidden", true)
        }
        console.log($(siblings[i]).attr("id"))
        $('.more-button').prop("hidden", false);
        $(this).prop("hidden", true)

    }
});

