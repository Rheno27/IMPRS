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
            gap: 12px;
            /* Jarak antar elemen */
        }

        /* Tambahkan class baru ini agar input teks memenuhi ruang sisa */
        .answer-option .input-text {
            flex-grow: 1;
            height: 36px;
        }

        /* Tambahkan class baru untuk kolom bobot agar ukurannya kecil fix */
        .answer-option .input-score {
            width: 80px;
            /* Lebar kolom bobot */
            flex-grow: 0;
            flex-shrink: 0;
            text-align: center;
            height: 36px;
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

        .custom-alert {
            padding: 16px 20px;
            border-radius: 12px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 4px 12px rgba(51, 115, 84, 0.15);
            /* Bayangan halus hijau */
            animation: slideDown 0.4s ease-out;
            border: 1px solid transparent;
        }

        .custom-alert.success {
            background-color: #eaf5f0;
            /* Hijau sangat muda */
            border-color: rgba(51, 115, 84, 0.2);
            color: var(--primary-color);
        }

        .custom-alert.error {
            background-color: #fdeaea;
            /* Merah sangat muda */
            border-color: rgba(220, 53, 69, 0.2);
            color: #dc3545;
        }

        .alert-content {
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 500;
            font-size: 15px;
        }

        .alert-close-btn {
            background: transparent;
            border: none;
            cursor: pointer;
            padding: 4px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.2s;
            color: inherit;
            opacity: 0.7;
        }

        .alert-close-btn:hover {
            background-color: rgba(0, 0, 0, 0.05);
            opacity: 1;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* --- CUSTOM MODAL STYLES --- */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            /* Latar belakang gelap transparan */
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            backdrop-filter: blur(2px);
            /* Efek blur di belakang */
        }

        .modal-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .modal-box {
            background: white;
            width: 400px;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            text-align: center;
            transform: translateY(20px);
            transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .modal-overlay.active .modal-box {
            transform: translateY(0);
        }

        .modal-icon-wrapper {
            width: 60px;
            height: 60px;
            margin: 0 auto 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-icon-wrapper.danger {
            background-color: #fdeaea;
            color: #dc3545;
        }

        .modal-icon-wrapper.success {
            background-color: #eaf5f0;
            color: #337354;
        }

        .modal-title {
            font-size: 20px;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 10px;
        }

        .modal-text {
            font-size: 15px;
            color: var(--text-light);
            margin-bottom: 25px;
            line-height: 1.5;
        }

        .modal-actions {
            display: flex;
            gap: 12px;
            justify-content: center;
        }

        .btn-modal {
            padding: 10px 24px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            border: none;
            transition: all 0.2s;
        }

        .btn-modal-cancel {
            background-color: #f1f3f5;
            color: #495057;
        }

        .btn-modal-cancel:hover {
            background-color: #e9ecef;
        }

        .btn-modal-confirm {
            background-color: #dc3545;
            /* Merah untuk hapus */
            color: white;
            box-shadow: 0 4px 12px rgba(220, 53, 69, 0.25);
        }

        .btn-modal-confirm:hover {
            background-color: #c92a2a;
            transform: translateY(-1px);
        }
    </style>
@endsection

@section('content')

    <section id="survey-nav" class="survey-nav-section">
        @include('superadmin.partials.skm_nav')

        <form id="survey-form" method="POST" action="{{ route('superadmin.skm.update_pertanyaan') }}">
            @csrf
            @method('PUT')
            <main id="survey-editor" class="survey-editor-container">
                {{-- NOTIFIKASI SUKSES --}}
                @if (session('success'))
                    <div class="custom-alert success" role="alert">
                        <div class="alert-content">
                            {{-- Ikon Centang Hijau --}}
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 22C17.5 22 22 17.5 22 12C22 6.5 17.5 2 12 2C6.5 2 2 6.5 2 12C2 17.5 6.5 22 12 22Z"
                                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M7.75 12L10.58 14.83L16.25 9.17004" stroke="currentColor" stroke-width="1.5"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <span>{{ session('success') }}</span>
                        </div>
                        <button type="button" class="alert-close-btn" onclick="this.parentElement.remove()">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M18 6L6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" />
                                <path d="M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                        </button>
                    </div>
                @endif

                {{-- NOTIFIKASI ERROR --}}
                @if (session('error'))
                    <div class="custom-alert error" role="alert">
                        <div class="alert-content">
                            {{-- Ikon Tanda Seru Merah --}}
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 22C17.5 22 22 17.5 22 12C22 6.5 17.5 2 12 2C6.5 2 2 6.5 2 12C2 17.5 6.5 22 12 22Z"
                                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M12 8V13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" />
                                <path d="M11.9945 16H12.0035" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                            <span>{{ session('error') }}</span>
                        </div>
                        <button type="button" class="alert-close-btn" onclick="this.parentElement.remove()">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M18 6L6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" />
                                <path d="M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                        </button>
                    </div>
                @endif
                <div class="view-survey-wrapper">
                    <a href="{{ route('guest.survei-1') }}" target="_blank" rel="noopener noreferrer"
                        class="view-survey-btn">
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
                                    <button class="icon icon-btn btn-move-up">
                                        <svg width="40" height="40" viewBox="0 0 40 40" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <rect x="0.5" y="-0.5" width="39" height="39" rx="19.5"
                                                transform="matrix(1 0 0 -1 0 39)" stroke="#337354" />
                                            <path d="M14.1172 22.1001L20.0005 16.2334L25.8839 22.1001" stroke="#337354"
                                                stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </button>

                                    <button class="icon icon-btn btn-move-down">
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
                                    {{-- TOMBOL HAPUS PERTANYAAN (BARU) --}}
                                    <button class="icon icon-btn btn-delete-question" style="border-color: #dc3545;">
                                        <svg width="40" height="40" viewBox="0 0 40 40" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <rect x="0.5" y="0.5" width="39" height="39" rx="19.5" stroke="#dc3545" />
                                            <path d="M12 15H28" stroke="#dc3545" stroke-width="1.5" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                            <path d="M16 15V12C16 11.4477 16.4477 11 17 11H23C23.5523 11 24 11.4477 24 12V15"
                                                stroke="#dc3545" stroke-width="1.5" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                            <path
                                                d="M14 15L15.446 27.2885C15.5445 28.1257 16.2559 28.75 17.0988 28.75H22.9012C23.7441 28.75 24.4555 28.1257 24.554 27.2885L26 15"
                                                stroke="#dc3545" stroke-width="1.5" stroke-linecap="round"
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
                                    </div>
                                </div>
                                <div class="form-row">
                                    <label class="form-label">Isi Pertanyaan</label>
                                    {{-- Tampilkan teks pertanyaan --}}
                                    <input type="text" class="form-input" value="{{ $pertanyaan->pertanyaan ?? '' }}">
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

                                                    {{-- Huruf A, B, C, D --}}
                                                    <div class="option-letter">{{ chr(65 + $loop->index) }}</div>

                                                    {{-- Input Teks Jawaban (Kasih class 'input-text') --}}
                                                    <input type="text" class="form-input input-text" placeholder="Teks Jawaban"
                                                        value="{{ $pilihan->pilihan ?? '' }}">

                                                    {{-- Input Bobot/Nilai (BARU) --}}
                                                    <input type="number" class="form-input input-score" placeholder="Nilai"
                                                        value="{{ $pilihan->nilai ?? '' }}">

                                                    <div class="action-icons">
                                                        <button class="icon icon-btn btn-add-answer">
                                                            <svg width="40" height="40" viewBox="0 0 40 40" fill="none"
                                                                xmlns="http://www.w3.org/2000/svg">
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

        {{-- CUSTOM MODAL COMPONENT --}}
        <div id="customConfirmModal" class="modal-overlay">
            <div class="modal-box">
                {{-- Ikon Tong Sampah Besar --}}
                <div class="modal-icon-wrapper danger">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 22C17.5 22 22 17.5 22 12C22 6.5 17.5 2 12 2C6.5 2 2 6.5 2 12C2 17.5 6.5 22 12 22Z"
                            stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M12 8V13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                            stroke-linejoin="round" />
                        <path d="M11.9945 16H12.0035" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                </div>

                <h3 class="modal-title">Hapus Pertanyaan?</h3>
                <p class="modal-text">Pertanyaan ini akan dihapus permanen dari database. Tindakan ini tidak dapat
                    dibatalkan.
                </p>

                <div class="modal-actions">
                    <button type="button" class="btn-modal btn-modal-cancel" id="closeModalBtn">Batal</button>
                    <button type="button" class="btn-modal btn-modal-confirm" id="confirmDeleteBtn">Ya, Hapus</button>
                </div>
            </div>
        </div>
        {{-- CUSTOM ALERT MODAL (Untuk Error/Info) --}}
        <div id="customAlertModal" class="modal-overlay">
            <div class="modal-box">
                {{-- Ikon Silang Merah (Error) --}}
                <div class="modal-icon-wrapper danger">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 22C17.5 22 22 17.5 22 12C22 6.5 17.5 2 12 2C6.5 2 2 6.5 2 12C2 17.5 6.5 22 12 22Z"
                            stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M15 9L9 15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                            stroke-linejoin="round" />
                        <path d="M9 9L15 15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                </div>

                <h3 class="modal-title" id="alertTitle">Gagal</h3>
                <p class="modal-text" id="alertMessage">Terjadi kesalahan.</p>

                <div class="modal-actions">
                    <button type="button" class="btn-modal btn-modal-confirm"
                        style="background-color: #337354; width: 100%;" id="btnAlertOk">OK, Mengerti</button>
                </div>
            </div>
        </div>
@endsection

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {

                // ==========================================================
                // 1. INISIALISASI VARIABEL MODAL (HANYA SEKALI)
                // ==========================================================
                const confirmModal = document.getElementById('customConfirmModal');
                const alertModal = document.getElementById('customAlertModal');
                const btnConfirm = document.getElementById('confirmDeleteBtn');
                const btnCancel = document.getElementById('closeModalBtn');
                const btnAlertOk = document.getElementById('btnAlertOk');

                let deleteCallback = null; // Variabel global untuk menyimpan aksi hapus

                // ==========================================================
                // 2. FUNGSI-FUNGSI MODAL
                // ==========================================================

                // --- Modal Konfirmasi (Ya/Batal) ---
                function openConfirmModal(callback) {
                    if (!confirmModal) return;
                    deleteCallback = callback; // Simpan fungsi hapus ke variabel global
                    confirmModal.classList.add('active');
                }

                function closeConfirmModal() {
                    if (!confirmModal) return;
                    confirmModal.classList.remove('active');
                    deleteCallback = null; // Reset callback
                }

                // --- Modal Alert (Info/Error) ---
                function openAlertModal(title, message) {
                    if (!alertModal) return;
                    const titleEl = document.getElementById('alertTitle');
                    const msgEl = document.getElementById('alertMessage');

                    if (titleEl) titleEl.textContent = title;
                    if (msgEl) msgEl.textContent = message;

                    alertModal.classList.add('active');
                }

                function closeAlertModal() {
                    if (!alertModal) return;
                    alertModal.classList.remove('active');
                }

                // ==========================================================
                // 3. EVENT LISTENER TOMBOL MODAL
                // ==========================================================

                // Tutup modal konfirmasi jika klik Batal
                if (btnCancel) {
                    btnCancel.addEventListener('click', closeConfirmModal);
                }

                // Jalankan aksi hapus jika klik Ya, Hapus
                if (btnConfirm) {
                    btnConfirm.addEventListener('click', function () {
                        if (deleteCallback) {
                            deleteCallback(); // Jalankan fungsi yang disimpan tadi
                        }
                        closeConfirmModal();
                    });
                }

                // Tutup modal alert jika klik OK
                if (btnAlertOk) {
                    btnAlertOk.addEventListener('click', closeAlertModal);
                }


                // ==========================================================
                // 4. LOGIKA UTAMA HALAMAN (Tombol Hapus, Tambah, dll)
                // ==========================================================
                const pageList = document.querySelector('.survey-page-list');
                if (pageList) {
                    pageList.addEventListener('click', function (e) {
                        // --- Deteksi Tombol ---
                        const addQuestionButton = e.target.closest('.btn-add-question');
                        const addAnswerButton = e.target.closest('.btn-add-answer');
                        const deleteAnswerButton = e.target.closest('.btn-delete-answer');
                        const toggleCollapseButton = e.target.closest('.question-title-group .icon');

                        if (e.target.closest('.btn-move-up')) {
                            e.preventDefault();
                            const btn = e.target.closest('.btn-move-up');
                            const currentBlock = btn.closest('.question-block');
                            const prevBlock = currentBlock.previousElementSibling;

                            // Cek apakah ada elemen sebelumnya dan apakah itu pertanyaan
                            if (prevBlock && prevBlock.classList.contains('question-block')) {
                                currentBlock.parentNode.insertBefore(currentBlock, prevBlock);
                                currentBlock.scrollIntoView({ behavior: 'smooth', block: 'center' });
                                renumberQuestions();
                            }
                        }

                        // 2. LOGIKA GESER KE BAWAH (BARU)
                        else if (e.target.closest('.btn-move-down')) {
                            e.preventDefault();
                            const btn = e.target.closest('.btn-move-down');
                            const currentBlock = btn.closest('.question-block');
                            const nextBlock = currentBlock.nextElementSibling;

                            // Cek apakah ada elemen setelahnya
                            if (nextBlock && nextBlock.classList.contains('question-block')) {
                                nextBlock.after(currentBlock);
                                currentBlock.scrollIntoView({ behavior: 'smooth', block: 'center' });
                                renumberQuestions();
                            }
                        }

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

                        } else if (e.target.closest('.btn-delete-question')) {
                            e.preventDefault();
                            const deleteBtn = e.target.closest('.btn-delete-question');
                            const questionBlock = deleteBtn.closest('.question-block');
                            const allQuestions = document.querySelectorAll('.question-block');

                            // 1. Cek jumlah pertanyaan
                            if (allQuestions.length <= 1) {
                                openAlertModal("Perhatian", "Minimal harus ada satu pertanyaan survei."); // GANTI ALERT DISINI
                                return;
                            }

                            const hiddenIdInput = questionBlock.querySelector('.hidden-id-pertanyaan');
                            const idPertanyaan = hiddenIdInput ? hiddenIdInput.value : '';

                            const runDeleteProcess = function () {
                                // Hapus Pertanyaan Baru
                                if (!idPertanyaan) {
                                    questionBlock.remove();
                                    renumberQuestions();
                                    return;
                                }

                                // Hapus Pertanyaan Lama (AJAX)
                                const csrfToken = document.querySelector('input[name="_token"]').value;
                                document.body.style.cursor = 'wait';

                                fetch(`/superadmin/skm/pertanyaan/${idPertanyaan}`, {
                                    method: 'DELETE',
                                    headers: {
                                        'X-CSRF-TOKEN': csrfToken,
                                        'Content-Type': 'application/json',
                                        'Accept': 'application/json'
                                    }
                                })
                                    .then(response => response.json())
                                    .then(data => {
                                        document.body.style.cursor = 'default';
                                        if (data.status === 'success') {
                                            questionBlock.remove();
                                            renumberQuestions();
                                        } else {
                                            // === DISINI KITA GANTI ALERT JADI MODAL KEREN ===
                                            openAlertModal("Gagal Menghapus", data.message);
                                        }
                                    })
                                    .catch(error => {
                                        document.body.style.cursor = 'default';
                                        console.error(error);
                                        openAlertModal("Error Sistem", "Terjadi kesalahan koneksi ke server.");
                                    });
                            };

                            // Panggil Modal Konfirmasi
                            openConfirmModal(runDeleteProcess);
                        } else if (addAnswerButton) {
                            e.preventDefault();
                            const currentOption = addAnswerButton.closest('.answer-option');
                            const newOption = currentOption.cloneNode(true);

                            // Bersihkan kloningan (Update bagian ini)
                            newOption.querySelector('.input-text').value = ''; // Reset Teks
                            newOption.querySelector('.input-score').value = ''; // Reset Nilai/Bobot

                            const hiddenPilihanId = newOption.querySelector('.hidden-id-pilihan');
                            if (hiddenPilihanId) hiddenPilihanId.value = '';

                            currentOption.after(newOption);
                            const optionsList = currentOption.closest('.answer-options-list');
                            renumberOptions(optionsList);

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
                            newOption.querySelector('.input-text').value = ''; // Reset teks
                            newOption.querySelector('.input-score').value = ''; // Reset nilai

                            const hiddenPilihanId = newOption.querySelector('.hidden-id-pilihan');
                            if (hiddenPilihanId) hiddenPilihanId.value = '';

                            optionsList.innerHTML = '';
                            optionsList.appendChild(newOption);
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
                                    // 1. Input Teks Jawaban
                                    const textInput = option.querySelector('.input-text');
                                    if (textInput) {
                                        textInput.name = `questions[${qIndex}][pilihan][${oIndex}][pilihan]`;
                                    }

                                    // 2. Input Nilai / Bobot (MANUAL DARI USER)
                                    const scoreInput = option.querySelector('.input-score');
                                    if (scoreInput) {
                                        scoreInput.name = `questions[${qIndex}][pilihan][${oIndex}][nilai]`;
                                    }

                                    // 3. ID Pilihan Hidden
                                    const hiddenIdPilihan = option.querySelector('.hidden-id-pilihan');
                                    if (hiddenIdPilihan) {
                                        hiddenIdPilihan.name = `questions[${qIndex}][pilihan][${oIndex}][id_pilihan]`;
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