/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';

function getUrl(type, param) {
    const functions = {
        newProject: window.location.origin + '/project/create',
        newProjectFromTemplate: window.location.origin + '/project/create/' + param,
    }

    return functions[type];
}

function main() {
    const templates = document.querySelectorAll('button[data-template-type]');
    for (const template of templates) {
        const type = template.dataset.templateType;
        const url = getUrl('newProjectFromTemplate', type);
        const add = template.children[0];
        const loading = template.children[1];
        template.addEventListener('click', (event) => {
            if (!template.disabled) {
                template.disabled = true;
                template.classList.add('animate-pulse');
                template.classList.add('cursor-not-allowed');
                add.classList.add('hidden');
                loading.classList.remove('hidden');
                window.location.href = url;
            } else {
                // Handle double click
            }
        })
    }
}

document.addEventListener('DOMContentLoaded', () => {
    main();
});