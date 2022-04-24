function ready(fn) {
    if (document.readyState != 'loading') {
        fn();
    } else {
        document.addEventListener('DOMContentLoaded', fn);
    }
}

ready(() => {
    document.querySelectorAll('img').forEach(img => {
        img.addEventListener('error', (e) => {
            e.target.src = '/assets/img/noimg.png';
        });
    });
});