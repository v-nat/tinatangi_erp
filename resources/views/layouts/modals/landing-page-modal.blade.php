<div class="modal fade" id="messageModal" tabindex="-1" aria-labelledby="messageModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content cafe-modal-content">
      <div class="cafe-modal-accent-bar"></div>
      <div class="modal-header border-0 px-4 pt-4 pb-2">
        <h5 class="modal-title cafe-modal-title" id="messageModalLabel">Message</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body px-4 pb-4 cafe-modal-body">
        <p id="modalMessageBody" class="mb-0"></p>
      </div>
    </div>
  </div>
</div>

{{-- Step 1: Choose a table category --}}
<div class="modal fade" id="tableRecommendationModal" tabindex="-1" aria-labelledby="tableRecommendationModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
    <div class="modal-content cafe-modal-content">
      <div class="cafe-modal-accent-bar"></div>

      <div class="modal-header border-0 px-4 pt-4 pb-2">
        <div>
          <span class="cafe-step-badge">Step 1 of 2</span>
          <h5 class="modal-title cafe-modal-title mt-1" id="tableRecommendationModalLabel">Choose Your Table Type</h5>
          <p class="cafe-modal-subtitle mb-0">Based on your party size and time, click a table to continue.</p>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body px-4 py-3">
        <div id="table-options-container" class="row gy-4"></div>
      </div>

      <div class="modal-footer border-0 px-4 pb-4 pt-1">
        <button type="button" class="cafe-btn-ghost" data-bs-dismiss="modal">Cancel</button>
      </div>
    </div>
  </div>
</div>

{{-- Step 2: Choose a specific table slot + duration --}}
<div class="modal fade" id="tableSlotModal" tabindex="-1" aria-labelledby="tableSlotModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-md">
    <div class="modal-content cafe-modal-content">
      <div class="cafe-modal-accent-bar"></div>

      <div class="modal-header border-0 px-4 pt-4 pb-2">
        <div>
          <span class="cafe-step-badge">Step 2 of 2</span>
          <h5 class="modal-title cafe-modal-title mt-1" id="tableSlotModalLabel">Pick a Table &amp; Duration</h5>
          <p class="cafe-modal-subtitle mb-0" id="slotCategoryName"></p>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body px-4 py-3">
        {{-- Duration selector --}}
        <div class="mb-4">
          <label for="slotDurationSelect" class="cafe-field-label">
            <i class="fa-regular fa-clock me-1" style="color:#cda45e;"></i>
            How long will you stay?
          </label>
          <select id="slotDurationSelect" class="cafe-select">
            <option value="1">1 hour</option>
            <option value="2" selected>2 hours</option>
            <option value="3">3 hours</option>
            <option value="4">4 hours</option>
            <option value="5">5 hours</option>
            <option value="6">6 hours</option>
            <option value="7">7 hours</option>
            <option value="8">8 hours</option>
          </select>
        </div>

        {{-- Slot grid --}}
        <label class="cafe-field-label">
          <i class="fa-solid fa-chair me-1" style="color:#cda45e;"></i>
          Select a table number:
        </label>

        <div id="slot-loading" class="text-center py-3 d-none">
          <div class="spinner-border spinner-border-sm me-2" style="color:#cda45e;" role="status"></div>
          <span style="color:#a89070;font-size:.85rem;">Checking availability...</span>
        </div>

        <div id="slot-grid" class="d-flex flex-wrap gap-2 mt-2"></div>
        <p id="slot-error" class="mt-2 d-none" style="color:#e07070;font-size:.85rem;"></p>
      </div>

      <div class="modal-footer border-0 px-4 pb-4 pt-2 d-flex justify-content-between">
        <button type="button" class="cafe-btn-ghost" id="slotBackBtn">
          <i class="fa-solid fa-arrow-left me-1"></i> Back
        </button>
        <button type="button" class="cafe-btn-primary" id="confirmSlotBtn" disabled>
          Confirm Booking <i class="fa-solid fa-check ms-1"></i>
        </button>
      </div>
    </div>
  </div>
</div>

<style>
  /* ── Cafe-themed modal base ── */
  .cafe-modal-content {
    background: #1a1611;
    border: 1px solid rgba(205, 164, 94, 0.22);
    border-radius: 16px;
    overflow: hidden;
    color: #c8bfb0;
  }

  .cafe-modal-accent-bar {
    height: 3px;
    background: linear-gradient(90deg, transparent 0%, #cda45e 40%, rgba(205,164,94,.6) 70%, transparent 100%);
  }

  .cafe-modal-title {
    font-family: 'Playfair Display', serif;
    font-size: 1.35rem;
    font-weight: 700;
    color: #f8f5f0;
    margin-bottom: 2px;
  }

  .cafe-modal-subtitle {
    font-size: .83rem;
    color: #8a7d6a;
  }

  .cafe-modal-body {
    color: #c8bfb0;
  }

  .cafe-step-badge {
    display: inline-block;
    font-size: .62rem;
    font-weight: 700;
    letter-spacing: .1em;
    text-transform: uppercase;
    padding: 2px 10px;
    border-radius: 20px;
    background: rgba(205, 164, 94, .12);
    border: 1px solid rgba(205, 164, 94, .38);
    color: #cda45e;
  }

  .cafe-field-label {
    display: block;
    font-size: .82rem;
    font-weight: 600;
    color: #c8bfb0;
    margin-bottom: 8px;
    letter-spacing: .02em;
  }

  /* ── Duration select ── */
  .cafe-select {
    width: 100%;
    background: #231e17;
    border: 1px solid rgba(205, 164, 94, .3);
    border-radius: 8px;
    color: #f0ebe3;
    padding: 10px 14px;
    font-size: .9rem;
    outline: none;
    appearance: none;
    -webkit-appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath fill='%23cda45e' d='M6 8L0 0h12z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 14px center;
    background-size: 10px;
    cursor: pointer;
    transition: border-color .25s;
  }
  .cafe-select:focus {
    border-color: #cda45e;
    box-shadow: 0 0 0 3px rgba(205,164,94,.12);
  }
  .cafe-select option {
    background: #231e17;
    color: #f0ebe3;
  }

  /* ── Buttons ── */
  .cafe-btn-primary {
    background: #cda45e;
    color: #1a1611;
    border: none;
    border-radius: 50px;
    padding: 9px 28px;
    font-size: .88rem;
    font-weight: 700;
    letter-spacing: .03em;
    cursor: pointer;
    transition: background .25s, box-shadow .25s, transform .15s;
  }
  .cafe-btn-primary:hover:not(:disabled) {
    background: #e0b870;
    box-shadow: 0 6px 18px rgba(205,164,94,.35);
    transform: translateY(-1px);
  }
  .cafe-btn-primary:disabled {
    opacity: .38;
    cursor: not-allowed;
  }

  .cafe-btn-ghost {
    background: transparent;
    color: #8a7d6a;
    border: 1px solid rgba(205, 164, 94, .25);
    border-radius: 50px;
    padding: 8px 20px;
    font-size: .85rem;
    cursor: pointer;
    transition: color .2s, border-color .2s;
  }
  .cafe-btn-ghost:hover {
    color: #cda45e;
    border-color: rgba(205, 164, 94, .6);
  }

  /* ── Category cards (rendered by JS) ── */
  .cafe-table-card {
    background: #231e17;
    border: 2px solid rgba(205, 164, 94, .14);
    border-radius: 14px;
    overflow: hidden;
    cursor: pointer;
    transition: border-color .25s, box-shadow .25s, transform .2s;
    height: 100%;
  }
  .cafe-table-card:hover {
    border-color: #cda45e;
    box-shadow: 0 8px 28px rgba(205, 164, 94, .15);
    transform: translateY(-3px);
  }
  .cafe-table-card .cafe-card-body {
    padding: 14px 16px 10px;
  }
  .cafe-table-card .cafe-card-title {
    font-family: 'Playfair Display', serif;
    font-size: 1rem;
    font-weight: 700;
    color: #f8f5f0;
    margin-bottom: 4px;
  }
  .cafe-table-card .cafe-card-meta {
    font-size: .78rem;
    color: #8a7d6a;
    margin-bottom: 2px;
  }
  .cafe-table-card .cafe-card-desc {
    font-size: .76rem;
    color: #6a6055;
    margin-top: 4px;
  }
  .cafe-table-card .cafe-card-footer {
    background: rgba(205, 164, 94, .07);
    border-top: 1px solid rgba(205, 164, 94, .14);
    text-align: center;
    padding: 9px;
    font-size: .8rem;
    font-weight: 600;
    color: #cda45e;
    letter-spacing: .04em;
    transition: background .25s, color .25s;
  }
  .cafe-table-card:hover .cafe-card-footer {
    background: #cda45e;
    color: #1a1611;
  }

  /* ── Slot buttons (rendered by JS) ── */
  .cafe-slot-btn {
    border: 2px solid rgba(205, 164, 94, .45);
    background: rgba(205, 164, 94, .06);
    color: #cda45e;
    border-radius: 10px;
    padding: 10px 14px;
    min-width: 80px;
    font-size: .82rem;
    font-weight: 600;
    cursor: pointer;
    text-align: center;
    line-height: 1.3;
    transition: background .2s, border-color .2s, color .2s, box-shadow .2s;
  }
  .cafe-slot-btn:hover {
    background: rgba(205, 164, 94, .18);
    border-color: #cda45e;
    box-shadow: 0 4px 12px rgba(205,164,94,.18);
  }
  .cafe-slot-btn.selected {
    background: #cda45e;
    border-color: #cda45e;
    color: #1a1611;
    box-shadow: 0 4px 14px rgba(205,164,94,.35);
  }
  .cafe-slot-btn.taken {
    border-color: rgba(255, 255, 255, .08);
    background: rgba(255, 255, 255, .03);
    color: rgba(255, 255, 255, .22);
    cursor: not-allowed;
  }
  .cafe-slot-btn small {
    display: block;
    font-size: .7rem;
    font-weight: 400;
    opacity: .75;
    margin-top: 1px;
  }
  .cafe-slot-btn.taken small {
    opacity: .4;
  }
</style>
