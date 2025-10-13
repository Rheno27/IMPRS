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
        }

        /* Global Styles */
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Roboto', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-primary);
        }

        .page-container {
            max-width: 1440px;
            position: relative;
            overflow: hidden;
        }

        button,
        input,
        select,
        textarea {
            font-family: inherit;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        /* Header Styles */
        .site-header {
            display: flex;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1000;
            justify-content: space-between;
            align-items: center;
            background-color: var(--bg-color);
            border-bottom: 1px solid var(--primary-color);
            padding: 0 30px;
            height: 60px;
        }


        .logo-image {
            height: 50px;
            width: auto;
            object-fit: contain;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-avatar {
            height: 25px;
            width: 25px;
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
            height: 28px;
            width: 28px;
            border-radius: 8px;
            transition: background 0.2s;
        }

        .logout-link:hover {
            background: rgba(51, 115, 84, 0.1);
        }

        .logout-icon {
            height: 24px;
            width: 24px;
        }

        /* Main Form Section */
        .form-section {
            padding: 54px;
        }

        .form-wrapper {
            max-width: 1100px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            gap: 40px;
        }

        /* --- PERUBAHAN DI SINI --- */
        .form-card {
            width: 100%;
            /* border: 1px solid var(--primary-color); <-- BORDER DIHAPUS */
            border-radius: 20px;
            padding: 40px 54px;
        }

        /* --- AKHIR PERUBAHAN --- */

        .form-header {
            background-color: var(--primary-color);
            border-radius: 10px;
            padding: 14px;
            margin-bottom: 30px;
        }

        /* Selector untuk header pertama (Data Responden) agar tidak punya margin atas */
        .form-card>.form-header:first-of-type {
            margin-top: 10;
        }

        .form-title {
            color: var(--text-white);
            font-weight: 600;
            font-size: 18px;
            text-align: center;
            margin: 0;
        }

        /* Data Responden Styles (Tetap dengan border) */
        .respondent-form {
            border: 1px solid var(--primary-color);
            border-radius: 20px;
            padding: 28px;
            margin-bottom: 30px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 28px;
        }

        .form-column {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .form-group label {
            font-weight: 600;
            font-size: 16px;
            color: var(--text-primary);
        }

        .required-asterisk {
            color: var(--error-red);
            margin-left: 4px;
        }

        .input-field,
        .form-select {
            border: 1px solid var(--primary-dark);
            border-radius: 8px;
            padding: 10px;
            font-size: 14px;
            height: 42px;
            width: 100%;
        }

        .select-wrapper {
            position: relative;
        }

        .form-select {
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            background-color: var(--text-white);
            padding-right: 35px;
        }

        .select-arrow {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            pointer-events: none;
            width: 20px;
            height: 20px;
        }

        /* Survey Question Styles */
        .survey-form {
            display: flex;
            flex-direction: column;
            gap: 28px;
        }

        .question-block {
            border: none;
            padding: 0;
            margin: 0;
        }

        .question-text {
            display: block;
            width: 100%;
            border-bottom: 1px solid var(--primary-dark);
            padding-bottom: 12px;
            margin-bottom: 16px;
            color: var(--text-primary);
            font-weight: 700;
            font-size: 15px;
        }

        .options-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .option-item {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .option-item input[type="radio"] {
            accent-color: var(--primary-color);
            width: 18px;
            height: 18px;
            flex-shrink: 0;
        }

        .option-item label {
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
        }

        .feedback-input {
            width: 100%;
            min-height: 120px;
            padding: 12px 14px;
            font-size: 14px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            resize: vertical;
        }

        /* Navigation Buttons */
        .form-navigation {
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .nav-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 28px;
            border-radius: 8px;
            border: none;
            font-weight: 550;
            font-size: 16px;
            cursor: pointer;
            text-decoration: none;
            transition: background-color 0.2s, transform 0.1s;
        }

        .nav-button:active {
            transform: scale(0.97);
        }

        .prev-button {
            background-color: var(--btn-disabled-bg);
            color: var(--btn-disabled-text);
        }

        .submit-button {
            background-color: var(--btn-active-bg);
            color: var(--btn-active-text);
        }

        /* Responsive Styles */
        @media (max-width: 992px) {
            .form-section {
                padding: 32px;
            }

            .form-card {
                padding: 20px;
            }

            .form-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .site-header {
                flex-direction: column;
                height: auto;
                padding: 12px 20px;
                gap: 10px;
            }

            main {
                margin-top: 105px;
            }

            .form-section {
                padding: 24px 16px;
            }

            .form-card {
                padding: 0;
            }

            .respondent-form {
                padding: 24px 16px;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .form-group label,
            .question-text {
                font-size: 14px;
            }

            .input-field,
            .form-select,
            .option-item label {
                font-size: 13px;
            }

            .form-navigation {
                flex-direction: column-reverse;
                gap: 16px;
                align-items: stretch;
            }

            .nav-button {
                width: 100%;
            }
        }
    </style>
@endsection

@section('content')
    <main>
        <section class="form-section">
            {{-- Sesuaikan action dengan nama route yang baru --}}
            <form class="form-wrapper" method="POST" action="{{ route('guest.survei-1.store') }}">
                @csrf
                <div class="form-card">

                    <div class="form-header">
                        <h2 class="form-title">Data Responden</h2>
                    </div>
                    <div class="respondent-form">
                        <div class="form-grid">
                            {{-- KOLOM 1 --}}
                            <div class="form-column">
                                <div class="form-group">
                                    <label for="no_rm">Nomor Responden (No. RM)<span
                                            class="required-asterisk">*</span></label>
                                    <input type="text" id="no_rm" name="no_rm" class="input-field" required
                                        value="{{ old('no_rm') }}">
                                </div>
                                <div class="form-group">
                                    <label for="umur">Umur<span class="required-asterisk">*</span></label>
                                    <input type="number" id="umur" name="umur" class="input-field" required
                                        value="{{ old('umur') }}">
                                </div>
                            </div>
                            {{-- KOLOM 2 --}}
                            <div class="form-column">
                                <div class="form-group">
                                    <label for="id_ruangan">Ruangan<span class="required-asterisk">*</span></label>
                                    <div class="select-wrapper">
                                        <select id="id_ruangan" name="id_ruangan" class="form-select" required>
                                            <option value="">Pilih Ruangan</option>
                                            @foreach ($ruangan as $item)
                                                <option value="{{ $item->id_ruangan }}" {{ old('id_ruangan') == $item->id_ruangan ? 'selected' : '' }}>
                                                    {{ $item->nama_ruangan }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <img src="{{ asset('image/arrow-circle-down.svg') }}" class="select-arrow"
                                            alt="arrow">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="jenis_kelamin">Jenis Kelamin<span class="required-asterisk">*</span></label>
                                    <div class="select-wrapper">
                                        <select id="jenis_kelamin" name="jenis_kelamin" class="form-select" required>
                                            <option value="">Pilih Jenis Kelamin</option>
                                            <option value="Laki-laki" {{ old('jenis_kelamin') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                            <option value="Perempuan" {{ old('jenis_kelamin') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                        </select>
                                        <img src="{{ asset('image/arrow-circle-down.svg') }}" class="select-arrow"
                                            alt="arrow">
                                    </div>
                                </div>
                            </div>
                            {{-- KOLOM 3 --}}
                            <div class="form-column">
                                <div class="form-group">
                                    <label for="pendidikan">Pendidikan Terakhir<span
                                            class="required-asterisk">*</span></label>
                                    <div class="select-wrapper">
                                        <select id="pendidikan" name="pendidikan" class="form-select" required>
                                            <option value="">Pilih Pendidikan</option>
                                            <option value="Tidak Sekolah" {{ old('pendidikan') == 'Tidak Sekolah' ? 'selected' : '' }}>Tidak Sekolah</option>
                                            <option value="SD" {{ old('pendidikan') == 'SD' ? 'selected' : '' }}>SD</option>
                                            <option value="SMP" {{ old('pendidikan') == 'SMP' ? 'selected' : '' }}>SMP
                                            </option>
                                            <option value="SMA" {{ old('pendidikan') == 'SMA' ? 'selected' : '' }}>SMA
                                            </option>
                                            <option value="D3" {{ old('pendidikan') == 'D3' ? 'selected' : '' }}>D3</option>
                                            <option value="S1" {{ old('pendidikan') == 'S1' ? 'selected' : '' }}>S1</option>
                                            <option value="S2" {{ old('pendidikan') == 'S2' ? 'selected' : '' }}>S2</option>
                                        </select>
                                        <img src="{{ asset('image/arrow-circle-down.svg') }}" class="select-arrow"
                                            alt="arrow">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="pekerjaan">Pekerjaan Utama<span class="required-asterisk">*</span></label>
                                    <div class="select-wrapper">
                                        <select id="pekerjaan" name="pekerjaan" class="form-select" required>
                                            <option value="">Pilih Pekerjaan</option>
                                            <option value="PNS" {{ old('pekerjaan') == 'PNS' ? 'selected' : '' }}>PNS</option>
                                            <option value="Swasta" {{ old('pekerjaan') == 'Swasta' ? 'selected' : '' }}>Swasta
                                            </option>
                                            <option value="Wirausaha" {{ old('pekerjaan') == 'Wirausaha' ? 'selected' : '' }}>
                                                Wirausaha</option>
                                            <option value="Lainnya" {{ old('pekerjaan') == 'Lainnya' ? 'selected' : '' }}>
                                                Lainnya</option>
                                        </select>
                                        <img src="{{ asset('image/arrow-circle-down.svg') }}" class="select-arrow"
                                            alt="arrow">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-header">
                        <h2 class="form-title">Pelayanan Publik & Keselamatan Pasien</h2>
                    </div>
                    <div class="survey-form">
                        @foreach ($pertanyaan as $item)
                            <fieldset class="question-block">
                                <legend class="question-text">{{ $loop->iteration }}. {{ $item->pertanyaan }}</legend>
                                <div class="options-list">
                                    @if (isset($pilihanJawaban[$item->id_pertanyaan]))
                                        @foreach ($pilihanJawaban[$item->id_pertanyaan] as $pilihan)
                                            <div class="option-item">
                                                <input type="radio" id="pilihan_{{ $pilihan->id_pilihan }}"
                                                    name="jawaban[{{ $item->id_pertanyaan }}]" value="{{ $pilihan->id_pilihan }}"
                                                    required>
                                                <label for="pilihan_{{ $pilihan->id_pilihan }}">{{ $pilihan->pilihan }}</label>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </fieldset>
                        @endforeach
                    </div>

                    @if ($pertanyaanKritikSaran)
                        <div class="question-block" style="margin-top: 30px;">
                            <label for="feedback" class="question-text">{{ $pertanyaanKritikSaran->pertanyaan }}</label>
                            <textarea id="feedback" name="kritik_saran" class="feedback-input"
                                placeholder="Tuliskan masukan Anda di sini...">{{ old('kritik_saran') }}</textarea>
                        </div>
                    @endif
                </div>

                <div class="form-navigation">
                    {{-- Pastikan route ini benar sesuai file web.php kamu --}}
                    <a href="{{ route('guest.dashboard') }}" class="nav-button prev-button">
                        <span>Sebelumnya</span>
                    </a>
                    <button type="submit" class="nav-button submit-button">
                        <span>Kirim</span>
                    </button>
                </div>
            </form>
        </section>
    </main>
@endsection