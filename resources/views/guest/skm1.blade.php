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
            --btn-active-bg: #ffbb00;
            --btn-active-text: #6f5405;
            --btn-disabled-bg: #eae8e8;
            --btn-disabled-text: rgba(0, 0, 0, 0.25);
            --error-red: #f90606;
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
            border: 1px solid var(--primary-color);
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
    
        /* CSS from section:header */
        .site-header {
            display: flex;
            position: fixed;
            top: 0;
            /* penting biar nempel di atas */
            left: 0;
            width: 100%;
            z-index: 1000;
            justify-content: space-between;
            align-items: center;
            background-color: var(--bg-color);
            border-bottom: 1px solid var(--primary-color);
            padding: 0 40px;
            height: 80px;
            /* lebih proporsional, jangan terlalu tinggi */
            box-sizing: border-box;
            /* biar padding gak nambah tinggi */
        }
    
        main {
            margin-top: 70px;
            /* samain dengan tinggi header */
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
    
        /* CSS from section:form */
        .form-section {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 72px;
            background-color: var(--bg-color);
        }
    
        .form-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 52px;
            width: 100%;
            max-width: 1296px;
        }
    
        .form-card {
            width: 100%;
            border: 1px solid var(--primary-color);
            border-radius: 25px;
            padding: 72px;
            display: flex;
            flex-direction: column;
            gap: 30px;
        }
    
        .form-header {
            background-color: var(--primary-color);
            border-radius: 10px;
            padding: 16px;
        }
    
        .form-title {
            color: var(--text-white);
            font-family: 'Roboto', sans-serif;
            font-weight: 600;
            font-size: 28px;
            line-height: 36px;
            text-align: center;
            margin: 0;
        }
    
        .respondent-form {
            border: 1px solid var(--primary-color);
            border-radius: 25px;
            padding: 36px;
        }
    
        .form-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 36px;
            /* Adjusted from large gap */
        }
    
        .form-column {
            display: flex;
            flex-direction: column;
            gap: 25px;
        }
    
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
    
        .form-group label {
            font-family: 'Roboto', sans-serif;
            font-weight: 600;
            font-size: 18px;
            line-height: 20px;
            color: var(--text-primary);
            display: flex;
            align-items: center;
        }
    
        .required-asterisk {
            color: var(--error-red);
            margin-left: 4px;
        }
    
        .input-field,
        .select-field {
            border: 1px solid var(--primary-dark);
            border-radius: 10px;
            padding: 10px;
            font-family: 'Inter', sans-serif;
            font-size: 14px;
            line-height: 20px;
            color: var(--text-placeholder);
            height: 40px;
            display: flex;
            align-items: center;
        }
    
        .select-arrow {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            pointer-events: none;
        }
    
        .select-wrapper {
            position: relative;
            width: 100%;
            max-width: 876px;
        }
    
        .select-field {
            justify-content: space-between;
            cursor: pointer;
        }
    
        .select-field img {
            width: 20px;
            height: 20px;
        }
    
        .form-navigation {
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
    
        .form-select {
            width: 100%;
            border: 1px solid var(--primary-color);
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 16px;
            background-color: var(--text-white);
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
    
        .nav-button {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 12px 24px;
            border-radius: 8px;
            border: none;
            font-family: 'Roboto', sans-serif;
            font-weight: 500;
            font-size: 18px;
            line-height: 20px;
            cursor: pointer;
            transition: background-color 0.2s, transform 0.1s;
        }
    
        .nav-button:hover {
            background-color: rgba(0, 0, 0, 0.05);
            /* efek hover lembut */
        }
    
        .nav-button:active {
            transform: scale(0.97);
            /* efek klik */
        }
    
        .prev-button {
            background-color: var(--btn-disabled-bg);
            color: var(--btn-disabled-text);
        }
    
        .prev-button img {
            transform: rotate(-90deg);
            width: 30px;
            height: 30px;
        }
    
        .next-button {
            background-color: var(--btn-active-bg);
            color: var(--btn-active-text);
        }
    
        .next-button img {
            transform: rotate(90deg);
            width: 30px;
            height: 30px;
        }
    
        .nav-button:disabled {
            cursor: not-allowed;
        }
    
        @media (max-width: 1200px) {
            .form-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    
        @media (max-width: 768px) {
            .form-section {
                padding: 24px;
            }
    
            .form-card {
                padding: 24px;
            }
    
            .respondent-form {
                padding: 20px;
            }
    
            .form-grid {
                grid-template-columns: 1fr;
                gap: 25px;
            }
    
            .form-navigation {
                flex-direction: column-reverse;
                gap: 20px;
                align-items: stretch;
            }
    
            .nav-button {
                justify-content: center;
            }
        }
    </style>
@endsection

@section('content')
    <main id="section-form" class="form-section">
        <div class="form-wrapper">
            <div class="form-card">
                <div class="form-header">
                    <h2 class="form-title">Data Responden</h2>
                </div>
                <form class="respondent-form">
                    <div class="form-grid">
                        <div class="form-column">
                            <div class="form-group">
                                <label for="nomor-responden">Nomor Responden<span class="required-asterisk">*</span></label>
                                <input type="text" class="input-field" value="12345678">
                            </div>
                            <div class="form-group">
                                <label for="umur">Umur<span class="required-asterisk">*</span></label>
                                <input type="text" class="input-field" value="12345678">
                            </div>
                        </div>
                        <div class="form-column">
                            <div class="form-group">
                                <label for="ruangan">Ruangan<span class="required-asterisk">*</span></label>
                                <div class="select-wrapper">
                                    <select id="q1-type" class="form-select">
                                        <option>Pilihan Ganda</option>
                                    </select>
                                    <img src="{{ asset('image/arrow-circle-down.svg') }}" class="select-arrow" alt="dropdown arrow">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="jenis-kelamin">Jenis Kelamin<span class="required-asterisk">*</span></label>
                                <div class="select-wrapper">
                                    <select id="q1-type" class="form-select">
                                        <option>Pilihan Ganda</option>
                                    </select>
                                    <img src="{{ asset('image/arrow-circle-down.svg') }}" class="select-arrow" alt="dropdown arrow">
                                </div>
                            </div>
                        </div>
                        <div class="form-column">
                            <div class="form-group">
                                <label for="pendidikan">Pendidikan Terakhir<span class="required-asterisk">*</span></label>
                                <div class="select-wrapper">
                                    <select id="q1-type" class="form-select">
                                        <option>Tidak Sekolah</option>
                                        <option>TK</option>
                                    </select>
                                    <img src="{{ asset('image/arrow-circle-down.svg') }}" class="select-arrow" alt="dropdown arrow">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="pekerjaan">Pekerjaan Utama<span class="required-asterisk">*</span></label>
                                <div class="select-wrapper">
                                    <select id="q1-type" class="form-select">
                                        <option>Pilihan Ganda</option>
                                    </select>
                                    <img src="{{ asset('image/arrow-circle-down.svg') }}" class="select-arrow" alt="dropdown arrow">
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="form-navigation">
                <button class="nav-button prev-button" onclick="window.Location.href='/SKM/data_responden'">
                    <span>Sebelumnya</span>
                </button>
                <button class="nav-button next-button" onclick="window.location.href='/SKM/survei-2'">
                    <span>Selanjutnya</span>
                </button>
            </div>
        </div>
    </main>
@endsection