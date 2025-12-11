@extends('layouts.app')

@section('styles')
    <style>
        /* Memastikan modal overlay menutupi layar dengan animasi fade */
        .modal-overlay {
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(2px);
            /* Efek blur di belakang */
            display: none;
            /* Default hidden */
            transition: opacity 0.3s ease;
        }

        /* Modifier agar Modal tepat di tengah (override margin: 15% auto dari app.css) */
        .modal-box.centered-alert {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            margin: 0;
            /* Reset margin dari app.css */
            width: 90%;
            max-width: 400px;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
            animation: popupScale 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            /* Efek membal */
        }

        /* Tipografi Judul Modal */
        .modal-title-alert {
            font-family: 'Instrument Sans', sans-serif;
            /* Sesuai font tema */
            font-size: 22px;
            font-weight: 700;
            color: #dc3545;
            /* Merah Error */
            margin-bottom: 10px;
        }

        /* Text Body Modal */
        .modal-body-text {
            color: var(--text-muted);
            font-size: 15px;
            line-height: 1.5;
            margin-bottom: 25px;
        }

        /* Input yang error kita beri border merah & background tipis */
        .input-error {
            border-bottom: 2px solid #dc3545 !important;
            background-color: #fff5f5 !important;
            transition: all 0.3s;
        }

        @keyframes popupScale {
            0% {
                transform: translate(-50%, -50%) scale(0.8);
                opacity: 0;
            }

            100% {
                transform: translate(-50%, -50%) scale(1);
                opacity: 1;
            }
        }

        .icon-centered {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 15px; /* Jarak ke judul */
        }

        /* Opsional: Membuat tombol tidak terlalu lebar agar lebih manis */
        .btn-modal-wide {
            width: 100%;
            max-width: 200px; /* Batasi lebar tombol */
        }
    </style>
@endsection

@section('content')
    <main id="main-content" class="main-content-section">
        {{-- Flash Messages (Menggunakan style global .custom-alert) --}}
        @if(session('info'))
            <div class="alert alert-info">{{ session('info') }}</div>
        @endif

        @if (session('success'))
            <div class="custom-alert success" role="alert">
                <div class="alert-content">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" class="alert-close-btn" onclick="this.parentElement.remove()">✕</button>
            </div>
        @endif

        @if (session('error'))
            <div class="custom-alert error" role="alert">
                <div class="alert-content">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
                <button type="button" class="alert-close-btn" onclick="this.parentElement.remove()">✕</button>
            </div>
        @endif

        {{-- DATE PICKER SECTION --}}
        <div class="date-picker">
            <button type="button" id="calendarTrigger" class="calendar-btn" aria-label="Pilih tanggal">
                {{-- SVG Calendar Icon --}}
                <svg width="45" height="45" viewBox="0 0 45 45" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M31.4067 6.675V3.75C31.4067 2.98125 30.7692 2.34375 30.0004 2.34375C29.2317 2.34375 28.5942 2.98125 28.5942 3.75V6.5625H16.4067V3.75C16.4067 2.98125 15.7692 2.34375 15.0004 2.34375C14.2317 2.34375 13.5942 2.98125 13.5942 3.75V6.675C8.53168 7.14375 6.07543 10.1625 5.70043 14.6437C5.66293 15.1875 6.11293 15.6375 6.63793 15.6375H38.3629C38.9067 15.6375 39.3567 15.1687 39.3004 14.6437C38.9254 10.1625 36.4692 7.14375 31.4067 6.675Z"
                        fill="#FFC107" />
                    <path
                        d="M37.5 18.45C38.5313 18.45 39.375 19.2937 39.375 20.325V31.875C39.375 37.5 36.5625 41.25 30 41.25H15C8.4375 41.25 5.625 37.5 5.625 31.875V20.325C5.625 19.2937 6.46875 18.45 7.5 18.45H37.5Z"
                        fill="#337354" />
                    <path
                        d="M15.9375 28.1249C15.45 28.1249 14.9625 27.9186 14.6063 27.5811C14.2688 27.2249 14.0625 26.7374 14.0625 26.2499C14.0625 25.7624 14.2688 25.2749 14.6063 24.9187C15.1313 24.3937 15.9563 24.2249 16.65 24.5249C16.8938 24.6186 17.1 24.7499 17.2687 24.9187C17.6062 25.2749 17.8125 25.7624 17.8125 26.2499C17.8125 26.7374 17.6062 27.2249 17.2687 27.5811C16.9125 27.9186 16.425 28.1249 15.9375 28.1249Z"
                        fill="#FFC107" />
                    <path
                        d="M22.5 28.1249C22.0125 28.1249 21.525 27.9186 21.1688 27.5811C20.8313 27.2249 20.625 26.7374 20.625 26.2499C20.625 25.7624 20.8313 25.2749 21.1688 24.9187C21.3375 24.7499 21.5437 24.6186 21.7875 24.5249C22.4812 24.2249 23.3062 24.3937 23.8312 24.9187C24.1687 25.2749 24.375 25.7624 24.375 26.2499C24.375 26.7374 24.1687 27.2249 23.8312 27.5811C23.7375 27.6561 23.6438 27.7311 23.55 27.8061C23.4375 27.8811 23.325 27.9374 23.2125 27.9749C23.1 28.0312 22.9875 28.0687 22.875 28.0874C22.7438 28.1062 22.6313 28.1249 22.5 28.1249Z"
                        fill="#FFC107" />
                    <path
                        d="M29.0625 28.125C28.575 28.125 28.0875 27.9188 27.7313 27.5813C27.3938 27.225 27.1875 26.7375 27.1875 26.25C27.1875 25.7625 27.3938 25.275 27.7313 24.9188C27.9188 24.75 28.1062 24.6187 28.35 24.525C28.6875 24.375 29.0625 24.3375 29.4375 24.4125C29.55 24.4313 29.6625 24.4687 29.775 24.525C29.8875 24.5625 30 24.6188 30.1125 24.6938C30.2063 24.7688 30.3 24.8438 30.3937 24.9188C30.7312 25.275 30.9375 25.7625 30.9375 26.25C30.9375 26.7375 30.7312 27.225 30.3937 27.5813C30.3 27.6563 30.2063 27.7312 30.1125 27.8062C30 27.8812 29.8875 27.9375 29.775 27.975C29.6625 28.0313 29.55 28.0688 29.4375 28.0875C29.3063 28.1063 29.175 28.125 29.0625 28.125Z"
                        fill="#FFC107" />
                    <path
                        d="M15.9375 34.6875C15.6938 34.6875 15.45 34.6313 15.225 34.5375C14.9812 34.4438 14.7938 34.3125 14.6063 34.1438C14.2688 33.7875 14.0625 33.3 14.0625 32.8125C14.0625 32.325 14.2688 31.8375 14.6063 31.4813C14.7938 31.3125 14.9812 31.1812 15.225 31.0875C15.5625 30.9375 15.9375 30.9 16.3125 30.975C16.425 30.9938 16.5375 31.0312 16.65 31.0875C16.7625 31.125 16.875 31.1813 16.9875 31.2563C17.0813 31.3313 17.175 31.4063 17.2687 31.4813C17.6062 31.8375 17.8125 32.325 17.8125 32.8125C17.8125 33.3 17.6062 33.7875 17.2687 34.1438C17.175 34.2188 17.0813 34.3125 16.9875 34.3687C16.875 34.4437 16.7625 34.5 16.65 34.5375C16.5375 34.5938 16.425 34.6313 16.3125 34.65C16.1813 34.6688 16.0688 34.6875 15.9375 34.6875Z"
                        fill="#FFC107" />
                    <path
                        d="M22.5 34.6875C22.0125 34.6875 21.525 34.4812 21.1688 34.1437C20.8313 33.7875 20.625 33.3 20.625 32.8125C20.625 32.325 20.8313 31.8375 21.1688 31.4813C21.8625 30.7875 23.1375 30.7875 23.8312 31.4813C24.1687 31.8375 24.375 32.325 24.375 32.8125C24.375 33.3 24.1687 33.7875 23.8312 34.1437C23.475 34.4812 22.9875 34.6875 22.5 34.6875Z"
                        fill="#FFC107" />
                    <path
                        d="M29.0625 34.6875C28.575 34.6875 28.0875 34.4812 27.7313 34.1437C27.3938 33.7875 27.1875 33.3 27.1875 32.8125C27.1875 32.325 27.3938 31.8375 27.7313 31.4813C28.425 30.7875 29.7 30.7875 30.3937 31.4813C30.7312 31.8375 30.9375 32.325 30.9375 32.8125C30.9375 33.3 30.7312 33.7875 30.3937 34.1437C30.0375 34.4812 29.55 34.6875 29.0625 34.6875Z"
                        fill="#FFC107" />
                </svg>
            </button>
            <span id="dateDisplay" class="date-text">--</span>

            <div id="calendarPopup" class="calendar-popup hidden" aria-hidden="true">
                <div class="cal-header">
                    <select id="monthSelect"></select>
                    <select id="yearSelect"></select>
                </div>
                <div class="weekday-row">
                    <span>Sun</span><span>Mon</span><span>Tue</span><span>Wed</span>
                    <span>Thu</span><span>Fri</span><span>Sat</span>
                </div>
                <div id="calendarGrid" class="calendar-grid"></div>
                <div class="cal-footer">
                    <button id="cancelBtn" class="cancel-btn">Cancel</button>
                    <button id="confirmBtn" class="confirm-btn">Confirm</button>
                </div>
            </div>
        </div>

        <div class="indicator-table-container">
            <h2 class="table-title">Penilaian Indikator Mutu di Ruang {{ $user->nama_ruangan }}</h2>
            <form id="indicatorForm" method="POST" action="{{ route('admin.input_indikator.store') }}">
                @csrf
                <input type="hidden" name="tanggal" value="{{ $tanggal }}">
                <div class="indicator-grid">
                    <div class="grid-header">No.</div>
                    <div class="grid-header">Variabel Penilaian</div>
                    <div class="grid-header">Jumlah Pasien/Kejadian yang <br>Memenuhi Indikator</div>
                    <div class="grid-header">Total Pasien/Kejadian</div>

                    @foreach($indikator as $i => $item)
                        <div class="grid-cell cell-no">{{ $i + 1 }}.</div>
                        <div class="grid-cell">{{ $item->variabel ?? $item->standar }}</div>
                        <div class="grid-cell">
                            <input type="number" name="pasien_sesuai[{{ $item->id_indikator }}]" class="input-plain"
                                placeholder="Isi jumlah"
                                value="{{ isset($mutu[$item->id_indikator]) ? $mutu[$item->id_indikator]->pasien_sesuai : '' }}">
                        </div>

                        <div class="grid-cell">
                            <input type="number" name="total_pasien[{{ $item->id_indikator }}]" class="input-plain"
                                placeholder="Isi total"
                                value="{{ isset($mutu[$item->id_indikator]) ? $mutu[$item->id_indikator]->total_pasien : '' }}">
                        </div>
                    @endforeach
                </div>
                <div style="text-align: right; margin-top: 24px;">
                    <button class="save-btn" type="submit">
                        <span>Simpan</span>
                    </button>
                </div>
            </form>
        </div>

        {{-- MODAL VALIDASI ERROR (Menggunakan struktur app.css + Custom style kita) --}}
        <div id="validationModal" class="modal-overlay">
            <div class="modal-box centered-alert">
                {{-- CONTAINER IKON: Flexbox Center --}}
                <div class="icon-centered">
                    {{-- Ukuran ikon disesuaikan sedikit (50px) agar proporsional --}}
                    <svg width="50" height="50" viewBox="0 0 24 24" fill="none" stroke="#dc3545" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                </div>

                <h3 class="modal-title-alert">Data Belum Lengkap</h3>
                <p class="modal-body-text">
                    Mohon maaf, semua indikator wajib diisi. <br>
                    Silakan lengkapi kolom yang berwarna merah.
                </p>

                <div class="modal-actions">
                    {{-- Tambahkan class btn-modal-wide jika ingin tombol tidak full width --}}
                    <button type="button" class="btn-primary btn-modal-wide" id="btnModalOk">Mengerti</button>
                </div>
            </div>
        </div>

    </main>
@endsection

@push('scripts')
    <script>
        // === Date Picker ===
        const months = [
            "Januari", "Februari", "Maret", "April", "Mei", "Juni",
            "Juli", "Agustus", "September", "Oktober", "November", "Desember"
        ];
        const calendarTrigger = document.getElementById("calendarTrigger");
        const calendarPopup = document.getElementById("calendarPopup");
        const dateDisplay = document.getElementById("dateDisplay");
        const calendarGrid = document.getElementById("calendarGrid");
        const monthSelect = document.getElementById("monthSelect");
        const yearSelect = document.getElementById("yearSelect");
        const cancelBtn = document.getElementById("cancelBtn");
        const confirmBtn = document.getElementById("confirmBtn");

        // Ambil tanggal dari URL atau gunakan tanggal hari ini
        const urlParams = new URLSearchParams(window.location.search);
        const dateFromUrl = urlParams.get('tanggal');
        let selectedDate = dateFromUrl ? new Date(dateFromUrl + 'T00:00:00') : new Date();


        // isi dropdown bulan & tahun
        months.forEach((m, i) => {
            let opt = document.createElement("option");
            opt.value = i;
            opt.textContent = m;
            monthSelect.appendChild(opt);
        });

        for (let y = 2020; y <= 2030; y++) {
            let opt = document.createElement("option");
            opt.value = y;
            opt.textContent = y;
            yearSelect.appendChild(opt);
        }

        function renderCalendar(date) {
            calendarGrid.innerHTML = "";
            monthSelect.value = date.getMonth();
            yearSelect.value = date.getFullYear();

            let firstDay = new Date(date.getFullYear(), date.getMonth(), 1).getDay();
            let daysInMonth = new Date(date.getFullYear(), date.getMonth() + 1, 0).getDate();

            for (let i = 0; i < firstDay; i++) {
                let empty = document.createElement("div");
                calendarGrid.appendChild(empty);
            }

            for (let d = 1; d <= daysInMonth; d++) {
                let btn = document.createElement("button");
                btn.classList.add("day-btn");
                btn.textContent = d;
                if (d === date.getDate()) btn.classList.add("selected");
                btn.addEventListener("click", () => {
                    selectedDate = new Date(date.getFullYear(), date.getMonth(), d);
                    renderCalendar(selectedDate);
                });
                calendarGrid.appendChild(btn);
            }
        }

        function formatDateForDisplay(date) {
            return `${months[date.getMonth()]} ${date.getDate()}, ${date.getFullYear()}`;
        }

        function formatDateForUrl(date) {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        }

        // event handler
        calendarTrigger.addEventListener("click", () => {
            calendarPopup.classList.toggle("hidden");
            renderCalendar(selectedDate);
        });

        monthSelect.addEventListener("change", () => {
            let day = selectedDate.getDate();
            const newDate = new Date(yearSelect.value, monthSelect.value, 1);
            const daysInNewMonth = new Date(newDate.getFullYear(), newDate.getMonth() + 1, 0).getDate();
            if (day > daysInNewMonth) day = daysInNewMonth;

            selectedDate = new Date(yearSelect.value, monthSelect.value, day);
            renderCalendar(selectedDate);
        });
        yearSelect.addEventListener("change", () => {
            let day = selectedDate.getDate();
            const newDate = new Date(yearSelect.value, monthSelect.value, 1);
            const daysInNewMonth = new Date(newDate.getFullYear(), newDate.getMonth() + 1, 0).getDate();
            if (day > daysInNewMonth) day = daysInNewMonth;

            selectedDate = new Date(yearSelect.value, monthSelect.value, day);
            renderCalendar(selectedDate);
        });

        cancelBtn.addEventListener("click", () => {
            calendarPopup.classList.add("hidden");
        });

        confirmBtn.addEventListener("click", () => {
            const formattedDate = formatDateForUrl(selectedDate);
            window.location.href = `{{ route('admin.input_indikator') }}?tanggal=${formattedDate}`;
        });

        // init
        dateDisplay.textContent = formatDateForDisplay(selectedDate);


        // === VALIDASI FORM & MODAL ===
        const indicatorForm = document.getElementById('indicatorForm');
        const validationModal = document.getElementById('validationModal');
        const btnModalOk = document.getElementById('btnModalOk');

        // Fungsi Tutup Modal
        function closeModal() {
            validationModal.style.display = 'none';
        }

        btnModalOk.addEventListener('click', closeModal);

        // Tutup modal jika klik di area gelap (overlay)
        validationModal.addEventListener('click', function (e) {
            if (e.target === validationModal) {
                closeModal();
            }
        });

        // Intercept Submit
        indicatorForm.addEventListener('submit', function (e) {
            let valid = true;
            let firstInvalidInput = null;

            // Reset style semua input dulu
            document.querySelectorAll('.indicator-grid input[type="number"]').forEach(input => {
                input.classList.remove('input-error');
            });

            // Cek validasi
            document.querySelectorAll('.indicator-grid input[type="number"]').forEach(function (input) {
                if (input.value === '' || input.value === null) {
                    valid = false;
                    input.classList.add('input-error'); // Tambah class error

                    if (!firstInvalidInput) firstInvalidInput = input;
                }
            });

            if (!valid) {
                e.preventDefault(); // Stop submit
                validationModal.style.display = 'block'; // Tampilkan Modal

                // Ketika tombol OK diklik, scroll ke input yang error
                btnModalOk.onclick = function () {
                    closeModal();
                    if (firstInvalidInput) {
                        firstInvalidInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        firstInvalidInput.focus();
                    }
                };
            }
        });
    </script>
@endpush