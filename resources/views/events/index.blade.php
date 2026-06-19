@extends('layouts.app')

@section('content')

    <style>
        /* ── Card ── */
        .card {
            background: var(--white);
            border-radius: 20px;
            padding: 36px;
            box-shadow: 0 8px 40px rgba(26,16,64,0.08);
            border: 1px solid var(--border);
        }

        /* ── Section title ── */
        .section-title {
            font-family: 'Baloo 2', cursive;
            font-size: 20px; font-weight: 700;
            color: var(--deep); margin-bottom: 6px;
        }
        .section-sub { font-size: 13px; color: var(--muted); margin-bottom: 24px; }

        /* ── Form fields ── */
        .form-group { margin-bottom: 18px; }
        .form-group label {
            display: block; font-size: 13px; font-weight: 600;
            color: var(--deep); margin-bottom: 6px;
        }
        .form-group input,
        .form-group select {
            width: 100%; padding: 11px 14px;
            border: 1.5px solid var(--border);
            border-radius: 10px; font-size: 14px;
            font-family: 'Poppins', sans-serif;
            background: var(--cream); outline: none;
            transition: border .2s, box-shadow .2s;
            color: var(--deep); box-sizing: border-box;
        }
        .form-group input:focus,
        .form-group select:focus {
            border-color: var(--saffron); background: #fff;
            box-shadow: 0 0 0 3px rgba(255,107,0,.10);
        }
        .form-group input.error,
        .form-group select.error {
            border-color: #ef4444;
            box-shadow: 0 0 0 3px rgba(239,68,68,.10);
        }
        .field-error { font-size: 12px; color: #ef4444; margin-top: 4px; display: none; }
        .field-error.show { display: block; }

        /* ── Divider ── */
        .divider { border: none; border-top: 1px dashed var(--border); margin: 28px 0; }

        /* ── Gender ── */
        .gender-group { display: flex; gap: 12px; margin-top: 4px; }
        .gender-option { flex: 1; position: relative; }
        .gender-option input[type="radio"] { position: absolute; opacity: 0; width: 0; height: 0; }
        .gender-label {
            display: flex; align-items: center; justify-content: center;
            gap: 8px; padding: 13px 16px;
            border: 2px solid var(--border);
            border-radius: 12px; cursor: pointer;
            background: var(--cream); color: var(--muted);
            font-size: 14px; font-weight: 600;
            transition: all .2s; user-select: none;
        }
        .gender-label .g-icon { font-size: 22px; }
        .gender-option input:checked + .gender-label {
            border-color: var(--saffron);
            background: rgba(255,107,0,.08);
            color: var(--saffron);
            box-shadow: 0 0 0 3px rgba(255,107,0,.12);
        }
        .gender-error { font-size: 12px; color: #ef4444; margin-top: 6px; display: none; }
        .gender-error.show { display: block; }

        /* ── Photo upload area ── */
        .photo-upload-area {
            border: 2px dashed var(--border);
            border-radius: 14px; padding: 28px;
            text-align: center; cursor: pointer;
            transition: all .25s; background: var(--cream);
            position: relative;
        }
        .photo-upload-area:hover { border-color: var(--saffron); background: #FFF0E0; }
        .upload-icon { font-size: 38px; margin-bottom: 8px; }
        .upload-text { font-size: 14px; color: var(--muted); }
        .upload-text b { color: var(--saffron); }
        #photo-input { position: absolute; inset: 0; opacity: 0; cursor: pointer; width:100%; height:100%; }

        /* ── Photo preview ── */
        .photo-preview-wrap { display: none; align-items: center; gap: 16px; margin-top: 14px; }
        .photo-preview-wrap.show { display: flex; }
        #photo-preview {
            width: 80px; height: 80px;
            border-radius: 10px; object-fit: cover;
            border: 2px solid var(--saffron);
        }
        .preview-info { font-size: 13px; color: var(--muted); }
        .preview-info b { color: var(--deep); display: block; margin-bottom: 2px; }
        .btn-reselect {
            background: none; border: 1.5px solid var(--border);
            padding: 5px 12px; border-radius: 8px;
            font-size: 12px; color: var(--muted);
            cursor: pointer; margin-top: 6px;
            font-family: 'Poppins', sans-serif;
        }

        /* ── Photo error ── */
        .photo-error { font-size: 12px; color: #ef4444; margin-top: 8px; display: none; }
        .photo-error.show { display: block; }

        /* ── Submit ── */
        .btn-submit {
            width: 100%; padding: 15px;
            background: linear-gradient(135deg, var(--saffron), #FF9A3C);
            color: #fff; border: none;
            border-radius: 12px; font-size: 16px; font-weight: 700;
            cursor: pointer; font-family: 'Poppins', sans-serif;
            box-shadow: 0 6px 20px rgba(255,107,0,0.35);
            transition: all .25s; margin-top: 8px; letter-spacing: .3px;
        }
        .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 10px 28px rgba(255,107,0,0.45); }
        .btn-submit:active { transform: translateY(0); }
        .btn-submit:disabled { opacity: .6; cursor: not-allowed; transform: none; }

        /* ── Toast ── */
        .toast {
            position: fixed; bottom: 24px; left: 50%;
            transform: translateX(-50%) translateY(80px);
            background: #1a1040; color: #fff;
            padding: 12px 24px; border-radius: 10px;
            font-size: 13px; font-weight: 600;
            box-shadow: 0 8px 24px rgba(0,0,0,0.3);
            z-index: 9999; transition: transform .35s ease;
            white-space: nowrap;
        }
        .toast.show { transform: translateX(-50%) translateY(0); }

        /* ── Crop modal ── */
        .modal-overlay {
            display: none; position: fixed; inset: 0;
            background: rgba(10,5,30,0.75);
            z-index: 1000; align-items: center; justify-content: center;
            backdrop-filter: blur(4px);
        }
        .modal-overlay.open { display: flex; }
        .modal-box {
            background: var(--white);
            border-radius: 20px; padding: 28px;
            max-width: 500px; width: 92%;
            box-shadow: 0 20px 60px rgba(0,0,0,0.4);
        }
        .modal-title {
            font-family: 'Baloo 2', cursive;
            font-size: 18px; margin-bottom: 12px;
            color: var(--deep);
        }
        #crop-container {
            width: 100%; aspect-ratio: 1/1;
            background: #1a1a1a; border-radius: 12px;
            overflow: hidden; position: relative;
            cursor: grab; touch-action: none; user-select: none;
        }
        #crop-container.dragging { cursor: grabbing; }
        #crop-canvas { position: absolute; top: 0; left: 0; image-rendering: auto; }
        #crop-guide {
            position: absolute;
            border: 2px solid rgba(255,107,0,1);
            border-radius: 2px;
            box-shadow: 0 0 0 9999px rgba(0,0,0,0.62);
            pointer-events: none;
        }
        #crop-guide::before, #crop-guide::after {
            content: ''; position: absolute;
            width: 18px; height: 18px;
            border-color: #FF6B00; border-style: solid;
        }
        #crop-guide::before { top:-2px; left:-2px; border-width:3px 0 0 3px; border-radius:2px 0 0 0; }
        #crop-guide::after  { bottom:-2px; right:-2px; border-width:0 3px 3px 0; border-radius:0 0 2px 0; }

        .crop-toolbar {
            display: flex; align-items: center; gap: 10px;
            margin: 12px 0 10px;
            background: var(--cream); border: 1.5px solid var(--border);
            border-radius: 10px; padding: 8px 12px;
        }
        .crop-toolbar label { font-size:12px; font-weight:600; color:var(--muted); white-space:nowrap; }
        .zoom-slider {
            flex: 1; -webkit-appearance: none; height: 4px; border-radius: 4px;
            background: linear-gradient(to right, var(--saffron) 0%, var(--border) 0%);
            outline: none; cursor: pointer;
        }
        .zoom-slider::-webkit-slider-thumb {
            -webkit-appearance: none; width:18px; height:18px; border-radius:50%;
            background: var(--saffron); border: 2px solid #fff;
            box-shadow: 0 1px 4px rgba(0,0,0,0.25); cursor: pointer;
        }
        .zoom-slider::-moz-range-thumb {
            width:18px; height:18px; border-radius:50%;
            background: var(--saffron); border:2px solid #fff; cursor:pointer;
        }
        .zoom-value { font-size:12px; font-weight:700; color:var(--saffron); min-width:34px; text-align:right; }
        .zoom-btns { display:flex; gap:4px; }
        .zoom-btn {
            width:28px; height:28px; border-radius:7px;
            border:1.5px solid var(--border); background:var(--white);
            font-size:16px; font-weight:700; color:var(--deep); cursor:pointer;
            display:flex; align-items:center; justify-content:center;
            transition:all .15s; line-height:1; font-family:monospace;
        }
        .zoom-btn:hover { background:var(--saffron); color:#fff; border-color:var(--saffron); }
        .modal-hint { font-size:12px; color:var(--muted); margin-bottom:14px; text-align:center; }
        .modal-actions { display:flex; gap:10px; }
        .btn-crop {
            flex:1; padding:12px; background:var(--saffron); color:#fff;
            border:none; border-radius:10px; font-size:14px; font-weight:600;
            cursor:pointer; font-family:'Poppins',sans-serif; transition:background .2s;
        }
        .btn-crop:hover { background:#E55F00; }
        .btn-cancel {
            flex:0 0 auto; padding:12px 18px;
            background:var(--cream); color:var(--muted);
            border:1.5px solid var(--border); border-radius:10px;
            font-size:14px; cursor:pointer; font-family:'Poppins',sans-serif;
        }
    </style>

    <form action="{{ route('event.store') }}" method="POST" id="regForm" novalidate>
        @csrf
        <input type="hidden" name="cropped_photo" id="cropped_photo_input">

        <div class="card">
            <div class="section-title">📍 Your Information</div>
            <div class="section-sub">Fill all details and upload your photo</div>

            {{-- City --}}
            <div class="form-group">
                <label>Select City <span style="color:red">*</span></label>
                <select name="city" id="city" required>
                    <option value="">-- Select City --</option>
                    @foreach($cities as $city => $info)
                        <option value="{{ $city }}" {{ old('city') == $city ? 'selected' : '' }}>
                            {{ $city }}
                        </option>
                    @endforeach
                </select>
                <div class="field-error" id="city-error">Please select a city to continue.</div>
            </div>

            {{-- Gender --}}
            <div class="form-group">
                <label>Gender <span style="color:red">*</span></label>
                <div class="gender-group">
                    <div class="gender-option">
                        <input type="radio" name="gender" id="gender-male" value="male"
                            {{ old('gender', 'male') === 'male' ? 'checked' : '' }}>
                        <label class="gender-label" for="gender-male">
                             Male
                        </label>
                    </div>
                    <div class="gender-option">
                        <input type="radio" name="gender" id="gender-female" value="female"
                            {{ old('gender') === 'female' ? 'checked' : '' }}>
                        <label class="gender-label" for="gender-female">
                             Female
                        </label>
                    </div>
                </div>
                <div class="gender-error" id="gender-error">Please select your gender.</div>
            </div>

            {{-- Name --}}
            <div class="form-group">
                <label>Full Name <span style="color:red">*</span></label>
                <input type="text" name="full_name" id="full_name"
                       value="{{ old('full_name') }}" placeholder="e.g. Rahul Sharma">
                <div class="field-error" id="name-error">Please enter your full name.</div>
            </div>

            <hr class="divider">

            {{-- Photo --}}
            <div class="section-title">🖼️ Upload Your Photo</div>
            <div class="section-sub">Upload a clear face photo – zoom & drag to position</div>

            <div class="photo-upload-area" id="upload-area">
                <input type="file" id="photo-input" accept="image/*">
                <div class="upload-icon">📷</div>
                <div class="upload-text">
                    <b>Click to upload</b> or drag &amp; drop<br>
                    <span>JPG, PNG – max 5 MB</span>
                </div>
            </div>
            <div class="photo-error" id="photo-error">
                Please upload and crop your photo before continuing.
            </div>

            <div class="photo-preview-wrap" id="preview-wrap">
                <img id="photo-preview" src="" alt="Cropped preview">
                <div class="preview-info">
                    <b>Photo ready ✓</b>
                    <span>Square crop applied</span><br>
                    <button type="button" class="btn-reselect" onclick="resetPhoto()">
                        🔄 Change photo
                    </button>
                </div>
            </div>

            <button type="button" class="btn-submit" id="generate-btn"
                    style="margin-top:28px;" onclick="validateAndSubmit()">
                🎟️ Generate My Banner
            </button>
        </div>
    </form>

    {{-- ── Crop Modal ── --}}
    <div class="modal-overlay" id="crop-modal">
        <div class="modal-box">
            <div class="modal-title">✂️ Crop Your Photo</div>

            <div id="crop-container">
                <canvas id="crop-canvas"></canvas>
                <div id="crop-guide"></div>
            </div>

            <div class="crop-toolbar">
                <label>Zoom</label>
                <div class="zoom-btns">
                    <button type="button" class="zoom-btn" onclick="adjustZoom(-0.15)">−</button>
                    <button type="button" class="zoom-btn" onclick="adjustZoom(+0.15)">+</button>
                </div>
                <input type="range" class="zoom-slider" id="zoom-slider"
                       min="0" max="1" step="0.01" value="0"
                       oninput="onZoomSlider(this.value)">
                <span class="zoom-value" id="zoom-value">1×</span>
            </div>

            <div class="modal-hint">🖱 Scroll to zoom · Drag to reposition face in the square</div>

            <div class="modal-actions">
                <button type="button" class="btn-cancel" onclick="closeCropModal()">Cancel</button>
                <button type="button" class="btn-crop" onclick="applyCrop()">✓ Apply Crop</button>
            </div>
        </div>
    </div>

    <div class="toast" id="toast"></div>

    <script>
        // ── Validate & Submit ──────────────────────────────────────────────────────────
        function validateAndSubmit() {
            let ok = true;

            const city = document.getElementById('city').value;
            if (!city) {
                document.getElementById('city-error').classList.add('show');
                document.getElementById('city').classList.add('error');
                if (ok) showToast('Please select a city.');
                ok = false;
            } else {
                document.getElementById('city-error').classList.remove('show');
                document.getElementById('city').classList.remove('error');
            }

            const gender = document.querySelector('input[name="gender"]:checked');
            if (!gender) {
                document.getElementById('gender-error').classList.add('show');
                if (ok) showToast('Please select your gender.');
                ok = false;
            } else {
                document.getElementById('gender-error').classList.remove('show');
            }

            const name = document.getElementById('full_name').value.trim();
            if (!name) {
                document.getElementById('name-error').classList.add('show');
                document.getElementById('full_name').classList.add('error');
                if (ok) showToast('Please enter your full name.');
                ok = false;
            } else {
                document.getElementById('name-error').classList.remove('show');
                document.getElementById('full_name').classList.remove('error');
            }

            if (!document.getElementById('cropped_photo_input').value) {
                document.getElementById('photo-error').classList.add('show');
                if (ok) showToast('Please upload and crop your photo.');
                ok = false;
            } else {
                document.getElementById('photo-error').classList.remove('show');
            }

            if (!ok) return;

            const btn = document.getElementById('generate-btn');
            btn.disabled = true;
            btn.innerHTML = '⏳ Generating your banner...';
            document.getElementById('regForm').submit();
        }

        // ── Input clear errors ─────────────────────────────────────────────────────────
        document.getElementById('city').addEventListener('change', function() {
            document.getElementById('city-error').classList.remove('show');
            this.classList.remove('error');
        });
        document.getElementById('full_name').addEventListener('input', function() {
            document.getElementById('name-error').classList.remove('show');
            this.classList.remove('error');
        });
        document.querySelectorAll('input[name="gender"]').forEach(r => {
            r.addEventListener('change', () => {
                document.getElementById('gender-error').classList.remove('show');
            });
        });

        // ── Toast ──────────────────────────────────────────────────────────────────────
        let _tt;
        function showToast(msg) {
            const t = document.getElementById('toast');
            t.textContent = msg; t.classList.add('show');
            clearTimeout(_tt); _tt = setTimeout(() => t.classList.remove('show'), 3200);
        }

        // ── Crop Engine ───────────────────────────────────────────────────────────────
        let originalImage = null;
        let zoom = 1, minZoom = 1, maxZoom = 4;
        let panX = 0, panY = 0;
        let isDrag = false, dragSX = 0, dragSY = 0, panSX = 0, panSY = 0;
        let lastPinch = null, cSize = 0, gSize = 0;
        const GRATIO = 0.74;

        const cCont   = document.getElementById('crop-container');
        const cCanvas = document.getElementById('crop-canvas');
        const cGuide  = document.getElementById('crop-guide');
        const zSlider = document.getElementById('zoom-slider');
        const zVal    = document.getElementById('zoom-value');

        function openCropModal() {
            document.getElementById('crop-modal').classList.add('open');
            requestAnimationFrame(() => {
                cSize = cCont.clientWidth;
                gSize = Math.round(cSize * GRATIO);
                const gl = (cSize - gSize) / 2;
                cGuide.style.cssText = `width:${gSize}px;height:${gSize}px;left:${gl}px;top:${gl}px`;
                cCanvas.width  = originalImage.width;
                cCanvas.height = originalImage.height;
                cCanvas.getContext('2d').drawImage(originalImage, 0, 0);
                const shorter = Math.min(originalImage.width, originalImage.height);
                minZoom = gSize / shorter;
                maxZoom = minZoom * 5;
                zoom = minZoom;
                panX = (cSize - originalImage.width  * zoom) / 2;
                panY = (cSize - originalImage.height * zoom) / 2;
                clamp(); draw(); syncSlider();
            });
        }

        function draw() {
            const w = originalImage.width  * zoom;
            const h = originalImage.height * zoom;
            cCanvas.style.cssText = `width:${w}px;height:${h}px;left:${panX}px;top:${panY}px`;
        }

        function clamp() {
            const gl = (cSize - gSize) / 2;
            const gr = gl + gSize;
            const w  = originalImage.width  * zoom;
            const h  = originalImage.height * zoom;
            if (panX + w < gr) panX = gr - w;
            if (panX > gl)     panX = gl;
            if (panY + h < gr) panY = gr - h;
            if (panY > gl)     panY = gl;
        }

        function setZoom(nz, ox, oy) {
            nz = Math.min(maxZoom, Math.max(minZoom, nz));
            ox = ox ?? cSize / 2; oy = oy ?? cSize / 2;
            const fx = (ox - panX) / (originalImage.width  * zoom);
            const fy = (oy - panY) / (originalImage.height * zoom);
            zoom = nz;
            panX = ox - fx * originalImage.width  * zoom;
            panY = oy - fy * originalImage.height * zoom;
            clamp(); draw(); syncSlider();
        }
        function adjustZoom(d) { setZoom(zoom + d * (maxZoom - minZoom) / 4); }
        function onZoomSlider(v) { setZoom(minZoom + parseFloat(v) * (maxZoom - minZoom)); }
        function syncSlider() {
            const pct = ((zoom - minZoom) / (maxZoom - minZoom)) * 100;
            zSlider.value = (zoom - minZoom) / (maxZoom - minZoom);
            zSlider.style.background = `linear-gradient(to right,var(--saffron) ${pct}%,var(--border) ${pct}%)`;
            zVal.textContent = zoom.toFixed(1) + '×';
        }

        cCont.addEventListener('wheel', function(e) {
            e.preventDefault();
            const r = cCont.getBoundingClientRect();
            const d = e.deltaY > 0 ? -0.06 : 0.06;
            setZoom(zoom + d * (maxZoom - minZoom) / 4, e.clientX - r.left, e.clientY - r.top);
        }, { passive: false });

        cCont.addEventListener('mousedown', e => {
            e.preventDefault();
            isDrag = true; dragSX = e.clientX; dragSY = e.clientY;
            panSX = panX; panSY = panY;
            cCont.classList.add('dragging');
        });
        document.addEventListener('mousemove', e => {
            if (!isDrag) return;
            panX = panSX + (e.clientX - dragSX);
            panY = panSY + (e.clientY - dragSY);
            clamp(); draw();
        });
        document.addEventListener('mouseup', () => { isDrag = false; cCont.classList.remove('dragging'); });

        cCont.addEventListener('touchstart', e => {
            if (e.touches.length === 1) {
                isDrag = true;
                dragSX = e.touches[0].clientX; dragSY = e.touches[0].clientY;
                panSX = panX; panSY = panY; lastPinch = null;
            } else if (e.touches.length === 2) {
                isDrag = false; lastPinch = pinchDist(e);
            }
        }, { passive: true });
        cCont.addEventListener('touchmove', e => {
            if (e.touches.length === 1 && isDrag) {
                panX = panSX + (e.touches[0].clientX - dragSX);
                panY = panSY + (e.touches[0].clientY - dragSY);
                clamp(); draw();
            } else if (e.touches.length === 2 && lastPinch) {
                const nd = pinchDist(e);
                const r  = cCont.getBoundingClientRect();
                const mx = ((e.touches[0].clientX + e.touches[1].clientX) / 2) - r.left;
                const my = ((e.touches[0].clientY + e.touches[1].clientY) / 2) - r.top;
                setZoom(zoom * (nd / lastPinch), mx, my);
                lastPinch = nd;
            }
        }, { passive: true });
        cCont.addEventListener('touchend', () => { isDrag = false; lastPinch = null; });
        function pinchDist(e) {
            return Math.hypot(
                e.touches[0].clientX - e.touches[1].clientX,
                e.touches[0].clientY - e.touches[1].clientY
            );
        }

        window.addEventListener('resize', () => {
            if (!document.getElementById('crop-modal').classList.contains('open')) return;
            cSize = cCont.clientWidth;
            gSize = Math.round(cSize * GRATIO);
            const gl = (cSize - gSize) / 2;
            cGuide.style.cssText = `width:${gSize}px;height:${gSize}px;left:${gl}px;top:${gl}px`;
            clamp(); draw();
        });

        function closeCropModal() {
            document.getElementById('crop-modal').classList.remove('open');
        }

        function applyCrop() {
            if (!originalImage) return;
            const gl   = (cSize - gSize) / 2;
            const imgX = (gl   - panX) / zoom;
            const imgY = (gl   - panY) / zoom;
            const imgS = gSize         / zoom;
            const OUT  = 400;
            const out  = document.createElement('canvas');
            out.width  = out.height = OUT;
            out.getContext('2d').drawImage(originalImage, imgX, imgY, imgS, imgS, 0, 0, OUT, OUT);
            const dataUrl = out.toDataURL('image/jpeg', 0.92);
            document.getElementById('cropped_photo_input').value = dataUrl;
            document.getElementById('photo-preview').src         = dataUrl;
            document.getElementById('preview-wrap').classList.add('show');
            document.getElementById('upload-area').style.display = 'none';
            document.getElementById('photo-error').classList.remove('show');
            closeCropModal();
        }

        document.getElementById('photo-input').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;
            if (file.size > 5 * 1024 * 1024) { showToast('File too large. Max 5 MB.'); return; }
            const reader = new FileReader();
            reader.onload = ev => {
                const img = new Image();
                img.onload = () => { originalImage = img; openCropModal(); };
                img.src = ev.target.result;
            };
            reader.readAsDataURL(file);
        });

        function resetPhoto() {
            document.getElementById('cropped_photo_input').value = '';
            document.getElementById('preview-wrap').classList.remove('show');
            document.getElementById('upload-area').style.display = 'block';
            document.getElementById('photo-input').value = '';
            originalImage = null; zoom = 1; panX = 0; panY = 0;
        }
    </script>

@endsection
