@extends('layouts.app')

@section('styles')
    <style>
        :root {
            --primary-green: #337354;
            --dark-green: #2a7f54;
            --light-green-bg: #d6e3dd;
            --text-dark: #2d2d2d;
            --text-light: #ffffff;
            --border-color: #337354;
            --btn-edit-bg: #5f4c14;
            --btn-save-bg: #004e28;
            --btn-delete-bg: #791b00;
            --placeholder-text: #77a28d;
            --border-color-semitransparent: rgba(51, 115, 84, 0.5);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Roboto', 'Inter', sans-serif;
            background-color: #fcfcfc;
            color: var(--text-dark);
        }

        .page-container {
            max-width: 1440px;
            margin: 0 auto;
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
            font-family: inherit;
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
            background-color: var(--text-light);
            border-bottom: 1px solid var(--border-color-semitransparent);
            padding: 0 40px;
            height: 80px;
            /* lebih proporsional, jangan terlalu tinggi */
            box-sizing: border-box;
            /* biar padding gak nambah tinggi */
        }

        /* Logo */
        .logo-container {
            display: flex;
            align-items: center;
        }

        .logo-image {
            height: 70px;
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

        @media (max-width: 1024px) {
            .site-header {
                padding: 0 24px;
                height: 100px;
            }

            .brand-name {
                font-size: 24px;
            }

            .username {
                font-size: 20px;
            }

            .user-icon,
            .logout-icon {
                width: 32px;
                height: 32px;
            }
        }

        @media (max-width: 768px) {
            .site-header {
                flex-direction: column;
                height: auto;
                padding: 20px;
                gap: 20px;
            }

            .user-profile {
                width: 100%;
                justify-content: flex-end;
            }
        }



        .main-content {
            padding: 72px 52px;
        }

        .toolbar {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 36px;
            padding-top: 60px;
        }

        .add-button {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 10px 16px;
            border: 1px solid var(--primary-green);
            border-radius: 12px;
            background-color: transparent;
            font-size: 20px;
            font-weight: 550;
            color: var(--text-dark);
        }

        .add-button img {
            width: 45px;
            height: 45px;
        }

        .table-container {
            display: flex;
            flex-direction: column;
        }

        .table-title {
            background-color: var(--primary-green);
            color: var(--text-light);
            font-size: 25px;
            font-weight: 600;
            text-align: center;
            margin: 0;
            padding: 18px 10px;
            border-radius: 20px 20px 0 0;
        }

        .table-wrapper {
            overflow-x: auto;
        }

        .indicator-table {
            display: table;
            width: 100%;
            min-width: 1336px;
            border-collapse: collapse;
        }

        .table-header,
        .table-row {
            display: table-row;
        }

        .table-cell {
            display: table-cell;
            border: 1px solid var(--border-color);
            padding: 12px;
            vertical-align: middle;
            text-align: center;
            line-height: 20px;
            min-height: 56px;
        }

        .table-header .table-cell {
            background-color: var(--light-green-bg);
            font-weight: 600;
            font-size: 18px;
            padding: 18px;
        }

        .table-row .table-cell {
            background-color: var(--text-light);
            font-size: 17px;
            font-weight: 500;
            font-family: 'Inter', sans-serif;
        }

        /* Title Variabel center */
        .table-header .col-variable {
            text-align: center;
        }

        /* Isi Variabel tetap kiri */
        .table-row .col-variable {
            text-align: left;
            font-size: 17px;
        }

        /* Lebar kolom */
        .col-no {
            width: 45px;
        }

        .col-variable {
            width: 681px;
            text-align: left;
        }

        .col-type {
            width: 310px;
        }

        .col-actions {
            width: 300px;
            text-align: center;
        }

        /* Tombol dalam kolom actions */
        .col-actions button {
            margin: 4px;
            vertical-align: middle;
        }

        .action-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 500;
            border: none;
            cursor: pointer;
            transition: background-color 0.2s ease, transform 0.15s ease;
            color: #fff;
        }

        /* Edit */
        .btn-edit {
            background-color: var(--btn-edit-bg);
            /* #5f4c14 */
        }

        .btn-edit:hover {
            background-color: #806622;
            /* sedikit lebih terang */
            transform: translateY(-1px);
        }

        /* Simpan */
        .btn-save {
            background-color: var(--btn-save-bg);
            /* #004e28 */
        }

        .btn-save:hover {
            background-color: #016638;
            /* sedikit lebih terang */
            transform: translateY(-1px);
        }

        /* Hapus */
        .btn-delete {
            background-color: var(--btn-delete-bg);
            /* #791b00 */
        }

        .btn-delete:hover {
            background-color: #993000;
            /* sedikit lebih terang */
            transform: translateY(-1px);
        }


        .dropdown-select {
            width: 100%;
            padding: 10px 40px 10px 14px;
            border: 1px solid #337354;
            border-radius: 8px;
            background-color: #fff;
            font-size: 17px;
            font-family: 'Inter', sans-serif;
            font-weight: 500;
            color: #0A0D12;
            appearance: none;
            background-image: url("data:image/svg+xml;utf8,<svg width='20' height='20' viewBox='0 0 20 20' fill='none' xmlns='http://www.w3.org/2000/svg'><path d='M5 7.5L10 12.5L15 7.5' stroke='%23337354' stroke-width='1.66667' stroke-linecap='round' stroke-linejoin='round'/></svg>");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 20px;
            cursor: pointer;
        }

        .dropdown-select:hover {
            border-color: #285c42;
        }

        .dropdown-select:focus {
            outline: none;
            box-shadow: 0 0 0 2px rgba(51, 115, 84, 0.25);
        }

        @media (max-width: 768px) {
            .main-content {
                padding: 30px 15px;
            }

            .toolbar {
                margin-bottom: 20px;
            }

            .add-button {
                font-size: 18px;
                gap: 8px;
            }

            .add-button img {
                width: 30px;
                height: 30px;
            }

            .table-title {
                font-size: 20px;
                padding: 15px 10px;
            }
        }
    </style>
    <style>
        :root {
            --primary-green: #337354;
            --dark-green: #2a7f54;
            --light-green-bg: #d6e3dd;
            --text-dark: #2d2d2d;
            --text-light: #ffffff;
            --border-color: #337354;
        }

        body {
            font-family: 'Roboto', sans-serif;
            padding-top: 80px;
            background-color: #fcfcfc;
        }

        .main-content {
            padding: 48px 52px;
        }

        .toolbar {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 36px;
        }

        .add-button {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 10px 16px;
            border: 1px solid var(--primary-green);
            border-radius: 12px;
            background-color: transparent;
            font-size: 20px;
            font-weight: 550;
            color: var(--text-dark);
        }

        .table-container {
            display: flex;
            flex-direction: column;
        }

        .table-title {
            background-color: var(--primary-green);
            color: var(--text-light);
            font-size: 25px;
            font-weight: 600;
            text-align: center;
            margin: 0;
            padding: 18px 10px;
            border-radius: 20px 20px 0 0;
        }

        .table-wrapper {
            overflow-x: auto;
        }

        .indicator-table {
            display: table;
            width: 100%;
            min-width: 1200px;
            border-collapse: collapse;
        }

        .table-header,
        .table-row {
            display: table-row;
        }

        .table-cell {
            display: table-cell;
            border: 1px solid var(--border-color);
            padding: 12px;
            vertical-align: middle;
            text-align: center;
        }

        .table-header .table-cell {
            background-color: var(--light-green-bg);
            font-weight: 600;
            font-size: 18px;
            padding: 18px;
        }

        .table-row .table-cell {
            background-color: var(--text-light);
            font-size: 17px;
            font-weight: 500;
        }

        .col-variable {
            text-align: left;
        }

        .col-no {
            width: 4%;
        }

        .col-variable {
            width: 45%;
        }

        .col-type {
            width: 28%;
        }

        .col-actions {
            width: 23%;
        }

        .action-btn {
            color: white;
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 15px;
            border: none;
            margin: 0 4px;
            cursor: pointer;
        }

        .btn-warning {
            background-color: #ffc107;
        }

        .modal-header {
            background-color: var(--primary-green);
            color: white;
        }
    </style>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
@endsection

@section('content')
    <main id="section-main" class="main-content">

        @if (session('success'))
        <div class="alert alert-success mx-4"> {{ session('success') }} </div>
        @endif
        <div class="toolbar">
            <button class="add-button" type="button" data-bs-toggle="modal" data-bs-target="#addIndicatorModal">
                <svg width="45" height="45" viewBox="0 0 45 45" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M14.6436 4.25H30.3564C33.6695 4.25004 36.2539 5.23607 38.0068 6.99121C39.7594 8.74627 40.7405 11.3301 40.7314 14.6426V30.3564C40.7314 33.6694 39.7456 36.2534 37.9902 38.0088C36.2349 39.7642 33.6508 40.7499 30.3379 40.75H14.6436C11.3306 40.75 8.74655 39.7644 6.99121 38.0068C5.23585 36.2493 4.25008 33.6605 4.25 30.3379V14.6436C4.25004 11.3306 5.23581 8.74661 6.99121 6.99121C8.63696 5.34547 11.011 4.37641 14.0312 4.26172L14.6436 4.25Z"
                        stroke="#337354" />
                    <path
                        d="M33.75 21.0938H23.9062V11.25C23.9062 10.4812 23.2688 9.84375 22.5 9.84375C21.7312 9.84375 21.0938 10.4812 21.0938 11.25V21.0938H11.25C10.4812 21.0938 9.84375 21.7312 9.84375 22.5C9.84375 23.2688 10.4812 23.9062 11.25 23.9062H21.0938V33.75C21.0938 34.5187 21.7312 35.1562 22.5 35.1562C23.2688 35.1562 23.9062 34.5187 23.9062 33.75V23.9062H33.75C34.5187 23.9062 35.1562 23.2688 35.1562 22.5C35.1562 21.7312 34.5187 21.0938 33.75 21.0938Z"
                        fill="#337354" />
                </svg>
                <span>Tambah Indikator Baru</span>
            </button>
        </div>

        <div class="table-container">
            <h2 class="table-title">Atur Indikator Aktif di Ruang {{ $ruangan->nama_ruangan }}</h2>
            <div class="table-wrapper">
                <div class="indicator-table">
                    <div class="table-header">
                        <div class="table-cell col-no">No.</div>
                        <div class="table-cell col-variable">Variabel Penilaian</div>
                        <div class="table-cell col-type">Jenis Indikator Mutu</div>
                        <div class="table-cell col-actions">Aksi</div>
                    </div>

                    @if ($activeIndikators->isNotEmpty())
                        @foreach ($activeIndikators as $item)
                            <div class="table-row">
                                <div class="table-cell col-no">{{ $loop->iteration }}.</div>
                                <div class="table-cell col-variable">{{ $item->indikatorMutu->variabel }}</div>
                                <div class="table-cell col-type">
                                    {{ $item->indikatorMutu->kategori->kategori ?? 'N/A' }}
                                </div>
                                <div class="table-cell col-actions">
                                    <button type="button" class="action-btn btn-warning text-dark" data-bs-toggle="modal"
                                        data-bs-target="#gantiModal-{{ $item->id_indikator_ruangan }}">
                                        Ganti
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
            @if ($activeIndikators->isEmpty())
                <div class="alert alert-danger mt-3" role="alert">
                    Tidak ada indikator aktif untuk ruangan ini.
                </div>
            @endif
        </div>
    </main>

    {{-- PERULANGAN KEDUA: UNTUK MODAL --}}
    @foreach ($activeIndikators as $item)
        <div class="modal fade" id="gantiModal-{{ $item->id_indikator_ruangan }}" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Ganti Indikator</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    {{-- Form untuk mengganti indikator --}}
                    <form action="{{ route('superadmin.ruangan.update_indikator') }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <p><strong>Indikator saat ini:</strong><br>{{ $item->indikatorMutu->variabel }}</p>

                            <input type="hidden" name="id_ruangan" value="{{ $ruangan->id_ruangan }}">
                            <input type="hidden" name="id_indikator_ruangan_lama" value="{{ $item->id_indikator_ruangan }}">

                            {{-- DROPDOWN PERTAMA: KATEGORI --}}
                            <div class="mb-3">
                                <label class="form-label"><strong>1. Pilih Jenis Indikator (Kategori)</strong></label>
                                {{-- Class 'category-select' digunakan oleh JavaScript --}}
                                <select class="form-select category-select"
                                    data-target-indicator-select="#indicator-select-{{ $item->id_indikator_ruangan }}" required>
                                    <option value="" selected>-- Pilih Kategori --</option>
                                    @foreach ($allKategoris as $kategori)
                                        <option value="{{ $kategori->id_kategori }}">{{ $kategori->kategori }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- DROPDOWN KEDUA: INDIKATOR BARU --}}
                            <div class="mb-3">
                                <label for="indicator-select-{{ $item->id_indikator_ruangan }}" class="form-label"><strong>2.
                                        Pilih Indikator Baru</strong></label>
                                <select name="id_indikator_baru" id="indicator-select-{{ $item->id_indikator_ruangan }}"
                                    class="form-select" required disabled>
                                    <option value="" selected>-- Pilih Kategori terlebih dahulu --</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan Penggantian</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
    {{-- MODAL BARU UNTUK TAMBAH INDIKATOR --}}
    <div class="modal fade" id="addIndicatorModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Indikator Baru ke Ruang {{ $ruangan->nama_ruangan }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('superadmin.ruangan.add_indikator') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="id_ruangan" value="{{ $ruangan->id_ruangan }}">

                        <div class="mb-3">
                            <label class="form-label"><strong>1. Pilih Jenis Indikator (Kategori)</strong></label>
                            <select class="form-select category-select" data-target-indicator-select="#new-indicator-select"
                                required>
                                <option value="" selected>-- Pilih Kategori --</option>
                                @foreach ($allKategoris as $kategori)
                                    <option value="{{ $kategori->id_kategori }}">{{ $kategori->kategori }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="new-indicator-select" class="form-label"><strong>2. Pilih Indikator
                                    Baru</strong></label>
                            <select name="id_indikator_baru" id="new-indicator-select" class="form-select" required
                                disabled>
                                <option value="" selected>-- Pilih Kategori terlebih dahulu --</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Tambahkan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- Kita tetap butuh JS Bootstrap untuk fungsionalitas modal --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // 1. Ambil semua data indikator master dari PHP dan simpan di variabel JS
            const allMasterIndikators = @json($allMasterIndikators);

            // 2. Cari semua dropdown kategori di halaman
            const categorySelects = document.querySelectorAll('.category-select');

            // 3. Untuk setiap dropdown kategori, tambahkan "event listener"
            categorySelects.forEach(select => {
                select.addEventListener('change', function (event) {
                    const selectedCategoryId = event.target.value;
                    const targetIndicatorSelectId = event.target.dataset.targetIndicatorSelect;
                    const indicatorSelect = document.querySelector(targetIndicatorSelectId);

                    // Selalu kosongkan dropdown kedua saat pilihan pertama berubah
                    indicatorSelect.innerHTML = '';
                    indicatorSelect.disabled = true;

                    if (selectedCategoryId) {
                        // Jika kategori dipilih, filter data indikator
                        const filteredIndikators = allMasterIndikators.filter(indicator =>
                            indicator.id_kategori == selectedCategoryId
                        );

                        let placeholder = document.createElement('option');
                        placeholder.value = "";
                        placeholder.textContent = "-- Pilih Indikator Baru --";
                        placeholder.disabled = true;
                        placeholder.selected = true;
                        indicatorSelect.appendChild(placeholder);

                        if (filteredIndikators.length > 0) {
                            // Jika ada indikator di kategori ini, isi dropdown kedua
                            filteredIndikators.forEach(indicator => {
                                let option = document.createElement('option');
                                option.value = indicator.id_indikator;
                                option.textContent = indicator.variabel;
                                indicatorSelect.appendChild(option);
                            });
                            indicatorSelect.disabled = false; // Aktifkan dropdown kedua
                        } else {
                            // Jika tidak ada
                            placeholder.textContent = "-- Tidak ada indikator di kategori ini --";
                        }
                    } else {
                        // Jika pilihan kategori dikosongkan
                        let placeholder = document.createElement('option');
                        placeholder.value = "";
                        placeholder.textContent = "-- Pilih Kategori terlebih dahulu --";
                        indicatorSelect.appendChild(placeholder);
                    }
                });
            });
        });
    </script>
@endpush