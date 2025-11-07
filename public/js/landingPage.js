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
    /**
     * Helper function to safely escape text for a data attribute.
     * @param {string} s - The string to escape.
     * @returns {string} The escaped string.
     */
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
                        ? `/storage/app/public/${feedback.photo}`
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

    const DEFAULT_PRODUCT_IMAGE = '/path/to/your/default-image.jpg';

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
                                 ? "/storage/app/public/" + imagePath
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
