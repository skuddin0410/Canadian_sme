<!-- footer -->
<footer>
    <div class="container">
        <div class="footer-top d-flex flex-column flex-sm-row gap-4 justify-content-sm-between align-items-center">
            <a href="{{ url('/') }}">
                <img class="logo" src="{{ asset('frontend/images/footer-logo.png') }}" alt="">
            </a>

            <div class="d-lg-flex align-items-center gap-4">
                <span class="small-heading-white text-center text-sm-start">
                    Share event information on
                </span>

                {{-- Compute safe fallback values so $shareUrl being undefined won't break --}}
                @php
                    $safeUrl = $shareUrl ?? request()->fullUrl();
                    $tweetText = (isset($event) && $event) ? $event->title : 'Check out this event!';
                @endphp

                <ul class="footer-social-group p-0 d-flex gap-3">
                    {{-- Facebook --}}
                    <li>
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($safeUrl) }}"
                           target="_blank" rel="noopener">
                            <i class="fa-brands fa-facebook-f text-light"></i>
                        </a>
                    </li>

                    {{-- Instagram (copy link) --}}
                    <li>
                        <a href="javascript:void(0)" onclick="copyLink(@json($safeUrl))">
                            <i class="fa-brands fa-instagram text-light"></i>
                        </a>
                    </li>

                    {{-- LinkedIn --}}
                    <li>
                        <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode($safeUrl) }}"
                           target="_blank" rel="noopener">
                            <i class="fa-brands fa-linkedin-in text-light"></i>
                        </a>
                    </li>

                    {{-- Twitter / X --}}
                    <li>
                        <a href="https://twitter.com/intent/tweet?url={{ urlencode($safeUrl) }}&text={{ urlencode($tweetText) }}"
                           target="_blank" rel="noopener">
                            <i class="fa-brands fa-x-twitter text-light"></i>
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <p class="black-text-18 text-light text-center">© {{ date('Y') }}
                <a class="text-light" href="{{ url('/') }}">{{ config('app.name') }}</a>
            </p>
        </div>
    </div>
</footer>
<!-- footer end -->

<script>
/* Use @json($safeUrl) in onclick above OR define once here for reuse */
function copyLink(link) {
    // modern secure API
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(link).then(() => {
            alert("✅ Event link copied! Paste it into your Instagram bio or story.");
        }).catch(err => {
            console.error("Clipboard error:", err);
            fallbackCopy(link);
        });
    } else {
        // fallback for non-secure contexts or older browsers
        fallbackCopy(link);
    }
}

function fallbackCopy(text) {
    const textarea = document.createElement("textarea");
    textarea.value = text;
    textarea.style.position = "fixed";
    textarea.style.opacity = 0;
    document.body.appendChild(textarea);
    textarea.focus();
    textarea.select();
    try {
        document.execCommand("copy");
        alert("✅ Event link copied! Paste it into your Instagram bio or story.");
    } catch (err) {
        console.error("Fallback copy error:", err);
        alert("Could not copy link automatically. Please copy it manually: " + text);
    }
    document.body.removeChild(textarea);
}
</script>
