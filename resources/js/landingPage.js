import $ from 'jquery';

$(document).ready(function () {
    function loadFaqs() {
        const faqContainer = $("#faqAccordion");
        if (faqContainer.length === 0) {
            return;
        }

        $.ajax({
            url: "/faqs-public",
            type: "GET",
            dataType: "json",
            success: function (response) {
                faqContainer.empty();

                if (response.data && response.data.length > 0) {
                    $.each(response.data, function (index, faq) {
                        const collapseId = "collapse-" + faq.id;
                        const headingId = "heading-" + faq.id;

                        const faqHtml = `
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="${headingId}">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#${collapseId}" aria-expanded="false" aria-controls="${collapseId}">
                                        ${faq.question}
                                    </button>
                                </h2>
                                <div id="${collapseId}" class="accordion-collapse collapse" aria-labelledby="${headingId}"
                                    data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        ${faq.answer}
                                    </div>
                                </div>
                            </div>
                        `;
                        faqContainer.append(faqHtml);
                    });
                } else {
                    const emptyHtml = `
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button">
                                    No frequently asked questions are available at this time.
                                </button>
                            </h2>
                        </div>
                    `;
                    faqContainer.append(emptyHtml);
                }
            },
            error: function (xhr) {
                console.error("Failed to load FAQs:", xhr);
                faqContainer.html(`
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button">
                                Error loading questions. Please try again later.
                            </button>
                        </h2>
                    </div>
                `);
            },
        });
    }

    loadFaqs();

    const numberFormatter = new Intl.NumberFormat("en-US");
    const currencyFormatter = new Intl.NumberFormat("en-US", {
        style: "currency",
        currency: "PHP",
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });
    const DEFAULT_PRODUCT_IMAGE = "/logo.png";

    const formatNumber = (value) => {
        if (value === undefined || value === null || isNaN(value)) {
            return "0";
        }
        return numberFormatter.format(Number(value));
    };

    const formatCurrency = (value) => {
        if (value === undefined || value === null || isNaN(value)) {
            return currencyFormatter.format(0);
        }
        return currencyFormatter.format(Number(value));
    };

    const bestSellerSwipers = {
        weekly: null,
        monthly: null,
    };

    function destroyBestSellerSwiper(type) {
        if (bestSellerSwipers[type]) {
            bestSellerSwipers[type].destroy(true, true);
            bestSellerSwipers[type] = null;
        }
    }

    function setBestSellerPlaceholder(type, message, isError = false) {
        const wrapper = document.getElementById(`${type}-best-seller-wrapper`);
        if (!wrapper) return;
        destroyBestSellerSwiper(type);
        wrapper.innerHTML = `
            <div class="swiper-slide">
                <div class="py-5 text-center ${isError ? "text-danger" : "text-muted"}">${message}</div>
            </div>
        `;
    }

    function resolveProductImage(imagePath) {
        if (!imagePath || imagePath === "N/A") {
            return DEFAULT_PRODUCT_IMAGE;
        }

        if (imagePath.startsWith("http://") || imagePath.startsWith("https://")) {
            return imagePath;
        }

        const sanitized = imagePath.replace(/^\/+/, "");

        if (sanitized.startsWith("storage/")) {
            return `/${sanitized}`;
        }

        if (sanitized.startsWith("app/public/")) {
            const trimmed = sanitized.replace(/^app\/public\//, "");
            return `/storage/${trimmed}`;
        }

        if (sanitized.startsWith("public/")) {
            const trimmed = sanitized.replace(/^public\//, "");
            return `/storage/${trimmed}`;
        }

        return `/storage/${sanitized}`;
    }

    function renderBestSellerSlides(type, periodData) {
        const wrapper = document.getElementById(`${type}-best-seller-wrapper`);
        const pagination = document.getElementById(`${type}-best-seller-pagination`);
        const swiperEl = document.getElementById(`${type}-best-seller-swiper`);

        if (!wrapper || !swiperEl || !pagination) {
            return;
        }

        const categories = Array.isArray(periodData?.categories)
            ? periodData.categories
            : [];

        const flattenedItems = categories.flatMap((category) =>
            (category.items || []).map((item) => ({
                ...item,
                category_name: category.category_name,
            }))
        );

        if (flattenedItems.length === 0) {
            setBestSellerPlaceholder(
                type,
                "No sales data available yet. Check back soon!"
            );
            return;
        }

        destroyBestSellerSwiper(type);
        wrapper.innerHTML = flattenedItems
            .map((item) => {
                const imageUrl = resolveProductImage(item.image);
                const avgPrice = parseFloat(item.average_unit_price || 0);
                const avgPriceText =
                    avgPrice > 0
                        ? `<small class="d-block mt-2" style="color: rgba(255,255,255,0.75);">Avg ${formatCurrency(avgPrice)}</small>`
                        : "";
                const withinLabel = periodData?.label
                    ? `<small class="d-block mt-1 text-uppercase" style="letter-spacing: 0.08em; color: rgba(255,255,255,0.6);">${periodData.label}</small>`
                    : "";

                return `
                    <div class="swiper-slide">
                        <div class="best-seller-slide h-100 d-flex flex-column justify-content-between align-items-center text-center p-4"
                            style="border-radius: 16px; background-color: rgba(36, 31, 25, 0.95); color: #fff; box-shadow: 0 18px 35px rgba(0,0,0,0.25); border: 1px solid rgba(255,255,255,0.08);">
                            <div class="best-seller-image mb-3 rounded-circle overflow-hidden position-relative"
                                style="width: 140px; height: 140px; border: 3px solid rgba(255,255,255,0.35); background: rgba(0,0,0,0.2);">
                                <span class="badge bg-success position-absolute" style="top: -6px; left: -6px; font-size: 0.85rem; border-radius: 999px; padding: 0.35rem 0.6rem;">#${item.global_rank || item.rank || 1}</span>
                                <img src="${imageUrl}" alt="${item.product_name}"
                                    class="w-100 h-100 object-fit-cover">
                            </div>
                            <div class="w-100">
                                <h5 class="fw-semibold mb-1">${item.product_name}</h5>
                                <small class="text-uppercase" style="letter-spacing: 0.1em; color: rgba(255,255,255,0.75);">${item.category_name}</small>
                            </div>
                            <div class="mt-3">
                                <span class="sold-badge rounded-pill px-3 py-2">
                                    ${formatNumber(item.total_units)} sold
                                </span>
                                ${avgPriceText}
                                ${withinLabel}
                            </div>
                        </div>
                    </div>
                `;
            })
            .join("");

        bestSellerSwipers[type] = new Swiper(swiperEl, {
            loop: flattenedItems.length > 1,
            speed: 600,
            autoplay: {
                delay: 5000,
                disableOnInteraction: false,
            },
            slidesPerView: 1,
            spaceBetween: 20,
            pagination: {
                el: pagination,
                clickable: true,
            },
            breakpoints: {
                992: {
                    slidesPerView: 1,
                    spaceBetween: 24,
                },
            },
        });
    }

    function loadBestSellers() {
        const weeklyWrapper = document.getElementById("weekly-best-seller-wrapper");
        const monthlyWrapper = document.getElementById("monthly-best-seller-wrapper");

        if (!weeklyWrapper || !monthlyWrapper) {
            return;
        }

        setBestSellerPlaceholder(
            "weekly",
            "Gathering this week's popular picks..."
        );
        setBestSellerPlaceholder(
            "monthly",
            "Checking our monthly best sellers..."
        );

        $.ajax({
            url: "/best-sellers/highlights",
            type: "GET",
            data: {
                limit: 1,
            },
            dataType: "json",
            success: function (response) {
        function assignGlobalRanks(periodData) {
            if (!periodData || !Array.isArray(periodData.categories)) {
                return periodData;
            }

            const categories = periodData.categories.map((category) => {
                const items = Array.isArray(category.items) ? category.items : [];
                if (!items.length) {
                    return null;
                }
                const best = [...items]
                    .sort((a, b) => Number(b.total_units || 0) - Number(a.total_units || 0))
                    .slice(0, 1)
                    .map((item) => ({ ...item }));
                if (!best.length || Number(best[0].total_units || 0) <= 0) {
                    return null;
                }
                return {
                    ...category,
                    items: best,
                };
            }).filter(Boolean);

            const flattened = categories.flatMap((category) =>
                (category.items || []).map((item) => ({
                    ...item,
                    category_id: category.category_id,
                }))
            );
            const sorted = [...flattened].sort(
                (a, b) => Number(b.total_units || 0) - Number(a.total_units || 0)
            );
            const rankMap = new Map();
            sorted.forEach((item, index) => {
                rankMap.set(
                    `${item.category_id}-${item.product_id}`,
                    index + 1
                );
            });

            const rankedCategories = categories.map((category) => ({
                ...category,
                items: (category.items || []).map((item) => ({
                    ...item,
                    global_rank:
                        rankMap.get(`${category.category_id}-${item.product_id}`) || 1,
                })),
            }));

            return {
                ...periodData,
                categories: rankedCategories,
            };
        }

        const weekly = assignGlobalRanks(response.weekly || {});
        const monthly = assignGlobalRanks(response.monthly || {});

                $("#public-weekly-range").text(weekly.label || "No data yet");
                $("#public-monthly-range").text(monthly.label || "No data yet");

                renderBestSellerSlides("weekly", weekly);
                renderBestSellerSlides("monthly", monthly);
            },
            error: function () {
                setBestSellerPlaceholder(
                    "weekly",
                    "Unable to load weekly best sellers right now.",
                    true
                );
                setBestSellerPlaceholder(
                    "monthly",
                    "Unable to load monthly best sellers right now.",
                    true
                );
            },
        });
    }

    loadBestSellers();

    var food_rating = raterJs({
        starSize: 28,
        step: 0.5,
        element: document.querySelector("#food-rater"),
        rateCallback: function rateCallback(rating, done) {
            this.setRating(rating);
            done();
        },
    });
    var staff_rating = raterJs({
        starSize: 28,
        step: 0.5,
        element: document.querySelector("#staff-rater"),
        rateCallback: function rateCallback(rating, done) {
            this.setRating(rating);
            done();
        },
    });
    var environment_rating = raterJs({
        starSize: 28,
        step: 0.5,
        element: document.querySelector("#environment-rater"),
        rateCallback: function rateCallback(rating, done) {
            this.setRating(rating);
            done();
        },
    });

    $("#submitFeedback").on("click", function (e) {
        e.preventDefault();
        let isValid = true;
        const form_validation = $("#serviceFeedbackForm");
        const staff_ratings = staff_rating.getRating();
        const food_ratings = food_rating.getRating();
        const environment_ratings = environment_rating.getRating();
        const $imageInput = $("#photo");

        form_validation.find(".is-invalid").removeClass("is-invalid");
        $("#image_error").text("");
        $imageInput.removeClass("is-invalid");

        form_validation
            .find("input[required], select[required], textarea[required]")
            .each(function () {
                if (!$(this).val()) {
                    $(this).addClass("is-invalid");
                    Toast.fire({
                        icon: "warning",
                        text: "Please fill all fields before submitting.",
                    });
                    isValid = false;
                }
            });

        if (
            staff_ratings === null ||
            food_ratings == null ||
            environment_ratings == null
        ) {
            Toast.fire({
                icon: "warning",
                text: "Please fill and rate all fields before submitting.",
            });
            isValid = false;
        }

        if ($imageInput[0].files.length > 0) {
            const file = $imageInput[0].files[0];
            const allowedTypes = [
                "image/jpeg",
                "image/png",
                "image/gif",
                "image/jpg",
            ];
            const maxSize = 10 * 1024 * 1024;

            if (!allowedTypes.includes(file.type)) {
                isValid = false;
                $imageInput.addClass("is-invalid");
                Toast.fire({
                    icon: "error",
                    text: "Invalid file type. Please use jpg, jpeg, or png.",
                });
                $("#image_error").text(
                    "Invalid file type. Please use jpg, jpeg, or png."
                );
            }

            if (file.size > maxSize) {
                isValid = false;
                $imageInput.addClass("is-invalid");
                const existingError = $("#image_error").text();
                const sizeError = "File is too large. Maximum size is 10MB.";
                Toast.fire({
                    icon: "error",
                    text: "File is too large. Maximum size is 10MB.",
                });
                $("#image_error").text(
                    existingError ? `${existingError} ${sizeError}` : sizeError
                );
            }
        }

        if (!isValid) {
            return;
        }

        const overall_rating =
            (staff_ratings + food_ratings + environment_ratings) / 3;

        const form = $("#serviceFeedbackForm");
        let formData = new FormData(form[0]);

        formData.append("food_rating", food_ratings);
        formData.append("staff_rating", staff_ratings);
        formData.append("environment_rating", environment_ratings);
        formData.append("overall_rating", overall_rating.toFixed(2));

        $.ajax({
            url: "/customer-service/submit-feedback",
            method: "POST",
            data: formData,
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            processData: false,
            contentType: false,
            beforeSend: function () {
                $("#LoadingScreen").fadeIn(200);
            },
            success: function (response) {
                $("#LoadingScreen").fadeOut(200);
                Toast.fire({
                    icon: "success",
                    text: response.message || "Thank you for your feedback!",
                });
                form[0].reset();
                food_rating.clear();
                staff_rating.clear();
                environment_rating.clear();
            },
            error: function (xhr) {
                $("#LoadingScreen").fadeOut(200);
                const errors = xhr.responseJSON.errors;
                let errorMessage = "An error occurred. Please try again.";
                if (errors) {
                    errorMessage = Object.values(errors).flat().join("\n");
                }
                Toast.fire({
                    icon: "error",
                    text: errorMessage,
                });
            },
        });
    });

    const commentField = $("#message");
    const charCount = $("#char-count");
    const maxLength = commentField.attr("maxlength");

    charCount.text(maxLength + " characters remaining");

    commentField.on("input", function () {
        let currentLength = $(this).val().length;
        let remaining = maxLength - currentLength;

        charCount.text(remaining + " characters remaining");

        if (remaining < 20) {
            charCount.addClass("text-warning");
        } else {
            charCount.removeClass("text-warning");
        }

        if (remaining < 1) {
            charCount.addClass("text-danger");
        } else {
            charCount.removeClass("text-danger");
        }
    });
});

$(document).ready(function () {
    function escapeAttribute(s) {
        if (!s) return "";
        return s
            .toString()
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#39;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;");
    }

    const swiperContainer = $("#testimonials .init-swiper");
    const swiperWrapper = swiperContainer.find(".swiper-wrapper");

    $.ajax({
        url: "/testimonials",
        method: "GET",
        success: function (testimonials) {
            if (testimonials && testimonials.length > 0) {
                swiperWrapper.empty();

                testimonials.forEach((feedback) => {
                    const imageUrl = feedback.photo
                        ? `/storage/${feedback.photo}`
                        : "assets/img/default-avatar.png";

                    const fullMessage =
                        feedback.message || "No comments provided.";
                    const maxLength = 100;
                    let messageHtml = "";

                    if (fullMessage.length > maxLength) {
                        const truncatedMessage =
                            fullMessage.substring(0, maxLength) + "...";
                        const encodedMessage = escapeAttribute(fullMessage);

                        messageHtml = `
                            <span>${truncatedMessage}</span>
                            <i class="bi bi-quote quote-icon-right"></i>
                            <a href="#" class="see-more-btn-testimonial ms-1"
                               data-bs-toggle="modal"
                               data-bs-target="#messageModal"
                               data-full-message="${encodedMessage}"
                               style="font-size: 14px; color: #cda45e; font-weight: 500;">
                               See More
                            </a>
                        `;
                    } else {
                        messageHtml = `
                            <span>${fullMessage}</span>
                            <i class="bi bi-quote quote-icon-right"></i>
                        `;
                    }

                    const slideHtml = `
                        <div class="swiper-slide">
                            <div class="testimonial-item">
                                <p>
                                    <i class="bi bi-quote quote-icon-left"></i>
                                    ${messageHtml}
                                </p>
                                <img src="${imageUrl}" class="testimonial-img" alt="No photo provided.">
                                <h3>${feedback.name}</h3>
                                <h4>Valued Customer</h4>
                            </div>
                        </div>
                    `;
                    swiperWrapper.append(slideHtml);
                });

                if (swiperContainer[0].swiper) {
                    swiperContainer[0].swiper.destroy(true, true);
                }

                const config = JSON.parse(
                    swiperContainer.find(".swiper-config").text()
                );
                new Swiper(swiperContainer[0], config);
            }
        },
        error: function (err) {
            console.error("Failed to load testimonials:", err);
        },
    });

    $(document).on("click", ".see-more-btn-testimonial", function (e) {
        e.preventDefault();
        const fullMessage = $(this).data("full-message");
        $("#modalMessageBody").text(fullMessage);
    });
});

$(document).ready(function () {


    const $menuContainer = $('.isotope-container');
    const $filterContainer = $('.menu-filters');
    const $loadingSpinner = $('#menu-loading');

    $.ajax({
        url: '/menu/products',
        type: 'GET',
        dataType: 'json',
        success: function (response) {
            if (response.data && Array.isArray(response.data)) {
                let menuItemsHtml = [];
                let categories = {};

                response.data.forEach(product => {
                    const imagePath = product.image;
                    let imageUrl = (imagePath && imagePath !== "N/A")
                                 ? "/storage/" + imagePath
                                 : DEFAULT_PRODUCT_IMAGE;

                    const price = parseFloat(product.base_price || 0).toFixed(2);

                    menuItemsHtml.push(`
                        <div class="col-lg-6 menu-item isotope-item ${product.filter_class}">
                            <img src="${imageUrl}" class="menu-img" alt="${product.name}">
                            <div class="menu-content">
                                <a href="#">${product.name}</a><span>₱${price}</span>
                            </div>
                            <div class="menu-ingredients">
                                ${product.description}
                            </div>
                        </div>
                    `);

                    if (!categories[product.category_name]) {
                        categories[product.category_name] = product.filter_class;
                    }
                });

                let filterHtml = [];
                for (const categoryName in categories) {
                    const filterClass = categories[categoryName];
                    filterHtml.push(`
                        <li data-filter=".${filterClass}">${categoryName}</li>
                    `);
                }

                $loadingSpinner.remove();
                $filterContainer.append(filterHtml.join(''));
                $menuContainer.html(menuItemsHtml.join(''));

                let menuIsotope = new Isotope($menuContainer[0], {
                    itemSelector: '.isotope-item',
                    layoutMode: 'masonry'
                });

                $filterContainer.on('click', 'li', function() {
                    $filterContainer.find('.filter-active').removeClass('filter-active');
                    $(this).addClass('filter-active');
                    menuIsotope.arrange({
                        filter: $(this).data('filter')
                    });
                });

            } else {
                $loadingSpinner.html('<p class="text-danger">Could not load menu items.</p>');
            }
        },
        error: function (xhr) {
            console.error("Failed to load menu:", xhr.responseText);
            $loadingSpinner.html('<p class="text-danger">Error: Could not load the menu. Please try again later.</p>');
        }
    });
});
