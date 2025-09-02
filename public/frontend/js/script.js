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

// ---------------countdown----------------
const targetDate = new Date(2025, 8, 31, 23, 59, 59);

const daysEl = document.getElementById('days');
const hoursEl = document.getElementById('hours');
const minutesEl = document.getElementById('minutes');
const secondsEl = document.getElementById('seconds');
const messageEl = document.getElementById('message');

function pad(num, size = 2) {
    return String(num).padStart(size, '0');
}

function updateCountdown() {
    const now = new Date();
    let diff = targetDate - now;

    if (diff <= 0) {
        daysEl.textContent = '0';
        hoursEl.textContent = '00';
        minutesEl.textContent = '00';
        secondsEl.textContent = '00';
        messageEl.textContent = "Time's up!";
        clearInterval(intervalId);
        return;
    }

    const secTotal = Math.floor(diff / 1000);
    const days = Math.floor(secTotal / (24 * 3600));
    const hours = Math.floor((secTotal % (24 * 3600)) / 3600);
    const minutes = Math.floor((secTotal % 3600) / 60);
    const seconds = secTotal % 60;

    daysEl.textContent = String(days);
    hoursEl.textContent = pad(hours);
    minutesEl.textContent = pad(minutes);
    secondsEl.textContent = pad(seconds);
}

updateCountdown();
const intervalId = setInterval(updateCountdown, 1000);

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