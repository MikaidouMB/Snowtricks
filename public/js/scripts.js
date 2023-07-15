$(document).ready(function() {
    $('#show').click(function() {
        $('.wrapper').toggle();
        $('.gallery-index').toggle();
    });

    $('#delete').click(function() {
        $('.hiding').toggle();
          });

  // Sélectionner tous les boutons d'effacement
var deleteButtons = document.querySelectorAll(".btn-remove");

// Fonction pour effacer l'élément vidéo
function deleteVideoElement(event) {
  var videoWrapper = event.target.closest(".video-edit");
  if (videoWrapper) {
    videoWrapper.remove();
  }
}

// Ajouter un écouteur d'événement à tous les boutons d'effacement
deleteButtons.forEach(function(button) {
  button.addEventListener("click", deleteVideoElement);
});

    });

