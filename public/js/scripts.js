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

    function scrollToSection(event) {
      event.preventDefault();
      
      const targetId = event.currentTarget.getAttribute('href');
      const targetElement = document.querySelector(targetId);
      
      targetElement.scrollIntoView({
          behavior: 'smooth',
          block: 'start'
      });
  }
  function handleScroll() {
    const scrollToTopButton = document.getElementById('scrollToTopButton');
    if (window.pageYOffset > 100) {
      scrollToTopButton.classList.add('show');
    } else {
      scrollToTopButton.classList.remove('show');
    }
  }

  // Fonction pour effectuer un défilement fluide vers le haut
  function scrollToTop() {
    window.scrollTo({
      top: 0,
      behavior: 'smooth'
    });
  }

