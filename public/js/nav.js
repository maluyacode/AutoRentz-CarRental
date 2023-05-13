const links = document.querySelectorAll('.nav-link-color');

links.forEach((link) => {
    const linkId = link.getAttribute('id');
    if (localStorage.getItem(linkId)) {
        link.classList.add('clicked');
    }
});

links.forEach((link) => {
    const linkId = link.getAttribute('id');
    localStorage.removeItem(linkId);
    link.addEventListener('click', function () {
        link.classList.add('clicked');
        const linkId = link.getAttribute('id');
        localStorage.setItem(linkId, true);
    });
});

