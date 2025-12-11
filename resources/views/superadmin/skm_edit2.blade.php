@extends('layouts.app')

@section('styles')
    <style>
    /* --- 1. NAVIGASI KHUSUS SKM (Tab Menu) --- */
        .survey-nav-container {
            background-color: rgba(214, 227, 221, 0.5);
            padding: 40px 0 0; /* Adjusted padding */
            text-align: center;
            margin-bottom: 40px;
        }

        .survey-title {
            font-family: 'Instrument Sans', sans-serif;
            font-weight: 700;
            font-size: 28px;
            color: var(--primary-color);
            margin: 0 0 30px 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .survey-tabs {
            display: flex;
            justify-content: center;
            gap: 10px;
            border-bottom: 1px solid #77a28d;
        }

        .tab-item {
            font-weight: 600;
            font-size: 16px;
            color: #888;
            text-decoration: none;
            padding: 12px 30px;
            border-bottom: 3px solid transparent;
            transition: all 0.3s;
            border-radius: 8px 8px 0 0;
        }

        .tab-item:hover {
            background-color: rgba(51, 115, 84, 0.05);
            color: var(--primary-color);
        }

        .tab-item.active {
            color: var(--primary-color);
            border-bottom-color: var(--primary-color);
            background-color: #fff; /* Tab aktif putih seolah menyatu dengan konten */
            font-weight: 700;
        }

        /* --- 2. EDITOR SURVEI CONTAINER --- */
        .survey-editor-container {
            max-width: 900px;
            margin: 0 auto;
            padding-bottom: 100px;
        }

        /* Header Control (Preview Button) */
        .editor-controls {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 20px;
        }

        /* --- 3. KARTU PERTANYAAN (QUESTION BLOCK) --- */
        .question-block {
            background: #fff;
            border: 1px solid #e0e0e0;
            border-left: 5px solid var(--primary-color); /* Aksen kiri */
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.03);
            transition: transform 0.2s, box-shadow 0.2s;
            position: relative;
        }

        .question-block:hover {
            box-shadow: 0 8px 20px rgba(0,0,0,0.06);
            border-color: #cbd5e0;
        }

        /* Header Pertanyaan (Judul & Tombol Aksi) */
        .question-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #f0f0f0;
        }

        .question-title-wrapper {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .question-number {
            background-color: var(--primary-color);
            color: #fff;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 14px;
        }

        .question-label {
            font-size: 18px;
            font-weight: 700;
            color: var(--text-dark);
            margin: 0;
        }

        /* Group Tombol Aksi (Geser, Hapus) */
        .action-icons {
            display: flex;
            gap: 8px;
        }

        .icon-btn {
            width: 34px;
            height: 34px;
            border-radius: 8px;
            border: 1px solid #ddd;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #555;
            background: #fff;
            cursor: pointer;
            transition: all 0.2s;
        }

        .icon-btn:hover {
            background-color: #f5f5f5;
            color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .icon-btn svg {
            width: 18px; height: 18px;
            stroke-width: 2;
        }

        .icon-btn.danger {
            color: #dc3545;
            border-color: #ffcccc;
            background-color: #fff5f5;
        }
        .icon-btn.danger:hover {
            background-color: #dc3545;
            color: #fff;
            border-color: #dc3545;
        }

        /* --- 4. FORM INPUT --- */
        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-weight: 600;
            font-size: 14px;
            color: var(--text-dark);
            margin-bottom: 8px;
        }

        .form-input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 15px;
            font-family: inherit;
            transition: border-color 0.2s;
            box-sizing: border-box; /* Penting agar padding tidak melebarkan input */
        }

        .form-input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(51, 115, 84, 0.1);
        }

        /* --- 5. PILIHAN JAWABAN --- */
        .answers-wrapper {
            background-color: #fafafa;
            padding: 15px;
            border-radius: 8px;
            border: 1px dashed #ccc;
        }

        .answer-row {
            display: flex;
            gap: 10px;
            align-items: center;
            margin-bottom: 10px;
        }

        .option-badge {
            width: 36px;
            height: 36px;
            background: #e0e0e0;
            color: #555;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            flex-shrink: 0;
        }

        .input-score {
            width: 80px;
            text-align: center;
        }

        /* --- 6. FOOTER ACTIONS --- */
        .bottom-actions {
            margin-top: 40px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 20px;
        }

        .btn-add-big {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #fff;
            border: 2px dashed var(--primary-color);
            color: var(--primary-color);
            padding: 12px 30px;
            border-radius: 30px;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.2s;
            width: 100%;
            justify-content: center;
            max-width: 400px;
        }

        .btn-add-big:hover {
            background: #f0f7f4;
            transform: translateY(-2px);
        }

        .btn-save-fixed {
            background-color: var(--secondary-color);
            color: #5f4c14; /* Teks kontras */
            padding: 14px 50px;
            border: none;
            border-radius: 12px;
            font-weight: 700;
            font-size: 18px;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(255, 187, 0, 0.3);
            transition: transform 0.2s;
        }
        .btn-save-fixed:hover {
            transform: scale(1.05);
            background-color: #e6a800;
        }
    </style>
@endsection

@section('content')
    {{-- Header Navigasi --}}
    @include('superadmin.partials.skm_nav')

    <form id="survey-form" method="POST" action="{{ route('superadmin.skm.update_pertanyaan') }}">
        @csrf @method('PUT')

        <main class="survey-editor-container">

            {{-- Notifikasi (Alert) --}}
            @if (session('success'))
                <div class="custom-alert success">
                    <div class="alert-content">
                        {{-- Icon Check --}}
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button type="button" class="alert-close-btn" onclick="this.parentElement.remove()">✕</button>
                </div>
            @endif

            {{-- Tombol Preview --}}
            <div class="editor-controls">
                <a href="{{ route('guest.survei-1') }}" target="_blank" class="btn-control btn-primary-action">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                    Lihat Preview Survei
                </a>
            </div>

            {{-- Container List Pertanyaan --}}
            <div class="survey-page-list">
                @forelse ($surveyData as $pertanyaan)
                    <div class="question-block">
                        {{-- Hidden ID untuk Update --}}
                        <input type="hidden" class="hidden-id-pertanyaan" value="{{ $pertanyaan->id_pertanyaan ?? '' }}">

                        {{-- HEADER KARTU --}}
                        <header class="question-header">
                            <div class="question-title-wrapper">
                                <div class="question-number">{{ $loop->iteration }}</div>
                                <h3 class="question-label">Pertanyaan {{ $loop->iteration }}</h3>
                            </div>

                            <div class="action-icons">
                                <button type="button" class="icon-btn btn-move-up" title="Geser Naik">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 19V5M5 12l7-7 7 7"/></svg>
                                </button>
                                <button type="button" class="icon-btn btn-move-down" title="Geser Turun">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 5v14M5 12l7 7 7-7"/></svg>
                                </button>
                                {{-- Tombol Insert/Sisip (Opsional, tapi tetap dipertahankan) --}}
                                <button type="button" class="icon-btn btn-add-insert" title="Sisipkan Pertanyaan Disini">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                                </button>
                                <button type="button" class="icon-btn danger btn-delete-question" title="Hapus Pertanyaan">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                </button>
                            </div>
                        </header>

                        {{-- BODY EDITOR --}}
                        <div class="question-editor">
                            {{-- Baris 1: Teks Pertanyaan --}}
                            <div class="form-group">
                                <label class="form-label">Isi Pertanyaan</label>
                                <input type="text" class="form-input question-text-input" 
                                       value="{{ $pertanyaan->pertanyaan ?? '' }}" 
                                       placeholder="Tulis pertanyaan survei disini...">
                            </div>

                            {{-- Hidden Tipe (Default Pilihan Ganda kecuali diubah logic lain) --}}
                            <input type="hidden" class="hidden-tipe-input" value="{{ $pertanyaan->tipe_pertanyaan ?? 'Pilihan Ganda' }}">

                            {{-- Baris 2: Pilihan Jawaban --}}
                            @if($pertanyaan->tipe_pertanyaan != 'Isian Teks')
                                <div class="form-group">
                                    <label class="form-label">Pilihan Jawaban & Bobot Nilai</label>
                                    <div class="answers-wrapper">
                                        <div class="answer-options-list">
                                            @foreach ($pertanyaan->pilihan as $pilihan)
                                                <div class="answer-row">
                                                    <input type="hidden" class="hidden-id-pilihan" value="{{ $pilihan->id_pilihan ?? '' }}">

                                                    <div class="option-badge">{{ chr(65 + $loop->index) }}</div>

                                                    <input type="text" class="form-input input-text" style="flex-grow:1;" 
                                                           value="{{ $pilihan->pilihan ?? '' }}" placeholder="Teks Jawaban">

                                                    <input type="number" class="form-input input-score" 
                                                           placeholder="Nilai" value="{{ $pilihan->nilai ?? '' }}" title="Bobot Nilai SKM">

                                                    <button type="button" class="icon-btn btn-add-answer" title="Tambah Opsi">
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                                                    </button>
                                                    <button type="button" class="icon-btn danger btn-delete-answer" title="Hapus Opsi">
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                                    </button>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    {{-- State Kosong --}}
                    <div class="question-block empty-state" style="text-align:center; color:#777;">
                        <p>Belum ada pertanyaan. Klik tombol di bawah untuk membuat baru.</p>
                    </div>
                @endforelse
            </div>

            {{-- Action Bawah --}}
            <div class="bottom-actions">
                {{-- Tombol Tambah Besar --}}
                <button type="button" id="btn-add-new-question-bottom" class="btn-add-big">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    Tambah Pertanyaan Baru
                </button>

                <button type="submit" class="btn-save-fixed">SIMPAN PERUBAHAN</button>
            </div>
        </main>
    </form>

    {{-- TEMPLATE HIDDEN (Untuk Kloning Pertanyaan Baru) --}}
    <div id="question-template" style="display: none;">
        <div class="question-block">
            <input type="hidden" class="hidden-id-pertanyaan" value="">
            <header class="question-header">
                <div class="question-title-wrapper">
                    <div class="question-number">#</div>
                    <h3 class="question-label">Pertanyaan Baru</h3>
                </div>
                <div class="action-icons">
                    <button type="button" class="icon-btn btn-move-up"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 19V5M5 12l7-7 7 7"/></svg></button>
                    <button type="button" class="icon-btn btn-move-down"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 5v14M5 12l7 7 7-7"/></svg></button>
                    <button type="button" class="icon-btn btn-add-insert"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg></button>
                    <button type="button" class="icon-btn danger btn-delete-question"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg></button>
                </div>
            </header>
            <div class="question-editor">
                <div class="form-group">
                    <label class="form-label">Isi Pertanyaan</label>
                    <input type="text" class="form-input question-text-input" placeholder="Tulis pertanyaan survei disini...">
                </div>
                <input type="hidden" class="hidden-tipe-input" value="Pilihan Ganda">
                <div class="form-group">
                    <label class="form-label">Pilihan Jawaban & Bobot Nilai</label>
                    <div class="answers-wrapper">
                        <div class="answer-options-list">
                            <div class="answer-row">
                                <input type="hidden" class="hidden-id-pilihan" value="">
                                <div class="option-badge">A</div>
                                <input type="text" class="form-input input-text" style="flex-grow:1;" placeholder="Teks Jawaban">
                                <input type="number" class="form-input input-score" placeholder="Nilai">
                                <button type="button" class="icon-btn btn-add-answer"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg></button>
                                <button type="button" class="icon-btn danger btn-delete-answer"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL KONFIRMASI HAPUS (Dengan Ikon Warning) --}}
    <div id="customConfirmModal" class="modal-overlay">
        <div class="modal-box centered-alert">
            <div class="icon-centered">
                {{-- Ikon Sampah Merah --}}
                <svg width="50" height="50" viewBox="0 0 24 24" fill="none" stroke="#dc3545" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="3 6 5 6 21 6"></polyline>
                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                    <line x1="10" y1="11" x2="10" y2="17"></line>
                    <line x1="14" y1="11" x2="14" y2="17"></line>
                </svg>
            </div>
            <h3 class="modal-title-alert">Hapus Pertanyaan?</h3>
            <p class="modal-body-text">
                Apakah Anda yakin ingin menghapus pertanyaan ini?<br>
                <small style="color: #dc3545;">Data yang dihapus tidak bisa dikembalikan.</small>
            </p>
            <div class="modal-actions">
                <button type="button" class="btn-modal secondary" id="closeModalBtn">Batal</button>
                <button type="button" class="btn-modal danger" id="confirmDeleteBtn">Ya, Hapus</button>
            </div>
        </div>
    </div>

    {{-- MODAL ALERT (Untuk Error/Info - Dengan Ikon Tanda Seru) --}}
    <div id="customAlertModal" class="modal-overlay">
        <div class="modal-box centered-alert">
            <div class="icon-centered">
                {{-- Ikon Tanda Seru Merah --}}
                <svg width="50" height="50" viewBox="0 0 24 24" fill="none" stroke="#dc3545" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
            </div>
            <h3 class="modal-title-alert" id="alertTitle">Perhatian</h3>
            <p class="modal-body-text" id="alertMessage">...</p>
            <div class="modal-actions">
                <button type="button" class="btn-modal primary" id="btnAlertOk">Mengerti</button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // --- VARIABLES ---
            const pageList = document.querySelector('.survey-page-list');
            const templateHTML = document.getElementById('question-template').innerHTML; // Ambil HTML template
            const confirmModal = document.getElementById('customConfirmModal');
            const alertModal = document.getElementById('customAlertModal');
            let deleteCallback = null;

            // --- 1. MODAL HANDLING ---
            function openConfirmModal(callback) {
                deleteCallback = callback;
                confirmModal.style.display = 'block'; // Fallback style
                confirmModal.classList.add('active'); // CSS class style
            }

            function closeConfirmModal() {
                deleteCallback = null;
                confirmModal.style.display = 'none';
                confirmModal.classList.remove('active');
            }

            document.getElementById('closeModalBtn').addEventListener('click', closeConfirmModal);

            document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
                if (deleteCallback) deleteCallback();
                closeConfirmModal();
            });

            // --- Alert Modal ---
            function showAlert(title, msg) {
                document.getElementById('alertTitle').textContent = title;
                document.getElementById('alertMessage').textContent = msg;
                alertModal.style.display = 'block';
            }
            document.getElementById('btnAlertOk').addEventListener('click', () => {
                alertModal.style.display = 'none';
            });

            // --- 2. RENUMBERING (Penting agar UI rapi) ---
            function renumberQuestions() {
                const blocks = pageList.querySelectorAll('.question-block:not(.empty-state)');
                blocks.forEach((block, index) => {
                    block.querySelector('.question-number').textContent = index + 1;
                    block.querySelector('.question-label').textContent = `Pertanyaan ${index + 1}`;
                });
                // Hapus "empty state" jika ada pertanyaan
                const emptyState = pageList.querySelector('.empty-state');
                if(blocks.length > 0 && emptyState) emptyState.remove();
            }

            function renumberOptions(list) {
                const badges = list.querySelectorAll('.option-badge');
                badges.forEach((badge, index) => {
                    badge.textContent = String.fromCharCode(65 + index); // A, B, C...
                });
            }

            // --- 3. ADDING QUESTIONS ---
            function createNewQuestion() {
                // Buat elemen div wrapper sementara
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = templateHTML;
                const newBlock = tempDiv.firstElementChild;
                return newBlock;
            }

            // Tombol Tambah Besar (Di Bawah)
            document.getElementById('btn-add-new-question-bottom').addEventListener('click', function() {
                const newBlock = createNewQuestion();
                pageList.appendChild(newBlock);
                newBlock.scrollIntoView({ behavior: 'smooth', block: 'center' });
                renumberQuestions();
            });

            // --- 4. CLICK EVENTS DELEGATION (Main Logic) ---
            pageList.addEventListener('click', function(e) {
                const target = e.target;

                // A. INSERT / DUPLICATE QUESTION (Tombol di Header)
                if(target.closest('.btn-add-insert')) {
                    const currentBlock = target.closest('.question-block');
                    const newBlock = createNewQuestion();
                    currentBlock.after(newBlock);
                    renumberQuestions();
                }

                // B. MOVE UP
                else if(target.closest('.btn-move-up')) {
                    const currentBlock = target.closest('.question-block');
                    const prevBlock = currentBlock.previousElementSibling;
                    if(prevBlock && prevBlock.classList.contains('question-block')) {
                        currentBlock.parentNode.insertBefore(currentBlock, prevBlock);
                        renumberQuestions();
                    }
                }

                // C. MOVE DOWN
                else if(target.closest('.btn-move-down')) {
                    const currentBlock = target.closest('.question-block');
                    const nextBlock = currentBlock.nextElementSibling;
                    if(nextBlock && nextBlock.classList.contains('question-block')) {
                        nextBlock.after(currentBlock);
                        renumberQuestions();
                    }
                }

                // D. DELETE QUESTION
                else if(target.closest('.btn-delete-question')) {
                    const currentBlock = target.closest('.question-block');
                    const idInput = currentBlock.querySelector('.hidden-id-pertanyaan');
                    const id = idInput ? idInput.value : null;

                    const doDelete = () => {
                        // Jika ID kosong (pertanyaan baru belum di-save ke DB), langsung hapus DOM
                        if(!id) {
                            currentBlock.remove();
                            renumberQuestions();
                            return;
                        }

                        // Jika ID ada, panggil AJAX
                        const csrfToken = document.querySelector('input[name="_token"]').value;

                        fetch(`/superadmin/skm/pertanyaan/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            }
                        })
                        .then(res => res.json())
                        .then(data => {
                            if(data.status === 'success') {
                                currentBlock.remove();
                                renumberQuestions();
                            } else {
                                showAlert('Gagal', data.message || 'Gagal menghapus pertanyaan.');
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            showAlert('Error', 'Terjadi kesalahan server.');
                        });
                    };

                    // Cek jumlah pertanyaan (Opsional: Cegah hapus semua)
                    // if(pageList.querySelectorAll('.question-block').length <= 1) {
                    //     showAlert('Perhatian', 'Minimal harus menyisakan satu pertanyaan.');
                    //     return;
                    // }

                    openConfirmModal(doDelete);
                }

                // E. ADD ANSWER OPTION
                else if(target.closest('.btn-add-answer')) {
                    const currentBlock = target.closest('.answer-row');
                    const list = currentBlock.parentElement;

                    // Clone row
                    const newRow = currentBlock.cloneNode(true);
                    // Bersihkan value
                    newRow.querySelector('.input-text').value = '';
                    newRow.querySelector('.input-score').value = '';
                    newRow.querySelector('.hidden-id-pilihan').value = ''; // Reset ID Pilihan

                    currentBlock.after(newRow);
                    renumberOptions(list);
                }

                // F. DELETE ANSWER OPTION
                else if (target.closest('.btn-delete-answer')) {
                    const currentBlock = target.closest('.answer-row');
                    const list = currentBlock.parentElement;

                    // Cek jumlah jawaban
                    if (list.querySelectorAll('.answer-row').length > 1) {
                        currentBlock.remove();
                        renumberOptions(list);
                    } else {
                        showAlert('Tidak Bisa Dihapus', 'Minimal satu pilihan jawaban harus tersedia.');
                    }
                }
            });

            // --- 5. PREPARE FORM BEFORE SUBMIT ---
            document.getElementById('survey-form').addEventListener('submit', function(e) {
                // Kita harus memberi "name" attribute yang urut (questions[0], questions[1]...)
                // agar Laravel bisa membacanya sebagai array.

                const blocks = pageList.querySelectorAll('.question-block:not(.empty-state)');

                blocks.forEach((block, qIndex) => {
                    // 1. ID Pertanyaan
                    const inputId = block.querySelector('.hidden-id-pertanyaan');
                    inputId.name = `questions[${qIndex}][id_pertanyaan]`;

                    // 2. Teks Pertanyaan
                    const inputText = block.querySelector('.question-text-input');
                    inputText.name = `questions[${qIndex}][pertanyaan]`;

                    // 3. Tipe Pertanyaan
                    const inputTipe = block.querySelector('.hidden-tipe-input');
                    inputTipe.name = `questions[${qIndex}][tipe]`;

                    // 4. Pilihan Jawaban
                    const opts = block.querySelectorAll('.answer-row');
                    opts.forEach((opt, oIndex) => {
                        const idOpt = opt.querySelector('.hidden-id-pilihan');
                        const textOpt = opt.querySelector('.input-text');
                        const scoreOpt = opt.querySelector('.input-score');

                        idOpt.name = `questions[${qIndex}][pilihan][${oIndex}][id_pilihan]`;
                        textOpt.name = `questions[${qIndex}][pilihan][${oIndex}][pilihan]`;
                        scoreOpt.name = `questions[${qIndex}][pilihan][${oIndex}][nilai]`;
                    });
                });
            });
        });
    </script>
@endpush