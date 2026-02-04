// ---------toggler header-----------
const custom_toggler_open  = document.querySelector(".custom-toggler-open");
const custom_toggler_close = document.querySelector(".custom-toggler-close");
const custom_nav = document.querySelector(".custom-nav");
const body = document.body;

function openNav() {
  custom_nav.classList.add("open");
  body.classList.add("lock");
  body.classList.add("overlay");
}

function closeNav() {
  custom_nav.classList.remove("open");
  body.classList.remove("lock");
  body.classList.remove("overlay");
}

// Open nav
custom_toggler_open.addEventListener("click", (e) => {
  e.stopPropagation();
  openNav();
});

// Close nav button
custom_toggler_close.addEventListener("click", (e) => {
  e.stopPropagation();
  closeNav();
});

// Prevent closing when clicking inside nav
custom_nav.addEventListener("click", (e) => {
  e.stopPropagation();
});

// Close when clicking outside
document.addEventListener("click", () => {
  if (custom_nav.classList.contains("open")) {
    closeNav();
  }
});
// ---------toggler header end-----------




// ----------------speakers swiper--------------
const speakers_swiper = new Swiper('.speakers-swiper', {
    loop: true,
    slidesPerView: 2.5,
    spaceBetween: 20,
    autoplay: {
        delay: 2500,
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
        delay: 2500,
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