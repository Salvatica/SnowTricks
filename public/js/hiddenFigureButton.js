const buttonHideMedia = document.getElementById("button-toggle-medias-list");
const figureMediasListElt = document.getElementById("figure-medias-list");

function togg() {
    $(figureMediasListElt) .toggleClass('d-none');
}

console.log(buttonHideMedia)
buttonHideMedia.addEventListener('click', togg);