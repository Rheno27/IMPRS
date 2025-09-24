@extends('layouts.app')

@section('styles')
    <style>
        :root {
            --primary-color: #337354;
            --primary-light: #77a28d;
            --primary-lighter: #d6e3dd;
            --primary-dark: #2a7f54;
            --text-primary: #2d2d2d;
            --text-secondary: #717680;
            --text-placeholder: #9e9e9e;
            --text-white: #ffffff;
            --bg-color: #fcfcfc;
            --border-color: #77a28d;
            --border-color-light: rgba(119, 162, 141, 0.5);
            --border-color-strong: #337354;
            --border-color-header: rgba(51, 115, 84, 0.5);
            --accent-color: #99ce55;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Roboto', sans-serif;
            background-color: var(--bg-light);
            color: var(--text-dark);
        }

        .page-container {
            max-width: 1440px;
            margin: 0 auto;
            position: relative;
            overflow: hidden;
        }

        img {
            max-width: 100%;
            height: auto;
            display: block;
        }

        button,
        input,
        select,
        textarea {
            font-family: inherit;
        }

        .icon-button {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            border: 1px solid var(--border-color-strong);
            border-radius: 50%;
            width: 40px;
            height: 40px;
            background-color: transparent;
            cursor: pointer;
            padding: 0;
        }

        .icon-button img {
            width: 24px;
            height: 24px;
        }

        /* .icon-button.delete-icon img 
      transform: rotate(-45deg);
    } */

        /* CSS from section:header */
        .site-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: var(--bg-light);
            border-bottom: 1px solid var(--primary-color);
            padding: 0 40px;
            height: 80px;
            /* header fix height */
        }

        /* Logo */
        .logo-container {
            display: flex;
            align-items: center;
        }

        .logo-image {
            height: 70px;
            /* hampir sama dengan tinggi avatar */
            width: auto;
            object-fit: contain;
        }

        /* User info */
        .user-info {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .user-avatar {
            height: 30px;
            width: 30px;
            border-radius: 50%;
            object-fit: cover;
        }

        .user-name {
            font-weight: 600;
            font-size: 18px;
            /* sebanding dengan tinggi avatar/logo */
            color: var(--primary-color);
            white-space: nowrap;
        }

        .logout-link {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 30px;
            width: 30px;
            border-radius: 8px;
            transition: background 0.2s;
        }

        .logout-link:hover {
            background: rgba(51, 115, 84, 0.1);
        }

        .logout-icon {
            height: 28px;
            width: 28px;
        }

        /* CSS from section:survey */
        .survey-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 52px;
            padding: 72px;
        }

        .survey-title {
            font-size: 54px;
            font-weight: 700;
            color: var(--primary-color);
            line-height: 60px;
            text-align: center;
            margin: 0;
        }

        .survey-form {
            width: 100%;
            max-width: 1296px;
            display: flex;
            flex-direction: column;
            gap: 36px;
        }

        .form-section {
            border: 1px solid var(--border-color);
            border-radius: 25px;
            padding: 24px 36px;
        }

        .form-field-group {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .form-label-header {
            border-bottom: 1px solid var(--primary-color);
            text-align: left;
            font-size: 28px;
            font-weight: 600;
            color: var(--primary-color);
            line-height: 44px;
        }

        .form-input-text {
            border: 1px solid var(--border-color);
            border-radius: 10px;
            padding: 16px 12px;
            font-size: 16px;
            color: var(--text-placeholder);
            width: 100%;
            height: fit-content;
            text-align: left;
        }

        .questions-section {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .page-header,
        .question-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--primary-color);
            padding: 16px;
        }

        .question-header {
            padding: 24px 16px;
        }

        .page-title,
        .question-title {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 36px;
            font-weight: 600;
            color: var(--primary-color);
        }


        .form-input-text-question {
            border: none;
            background: transparent;
            font-size: 28px;
            font-weight: 600;
            color: var(--primary-color);
            padding: 4px 0;
            min-width: 12ch;
            max-width: 30ch;
            width: auto;
        }

        .form-input-text-question:focus {
            outline: none;
            border-bottom: 1px solid var(--primary-color);
        }

        .page-content {
            display: none;
            /* awalnya tertutup */
        }

        .page-content.active {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .toggle-btn-down {
            cursor: pointer;
            transition: transform 0.3s ease;
        }

        .toggle-btn-down.rotate {
            transform: rotate(180deg);
        }

        .toggle-btn-question {
            cursor: pointer;
            transition: transform 0.3s ease;
        }

        .toggle-btn-question.rotate {
            transform: rotate(180deg);
        }

        .question-title {
            font-size: 28px;
        }

        .page-actions,
        .question-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .question-card {
            border: 1px solid var(--border-color);
            border-radius: 25px;
            padding: 26px;
            display: flex;
            flex-direction: column;

        }

        .question-editor {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .form-row,
        .form-row-vertical {
            display: flex;
            align-items: center;
            /* isi konten tetap di tengah secara vertikal */
            justify-content: space-between;
            border-top: 1px solid var(--primary-color);
            margin: 0;
            /* biar antar row gak ada gap ekstra */
            padding: 24px 0 0 0;
            /* jarak isi ke border atas = 24px */
        }

        .form-row:not(:first-child),
        .form-row-vertical:not(:first-child) {
            margin-top: 0;
            /* pastikan border ketemu border tanpa jeda */
        }

        .form-row-vertical {
            align-items: flex-start;
            /* kalau vertikal, label ke atas */
        }

        .form-row label,
        .form-row-vertical label {
            font-size: 20px;
            font-weight: 500;
            color: var(--text-primary);
            flex-shrink: 0;
            width: 300px;
        }


        .select-wrapper {
            position: relative;
            width: 100%;
            max-width: 876px;
        }

        .form-select,
        .form-input {
            width: 100%;
            border: 1px solid var(--border-color-strong);
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 16px;
            background-color: var(--text-white);
            box-shadow: 0px 1px 2px 0px rgba(10, 13, 18, 0.05);
        }

        .form-select {
            color: var(--primary-light);
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            padding-right: 40px;
        }

        .form-select:focus,
        .form-select:active {
            border: 1px solid var(--primary-color) !important;
            /* tetap pakai warna default */
            outline: none;
            /* hilangin highlight biru */
            box-shadow: none;
            /* hilangin efek glow di browser tertentu */
        }

        .form-input {
            color: var(--text-secondary);
            font-size: 18px;
        }

        .select-arrow {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            pointer-events: none;
        }

        .choices-list {
            display: flex;
            flex-direction: column;
            gap: 16px;
            width: 100%;
        }

        .choice-item {
            display: flex;
            align-items: center;
            gap: 24px;
        }

        .choice-label {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 44px;
            height: 44px;
            background-color: var(--primary-lighter);
            border: 1px solid var(--border-color);
            border-radius: 6px;
            font-size: 20px;
            font-weight: 500;
            color: var(--text-primary);
            flex-shrink: 0;
        }

        .choice-item .form-input {
            flex-grow: 1;
        }

        .choice-actions {
            display: flex;
            gap: 16px;
        }

        .submit-button {
            background-color: var(--primary-color);
            color: var(--text-white);
            font-size: 28px;
            font-weight: 500;
            line-height: 28px;
            padding: 24px;
            border-radius: 15px;
            width: 100%;
            cursor: pointer;
            border: none;
        }

        @media (max-width: 1200px) {

            .form-row,
            .form-row-vertical {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }

            .form-row label,
            .form-row-vertical label {
                width: auto;
            }

            .select-wrapper,
            .form-input {
                max-width: 100%;
            }
        }

        @media (max-width: 768px) {
            .survey-container {
                padding: 32px 24px;
                gap: 32px;
            }

            .survey-title {
                font-size: 36px;
                line-height: 1.2;
            }

            .form-section {
                padding: 16px;
            }

            .form-label-header {
                font-size: 24px;
                padding: 12px;
            }

            .page-header,
            .question-header {
                flex-direction: column;
                gap: 16px;
                padding: 12px;
            }

            .page-title,
            .question-title {
                font-size: 24px;
            }

            .question-card {
                padding: 16px;
            }

            .choice-item {
                flex-wrap: wrap;
                gap: 12px;
            }

            .choice-item .form-input {
                width: 100%;
                flex-basis: 100%;
            }

            .choice-actions {
                margin-left: auto;
            }
        }
    </style>
@endsection

@section('content')
    <main id="section-survey" class="survey-container">
        <h1 class="survey-title">SURVEI KEPUASAN MASYARAKAT</h1>

        <form class="survey-form">
            <div class="form-section">
                <div class="form-field-group">
                    <label class="form-label-header">Judul Kuesioner</label>
                    <input type="text" class="form-input-text" value="SURVEI KEPUASAN MASYARAKAT">
                </div>
            </div>

            <div class="form-section">
                <div class="form-field-group">
                    <label class="form-label-header">Deskripsi Kuesioner</label>
                    <input type="text" class="form-input-text" value="SURVEI KEPUASAN MASYARAKAT">
                </div>
            </div>

            <div class="form-section questions-section">
                <div class="page-header">
                    <div class="page-title">
                        <input type="text" class="form-input-text-question" value="HALAMAN 1">
                        <img src="image/arrow-circle-down.svg" alt="toggle" class="toggle-btn-down">
                    </div>
                    <div class="page-actions">
                        <button type="button" class="icon-button"><img src="image/arrow-up-round.svg" alt="Up"></button>
                        <button type="button" class="icon-button"><img src="image/arrow-circle-down.svg"
                                alt="Down"></button>
                        <button type="button" class="icon-button"><img src="image/add-round.svg" alt="Add"></button>
                        <button type="button" class="icon-button"><img src="image/copy-round.svg" alt="Copy"></button>
                    </div>
                </div>

                <div class="page-content">
                    <div class="question-card">
                        <div class="question-header">
                            <div class="question-title">Pertanyaan 1
                                <img src="image/arrow-circle-down.svg" alt="toggle" class="toggle-btn-question">
                            </div>
                            <div class="question-actions">
                                <button type="button" class="icon-button"><img src="image/arrow-up-round.svg"
                                        alt="Up"></button>
                                <button type="button" class="icon-button"><img src="image/arrow-circle-down.svg"
                                        alt="Down"></button>
                                <button type="button" class="icon-button"><img src="image/add-round.svg" alt="Add"></button>
                                <button type="button" class="icon-button"><img src="image/copy-round.svg"
                                        alt="Copy"></button>
                                <button type="button" class="icon-button"><img src="image/cancel-round.svg"
                                        alt="Delete"></button>
                            </div>
                        </div>
                        <div class="page-content">
                            <div class="question-editor">
                                <div class="form-row">
                                    <label for="q1-type">Tipe Pertanyaan</label>
                                    <div class="select-wrapper">
                                        <select id="q1-type" class="form-select">
                                            <option>Pilihan Ganda</option>
                                        </select>
                                        <img src="image/arrow-circle-down.svg" class="select-arrow" alt="dropdown arrow">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <label for="q1-text">Isi Pertanyaan</label>
                                    <input type="text" id="q1-text" class="form-input" value="SURVEI KEPUASAN MASYARAKAT">
                                </div>
                                <div class="form-row">
                                    <label for="q1-desc">Deskripsi Pertanyaan</label>
                                    <input type="text" id="q1-desc" class="form-input" value="SURVEI KEPUASAN MASYARAKAT">
                                </div>
                                <div class="form-row-vertical">
                                    <label>Pilihan Jawaban</label>
                                    <div class="choices-list">
                                        <div class="choice-item">
                                            <div class="choice-label">A</div>
                                            <input type="text" class="form-input" value="SURVEI KEPUASAN MASYARAKAT">
                                            <div class="choice-actions">
                                                <button type="button" class="icon-button"><img src="image/add-round.svg"
                                                        alt="Add"></button>
                                                <button type="button" class="icon-button"><img src="image/cancel-round.svg"
                                                        alt="Delete"></button>
                                            </div>
                                        </div>
                                        <div class="choice-item">
                                            <div class="choice-label">A</div>
                                            <input type="text" class="form-input" value="SURVEI KEPUASAN MASYARAKAT">
                                            <div class="choice-actions">
                                                <button type="button" class="icon-button"><img src="image/add-round.svg"
                                                        alt="Add"></button>
                                                <button type="button" class="icon-button"><img src="image/cancel-round.svg"
                                                        alt="Delete"></button>
                                            </div>
                                        </div>
                                        <div class="choice-item">
                                            <div class="choice-label">A</div>
                                            <input type="text" class="form-input" value="SURVEI KEPUASAN MASYARAKAT">
                                            <div class="choice-actions">
                                                <button type="button" class="icon-button"><img src="image/add-round.svg"
                                                        alt="Add"></button>
                                                <button type="button" class="icon-button"><img src="image/cancel-round.svg"
                                                        alt="Delete"></button>
                                            </div>
                                        </div>
                                        <div class="choice-item">
                                            <div class="choice-label">A</div>
                                            <input type="text" class="form-input" value="SURVEI KEPUASAN MASYARAKAT">
                                            <div class="choice-actions">
                                                <button type="button" class="icon-button"><img src="image/add-round.svg"
                                                        alt="Add"></button>
                                                <button type="button" class="icon-button"><img src="image/cancel-round.svg"
                                                        alt="Delete"></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="question-card">
                        <div class="question-header">
                            <div class="question-title">Pertanyaan 2
                                <img src="image/arrow-circle-down.svg" alt="toggle" class="toggle-btn-question">
                            </div>
                            <div class="question-actions">
                                <button type="button" class="icon-button"><img src="image/arrow-up-round.svg"
                                        alt="Up"></button>
                                <button type="button" class="icon-button"><img src="image/arrow-circle-down.svg"
                                        alt="Down"></button>
                                <button type="button" class="icon-button"><img src="image/add-round.svg" alt="Add"></button>
                                <button type="button" class="icon-button"><img src="image/copy-round.svg"
                                        alt="Copy"></button>
                                <button type="button" class="icon-button"><img src="image/cancel-round.svg"
                                        alt="Delete"></button>
                            </div>
                        </div>
                        <div class="page-content">
                            <div class="question-editor">
                                <div class="form-row">
                                    <label for="q2-type">Tipe Pertanyaan</label>
                                    <div class="select-wrapper">
                                        <select id="q2-type" class="form-select">
                                            <option>Isian Teks</option>
                                        </select>
                                        <img src="image/arrow-circle-down.svg" class="select-arrow" alt="dropdown arrow">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <label for="q2-text">Isi Pertanyaan</label>
                                    <input type="text" id="q2-text" class="form-input" value="SURVEI KEPUASAN MASYARAKAT">
                                </div>
                                <div class="form-row">
                                    <label for="q2-desc">Deskripsi Pertanyaan</label>
                                    <input type="text" id="q2-desc" class="form-input" value="SURVEI KEPUASAN MASYARAKAT">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <button type="submit" class="submit-button">Simpan Perubahan</button>
        </form>
    </main>
@endsection

@push('scripts')
    <script>

        document.querySelectorAll('.questions-section').forEach(section => {
            const toggleBtn = section.querySelector('.toggle-btn-down');
            const pageContent = section.querySelector('.page-content');

            toggleBtn.addEventListener('click', () => {
                pageContent.classList.toggle('active');
                toggleBtn.classList.toggle('rotate');
            });
        });

        // toggle pertanyaan
        document.querySelectorAll('.toggle-btn-question').forEach(btn => {
            const questionContent = btn.closest('.question-header').nextElementSibling;
            btn.addEventListener('click', () => {
                questionContent.classList.toggle('active');
                btn.classList.toggle('rotate');

            });
        });

        document.querySelectorAll('.form-input-text-question').forEach(input => {
            const resize = () => {
                input.style.width = Math.min((input.value.length + 1) * 1, 30) + "ch"; // max 30ch
            };
            resize();
            input.addEventListener("input", resize);
        });


    </script>
@endpush