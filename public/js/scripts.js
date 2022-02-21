const addVideos = () => {
    const collectionHolder = document.querySelector('#trick_videos')
    collectionHolder.innerHTML += collectionHolder.dataset.prototype;
    console.log(collectionHolder.dataset.prototype);
};
document.querySelector('#new-bar').addEventListener('click',addVideos);

