    <!-- countdown -->
    @if(!empty($session))
    <section class="countdown">
        <div class="container">
            <div class="countdown-inner">
                <div  id="startInTime" data-start="{{ $session->start_time->toIso8601String() }}"
                >
                    <span class="small-heading-white text-center">{{$event->title ?? ''}}</span>
                    <h2 class="h2-white text-center">
                       {{$session->title ?? ''}}
                    </h2>
                    <div id="countdown" class="d-flex align-items-center justify-content-center pt-5 gap-3">
                        <div class="time-box">
                            <div id="days" class="number">0</div>
                            <div class="label">Days</div>
                        </div>
                        <div class="time-box">
                            <div id="hours" class="number">00</div>
                            <div class="label">Hours</div>
                        </div>
                        <div class="time-box">
                            <div id="minutes" class="number">00</div>
                            <div class="label">Minutes</div>
                        </div>
                        <div class="time-box">
                            <div id="seconds" class="number">00</div>
                            <div class="label">Seconds</div>
                        </div>
                    </div>

                    <div id="message"></div>
                </div>
            </div>
        </div>
    </section>
    @endif
    <!-- countdown end -->

   @push('scripts')
   <script>
const startEl = document.getElementById('startInTime');

// Prefer the real ISO datetime from PHP
let targetDate = startEl?.dataset.start ? new Date(startEl.dataset.start) : null;

// Fallback: if ISO missing, compute from diff parts you already output
if (!targetDate || isNaN(targetDate.getTime())) {
  const d = parseInt(startEl?.dataset.days ?? '0', 10);
  const h = parseInt(startEl?.dataset.hours ?? '0', 10);
  const m = parseInt(startEl?.dataset.minutes ?? '0', 10);
  const s = parseInt(startEl?.dataset.seconds ?? '0', 10);
  const ms = (((d * 24 + h) * 60 + m) * 60 + s) * 1000;
  targetDate = new Date(Date.now() + ms);
}

const daysEl    = document.getElementById('days');
const hoursEl   = document.getElementById('hours');
const minutesEl = document.getElementById('minutes');
const secondsEl = document.getElementById('seconds');
const messageEl = document.getElementById('message');

function pad(n) { return String(n).padStart(2, '0'); }

function updateCountdown() {
  const now = new Date();
  const diff = targetDate - now;

  if (diff <= 0) {
    daysEl.textContent = '0';
    hoursEl.textContent = '00';
    minutesEl.textContent = '00';
    secondsEl.textContent = '00';
    messageEl.textContent = "Event started!";
    clearInterval(intervalId);
    return;
  }

  const secTotal = Math.floor(diff / 1000);
  const days     = Math.floor(secTotal / (24 * 3600));
  const hours    = Math.floor((secTotal % (24 * 3600)) / 3600);
  const minutes  = Math.floor((secTotal % 3600) / 60);
  const seconds  = secTotal % 60;

  daysEl.textContent    = String(days);
  hoursEl.textContent   = pad(hours);
  minutesEl.textContent = pad(minutes);
  secondsEl.textContent = pad(seconds);
}

updateCountdown();
const intervalId = setInterval(updateCountdown, 1000);
</script>
<script>
(() => {
    // ---------toggler header-----------
    const custom_toggler_open = document.querySelector(".custom-toggler-open");
    const custom_toggler_close = document.querySelector(".custom-toggler-close");
    const custom_nav = document.querySelector(".custom-nav");
    const body = document.querySelector("body");

    if (custom_toggler_open && custom_toggler_close && custom_nav) {
        custom_toggler_open.addEventListener("click", function () {
            custom_nav.classList.add("open");
            body.classList.add("lock");
        });

        custom_toggler_close.addEventListener("click", function () {
            custom_nav.classList.remove("open");
            body.classList.remove("lock");
        });
    }

    // ----------------speakers swiper--------------
    new Swiper('.speakers-swiper', {
        loop: true,
        slidesPerView: 2.5,
        spaceBetween: 20,
        autoplay: {
            delay: 2500000,
            disableOnInteraction: false,
        },
        breakpoints: {
            650: { slidesPerView: 2.5 },
            575: { slidesPerView: 2 },
            0:   { slidesPerView: 1 }
        }
    });

    // ---------------sponsors---------------
    new Swiper('.sponsors-swiper', {
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
            1199: { slidesPerView: 4 },
            991:  { slidesPerView: 3 },
            575:  { slidesPerView: 2 },
            0:    { slidesPerView: 1 }
        }
    });
    // ---------------sponsors end---------------
})();
</script>

     {{-- <script>
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
     </script> --}}
   @endpush