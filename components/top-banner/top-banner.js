const glideElement = document.querySelector('.splide');

console.log(glideElement )

if (glideElement) {
    new Splide( '.splide', {
        type   : 'fade',
        perPage: 1,
        arrows: false,
        autoplay: true,
        interval: 6000,
        speed: 2000,
        rewind: true
    } ).mount();
}

  