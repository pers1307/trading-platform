$(function() {
    'use strict';

    const notificationBell = document.querySelector('#notification');
    const url = notificationBell.dataset.url;

    notificationBell.addEventListener('click', async (event) => {
        await fetch(url, {
            method: 'POST',
        });
    });
});
