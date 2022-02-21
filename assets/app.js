/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.scss in this case)
import './styles/app.scss';

// start the Stimulus application
import './bootstrap';
import {add} from "@hotwired/stimulus";

const addVideos = () => {
    const collectionHolder = document.querySelector('#trick_videos')
    collectionHolder.innerHTML += collectionHolder.dataset.prototype;
    console.log(collectionHolder.dataset.prototype);
};
document.querySelector('#new-bar').addEventListener('click',addVideos);


