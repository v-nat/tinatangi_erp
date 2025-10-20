<script>
    function toggleLogosByTheme() {
        const currentTheme = document.documentElement.getAttribute('data-bs-theme');

        const $lightLogo = $('#light-logo');
        const $darkLogo = $('#dark-logo');
        const $loadingScreen = $('#LoadingScreen');
        const $untilLoadedScreen = $('#untilLoaded');

        const lightModeBg = 'rgba(255, 255, 255, 0.7)';
        const darkModeBg = 'rgba(0, 0, 0, 0.7)';

        const spinnerLightText = 'text-primary';
        const spinnerDarkText = 'text-primary';

        if (currentTheme === 'dark') {
            $darkLogo.removeClass('d-none');
            $lightLogo.addClass('d-none');

            $loadingScreen.css('background', darkModeBg);
            $untilLoadedScreen.css('background', darkModeBg);

            $loadingScreen.find('.spinner-border').removeClass(spinnerLightText).addClass(spinnerDarkText);
            $untilLoadedScreen.find('.spinner-border').removeClass(spinnerLightText).addClass(spinnerDarkText);

        } else {
            $lightLogo.removeClass('d-none');
            $darkLogo.addClass('d-none');

            $loadingScreen.css('background', lightModeBg);
            $untilLoadedScreen.css('background', lightModeBg);

            $loadingScreen.find('.spinner-border').removeClass(spinnerDarkText).addClass(spinnerLightText);
            $untilLoadedScreen.find('.spinner-border').removeClass(spinnerDarkText).addClass(spinnerLightText);
        }
    }

    $(document).ready(function () {
        toggleLogosByTheme();
    });
</script>
<?php /**PATH C:\Users\Nathaniel\Documents\Nathaniel\Thesis A\tinatangi_erp\resources\views/layouts/dark-mode-toggler-setting.blade.php ENDPATH**/ ?>