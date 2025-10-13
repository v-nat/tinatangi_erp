<div id="LoadingScreen"
    style="display: none; position: fixed; z-index: 9999; background: rgba(0,0,0,0.5); top: 0; left: 0; width: 100%; height: 100%;">
    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
        <div class="spinner-border text-primary" style="width: 3rem; height: 3rem" role="status"></div>
    </div>
</div>

<div id="untilLoaded"
    style="display:block; position: fixed; z-index: 9999; background: rgba(0,0,0,0.5); top: 0; left: 0; width: 100%; height: 100%;">
    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
        <div class="spinner-border text-primary" style="width: 3rem; height: 3rem" role="status"></div>
    </div>
</div>

<style>
    #untilLoaded {
        transition: opacity 0.5s ease;
    }
</style>
<script>
    $(document).on(
        "click",
        'a[href]:not([target="_blank"]):not([href^="#"]):not([href^=" "])',
        function (e) {
            var href = $(this).attr("href");
            if (!href || href === "#" || href.startsWith("javascript:")) return;

            $("#LoadingScreen").fadeIn(200);

            setTimeout(() => {
                window.location.href = href;
            }, 200);

            e.preventDefault();
        }
    );
    window.addEventListener('load', function () {
        const loader = document.getElementById('untilLoaded');
        loader.style.opacity = '0';
        setTimeout(() => {
            loader.style.display = 'none';
            $('#sidebar').removeClass('active');

        }, 700);
    });
</script>
