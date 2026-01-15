// ---------toggler header-----------
const custom_toggler_open = document.querySelector(".custom-toggler-open");
const custom_toggler_close = document.querySelector(".custom-toggler-close");
const custom_nav = document.querySelector(".custom-nav");
const body = document.querySelector("body");

custom_toggler_open.addEventListener("click", function () {
    custom_nav.classList.add("open");
    body.classList.add("lock");

})

custom_toggler_close.addEventListener("click", function () {
    custom_nav.classList.remove("open");
    body.classList.remove("lock");
})



// ----------------speakers swiper--------------
const speakers_swiper = new Swiper('.speakers-swiper', {
    loop: true,
    slidesPerView: 2.5,
    spaceBetween: 20,
    autoplay: {
        delay: 2500000,
        disableOnInteraction: false,
    },
    breakpoints: {
        650: {
            slidesPerView: 2.5
        },
        575: {
            slidesPerView: 2
        },
        0: {
            slidesPerView: 1
        }
    }
});

// ---------------sponsors---------------
const sponsors_swiper = new Swiper('.sponsors-swiper', {
    loop: true,
    slidesPerView: 4,
    spaceBetween: 20,
    navigation: {
        nextEl: '.sponsors-next',
        prevEl: '.sponsors-prev',
    },
    autoplay: {
        delay: 2500000,
        disableOnInteraction: false,
    },
    breakpoints: {
        1199: {
            slidesPerView: 4
        },
        991: {
            slidesPerView: 3
        },
        575: {
            slidesPerView: 2
        },
        0: {
            slidesPerView: 1
        }
    }
});
// ---------------sponsors end---------------