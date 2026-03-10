
(function () {
    'use strict';

    const THEME_GLOWS = {
        gold:    '205,164,94',
        crimson: '192,57,43',
        forest:  '39,174,96',
        ocean:   '41,128,185',
        royal:   '142,68,173',
        slate:   '93,138,168',
    };


    const SEASON_ACCENTS = {
        summer:     '255,170,64',
        christmas:  '255,100,80',
        halloween:  '255,140,0',
        valentines: '255,107,157',
        newyear:    '255,215,0',
        easter:     '201,102,255',
        ramadan:    '218,165,32',
    };


    function loadAnnouncements() {
        fetch('/customer-service/announcements-public')
            .then(r => r.json())
            .then(({ data }) => {
                if (!data || !data.length) return;

                const announcements = data.filter(d => d.type === 'announcement');
                const promotions    = data.filter(d => d.type === 'discount');
                const promos        = data.filter(d => d.type === 'promo');

                if (announcements.length) renderTicker(announcements);
                if (promos.length)        renderSeasonalBanners(promos);
                if (promotions.length)    renderPromotions(promotions);
            })
            .catch(() => { /* silent fail on landing page */ });
    }


    function renderTicker(items) {
        const bar      = document.getElementById('announcement-ticker');
        const iconEl   = document.getElementById('ticker-icon');
        const badgeEl  = document.getElementById('ticker-badge');
        const textEl   = document.getElementById('ticker-text');
        const dotsEl   = document.getElementById('ticker-dots');
        const closeBtn = document.getElementById('ticker-close-btn');

        if (!bar) return;

        let current = 0;

        function show(idx) {
            const item = items[idx];


            textEl.style.opacity = '0';
            setTimeout(() => {

                iconEl.className = (item.icon || 'fa-solid fa-bullhorn') + ' ticker-icon';


                if (item.badge_text) {
                    badgeEl.textContent = item.badge_text;
                    badgeEl.classList.remove('d-none');
                } else {
                    badgeEl.classList.add('d-none');
                }


                textEl.textContent = item.title + (item.content ? ' — ' + item.content : '');
                textEl.style.opacity = '1';


                dotsEl.querySelectorAll('.tdot').forEach((d, i) => {
                    d.classList.toggle('active', i === idx);
                });


                const accent = THEME_GLOWS[item.theme] ?? '205,164,94';
                bar.style.borderBottomColor = `rgba(${accent}, 0.35)`;
                iconEl.style.color = `rgb(${accent})`;
                badgeEl.style.background  = `rgba(${accent}, .15)`;
                badgeEl.style.borderColor = `rgba(${accent}, .45)`;
                badgeEl.style.color       = `rgb(${accent})`;
            }, 200);
        }


        items.forEach((_, i) => {
            const dot = document.createElement('div');
            dot.className = 'tdot' + (i === 0 ? ' active' : '');
            dot.addEventListener('click', () => { current = i; show(i); clearInterval(timer); timer = setInterval(rotate, 4500); });
            dotsEl.appendChild(dot);
        });

        show(0);
        bar.style.display = 'block';
        textEl.style.transition = 'opacity .25s';


        const mainEl = document.querySelector('main.main');
        function applyOffset() {
            if (mainEl) mainEl.style.marginTop = bar.offsetHeight + 'px';
        }
        applyOffset();
        window.addEventListener('resize', applyOffset);

        function rotate() {
            current = (current + 1) % items.length;
            show(current);
        }

        let timer = items.length > 1 ? setInterval(rotate, 4500) : null;

        closeBtn.addEventListener('click', () => {
            clearInterval(timer);
            bar.style.display = 'none';
            if (mainEl) mainEl.style.marginTop = '';
            window.removeEventListener('resize', applyOffset);
        });
    }


    function renderSeasonalBanners(items) {
        const section   = document.getElementById('seasonal-promos');
        const container = document.getElementById('seasonal-promos-container');
        if (!section || !container) return;

        container.innerHTML = '';

        items.forEach(item => {
            const season  = item.season  || 'generic';
            const theme   = item.theme   || 'gold';
            const bgStyle = item.bg_style || 'solid';


            const accentRgb = (season !== 'generic' && SEASON_ACCENTS[season])
                ? SEASON_ACCENTS[season]
                : (THEME_GLOWS[theme] ?? '205,164,94');


            const cardClass = season !== 'generic'
                ? `seasonal-banner promo-season-${season}`
                : `seasonal-banner ann-theme-${theme} ann-style-${bgStyle}`;

            const glowStyle = (season === 'generic' && bgStyle === 'glow')
                ? `box-shadow:0 0 40px rgba(${accentRgb},.18) inset,0 0 25px rgba(${accentRgb},.1);`
                : '';

            const badgeHtml = item.badge_text
                ? `<div class="seasonal-badge" style="background:rgba(${accentRgb},.12);border-color:rgba(${accentRgb},.35);color:rgb(${accentRgb})">${escHtml(item.badge_text)}</div>`
                : '';

            let discountHtml = '';
            if (item.discount_value) {
                const sym   = item.discount_type === 'percentage' ? '%' : '₱';
                const label = item.discount_type === 'percentage' ? 'OFF' : 'DISCOUNT';
                const val   = item.discount_type === 'percentage'
                    ? `${item.discount_value}${sym}`
                    : `${sym}${item.discount_value}`;
                discountHtml = `
                    <div class="seasonal-discount-row">
                        <span class="seasonal-discount-value" style="color:rgb(${accentRgb});text-shadow:0 0 28px rgba(${accentRgb},.35)">${escHtml(val)}</span>
                        <span class="seasonal-discount-label">${label}</span>
                    </div>`;
            }

            const minSpendHtml = item.min_spend
                ? `<div class="seasonal-min-spend">Min. spend: ₱${parseFloat(item.min_spend).toFixed(2)}</div>` : '';

            let validityHtml = '';
            if (item.valid_from || item.valid_until) {
                const from  = item.valid_from  ? `From ${item.valid_from}`  : '';
                const until = item.valid_until ? `Until ${item.valid_until}` : '';
                validityHtml = `<div class="seasonal-validity">${[from, until].filter(Boolean).join(' · ')}</div>`;
            }

            const contentHtml = item.content
                ? `<p class="seasonal-content">${escHtml(item.content)}</p>` : '';

            const row = document.createElement('div');
            row.setAttribute('data-aos', 'fade-up');
            row.innerHTML = `
                <div class="${cardClass}" style="${glowStyle}">
                    <div class="seasonal-banner-overlay"></div>
                    <div class="seasonal-banner-inner">
                        <div class="row align-items-center g-4">
                            <div class="col-lg-9 col-12">
                                ${badgeHtml}
                                <div class="seasonal-icon-wrap" style="background:rgba(${accentRgb},.12);color:rgb(${accentRgb})">
                                    <i class="${escHtml(item.icon || 'fa-solid fa-wand-magic-sparkles')}"></i>
                                </div>
                                <h2 class="seasonal-title">${escHtml(item.title)}</h2>
                                ${contentHtml}
                                ${discountHtml}
                                ${minSpendHtml}
                                ${validityHtml}
                            </div>
                        </div>
                    </div>
                </div>`;
            container.appendChild(row);
        });

        section.style.display = 'block';
    }


    function renderPromotions(items) {
        const section = document.getElementById('promotions');
        const grid    = document.getElementById('promotions-grid');
        if (!section || !grid) return;

        grid.innerHTML = '';

        items.forEach(item => {
            const theme   = item.theme   || 'gold';
            const bgStyle = item.bg_style || 'solid';
            const accent  = THEME_GLOWS[theme] ?? '205,164,94';
            const accentHex = themeToHex(theme);

            let badgeHtml = '';
            if (item.badge_text) {
                badgeHtml = `<div class="promo-badge">${escHtml(item.badge_text)}</div>`;
            }

            let discountHtml = '';
            if (item.discount_value) {
                const sym   = item.discount_type === 'percentage' ? '%' : '₱';
                const label = item.discount_type === 'percentage' ? 'OFF' : 'DISCOUNT';
                const val   = item.discount_type === 'percentage'
                    ? `${item.discount_value}${sym}`
                    : `${sym}${item.discount_value}`;
                discountHtml = `
                    <div class="promo-discount-value">${escHtml(val)}</div>
                    <div class="promo-discount-label">${label}</div>`;
            }

            let minSpendHtml = '';
            if (item.min_spend) {
                minSpendHtml = `<div class="promo-min-spend">Min. spend: ₱${parseFloat(item.min_spend).toFixed(2)}</div>`;
            }

            let validityHtml = '';
            if (item.valid_from || item.valid_until) {
                const from  = item.valid_from  ? `From ${item.valid_from}`  : '';
                const until = item.valid_until ? `Until ${item.valid_until}` : '';
                validityHtml = `<div class="promo-validity">${[from, until].filter(Boolean).join(' · ')}</div>`;
            }

            let contentHtml = '';
            if (item.content) {
                contentHtml = `<p class="promo-content">${escHtml(item.content)}</p>`;
            }

            const glowStyle = bgStyle === 'glow'
                ? `box-shadow: 0 0 35px rgba(${accent},.22) inset, 0 0 20px rgba(${accent},.14);`
                : '';

            const hoverStyle = `--ann-accent:${accentHex};`;

            const col = document.createElement('div');
            col.className = 'col-lg-4 col-md-6';
            col.setAttribute('data-aos', 'fade-up');
            col.innerHTML = `
                <div class="promo-card ann-theme-${theme} ann-style-${bgStyle}"
                     style="${glowStyle}${hoverStyle}">
                    <div class="promo-card-inner">
                        ${badgeHtml}
                        <div class="promo-icon-wrap">
                            <i class="${escHtml(item.icon || 'fa-solid fa-tag')}"></i>
                        </div>
                        <div class="promo-title">${escHtml(item.title)}</div>
                        ${contentHtml}
                        ${discountHtml}
                        ${minSpendHtml}
                        ${validityHtml}
                    </div>
                </div>`;
            grid.appendChild(col);
        });

        section.style.display = 'block';
    }


    function escHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function themeToHex(theme) {
        const map = {
            gold:    '#cda45e', crimson: '#c0392b',
            forest:  '#27ae60', ocean:   '#2980b9',
            royal:   '#8e44ad', slate:   '#5d8aa8',
        };
        return map[theme] ?? '#cda45e';
    }


    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', loadAnnouncements);
    } else {
        loadAnnouncements();
    }
})();
