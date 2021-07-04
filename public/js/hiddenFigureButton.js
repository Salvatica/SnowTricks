const buttonHideMedia = document.getElementById("button-toggle-medias-list");
const figureMediasListElt = document.getElementById("figure-medias-list");

function togg() {
    $(figureMediasListElt) .toggleClass('d-none');
}
buttonHideMedia.addEventListener('click', togg);