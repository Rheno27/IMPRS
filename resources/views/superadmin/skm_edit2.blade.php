@extends('layouts.app')

@section('styles')
    <style>
        :root {
            --primary-color: #337354;
            --secondary-color: #77a28d;
            --background-color: #fcfcfc;
            --text-dark: #2d2d2d;
            --text-light: #717680;
            --text-white: #ffffff;
            --border-color: rgba(51, 115, 84, 0.5);
            --border-light: rgba(119, 162, 141, 0.5);
            --accent-green-1: #99ce55;
            --accent-green-2: #2a7f54;
            --accent-bg: rgba(214, 227, 221, 0.5);
            --option-bg: #d6e3dd;
            --border-color-semitransparent: rgba(51, 115, 84, 0.5);
            --border-medium: #77a28d;
            --bg-accent: rgba(214, 227, 221, 0.5);
        }

        /* Reset */
        body {
            margin: 0;
            padding: 0;
            font-family: 'Roboto', sans-serif;
            background-color: var(--background-color);
            color: var(--text-dark);
        }

        * {
            box-sizing: border-box;
        }

        .page-container {
            max-width: 1440px;
            margin: 0 auto;
            background-color: var(--background-color);
            overflow: hidden;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        button {
            cursor: pointer;
            border: none;
            background: none;
            padding: 0;
            font-family: inherit;
        }

        .icon {
            width: 36px;
            height: 36px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .icon img {
            max-width: 100%;
            max-height: 100%;
        }

        /* Header */
        .site-header {
            display: flex;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1000;
            justify-content: space-between;
            align-items: center;
            background-color: var(--text-white);
            border-bottom: 1px solid var(--border-color-semitransparent);
            padding: 0 35px;
            height: 70px;
            box-sizing: border-box;
        }

        main {
            margin-top: 65px;
        }

        /* Logo */
        .logo-container {
            display: flex;
            align-items: center;
        }

        .logo-image {
            height: 60px;
            width: auto;
            object-fit: contain;
        }

        /* User info */
        .user-info {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .user-avatar {
            height: 26px;
            width: 26px;
            border-radius: 50%;
            object-fit: cover;
        }

        .user-name {
            font-weight: 600;
            font-size: 16px;
            color: var(--primary-color);
            white-space: nowrap;
        }

        .logout-link {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 26px;
            width: 26px;
            border-radius: 6px;
            transition: background 0.2s;
        }

        .logout-link:hover {
            background: rgba(51, 115, 84, 0.1);
        }

        .logout-icon {
            height: 24px;
            width: 24px;
        }

        /* Survey Navigation */
        .survey-nav-container {
            background-color: var(--bg-accent);
            padding: 100px 0 0;
            text-align: center;
        }

        .survey-title {
            font-family: 'Roboto', sans-serif;
            font-weight: 700;
            font-size: 28px;
            line-height: 44px;
            color: var(--primary-color);
            margin: 0 0 30px 0;
        }

        /* Tabs */
        .survey-tabs {
            display: flex;
            justify-content: center;
            border-bottom: 1px solid var(--border-medium);
        }

        .tab-item {
            font-family: 'Roboto', sans-serif;
            font-weight: 600;
            font-size: 16px;
            line-height: 24px;
            color: #aaa;
            text-decoration: none;
            padding: 8px 16px;
            text-align: center;
            min-width: 32%;
            border-bottom: 3px solid transparent;
            margin-bottom: -1px;
            transition: all 0.2s ease-in-out;
        }

        .tab-item:hover {
            color: var(--primary-color);
        }

        .tab-item.active {
            color: var(--primary-color);
            border-bottom-color: var(--primary-color);
            font-weight: 700;
        }

        /* Survey Editor */
        .survey-editor-container {
            padding: 0 48px;
        }

        .view-survey-wrapper {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 24px;
        }

        .view-survey-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background-color: var(--primary-color);
            color: var(--text-white);
            padding: 6px 10px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 500;
        }

        .view-survey-btn:hover {
            background-color: #2b6147;
            transform: translateY(-1px);
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.12);
        }

        /* Survey Pages */
        .survey-page-list {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .survey-page {
            border: 1px solid var(--secondary-color);
            border-radius: 16px;
            padding: 16px 24px;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .page-header,
        .question-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px;
            border-bottom: 1px solid var(--border-light);
        }

        .question-header {
            padding: 16px 12px;
        }

        .page-title {
            font-size: 24px;
            font-weight: 600;
            color: var(--primary-color);
            margin: 0;
        }

        .page-title-group {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .question-title-group {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .question-title {
            font-size: 20px;
            font-weight: 600;
            color: var(--primary-color);
            margin: 0;
        }

        /* Action Icons */
        .action-icons {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .icon-btn {
            border: 1px solid var(--primary-color);
            border-radius: 50%;
        }

        /* Question List */
        .question-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .question-block {
            border: 1px solid var(--secondary-color);
            border-radius: 16px;
            padding: 16px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .question-editor {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        /* Form Rows */
        .form-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid var(--border-light);
            padding: 6px;
            gap: 12px;
            position: relative;
        }

        .form-label {
            font-size: 16px;
            font-weight: 500;
            color: var(--text-dark);
            flex-basis: 200px;
            flex-shrink: 0;
        }

        .form-input,
        .custom-select {
            flex-grow: 1;
            background-color: var(--text-white);
            border: 1px solid var(--primary-color);
            border-radius: 6px;
            padding: 6px 10px;
            font-size: 14px;
            color: var(--text-light);
            box-shadow: 0px 1px 2px rgba(10, 13, 18, 0.05);
            height: 40px;
            display: flex;
            align-items: center;
        }

        /* Dropdown Tipe Pertanyaan */
        .custom-select {
            position: relative;
            cursor: pointer;
        }

        .custom-select span {
            flex-grow: 1;
        }

        .custom-select svg {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            pointer-events: none;
            transition: transform 0.2s ease;
        }

        .custom-select.active svg {
            transform: translateY(-50%) rotate(180deg);
        }

        /* Dropdown menu */
        .custom-select-menu {
            display: none;
            position: absolute;
            top: calc(100% + 4px);
            left: 0;
            background: white;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            z-index: 10;
            overflow: hidden;
        }

        .custom-select-option {
            padding: 10px 12px;
            cursor: pointer;
            color: var(--text-dark);
            border-bottom: 1px solid #f0f0f0;
        }

        .custom-select-option:last-child {
            border-bottom: none;
        }

        .custom-select-option:hover {
            background-color: #f3f9f5;
        }

        /* Answer Options */
        .answer-options-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
            width: 100%;
        }

        .answer-option {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .option-letter {
            width: 32px;
            height: 32px;
            background-color: var(--option-bg);
            border: 1px solid var(--secondary-color);
            border-radius: 6px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 16px;
            font-weight: 500;
            color: var(--text-dark);
            flex-shrink: 0;
        }

        .answer-option .form-input {
            height: 36px;
        }

        .answer-option .action-icons {
            gap: 12px;
        }

        /* Save Button */
        .save-action-section {
            padding: 24px 48px 32px;
            margin-top: 24px;
        }

        .save-button {
            background-color: var(--primary-color);
            color: var(--text-white);
            font-size: 17px;
            font-weight: 550;
            line-height: 24px;
            padding: 10px 0;
            border-radius: 8px;
            width: 100%;
            text-align: center;
            display: block;
            margin: 0 auto;
        }

        .save-button:hover {
            background-color: #2b6147;
            transform: translateY(-1px);
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.12);
        }

        /* Responsive Adjustments */
        @media (max-width: 1024px) {

            .form-row,
            .form-row-vertical {
                flex-direction: column;
                align-items: flex-start;
                gap: 6px;
            }

            .form-label {
                flex-basis: auto;
            }

            .form-input,
            .custom-select {
                width: 100%;
            }

            .answer-option {
                flex-wrap: wrap;
            }
        }

        @media (max-width: 768px) {
            .survey-editor-container {
                padding: 16px;
            }

            .survey-page {
                padding: 12px;
            }

            .page-header,
            .question-header {
                flex-direction: column;
                gap: 12px;
                padding: 8px;
            }

            .page-title {
                font-size: 20px;
            }

            .question-title {
                font-size: 18px;
            }

            .view-survey-btn {
                font-size: 16px;
                padding: 8px 12px;
            }

            .save-action-section {
                padding: 16px;
                margin-top: 16px;
            }

            .save-button {
                font-size: 14px;
                padding: 8px;
                width: 90%;
            }

            .tab-item {
                padding: 6px 12px;
                font-size: 14px;
            }
        }
    </style>
@endsection

@section('content')

    <section id="survey-nav" class="survey-nav-section">
        <section id="survey-navigation" class="survey-nav-container">
            <h2 class="survey-title">SURVEI KEPUASAN MASYARAKAT</h2>
            <nav class="survey-tabs">
                <a href="{{ route('superadmin.skm_rekap') }}"
                    class="tab-item {{ request()->routeIs('superadmin.skm_rekap') ? 'active' : '' }}">
                    Rekap SKM
                </a>
                <a href="{{ route('superadmin.skm_edit2') }}"
                    class="tab-item {{ request()->routeIs('superadmin.skm_edit2') ? 'active' : '' }}">
                    Edit Pertanyaan
                </a>
                <a href="{{ route('superadmin.skm_hasil') }}"
                    class="tab-item {{ request()->routeIs('superadmin.skm_hasil') ? 'active' : '' }}">
                    Hasil Survei
                </a>
            </nav>
        </section>

        <form id="survey-form" method="POST" action="{{ route('superadmin.skm.update_pertanyaan') }}">
            @csrf
            @method('PUT')
            <main id="survey-editor" class="survey-editor-container">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                <div class="view-survey-wrapper">
                    <a href="#" class="view-survey-btn">
                        <svg width="30" height="30" viewBox="0 0 50 50" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M32.4577 25.0001C32.4577 29.1251 29.1243 32.4584 24.9993 32.4584C20.8743 32.4584 17.541 29.1251 17.541 25.0001C17.541 20.8751 20.8743 17.5417 24.9993 17.5417C29.1243 17.5417 32.4577 20.8751 32.4577 25.0001Z"
                                stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            <path
                                d="M25.0007 42.2292C32.3548 42.2292 39.209 37.8958 43.9798 30.3958C45.8548 27.4583 45.8548 22.5208 43.9798 19.5833C39.209 12.0833 32.3548 7.75 25.0007 7.75C17.6465 7.75 10.7923 12.0833 6.02148 19.5833C4.14648 22.5208 4.14648 27.4583 6.02148 30.3958C10.7923 37.8958 17.6465 42.2292 25.0007 42.2292Z"
                                stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <span>Lihat Tampilan Survei</span>
                    </a>
                </div>


                <div class="survey-page-list">

                    {{-- LOOPING LANGSUNG KE PERTANYAAN --}}
                    @forelse ($surveyData as $pertanyaan)
                        <div class="question-block">
                            <input type="hidden" class="hidden-id-pertanyaan" value="{{ $pertanyaan->id_pertanyaan ?? '' }}">
                            <header class="question-header">
                                <div class="question-title-group">
                                    {{-- Menampilkan nomor urut pertanyaan --}}
                                    <h3 class="question-title">Pertanyaan {{ $loop->iteration }}</h3>
                                    <button class="icon">
                                        <svg width="35" height="35" viewBox="0 0 40 40" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path d="M10 15L20 25L30 15" stroke="#337354" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        </svg>
                                    </button>
                                </div>
                                <div class="action-icons">
                                    <button class="icon icon-btn">
                                        <svg width="40" height="40" viewBox="0 0 40 40" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <rect x="0.5" y="-0.5" width="39" height="39" rx="19.5"
                                                transform="matrix(1 0 0 -1 0 39)" stroke="#337354" />
                                            <path d="M14.1172 22.1001L20.0005 16.2334L25.8839 22.1001" stroke="#337354"
                                                stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </button>
                                    <button class="icon icon-btn">
                                        <svg width="40" height="40" viewBox="0 0 40 40" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <rect x="0.5" y="0.5" width="39" height="39" rx="19.5" stroke="#337354" />
                                            <path d="M14.1172 17.8999L20.0005 23.7666L25.8839 17.8999" stroke="#337354"
                                                stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </button>
                                    <button class="icon icon-btn btn-add-question">
                                        <svg width="40" height="40" viewBox="0 0 40 40" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <rect x="0.5" y="0.5" width="39" height="39" rx="19.5" stroke="#337354" />
                                            <path d="M10 20H30" stroke="#337354" stroke-width="1.5" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                            <path d="M20 30V10" stroke="#337354" stroke-width="1.5" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                        </svg>
                                    </button>
                                    <button class="icon icon-btn">
                                        <svg width="40" height="40" viewBox="0 0 40 40" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <rect x="0.5" y="0.5" width="39" height="39" rx="19.5" stroke="#337354" />
                                            <path
                                                d="M23.6673 20.4374V24.8124C23.6673 28.4583 22.209 29.9166 18.5632 29.9166H14.1882C10.5423 29.9166 9.08398 28.4583 9.08398 24.8124V20.4374C9.08398 16.7916 10.5423 15.3333 14.1882 15.3333H18.5632C22.209 15.3333 23.6673 16.7916 23.6673 20.4374Z"
                                                stroke="#337354" stroke-width="1.5" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                            <path
                                                d="M29.9173 14.1874V18.5624C29.9173 22.2083 28.459 23.6666 24.8132 23.6666H23.6673V20.4374C23.6673 16.7916 22.209 15.3333 18.5632 15.3333H15.334V14.1874C15.334 10.5416 16.7923 9.08325 20.4382 9.08325H24.8132C28.459 9.08325 29.9173 10.5416 29.9173 14.1874Z"
                                                stroke="#337354" stroke-width="1.5" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                        </svg>
                                    </button>
                                </div>
                            </header>
                            <div class="question-editor" style="display: block;"> {{-- Pastikan terbuka default --}}
                                <div class="form-row">
                                    <label class="form-label">Tipe Pertanyaan</label>
                                    <div class="custom-select">
                                        {{-- Tampilkan tipe dari controller --}}
                                        <span>{{ $pertanyaan->tipe_pertanyaan }}</span>
                                        <svg width="20" height="21" viewBox="0 0 20 21" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path d="M5 8L10 13L15 8" stroke="#337354" stroke-width="1.66667"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <label class="form-label">Isi Pertanyaan</label>
                                    {{-- Tampilkan teks pertanyaan --}}
                                    <input type="text" class="form-input" value="{{ $pertanyaan->pertanyaan ?? '' }}">
                                </div>
                                <div class="form-row">
                                    <label class="form-label">Deskripsi Pertanyaan</label>
                                    {{-- Kolom deskripsi sepertinya tidak ada di DB, kita kosongkan atau beri placeholder --}}
                                    <input type="text" class="form-input" value="{{-- $pertanyaan->deskripsi ?? '' --}}"
                                        placeholder="(Opsional)">
                                </div>

                                {{-- Tampilkan pilihan HANYA jika tipenya bukan 'Isian Teks' --}}
                                @if($pertanyaan->tipe_pertanyaan != 'Isian Teks' && $pertanyaan->pilihan->isNotEmpty())
                                    <div class="form-row form-row-vertical">
                                        <label class="form-label">Pilihan Jawaban</label>
                                        <div class="answer-options-list">

                                            {{-- LOOPING PILIHAN JAWABAN --}}
                                            @foreach ($pertanyaan->pilihan as $pilihan)
                                                <div class="answer-option">
                                                    <input type="hidden" class="hidden-id-pilihan"
                                                        value="{{ $pilihan->id_pilihan ?? '' }}">
                                                    {{-- Generate huruf A, B, C, D --}}
                                                    <div class="option-letter">{{ chr(65 + $loop->index) }}</div> {{-- Tampilkan teks
                                                    pilihan --}}
                                                    <input type="text" class="form-input" value="{{ $pilihan->pilihan ?? '' }}">
                                                    <div class="action-icons">
                                                        <button class="icon icon-btn btn-add-answer"> <svg width="40" height="40"
                                                                viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <rect x="0.5" y="0.5" width="39" height="39" rx="19.5"
                                                                    stroke="#337354" />
                                                                <path d="M10 20H30" stroke="#337354" stroke-width="1.5"
                                                                    stroke-linecap="round" stroke-linejoin="round" />
                                                                <path d="M20 30V10" stroke="#337354" stroke-width="1.5"
                                                                    stroke-linecap="round" stroke-linejoin="round" />
                                                            </svg>
                                                        </button>
                                                        <button class="icon icon-btn btn-delete-answer">
                                                            <svg width="40" height="40" viewBox="0 0 40 40" fill="none"
                                                                xmlns="http://www.w3.org/2000/svg">
                                                                <rect x="0.5" y="0.5" width="39" height="39" rx="19.5"
                                                                    stroke="#337354" />
                                                                <path d="M12.9277 27.071L27.0699 12.9289" stroke="#337354"
                                                                    stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                                                <path d="M27.0699 27.0711L12.9277 12.929" stroke="#337354"
                                                                    stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </div>
                                            @endforeach

                                        </div>
                                    </div>
                                @endif
                                {{-- Akhir dari if Pilihan Jawaban --}}

                            </div>
                        </div>
                    @empty
                        <div class="alert alert-info" style="margin: 20px; text-align: center;">
                            Belum ada pertanyaan survei yang dibuat di database.
                        </div>
                    @endforelse

                </div>
            </main>

            <section id="save-action" class="save-action-section">
                <button type="submit" class="save-button">Simpan Perubahan</button>
            </section>
        </form>
@endsection

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // ==========================================================
                // === FUNGSI UTAMA (EVENT DELEGATION) ===
                // ==========================================================
                const pageList = document.querySelector('.survey-page-list');
                if (pageList) {
                    pageList.addEventListener('click', function (e) {
                        // --- Deteksi Tombol ---
                        const addQuestionButton = e.target.closest('.btn-add-question');
                        const addAnswerButton = e.target.closest('.btn-add-answer');
                        const deleteAnswerButton = e.target.closest('.btn-delete-answer');
                        const toggleCollapseButton = e.target.closest('.question-title-group .icon');

                        // --- Logika Tombol ---
                        if (addQuestionButton) {
                            e.preventDefault();
                            const templateBlock = document.querySelector('.question-block');
                            if (!templateBlock) return;

                            const newQuestion = templateBlock.cloneNode(true);
                            cleanQuestionBlock(newQuestion); // Bersihkan kloningan

                            const currentBlock = addQuestionButton.closest('.question-block');
                            currentBlock.after(newQuestion); // Sisipkan setelah blok saat ini
                            renumberQuestions(); // Perbarui nomor

                        } else if (addAnswerButton) {
                            e.preventDefault();
                            const currentOption = addAnswerButton.closest('.answer-option');
                            const newOption = currentOption.cloneNode(true);

                            // Bersihkan kloningan
                            newOption.querySelector('.form-input').value = '';
                            const hiddenPilihanId = newOption.querySelector('.hidden-id-pilihan');
                            if (hiddenPilihanId) {
                                hiddenPilihanId.value = ''; // KOSONGKAN ID
                            }

                            currentOption.after(newOption);
                            const optionsList = currentOption.closest('.answer-options-list');
                            renumberOptions(optionsList); // Perbarui huruf (A, B, C)

                        } else if (deleteAnswerButton) {
                            e.preventDefault();
                            const currentOption = deleteAnswerButton.closest('.answer-option');
                            const optionsList = currentOption.closest('.answer-options-list');

                            // Jangan hapus jika ini pilihan terakhir
                            if (optionsList.querySelectorAll('.answer-option').length > 1) {
                                currentOption.remove();
                                renumberOptions(optionsList); // Perbarui huruf
                            } else {
                                alert('Setidaknya harus ada satu pilihan jawaban.');
                            }

                        } else if (toggleCollapseButton) {
                            const questionBlock = toggleCollapseButton.closest('.question-block');
                            const editor = questionBlock.querySelector('.question-editor');
                            toggleCollapseButton.classList.toggle('collapsed');
                            editor.style.display = editor.style.display === 'none' ? 'flex' : 'none';
                            const svg = toggleCollapseButton.querySelector('svg path');
                            svg.setAttribute('d', toggleCollapseButton.classList.contains('collapsed')
                                ? 'M10 25L20 15L30 25' // arah panah ke atas
                                : 'M10 15L20 25L30 15' // arah panah ke bawah
                            );
                        }
                    });
                }

                // ==========================================================
                // === FUNGSI DROPDOWN TIPE PERTANYAAN ===
                // ==========================================================
                document.querySelectorAll('.custom-select').forEach(select => {
                    select.addEventListener('click', function (e) {
                        e.stopPropagation();
                        // ... (Sisakan kode dropdown Anda yang sudah ada di sini,
                        //      saya potong agar ringkas, tapi JANGAN HAPUS KODE DROPDOWN)
                        // ... (Kode dropdown Anda dari baris 570 - 612) ...
                    });
                });
                window.addEventListener('click', () => {
                    // ... (Sisakan kode window.click Anda) ...
                });


                // ==========================================================
                // === FUNGSI HELPERS (CLEAN & RENUMBER) ===
                // ==========================================================

                // Fungsi untuk membersihkan template pertanyaan yang baru di-clone
                function cleanQuestionBlock(block) {
                    // 1. Set default tipe pertanyaan
                    const selectSpan = block.querySelector('.custom-select span');
                    if (selectSpan) selectSpan.textContent = 'Pilihan Ganda';

                    // 2. Kosongkan input "Isi Pertanyaan"
                    const inputPertanyaan = block.querySelectorAll('.question-editor > .form-row .form-input')[0];
                    if (inputPertanyaan) inputPertanyaan.value = '';

                    // 3. Kosongkan input "Deskripsi"
                    const inputDeskripsi = block.querySelectorAll('.question-editor > .form-row .form-input')[1];
                    if (inputDeskripsi) {
                        inputDeskripsi.value = '';
                        inputDeskripsi.placeholder = '(Opsional)';
                    }

                    // 4. KOSONGKAN HIDDEN ID PERTANYAAN
                    const hiddenId = block.querySelector('.hidden-id-pertanyaan');
                    if (hiddenId) {
                        hiddenId.value = '';
                    }

                    // 5. Hapus semua pilihan jawaban KECUALI satu
                    const optionsList = block.querySelector('.answer-options-list');
                    if (optionsList) {
                        const optionTemplate = optionsList.querySelector('.answer-option');
                        if (optionTemplate) {
                            const newOption = optionTemplate.cloneNode(true);

                            // Bersihkan pilihan pertama
                            newOption.querySelector('.option-letter').textContent = 'A';
                            newOption.querySelector('.form-input').value = '';
                            const hiddenPilihanId = newOption.querySelector('.hidden-id-pilihan');
                            if (hiddenPilihanId) {
                                hiddenPilihanId.value = ''; // KOSONGKAN ID PILIHAN
                            }

                            optionsList.innerHTML = ''; // Hapus semua
                            optionsList.appendChild(newOption); // Tambah satu yang bersih
                        }
                    }

                    // 6. Pastikan editornya terlihat
                    const editor = block.querySelector('.question-editor');
                    if (editor) editor.style.display = 'flex';
                }

                // Fungsi untuk menomori ulang semua pertanyaan
                function renumberQuestions() {
                    const allQuestions = pageList.querySelectorAll('.question-block');
                    allQuestions.forEach((block, index) => {
                        const title = block.querySelector('.question-title');
                        if (title) title.textContent = `Pertanyaan ${index + 1}`;
                    });
                }

                // Fungsi untuk menomori ulang huruf pilihan (A, B, C)
                function renumberOptions(optionsList) {
                    if (!optionsList) return;
                    const allOptions = optionsList.querySelectorAll('.option-letter');
                    allOptions.forEach((letter, index) => {
                        letter.textContent = String.fromCharCode(65 + index); // 65 = 'A'
                    });
                }

                // ==========================================================
                // === FUNGSI SUBMIT FORM ===
                // ==========================================================
                const surveyForm = document.getElementById('survey-form');
                if (surveyForm) {
                    surveyForm.addEventListener('submit', function (event) {
                        event.preventDefault();

                        try {
                            const questions = surveyForm.querySelectorAll('.question-block');

                            questions.forEach((questionBlock, qIndex) => {
                                // --- Beri Nama Input ID Pertanyaan (BARU) ---
                                const hiddenIdPertanyaan = questionBlock.querySelector('.hidden-id-pertanyaan');
                                if (hiddenIdPertanyaan) {
                                    hiddenIdPertanyaan.name = `questions[${qIndex}][id_pertanyaan]`;
                                }

                                // --- Beri Nama Input Pertanyaan ---
                                const questionInput = questionBlock.querySelector('.question-editor > .form-row:nth-child(2) .form-input');
                                if (questionInput) {
                                    questionInput.name = `questions[${qIndex}][pertanyaan]`;
                                }

                                // --- Beri Nama Tipe Pertanyaan ---
                                const typeSpan = questionBlock.querySelector('.custom-select span');
                                if (typeSpan) {
                                    let hiddenTypeInput = questionBlock.querySelector('input[name^="questions["][name$="][tipe]"]');
                                    if (!hiddenTypeInput) {
                                        hiddenTypeInput = document.createElement('input');
                                        hiddenTypeInput.type = 'hidden';
                                        questionBlock.appendChild(hiddenTypeInput);
                                    }
                                    hiddenTypeInput.name = `questions[${qIndex}][tipe]`;
                                    hiddenTypeInput.value = typeSpan.textContent.trim();
                                }

                                // --- Beri Nama Input Pilihan Jawaban ---
                                const answerOptions = questionBlock.querySelectorAll('.answer-option');
                                answerOptions.forEach((option, oIndex) => {
                                    const optionInput = option.querySelector('.form-input');
                                    if (optionInput) {
                                        // Beri Nama ID Pilihan (BARU)
                                        const hiddenIdPilihan = option.querySelector('.hidden-id-pilihan');
                                        if (hiddenIdPilihan) {
                                            hiddenIdPilihan.name = `questions[${qIndex}][pilihan][${oIndex}][id_pilihan]`;
                                        }

                                        // Beri Nama Teks Pilihan
                                        optionInput.name = `questions[${qIndex}][pilihan][${oIndex}][pilihan]`;

                                        // Beri Nama Nilai Pilihan
                                        const optionLetter = option.querySelector('.option-letter');
                                        if (optionLetter) {
                                            let hiddenNilaiInput = option.querySelector('input[name$="][nilai]"]');
                                            if (!hiddenNilaiInput) {
                                                hiddenNilaiInput = document.createElement('input');
                                                hiddenNilaiInput.type = 'hidden';
                                                option.appendChild(hiddenNilaiInput);
                                            }
                                            hiddenNilaiInput.name = `questions[${qIndex}][pilihan][${oIndex}][nilai]`;

                                            const questionNumber = qIndex + 1;
                                            if (questionNumber >= 11 && questionNumber <= 15) {
                                                hiddenNilaiInput.value = (oIndex === 0) ? '10' : '0';
                                            } else {
                                                hiddenNilaiInput.value = oIndex + 1;
                                            }
                                        }
                                    }
                                });
                            });

                            surveyForm.submit(); // Kirim form setelah nama diatur

                        } catch (error) {
                            console.error("Error during form processing:", error);
                            alert("Terjadi error saat memproses form. Cek console log.");
                        }
                    });
                }
            });
        </script>
    @endpush