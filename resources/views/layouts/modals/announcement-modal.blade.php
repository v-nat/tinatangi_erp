<style>
    /* ── Type selector cards ── */
    .type-card {
        border: 2px solid transparent;
        border-radius: 10px;
        cursor: pointer;
        transition: border-color .2s, background .2s;
        padding: 12px 8px;
        text-align: center;
        background: rgba(255,255,255,.04);
    }
    .type-card:hover { border-color: #cda45e; }
    .type-card.selected { border-color: #cda45e; background: rgba(205,164,94,.12); }
    .type-card .type-icon { font-size: 1.5rem; color: #cda45e; }
    .type-card .type-label { font-size: .75rem; font-weight: 600; margin-top: 4px; }

    /* ── Icon picker grid ── */
    .icon-grid { display: grid; grid-template-columns: repeat(8, 1fr); gap: 6px; }
    .icon-tile {
        width: 38px; height: 38px;
        display: flex; align-items: center; justify-content: center;
        border: 1.5px solid rgba(255,255,255,.15);
        border-radius: 8px;
        cursor: pointer;
        font-size: 1rem;
        transition: border-color .15s, background .15s;
        color: inherit;
    }
    .icon-tile:hover { border-color: #cda45e; color: #cda45e; }
    .icon-tile.selected { border-color: #cda45e; background: rgba(205,164,94,.15); color: #cda45e; }

    /* ── Theme swatches ── */
    .theme-swatches { display: flex; gap: 10px; flex-wrap: wrap; }
    .theme-swatch {
        width: 36px; height: 36px;
        border-radius: 50%;
        cursor: pointer;
        border: 3px solid transparent;
        transition: border-color .15s, transform .15s;
        position: relative;
    }
    .theme-swatch:hover { transform: scale(1.15); }
    .theme-swatch.selected { border-color: #fff; transform: scale(1.2); }
    .theme-swatch[data-theme="gold"]    { background: linear-gradient(135deg, #1e1b16 50%, #cda45e 100%); }
    .theme-swatch[data-theme="crimson"] { background: linear-gradient(135deg, #1a0a0a 50%, #c0392b 100%); }
    .theme-swatch[data-theme="forest"]  { background: linear-gradient(135deg, #0a1a0f 50%, #27ae60 100%); }
    .theme-swatch[data-theme="ocean"]   { background: linear-gradient(135deg, #0a0f1a 50%, #2980b9 100%); }
    .theme-swatch[data-theme="royal"]   { background: linear-gradient(135deg, #110a1a 50%, #8e44ad 100%); }
    .theme-swatch[data-theme="slate"]   { background: linear-gradient(135deg, #0f1219 50%, #5d8aa8 100%); }

    /* ── Background style tiles ── */
    .bg-style-tiles { display: flex; gap: 8px; }
    .bg-tile {
        flex: 1; padding: 8px 4px;
        border: 2px solid rgba(255,255,255,.15);
        border-radius: 8px;
        text-align: center;
        cursor: pointer;
        font-size: .72rem;
        font-weight: 600;
        transition: border-color .15s;
    }
    .bg-tile:hover { border-color: #cda45e; }
    .bg-tile.selected { border-color: #cda45e; background: rgba(205,164,94,.1); color: #cda45e; }

    /* ── Live preview card ── */
    .preview-wrapper {
        position: sticky;
        top: 16px;
        min-height: 200px;
    }
    .preview-label {
        font-size: .7rem;
        font-weight: 700;
        letter-spacing: .08em;
        text-transform: uppercase;
        opacity: .5;
        margin-bottom: 10px;
    }

    /* announcement card on landing — preview replicates it */
    .ann-card {
        border-radius: 16px;
        padding: 24px 22px;
        color: #f8f5f0;
        position: relative;
        overflow: hidden;
        font-family: 'Poppins', sans-serif;
        min-height: 140px;
    }
    .ann-card-inner { position: relative; z-index: 1; }
    .ann-badge {
        display: inline-block;
        font-size: .65rem;
        font-weight: 700;
        letter-spacing: .1em;
        text-transform: uppercase;
        padding: 3px 10px;
        border-radius: 20px;
        margin-bottom: 10px;
    }
    .ann-icon-wrap {
        width: 44px; height: 44px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.2rem;
        margin-bottom: 10px;
        background: rgba(255,255,255,.12);
    }
    .ann-title { font-size: 1.1rem; font-weight: 700; margin-bottom: 6px; line-height: 1.3; }
    .ann-content { font-size: .82rem; opacity: .82; margin-bottom: 0; }
    .ann-discount-value {
        font-size: 2rem; font-weight: 800;
        line-height: 1; margin-bottom: 4px;
    }
    .ann-discount-label { font-size: .75rem; opacity: .7; margin-bottom: 8px; }
    .ann-voucher-code {
        display: inline-flex; align-items: center; gap: 8px;
        background: rgba(255,255,255,.1);
        border: 1px dashed rgba(255,255,255,.35);
        border-radius: 8px;
        padding: 6px 14px;
        font-family: monospace;
        font-size: .9rem;
        letter-spacing: .12em;
        margin-top: 8px;
    }

    /* Theme color maps */
    .ann-theme-gold    { --ann-bg:#1e1b16; --ann-accent:#cda45e; --ann-bg2:#2d2418; }
    .ann-theme-crimson { --ann-bg:#1a0a0a; --ann-accent:#c0392b; --ann-bg2:#2e0f0f; }
    .ann-theme-forest  { --ann-bg:#0a1a0f; --ann-accent:#27ae60; --ann-bg2:#0f2818; }
    .ann-theme-ocean   { --ann-bg:#0a0f1a; --ann-accent:#2980b9; --ann-bg2:#0f1a2e; }
    .ann-theme-royal   { --ann-bg:#110a1a; --ann-accent:#8e44ad; --ann-bg2:#1c0f2e; }
    .ann-theme-slate   { --ann-bg:#0f1219; --ann-accent:#5d8aa8; --ann-bg2:#171d2e; }

    .ann-style-solid    { background: var(--ann-bg); }
    .ann-style-gradient { background: linear-gradient(135deg, var(--ann-bg) 0%, var(--ann-bg2) 100%); }
    .ann-style-glow     { background: var(--ann-bg); box-shadow: 0 0 30px rgba(var(--ann-accent-rgb, 205,164,94), .25) inset; }

    .ann-card .ann-badge    { background: rgba(255,255,255,.12); color: var(--ann-accent); border: 1px solid var(--ann-accent); }
    .ann-card .ann-icon-wrap { color: var(--ann-accent); }
    .ann-card .ann-discount-value { color: var(--ann-accent); }

    /* separator in modal form */
    .modal-section-label {
        font-size: .7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .07em;
        opacity: .5;
        margin: 14px 0 8px;
    }

    /* Fix: <form> sits between modal-content and modal-body, breaking
       Bootstrap's modal-dialog-scrollable flex layout. Make the form
       itself a flex column so modal-body gets overflow-y: auto. */
    #announcementModal form {
        display: flex;
        flex-direction: column;
        overflow: hidden;
        flex: 1;
        min-height: 0;
    }

    /* Stack product picker modal above announcement modal */
    #productPickerModal { z-index: 1075; }

    /* Picker list-group item hover state */
    #product-picker-list .list-group-item:hover {
        background: rgba(205,164,94,.06) !important;
    }
    #product-picker-list .list-group-item:has(.picker-product-check:checked) {
        background: rgba(205,164,94,.08) !important;
        border-color: rgba(205,164,94,.25) !important;
    }
</style>

<div class="modal fade" id="announcementModal" tabindex="-1" aria-labelledby="announcementModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="announcementModalLabel">
                    <i class="fa-solid fa-bullhorn me-2" style="color:#cda45e"></i>
                    Create Announcement
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="announcementForm">
                @csrf
                <input type="hidden" id="ann_id" name="ann_id">
                <input type="hidden" id="ann_method" name="_method" value="POST">

                <div class="modal-body">
                    <div class="row g-4">

                        {{-- ────────────────── LEFT: FORM ────────────────── --}}
                        <div class="col-lg-7">

                            {{-- Type --}}
                            <p class="modal-section-label">Type</p>
                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <div class="type-card selected" data-type="announcement" id="typeCard-announcement">
                                        <div class="type-icon"><i class="fa-solid fa-bullhorn"></i></div>
                                        <div class="type-label">Announcement</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="type-card" data-type="discount" id="typeCard-discount">
                                        <div class="type-icon"><i class="fa-solid fa-percent"></i></div>
                                        <div class="type-label">Discount</div>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" id="ann_type" name="type" value="announcement">

                            {{-- Title --}}
                            <div class="mb-3">
                                <label class="form-label">Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="ann_title" name="title" maxlength="255" placeholder="e.g. Weekend Flash Sale!" required>
                            </div>

                            {{-- Content --}}
                            <div class="mb-3">
                                <label class="form-label">Message / Description</label>
                                <textarea class="form-control" id="ann_content" name="content" rows="3" placeholder="Tell customers about this offer or news..."></textarea>
                            </div>

                            {{-- Badge --}}
                            <div class="mb-3">
                                <label class="form-label">Badge Text <small class="text-muted">(short label)</small></label>
                                <input type="text" class="form-control" id="ann_badge" name="badge_text" maxlength="50" placeholder="e.g. HOT, NEW, LIMITED TIME">
                            </div>

                            {{-- Icon picker --}}
                            <p class="modal-section-label">Icon</p>
                            <input type="hidden" id="ann_icon" name="icon" value="fa-solid fa-bullhorn">
                            <div class="icon-grid mb-3" id="iconGrid">
                                @php
                                    $icons = [
                                        'fa-solid fa-bullhorn',
                                        'fa-solid fa-tag',
                                        'fa-solid fa-percent',
                                        'fa-solid fa-ticket',
                                        'fa-solid fa-gift',
                                        'fa-solid fa-star',
                                        'fa-solid fa-fire',
                                        'fa-solid fa-bolt',
                                        'fa-solid fa-mug-hot',
                                        'fa-solid fa-cake-candles',
                                        'fa-solid fa-champagne-glasses',
                                        'fa-solid fa-heart',
                                        'fa-solid fa-crown',
                                        'fa-solid fa-gem',
                                        'fa-solid fa-leaf',
                                        'fa-solid fa-clock',
                                    ];
                                @endphp
                                @foreach($icons as $ic)
                                    <div class="icon-tile {{ $ic === 'fa-solid fa-bullhorn' ? 'selected' : '' }}"
                                         data-icon="{{ $ic }}" title="{{ $ic }}">
                                        <i class="{{ $ic }}"></i>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Theme --}}
                            <p class="modal-section-label">Theme</p>
                            <input type="hidden" id="ann_theme" name="theme" value="gold">
                            <div class="theme-swatches mb-1" id="themeSwatches">
                                @foreach(['gold','crimson','forest','ocean','royal','slate'] as $t)
                                    <div class="theme-swatch {{ $t === 'gold' ? 'selected' : '' }}"
                                         data-theme="{{ $t }}" title="{{ ucfirst($t) }}"></div>
                                @endforeach
                            </div>
                            <div class="d-flex gap-3 mb-3" style="font-size:.72rem; opacity:.55">
                                <span>Gold</span><span>Crimson</span><span>Forest</span><span>Ocean</span><span>Royal</span><span>Slate</span>
                            </div>

                            {{-- Background style --}}
                            <p class="modal-section-label">Background Style</p>
                            <input type="hidden" id="ann_bg_style" name="bg_style" value="solid">
                            <div class="bg-style-tiles mb-3" id="bgStyleTiles">
                                <div class="bg-tile selected" data-style="solid">
                                    <i class="fa-solid fa-square-full d-block mb-1"></i> Solid
                                </div>
                                <div class="bg-tile" data-style="gradient">
                                    <i class="fa-solid fa-circle-half-stroke d-block mb-1"></i> Gradient
                                </div>
                                <div class="bg-tile" data-style="glow">
                                    <i class="fa-solid fa-sun d-block mb-1"></i> Glow
                                </div>
                            </div>

                            {{-- Discount / Voucher specific fields --}}
                            <div id="discountFields" class="d-none">
                                <p class="modal-section-label">Discount Details</p>
                                <div class="row g-2 mb-3">
                                    <div class="col-md-4">
                                        <label class="form-label">Discount Type</label>
                                        <select class="form-select" id="ann_discount_type" name="discount_type">
                                            <option value="percentage">Percentage (%)</option>
                                            <option value="fixed">Fixed Amount (₱)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Discount Value</label>
                                        <input type="number" class="form-control" id="ann_discount_value" name="discount_value" min="0" step="0.01" placeholder="0">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Min. Spend (₱)</label>
                                        <input type="number" class="form-control" id="ann_min_spend" name="min_spend" min="0" step="0.01" placeholder="0">
                                    </div>
                                </div>
                            </div>

                            {{-- Applies To --}}
                            <div id="applicableToSection" class="d-none">
                                <p class="modal-section-label">Applies To</p>
                                <input type="hidden" id="ann_applicable_to" name="applicable_to" value="all">
                                <div class="bg-style-tiles mb-3" id="applicableToTiles">
                                    <div class="bg-tile selected" data-scope="all">
                                        <i class="fa-solid fa-layer-group d-block mb-1"></i> All Products
                                    </div>
                                    <div class="bg-tile" data-scope="specific">
                                        <i class="fa-solid fa-list-check d-block mb-1"></i> Specific Products
                                    </div>
                                </div>

                                {{-- Product Picker --}}
                                <div id="productPickerSection" class="d-none mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span style="font-size:.7rem;opacity:.5;font-weight:700;text-transform:uppercase;letter-spacing:.07em">Selected Products</span>
                                        <button type="button" class="btn btn-sm btn-outline-secondary" id="btn-open-product-picker" style="font-size:.72rem;padding:2px 10px">
                                            <i class="fa-solid fa-list-check me-1"></i> Pick Products
                                        </button>
                                    </div>
                                    <div id="product-selection-summary"
                                         class="small py-2 px-2 rounded"
                                         style="border:1px solid rgba(255,255,255,.12);min-height:36px;color:rgba(255,255,255,.35)">
                                        No products selected
                                    </div>
                                    <div id="product-ids-container"></div>
                                </div>

                                {{-- Usage Limit --}}
                                <p class="modal-section-label">Usage Limit</p>
                                <div class="row g-2 mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Max Redemptions <small class="text-muted">(optional)</small></label>
                                        <input type="number" class="form-control" id="ann_usage_limit" name="usage_limit" min="1" placeholder="Unlimited">
                                    </div>
                                    <div class="col-md-6 d-none" id="usage_count_info">
                                        <label class="form-label">Times Used</label>
                                        <div class="form-control-plaintext small" id="usage_count_display" style="color:#cda45e"></div>
                                    </div>
                                </div>
                            </div>

                            {{-- Validity + settings --}}
                            <p class="modal-section-label">Schedule & Settings</p>
                            <div class="row g-2 mb-3">
                                <div class="col-md-4">
                                    <label class="form-label">Valid From</label>
                                    <input type="date" class="form-control" id="ann_valid_from" name="valid_from">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Valid Until</label>
                                    <input type="date" class="form-control" id="ann_valid_until" name="valid_until">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Order</label>
                                    <input type="number" class="form-control" id="ann_order" name="display_order" value="0" min="0">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Status</label>
                                    <select class="form-select" id="ann_status" name="status">
                                        <option value="35">Displayed</option>
                                        <option value="34" selected>Hidden</option>
                                    </select>
                                </div>
                            </div>

                        </div>

                        {{-- ────────────────── RIGHT: LIVE PREVIEW ────────────────── --}}
                        <div class="col-lg-5">
                            <div class="preview-wrapper">
                                <div class="preview-label">Live Preview</div>
                                <div id="ann-preview-card" class="ann-card ann-theme-gold ann-style-solid">
                                    <div class="ann-card-inner">
                                        <div id="prev-badge" class="ann-badge d-none"></div>
                                        <div id="prev-icon-wrap" class="ann-icon-wrap">
                                            <i id="prev-icon" class="fa-solid fa-bullhorn"></i>
                                        </div>
                                        <div id="prev-title" class="ann-title">Your announcement title</div>
                                        <p id="prev-content" class="ann-content d-none"></p>

                                        {{-- Discount preview --}}
                                        <div id="prev-discount-block" class="d-none">
                                            <div id="prev-discount-value" class="ann-discount-value">0%</div>
                                            <div id="prev-discount-label" class="ann-discount-label">OFF</div>
                                        </div>

                                    </div>
                                </div>

                                {{-- Mini legend --}}
                                <div class="mt-3 p-3 rounded" style="background:rgba(255,255,255,.04); font-size:.72rem;">
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <i class="fa-solid fa-circle-info" style="color:#cda45e"></i>
                                        <strong>How it appears on the landing page</strong>
                                    </div>
                                    <ul class="mb-0 ps-3" style="opacity:.65">
                                        <li><strong>Announcements</strong> → rotating ticker bar at the top</li>
                                        <li><strong>Discounts</strong> → Promotions section card grid</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                    </div>{{-- /row --}}
                </div>{{-- /modal-body --}}

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="btn-save-announcement">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Save
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

{{-- ── Product Picker Modal (stacks above announcement modal) ── --}}
<div class="modal fade" id="productPickerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fa-solid fa-list-check me-2" style="color:#cda45e"></i>
                    Select Products
                </h5>
                <button type="button" class="btn-close" id="btn-picker-close"></button>
            </div>

            <div class="modal-body">
                <div class="mb-3">
                    <input type="text" id="product-picker-search" class="form-control"
                           placeholder="Search by product name or category…">
                </div>
                <div id="product-picker-list">
                    <div class="text-center py-4 opacity-50">
                        <i class="fa-solid fa-spinner fa-spin me-1"></i> Loading products…
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <span id="picker-selection-count" class="me-auto small" style="opacity:.55">0 products selected</span>
                <button type="button" id="btn-picker-select-all" class="btn btn-sm btn-outline-secondary">Select All</button>
                <button type="button" id="btn-picker-clear" class="btn btn-sm btn-outline-secondary">Clear All</button>
                <button type="button" id="btn-picker-cancel" class="btn btn-sm btn-secondary">Cancel</button>
                <button type="button" id="btn-picker-confirm" class="btn btn-sm btn-primary">
                    <i class="fa-solid fa-check me-1"></i> Confirm Selection
                </button>
            </div>

        </div>
    </div>
</div>
