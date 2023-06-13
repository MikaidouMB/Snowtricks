$(document).ready(function() {
    $('#show').click(function() {
        $('.wrapper').toggle();
        $('.gallery-index').toggle();
    });

   // ...

    // Gérer l'ajout de vidéos
    var videosContainer = $('#videos');
    var addButton = $('#btn-add');
    var newVideoUrlInput = $('#newVideoUrl');

    addButton.on('click', function () {
        var template = videosContainer.data('prototype');
        var form = template.replace(/__name__/g, new Date().getTime());
        videosContainer.append(form);
    });

    // ...

    // Gérer l'ajout de la nouvelle vidéo saisie dans le champ d'URL
    $('#videoForm').on('submit', function(event) {
        event.preventDefault();

        var newVideoUrl = newVideoUrlInput.val();
        var videoId = getDailymotionVideoId(newVideoUrl);

        if (videoId) {
            var newVideoForm = videosContainer.data('prototype');
            newVideoForm = newVideoForm.replace(/__name__/g, new Date().getTime());
            newVideoForm = newVideoForm.replace(/__video_url__/g, newVideoUrl);
            videosContainer.append(newVideoForm);

            newVideoUrlInput.val(''); // Réinitialiser le champ d'URL
        } else {
            alert("URL de vidéo Dailymotion invalide");
        }
    });

});

// Fonction pour extraire l'identifiant de la vidéo Dailymotion à partir de l'URL
function getDailymotionVideoId(url) {
    var regex = /https?:\/\/(www\.)?dailymotion\.com\/video\/([A-Za-z0-9]+)/;
    var match = url.match(regex);
    if (match) {
        return match[2];
    } else {
        return null;
    }

}
  