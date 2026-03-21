import $ from 'jquery';
import Swal from 'sweetalert2';

$(function () {

    const csrfToken = $('meta[name="csrf-token"]').attr('content');

    const THEME_ACCENTS = {
        gold:    '#cda45e',
        crimson: '#c0392b',
        forest:  '#27ae60',
        ocean:   '#2980b9',
        royal:   '#8e44ad',
        slate:   '#5d8aa8',
    };

    // Season → gradient for live preview
    const SEASON_GRADIENTS = {
        summer:     'linear-gradient(135deg, #1c0900 0%, #6b2800 40%, #b86a00 100%)',
        christmas:  'linear-gradient(135deg, #080e00 0%, #7a0000 50%, #162e00 100%)',
        halloween:  'linear-gradient(135deg, #0a0015 0%, #3a0060 45%, #7a3200 100%)',
        valentines: 'linear-gradient(135deg, #180010 0%, #85002c 50%, #be0060 100%)',
        newyear:    'linear-gradient(135deg, #020810 0%, #0b1840 55%, #c0a000 100%)',
        easter:     'linear-gradient(135deg, #120920 0%, #3c1a58 50%, #083a1c 100%)',
        ramadan:    'linear-gradient(135deg, #041018 0%, #0a3040 50%, #7a6800 100%)',
    };


    const table = $('#announcementsTable').DataTable({
        ajax: {
            url: '/customer-service/announcements/list',
            type: 'GET',
            dataSrc: 'data',
        },
        columns: [
            {
                data: null, orderable: false, searchable: false,
                title: '<input type="checkbox" id="select-all-ann">',
                render: function (_d, _t, row) {
                    return `<input type="checkbox" class="row-check" data-id="${row.id}">`;
                },
            },
            { data: 'display_order' },
            {
                data: 'type',
                render: function (v) {
                    var styles = {
                        announcement: 'background:#1a3a6b;color:#6ea8fe;border:1px solid #6ea8fe',
                        discount:     'background:#3a2e00;color:#ffc107;border:1px solid #ffc107',
                        promo:        'background:#1a003a;color:#d399f5;border:1px solid #d399f5',
                    };
                    var s = styles[v] || 'background:#2a2a2a;color:#aaa;border:1px solid #aaa';
                    return `<span class="badge" style="${s}">${v}</span>`;
                },
            },
            { data: 'title' },
            {
                data: 'badge_text',
                render: function (v) {
                    return v ? `<span class="badge" style="background:rgba(205,164,94,.15);border:1px solid #cda45e;color:#cda45e">${v}</span>` : '—';
                },
            },
            {
                data: 'theme',
                render: function (v) {
                    var accent = THEME_ACCENTS[v] || '#cda45e';
                    return `<span style="display:inline-block;width:12px;height:12px;border-radius:50%;background:${accent};vertical-align:middle;margin-right:4px"></span>${v}`;
                },
            },
            { data: 'valid_from',  render: function (v) { return v || '—'; } },
            { data: 'valid_until', render: function (v) { return v || '—'; } },
            { data: 'status_html' },
            {
                data: null, orderable: false, searchable: false,
                render: function (_d, _t, row) {
                    return `
                        <div class="d-flex gap-1">
                            <button class="btn btn-sm btn-outline-secondary btn-toggle-status" data-id="${row.id}" title="Toggle Status">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-primary btn-edit-ann" data-id="${row.id}" title="Edit">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger btn-delete-ann" data-id="${row.id}" title="Delete">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>`;
                },
            },
        ],
        order: [[1, 'asc']],
        pageLength: 15,
        responsive: true,
        language: { emptyTable: 'No announcements found.' },
    });


    $('#filter_type').on('change', function () {
        table.column(2).search(this.value).draw();
    });
    $('#filter_status').on('change', function () {
        table.column(8).search(this.value).draw();
    });

    const annModal  = new bootstrap.Modal(document.getElementById('announcementModal'));
    const $form     = $('#announcementForm');
    const $modalLbl = $('#announcementModalLabel');

    function resetModal() {
        $form[0].reset();
        $('#ann_id').val('');
        setType('announcement');
        setIcon('fa-solid fa-bullhorn');
        setTheme('gold');
        setBgStyle('solid');
        setSeason('generic');
        setApplicableTo('all');
        $('#ann_status').val('34');
        $('#ann_order').val('0');
        $('#ann_valid_from, #ann_valid_until').val('');
        $('#ann_discount_type').val('percentage');
        $('#ann_discount_value, #ann_min_spend').val('');
        $('#ann_usage_limit').val('');
        $('#usage_count_info').addClass('d-none');

        selectedProductIds = [];
        $('#product-ids-container').html('');
        $('#product-selection-summary').css('color', 'rgba(255,255,255,.35)').text('No products selected');
        updatePreview();
    }


    function setType(type) {
        $('#ann_type').val(type);
        $('.type-card').each(function () {
            $(this).toggleClass('selected', $(this).data('type') === type);
        });
        var isDiscount      = type === 'discount';
        var isPromo         = type === 'promo';
        var hasDiscountArea = isDiscount || isPromo;

        $('#discountFields').toggleClass('d-none', !hasDiscountArea);
        $('#applicableToSection').toggleClass('d-none', !hasDiscountArea);
        $('#promoSeasonSection').toggleClass('d-none', !isPromo);

        if (!hasDiscountArea) {
            $('#productPickerSection').addClass('d-none');
        }
        if (!isPromo) {
            setSeason('generic');
        }
    }

    $(document).on('click', '.type-card', function () {
        setType($(this).data('type'));
        updatePreview();
    });


    function setIcon(iconClass) {
        $('#ann_icon').val(iconClass);
        $('.icon-tile').each(function () {
            $(this).toggleClass('selected', $(this).data('icon') === iconClass);
        });
    }

    $(document).on('click', '.icon-tile', function () {
        setIcon($(this).data('icon'));
        updatePreview();
    });


    function setTheme(theme) {
        $('#ann_theme').val(theme);
        $('.theme-swatch').each(function () {
            $(this).toggleClass('selected', $(this).data('theme') === theme);
        });
    }

    $(document).on('click', '.theme-swatch', function () {
        setTheme($(this).data('theme'));
        updatePreview();
    });


    function setBgStyle(style) {
        $('#ann_bg_style').val(style);
        $('#bgStyleTiles .bg-tile').each(function () {
            $(this).toggleClass('selected', $(this).data('style') === style);
        });
    }


    function setSeason(season) {
        $('#ann_season').val(season);
        $('.season-tile').each(function () {
            $(this).toggleClass('selected', $(this).data('season') === season);
        });
    }

    $(document).on('click', '.season-tile', function () {
        setSeason($(this).data('season'));
        updatePreview();
    });


    function setApplicableTo(scope) {
        $('#ann_applicable_to').val(scope);
        $('#applicableToTiles .bg-tile').each(function () {
            $(this).toggleClass('selected', $(this).data('scope') === scope);
        });
        $('#productPickerSection').toggleClass('d-none', scope !== 'specific');
    }


    $(document).on('click', '.bg-tile', function () {
        var style = $(this).data('style');
        var scope = $(this).data('scope');
        if (style !== undefined) {
            setBgStyle(style);
            updatePreview();
        } else if (scope !== undefined) {
            setApplicableTo(scope);
        }
    });


    var productsCache    = null;
    var selectedProductIds = [];
    var $pickerModal     = null;

    function getPickerModal() {
        if (!$pickerModal) {
            $pickerModal = new bootstrap.Modal(document.getElementById('productPickerModal'), {
                backdrop: true,
                keyboard: true,
            });
        }
        return $pickerModal;
    }


    document.getElementById('productPickerModal').addEventListener('show.bs.modal', function () {
        var self = this;
        setTimeout(function () {
            $(self).css('z-index', 1075);
            $('.modal-backdrop').last().css('z-index', 1074);
        }, 10);
    });

    function openProductPicker() {
        $('#product-picker-search').val('');
        if (productsCache !== null) {
            renderPickerProducts(productsCache);
            syncPickerChecks();
            getPickerModal().show();
        } else {
            $('#product-picker-list').html(
                '<div class="text-center py-4 opacity-50"><i class="fa-solid fa-spinner fa-spin me-1"></i> Loading products…</div>'
            );
            getPickerModal().show();
            $.get('/customer-service/announcements/products', function (res) {
                productsCache = res.data || [];
                renderPickerProducts(productsCache);
                syncPickerChecks();
            }).fail(function () {
                $('#product-picker-list').html('<div class="text-danger small px-2 py-3">Failed to load products. Please try again.</div>');
            });
        }
    }

    function renderPickerProducts(products) {
        if (!products.length) {
            $('#product-picker-list').html('<div class="small opacity-50 py-3 text-center">No products with available stock found.</div>');
            updatePickerCount();
            return;
        }
        var html = '<div class="list-group list-group-flush">';
        products.forEach(function (p) {
            html += `<label class="list-group-item list-group-item-action d-flex align-items-center gap-3 py-2 px-3 picker-product-row"
                          data-name="${escJs(p.name).toLowerCase()}"
                          data-cat="${escJs(p.category_name || '').toLowerCase()}"
                          style="cursor:pointer;background:transparent;border-color:rgba(255,255,255,.1)">
                        <input type="checkbox" class="form-check-input picker-product-check mt-0 flex-shrink-0" value="${p.id}">
                        <div class="flex-grow-1 min-w-0">
                            <div class="fw-semibold" style="font-size:.85rem">${escJs(p.name)}</div>
                            <div class="small opacity-50">${escJs(p.category_name || '—')} &middot; &#8369;${parseFloat(p.base_price).toFixed(2)}</div>
                        </div>
                        <span class="badge rounded-pill flex-shrink-0"
                              style="background:rgba(205,164,94,.15);color:#cda45e;font-size:.7rem;white-space:nowrap">
                            ${p.available_servings} avail.
                        </span>
                    </label>`;
        });
        html += '</div>';
        $('#product-picker-list').html(html);
        updatePickerCount();
    }

    function syncPickerChecks() {
        $('.picker-product-check').each(function () {
            $(this).prop('checked', selectedProductIds.indexOf(parseInt($(this).val())) !== -1);
        });
        updatePickerCount();
    }

    function updatePickerCount() {
        var n = $('.picker-product-check:checked').length;
        $('#picker-selection-count').text(n + (n === 1 ? ' product selected' : ' products selected'));
    }

    function updateMainSummary() {
        var n = selectedProductIds.length;
        if (n === 0) {
            $('#product-selection-summary').css('color', 'rgba(255,255,255,.35)').text('No products selected');
        } else {
            var names = [];
            if (productsCache) {
                productsCache.forEach(function (p) {
                    if (selectedProductIds.indexOf(p.id) !== -1) names.push(p.name);
                });
            }
            var text = names.length ? names.join(', ') : n + ' product(s) selected';
            $('#product-selection-summary').css('color', '#cda45e').text(text);
        }
        var html = '';
        selectedProductIds.forEach(function (id) {
            html += `<input type="hidden" name="product_ids[]" value="${id}">`;
        });
        $('#product-ids-container').html(html);
    }

    function escJs(str) {
        return String(str || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }


    $(document).on('click', '#btn-open-product-picker', function () {
        openProductPicker();
    });


    $(document).on('change', '.picker-product-check', function () {
        updatePickerCount();
    });


    $(document).on('input', '#product-picker-search', function () {
        var q = $(this).val().toLowerCase().trim();
        $('.picker-product-row').each(function () {
            var name = $(this).data('name') || '';
            var cat  = $(this).data('cat')  || '';
            $(this).toggle(!q || name.indexOf(q) !== -1 || cat.indexOf(q) !== -1);
        });
    });


    $(document).on('click', '#btn-picker-select-all', function () {
        $('.picker-product-row:visible .picker-product-check').prop('checked', true);
        updatePickerCount();
    });


    $(document).on('click', '#btn-picker-clear', function () {
        $('.picker-product-check').prop('checked', false);
        updatePickerCount();
    });


    $(document).on('click', '#btn-picker-cancel, #btn-picker-close', function () {
        getPickerModal().hide();
    });


    $(document).on('click', '#btn-picker-confirm', function () {
        selectedProductIds = [];
        $('.picker-product-check:checked').each(function () {
            selectedProductIds.push(parseInt($(this).val()));
        });
        updateMainSummary();
        getPickerModal().hide();
    });


    function updatePreview() {
        var theme    = $('#ann_theme').val() || 'gold';
        var bgStyle  = $('#ann_bg_style').val() || 'solid';
        var type     = $('#ann_type').val() || 'announcement';
        var season   = $('#ann_season').val() || 'generic';
        var icon     = $('#ann_icon').val() || 'fa-solid fa-bullhorn';
        var title    = $('#ann_title').val() || 'Your announcement title';
        var content  = $('#ann_content').val();
        var badge    = $('#ann_badge').val();
        var discType = $('#ann_discount_type').val();
        var discVal  = $('#ann_discount_value').val();

        var $card = $('#ann-preview-card');


        if (type === 'promo' && season !== 'generic' && SEASON_GRADIENTS[season]) {
            $card.attr('class', 'ann-card');
            $card.css({ background: SEASON_GRADIENTS[season], 'box-shadow': '' });
        } else {
            $card.attr('class', 'ann-card ann-theme-' + theme + ' ann-style-' + bgStyle);
            $card.css('background', '');
            if (bgStyle === 'glow') {
                var accent = THEME_ACCENTS[theme] || '#cda45e';
                $card.css('box-shadow', '0 0 35px ' + accent + '33 inset, 0 0 20px ' + accent + '22');
            } else {
                $card.css('box-shadow', '');
            }
        }

        var $badge = $('#prev-badge');
        if (badge) {
            $badge.text(badge).removeClass('d-none');
        } else {
            $badge.addClass('d-none');
        }

        $('#prev-icon').attr('class', icon);
        $('#prev-title').text(title);

        var $cont = $('#prev-content');
        if (content) {
            $cont.text(content).removeClass('d-none');
        } else {
            $cont.addClass('d-none');
        }

        var $dBlock = $('#prev-discount-block');
        if ((type === 'discount' || type === 'promo') && discVal) {
            var sym   = discType === 'percentage' ? '%' : '₱';
            var label = discType === 'percentage' ? 'OFF' : 'DISCOUNT';
            var val   = discType === 'percentage' ? (discVal + sym) : (sym + discVal);
            $('#prev-discount-value').text(val);
            $('#prev-discount-label').text(label);
            $dBlock.removeClass('d-none');
        } else {
            $dBlock.addClass('d-none');
        }
    }

    $('#ann_title, #ann_content, #ann_badge, #ann_discount_type, #ann_discount_value').on('input change', updatePreview);


    $('#btn-add-announcement').on('click', function () {
        resetModal();
        $modalLbl.html('<i class="fa-solid fa-bullhorn me-2" style="color:#cda45e"></i> Create Announcement');
        annModal.show();
    });


    $('#announcementsTable tbody').on('click', '.btn-edit-ann', function () {
        var id = $(this).data('id');
        $.get('/customer-service/announcements/edit/' + id, function (data) {
            resetModal();
            $modalLbl.html('<i class="fa-solid fa-pen me-2" style="color:#cda45e"></i> Edit Announcement');
            $('#ann_id').val(data.id);
            setType(data.type || 'announcement');
            setIcon(data.icon || 'fa-solid fa-bullhorn');
            setTheme(data.theme || 'gold');
            setBgStyle(data.bg_style || 'solid');
            setSeason(data.season || 'generic');
            $('#ann_title').val(data.title);
            $('#ann_content').val(data.content);
            $('#ann_badge').val(data.badge_text);
            $('#ann_discount_type').val(data.discount_type || 'percentage');
            $('#ann_discount_value').val(data.discount_value);
            $('#ann_min_spend').val(data.min_spend);
            $('#ann_valid_from').val(data.valid_from);
            $('#ann_valid_until').val(data.valid_until);
            $('#ann_order').val(data.display_order || 0);
            $('#ann_status').val(data.status || 34);
            $('#ann_usage_limit').val(data.usage_limit || '');


            if (data.type === 'discount' || data.type === 'promo') {
                setApplicableTo(data.applicable_to || 'all');

                if (data.usage_count > 0) {
                    var limitText = data.usage_limit ? ' / ' + data.usage_limit : ' (unlimited)';
                    $('#usage_count_display').text(data.usage_count + limitText);
                    $('#usage_count_info').removeClass('d-none');
                }

                if (data.applicable_to === 'specific' && data.product_ids && data.product_ids.length) {
                    selectedProductIds = data.product_ids.map(Number);
                    updateMainSummary();
                }
            }

            updatePreview();
            annModal.show();
        }).fail(function () {
            Swal.fire('Error', 'Failed to load announcement.', 'error');
        });
    });


    $form.on('submit', function (e) {
        e.preventDefault();
        var id  = $('#ann_id').val();
        var url = id
            ? '/customer-service/announcements/update/' + id
            : '/customer-service/announcements/store';

        var payload = new FormData(this);
        payload.set('_token', csrfToken);

        $.ajax({
            url: url,
            type: 'POST',
            data: payload,
            processData: false,
            contentType: false,
            success: function (res) {
                annModal.hide();
                table.ajax.reload();
                Toast.fire('Success!', res.success, 'success');
            },
            error: function (xhr) {
                var errors = xhr.responseJSON && xhr.responseJSON.errors;
                var msg    = errors ? Object.values(errors).flat().join('\n') : 'Something went wrong.';
                Swal.fire('Validation Error', msg, 'error');
            },
        });
    });


    $('#announcementsTable tbody').on('click', '.btn-toggle-status', function () {
        var id = $(this).data('id');
        $.ajax({
            url:  '/customer-service/announcements/toggle-status/' + id,
            type: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken },
            success: function () { table.ajax.reload(null, false); },
            error:   function () { Toast.fire('Error', 'Failed to toggle status.', 'error'); },
        });
    });


    $('#announcementsTable tbody').on('click', '.btn-delete-ann', function () {
        var id = $(this).data('id');
        Swal.fire({
            title: 'Delete this announcement?',
            text: 'This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!',
        }).then(function (result) {
            if (!result.isConfirmed) return;
            $.ajax({
                url:  '/customer-service/announcements/destroy/' + id,
                type: 'DELETE',
                headers: { 'X-CSRF-TOKEN': csrfToken },
                success: function (res) {
                    table.ajax.reload();
                    Toast.fire('Deleted!', res.success, 'success');
                },
                error: function () { Toast.fire('Error', 'Failed to delete.', 'error'); },
            });
        });
    });


    var $batchBtn = $('#btn-batch-delete-announcements');

    $(document).on('change', '.row-check', function () {
        $batchBtn.toggleClass('d-none', $('.row-check:checked').length === 0);
    });

    $('#select-all-ann').on('change', function () {
        $('.row-check').prop('checked', this.checked);
        $batchBtn.toggleClass('d-none', !this.checked);
    });

    $batchBtn.on('click', function () {
        var ids = $('.row-check:checked').map(function () { return $(this).data('id'); }).get();
        if (!ids.length) return;

        Swal.fire({
            title: 'Delete ' + ids.length + ' announcement(s)?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Yes, delete all!',
        }).then(function (result) {
            if (!result.isConfirmed) return;
            $.ajax({
                url:  '/customer-service/announcements/batch-destroy',
                type: 'DELETE',
                data: { ids: ids },
                headers: { 'X-CSRF-TOKEN': csrfToken },
                success: function (res) {
                    table.ajax.reload();
                    $batchBtn.addClass('d-none');
                    Toast.fire('Deleted!', res.success, 'success');
                },
                error: function () { Toast.fire('Error', 'Failed to delete selected items.', 'error'); },
            });
        });
    });

    table.on('draw', function () {
        $('#select-all-ann').prop('checked', false);
        $batchBtn.addClass('d-none');
    });

    
    $('#btn-refresh-announcements').on('click', function () {
        table.ajax.reload(null, false);
    });


    updatePreview();

});
