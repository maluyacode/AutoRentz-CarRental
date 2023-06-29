// Add slider functionality to each slider div
let sliders = document.querySelectorAll('.slider');
sliders.forEach(slider => {
    let slides = slider.querySelectorAll('.slide');
    let currentSlide = 0;
    let prevButton = slider.querySelector('.prev');
    let nextButton = slider.querySelector('.next');

    function showSlide() {
        slides.forEach(slide => {
            slide.style.display = 'none';
        });
        slides[currentSlide].style.display = 'block';
    }

    function nextSlide() {
        currentSlide++;
        if (currentSlide >= slides.length) {
            currentSlide = 0;
        }
        showSlide();
    }

    function prevSlide() {
        currentSlide--;
        if (currentSlide < 0) {
            currentSlide = slides.length - 1;
        }
        showSlide();
    }

    showSlide();
    prevButton.addEventListener('click', prevSlide);
    nextButton.addEventListener('click', nextSlide);
});
