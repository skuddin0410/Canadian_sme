// ---------toggler header-----------
const custom_toggler_open = document.querySelector(".custom-toggler-open");
const custom_toggler_close = document.querySelector(".custom-toggler-close");
const custom_nav = document.querySelector(".custom-nav");
const body = document.querySelector("body");

custom_toggler_open.addEventListener("click", function () {
  custom_nav.classList.add("open");
  body.classList.add("lock");
});

custom_toggler_close.addEventListener("click", function () {
  custom_nav.classList.remove("open");
  body.classList.remove("lock");
});

// ------------banner bottom swiper start------------
const bannerBottomSwiper = new Swiper(".banner-bottom-swiper", {
  slidesPerView: 6,
  spaceBetween: 20,
  loop: true,
  breakpoints: {
    1599: {
      slidesPerView: 6,
    },
    1024: {
      slidesPerView: 4,
    },
    // 991: {
    //     slidesPerView: 3
    // },
    575: {
      slidesPerView: 3,
      spaceBetween: 20,
    },
    0: {
      slidesPerView: 3,
      spaceBetween: 10,
    },
  },
});
// ------------banner bottom swiper end------------

// ----------------speakers swiper--------------
const speakers_swiper = new Swiper(".speakers-swiper", {
  loop: true,
  slidesPerView: 2.5,
  spaceBetween: 20,
  autoplay: {
    delay: 2500,
    disableOnInteraction: false,
  },
  breakpoints: {
    650: {
      slidesPerView: 2.5,
    },
    575: {
      slidesPerView: 2,
    },
    0: {
      slidesPerView: 1,
    },
  },
});

// ---------------sponsors---------------
const sponsors_swiper = new Swiper(".sponsors-swiper", {
  loop: true,
  slidesPerView: 4,
  spaceBetween: 20,
  navigation: {
    nextEl: ".sponsors-next",
    prevEl: ".sponsors-prev",
  },
  autoplay: {
    delay: 2500,
    disableOnInteraction: false,
  },
  breakpoints: {
    1199: {
      slidesPerView: 4,
    },
    991: {
      slidesPerView: 3,
    },
    575: {
      slidesPerView: 2,
    },
    0: {
      slidesPerView: 1,
    },
  },
});
// ---------------sponsors end---------------

// ---------------event booking start-----------
const eventSwiper = new Swiper(".eventSwiper", {
  slidesPerView: 7,
  spaceBetween: 15,
  loop: false,

  navigation: {
    nextEl: ".swiper-button-next",
    prevEl: ".swiper-button-prev",
  },

  breakpoints: {
    575: { spaceBetween: 8 },
    0: { spaceBetween: 4 },
  },
});
// ---------------event booking end-----------

// -------------testimonial slider start-------------
const testimonialSwiper = new Swiper(".testimonial-swiper .mySwiper", {
  slidesPerView: 7,
  spaceBetween: 20,
  // loop: true,
  speed: 800,
  centeredSlides: true,
  initialSlide: 3,

  // navigation: {
  //   nextEl: ".testimonial-swiper .swiper-button-next",
  //   prevEl: ".testimonial-swiper .swiper-button-prev",
  // },
  breakpoints: {
    991: { slidesPerView: 7,spaceBetween: 20, },
    0: { slidesPerView: 5,spaceBetween: 10, },
  },

  on: {
    slideChange: function () {
      const totalSlides = this.slides.length;
      const active = this.activeIndex;

      // আগে সব level class remove
      this.slides.forEach((slide) => {
        slide.className = slide.className.replace(/level-\d+/g, "");
      });

      // প্রতিটা slide এর distance calculate
      this.slides.forEach((slide, index) => {
        let distance = Math.abs(index - active);

        // যদি loop false থাকে তাহলে simple distance
        // যদি loop true থাকে তাহলে নিচেরটা ব্যবহার কর
        if (this.params.loop) {
          distance = Math.min(
            Math.abs(index - active),
            totalSlides - Math.abs(index - active),
          );
        }

        slide.classList.add(`level-${distance}`);
      });
    },
  },

});

// caption swiper
const captionSwiper = new Swiper(".captionSwiper", {

  slidesPerView: 1,
  allowTouchMove: true,
  centeredSlides: true,
  speed: 800,

  navigation: {
    nextEl: ".testimonial-swiper .swiper-button-next",
    prevEl: ".testimonial-swiper .swiper-button-prev",
  },

});


// connect both swiper
testimonialSwiper.controller.control = captionSwiper;

captionSwiper.controller.control = testimonialSwiper;



//  on: {
//     slideChange: function () {
//       const totalSlides = this.slides.length;
//       const active = this.activeIndex;

//       // আগে সব level class remove
//       this.slides.forEach((slide) => {
//         slide.className = slide.className.replace(/level-\d+/g, "");
//       });

//       // প্রতিটা slide এর distance calculate
//       this.slides.forEach((slide, index) => {
//         let distance = Math.abs(index - active);

//         // যদি loop false থাকে তাহলে simple distance
//         // যদি loop true থাকে তাহলে নিচেরটা ব্যবহার কর
//         if (this.params.loop) {
//           distance = Math.min(
//             Math.abs(index - active),
//             totalSlides - Math.abs(index - active),
//           );
//         }

//         slide.classList.add(`level-${distance}`);
//       });
//     },
//   },
// -------------testimonial slider end-------------

// ---------------custom tab start--------------
document.querySelectorAll('.tab-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    let group = btn.dataset.group;
    let target = btn.dataset.target;

    // hide all tabs in this group
    document.querySelectorAll(`[id^='${group}-']`).forEach(t => {
      t.classList.remove('active');
    });

    // show clicked tab content
    document.getElementById(target).classList.add('active');

    // active button highlight
    document.querySelectorAll(`.tab-btn[data-group='${group}']`).forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
  });
});
// ---------------custom tab end--------------
