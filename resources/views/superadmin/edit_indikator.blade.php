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
@endsection

@section('content')
    <main id="section-main" class="main-content">
        <div class="toolbar">
            <button class="add-button">
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
            <h2 class="table-title">Penilaian Indikator Mutu di Ruang Nifas</h2>
            <div class="table-wrapper">
                <div class="indicator-table">
                    <div class="table-header">
                        <div class="table-cell col-no">No.</div>
                        <div class="table-cell col-variable">Variabel Penilaian</div>
                        <div class="table-cell col-type">Jenis Indikator Mutu</div>
                        <div class="table-cell col-actions">Perbarui Data</div>
                    </div>

                    <div class="table-row">
                        <div class="table-cell col-no">1.</div>
                        <div class="table-cell col-variable">Kejadian retensi urin pada pasien postpartum</div>
                        <div class="table-cell col-type">
                            <select class="dropdown-select">
                                <option value="IMPU" selected>Indikator Mutu Prioritas Unit (IMPU)</option>
                                <option value="IMPRS">Indikator Mutu Prioritas RS (IMPRS)</option>
                                <option value="INM">Indikator Nasional Mutu (INM)</option>
                            </select>
                        </div>
                        <div class="table-cell col-actions">
                            <button class="action-btn btn-edit">
                                <svg width="21" height="20" viewBox="0 0 21 20" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M18 18.3333H3C2.65833 18.3333 2.375 18.0499 2.375 17.7083C2.375 17.3666 2.65833 17.0833 3 17.0833H18C18.3417 17.0833 18.625 17.3666 18.625 17.7083C18.625 18.0499 18.3417 18.3333 18 18.3333Z"
                                        fill="#DC5E3A" />
                                    <path
                                        d="M16.3495 2.90005C14.7328 1.28338 13.1495 1.24172 11.4912 2.90005L10.4828 3.90838C10.3995 3.99172 10.3662 4.12505 10.3995 4.24172C11.0328 6.45005 12.7995 8.21672 15.0078 8.85005C15.0412 8.85838 15.0745 8.86672 15.1078 8.86672C15.1995 8.86672 15.2828 8.83338 15.3495 8.76672L16.3495 7.75838C17.1745 6.94172 17.5745 6.15005 17.5745 5.35005C17.5828 4.52505 17.1828 3.72505 16.3495 2.90005Z"
                                        fill="#DC5E3A" />
                                    <path
                                        d="M13.5089 9.60841C13.2673 9.49175 13.0339 9.37508 12.8089 9.24175C12.6256 9.13341 12.4506 9.01675 12.2756 8.89175C12.1339 8.80008 11.9673 8.66675 11.8089 8.53341C11.7923 8.52508 11.7339 8.47508 11.6673 8.40841C11.3923 8.17508 11.0839 7.87508 10.8089 7.54175C10.7839 7.52508 10.7423 7.46675 10.6839 7.39175C10.6006 7.29175 10.4589 7.12508 10.3339 6.93341C10.2339 6.80841 10.1173 6.62508 10.0089 6.44175C9.87559 6.21675 9.75892 5.99175 9.64226 5.75841C9.52559 5.50841 9.43392 5.26675 9.35059 5.04175L4.11726 10.2751C4.00892 10.3834 3.90892 10.5917 3.88392 10.7334L3.43392 13.9251C3.35059 14.4917 3.50892 15.0251 3.85892 15.3834C4.15892 15.6751 4.57559 15.8334 5.02559 15.8334C5.12559 15.8334 5.22559 15.8251 5.32559 15.8084L8.52559 15.3584C8.67559 15.3334 8.88392 15.2334 8.98392 15.1251L14.2173 9.89175C13.9839 9.80841 13.7589 9.71675 13.5089 9.60841Z"
                                        fill="#FFC107" />
                                </svg>
                                Edit
                            </button>
                            <button class="action-btn btn-save">
                                <svg width="21" height="20" viewBox="0 0 21 20" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M13.8327 7.49156V16.9582C13.8327 18.1666 12.966 18.6749 11.9077 18.0916L8.63269 16.2666C8.28269 16.0749 7.71601 16.0749 7.36601 16.2666L4.09101 18.0916C3.03268 18.6749 2.16602 18.1666 2.16602 16.9582V7.49156C2.16602 6.06656 3.33267 4.8999 4.75767 4.8999H11.241C12.666 4.8999 13.8327 6.06656 13.8327 7.49156Z"
                                        fill="#FFC107" />
                                    <path
                                        d="M18.8327 4.2584V13.7251C18.8327 14.9334 17.966 15.4417 16.9077 14.8584L13.8327 13.1417V7.49174C13.8327 6.06674 12.666 4.90009 11.241 4.90009H7.16602V4.2584C7.16602 2.8334 8.33267 1.66675 9.75767 1.66675H16.241C17.666 1.66675 18.8327 2.8334 18.8327 4.2584Z"
                                        fill="#337354" />
                                    <path
                                        d="M9.66732 9.37492H8.62565V8.33325C8.62565 7.99159 8.34232 7.70825 8.00065 7.70825C7.65898 7.70825 7.37565 7.99159 7.37565 8.33325V9.37492H6.33398C5.99232 9.37492 5.70898 9.65825 5.70898 9.99992C5.70898 10.3416 5.99232 10.6249 6.33398 10.6249H7.37565V11.6666C7.37565 12.0083 7.65898 12.2916 8.00065 12.2916C8.34232 12.2916 8.62565 12.0083 8.62565 11.6666V10.6249H9.66732C10.009 10.6249 10.2923 10.3416 10.2923 9.99992C10.2923 9.65825 10.009 9.37492 9.66732 9.37492Z"
                                        fill="#337354" />
                                </svg>
                                Simpan
                            </button>
                            <button class="action-btn btn-delete">
                                <svg width="21" height="20" viewBox="0 0 21 20" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M18.0574 4.35842C16.7157 4.22508 15.3741 4.12508 14.0241 4.05008V4.04175L13.8407 2.95841C13.7157 2.19175 13.5324 1.04175 11.5824 1.04175H9.39907C7.45741 1.04175 7.27407 2.14175 7.14074 2.95008L6.96574 4.01675C6.19074 4.06675 5.41574 4.11675 4.64074 4.19175L2.94074 4.35842C2.59074 4.39175 2.34074 4.70008 2.37407 5.04175C2.40741 5.38342 2.70741 5.63342 3.05741 5.60008L4.75741 5.43342C9.12407 5.00008 13.5241 5.16675 17.9407 5.60842C17.9657 5.60842 17.9824 5.60842 18.0074 5.60842C18.3241 5.60842 18.5991 5.36675 18.6324 5.04175C18.6574 4.70008 18.4074 4.39175 18.0574 4.35842Z"
                                        fill="#FFC107" />
                                    <path
                                        d="M16.5245 6.78325C16.3245 6.57492 16.0495 6.45825 15.7661 6.45825H5.2328C4.94947 6.45825 4.66613 6.57492 4.47447 6.78325C4.2828 6.99159 4.17447 7.27492 4.19113 7.56659L4.7078 16.1166C4.79947 17.3833 4.91613 18.9666 7.82447 18.9666H13.1745C16.0828 18.9666 16.1995 17.3916 16.2911 16.1166L16.8078 7.57492C16.8245 7.27492 16.7161 6.99159 16.5245 6.78325Z"
                                        fill="#DC5E3A" />
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M8.48242 14.1667C8.48242 13.8216 8.76224 13.5417 9.10742 13.5417H11.8824C12.2276 13.5417 12.5074 13.8216 12.5074 14.1667C12.5074 14.5119 12.2276 14.7917 11.8824 14.7917H9.10742C8.76224 14.7917 8.48242 14.5119 8.48242 14.1667Z"
                                        fill="#FFC107" />
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M7.79102 10.8333C7.79102 10.4881 8.07084 10.2083 8.41602 10.2083H12.5827C12.9279 10.2083 13.2077 10.4881 13.2077 10.8333C13.2077 11.1784 12.9279 11.4583 12.5827 11.4583H8.41602C8.07084 11.4583 7.79102 11.1784 7.79102 10.8333Z"
                                        fill="#FFC107" />
                                </svg>
                                Hapus
                            </button>
                        </div>
                    </div>

                    <div class="table-row">
                        <div class="table-cell col-no">1.</div>
                        <div class="table-cell col-variable">Kejadian retensi urin pada pasien postpartum</div>
                        <div class="table-cell col-type">
                            <select class="dropdown-select">
                                <option value="IMPU" selected>Indikator Mutu Prioritas Unit (IMPU)</option>
                                <option value="IMPRS">Indikator Mutu Prioritas RS (IMPRS)</option>
                                <option value="INM">Indikator Nasional Mutu (INM)</option>
                            </select>
                        </div>
                        <div class="table-cell col-actions">
                            <button class="action-btn btn-edit">
                                <svg width="21" height="20" viewBox="0 0 21 20" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M18 18.3333H3C2.65833 18.3333 2.375 18.0499 2.375 17.7083C2.375 17.3666 2.65833 17.0833 3 17.0833H18C18.3417 17.0833 18.625 17.3666 18.625 17.7083C18.625 18.0499 18.3417 18.3333 18 18.3333Z"
                                        fill="#DC5E3A" />
                                    <path
                                        d="M16.3495 2.90005C14.7328 1.28338 13.1495 1.24172 11.4912 2.90005L10.4828 3.90838C10.3995 3.99172 10.3662 4.12505 10.3995 4.24172C11.0328 6.45005 12.7995 8.21672 15.0078 8.85005C15.0412 8.85838 15.0745 8.86672 15.1078 8.86672C15.1995 8.86672 15.2828 8.83338 15.3495 8.76672L16.3495 7.75838C17.1745 6.94172 17.5745 6.15005 17.5745 5.35005C17.5828 4.52505 17.1828 3.72505 16.3495 2.90005Z"
                                        fill="#DC5E3A" />
                                    <path
                                        d="M13.5089 9.60841C13.2673 9.49175 13.0339 9.37508 12.8089 9.24175C12.6256 9.13341 12.4506 9.01675 12.2756 8.89175C12.1339 8.80008 11.9673 8.66675 11.8089 8.53341C11.7923 8.52508 11.7339 8.47508 11.6673 8.40841C11.3923 8.17508 11.0839 7.87508 10.8089 7.54175C10.7839 7.52508 10.7423 7.46675 10.6839 7.39175C10.6006 7.29175 10.4589 7.12508 10.3339 6.93341C10.2339 6.80841 10.1173 6.62508 10.0089 6.44175C9.87559 6.21675 9.75892 5.99175 9.64226 5.75841C9.52559 5.50841 9.43392 5.26675 9.35059 5.04175L4.11726 10.2751C4.00892 10.3834 3.90892 10.5917 3.88392 10.7334L3.43392 13.9251C3.35059 14.4917 3.50892 15.0251 3.85892 15.3834C4.15892 15.6751 4.57559 15.8334 5.02559 15.8334C5.12559 15.8334 5.22559 15.8251 5.32559 15.8084L8.52559 15.3584C8.67559 15.3334 8.88392 15.2334 8.98392 15.1251L14.2173 9.89175C13.9839 9.80841 13.7589 9.71675 13.5089 9.60841Z"
                                        fill="#FFC107" />
                                </svg>
                                Edit
                            </button>
                            <button class="action-btn btn-save">
                                <svg width="21" height="20" viewBox="0 0 21 20" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M13.8327 7.49156V16.9582C13.8327 18.1666 12.966 18.6749 11.9077 18.0916L8.63269 16.2666C8.28269 16.0749 7.71601 16.0749 7.36601 16.2666L4.09101 18.0916C3.03268 18.6749 2.16602 18.1666 2.16602 16.9582V7.49156C2.16602 6.06656 3.33267 4.8999 4.75767 4.8999H11.241C12.666 4.8999 13.8327 6.06656 13.8327 7.49156Z"
                                        fill="#FFC107" />
                                    <path
                                        d="M18.8327 4.2584V13.7251C18.8327 14.9334 17.966 15.4417 16.9077 14.8584L13.8327 13.1417V7.49174C13.8327 6.06674 12.666 4.90009 11.241 4.90009H7.16602V4.2584C7.16602 2.8334 8.33267 1.66675 9.75767 1.66675H16.241C17.666 1.66675 18.8327 2.8334 18.8327 4.2584Z"
                                        fill="#337354" />
                                    <path
                                        d="M9.66732 9.37492H8.62565V8.33325C8.62565 7.99159 8.34232 7.70825 8.00065 7.70825C7.65898 7.70825 7.37565 7.99159 7.37565 8.33325V9.37492H6.33398C5.99232 9.37492 5.70898 9.65825 5.70898 9.99992C5.70898 10.3416 5.99232 10.6249 6.33398 10.6249H7.37565V11.6666C7.37565 12.0083 7.65898 12.2916 8.00065 12.2916C8.34232 12.2916 8.62565 12.0083 8.62565 11.6666V10.6249H9.66732C10.009 10.6249 10.2923 10.3416 10.2923 9.99992C10.2923 9.65825 10.009 9.37492 9.66732 9.37492Z"
                                        fill="#337354" />
                                </svg>
                                Simpan
                            </button>
                            <button class="action-btn btn-delete">
                                <svg width="21" height="20" viewBox="0 0 21 20" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M18.0574 4.35842C16.7157 4.22508 15.3741 4.12508 14.0241 4.05008V4.04175L13.8407 2.95841C13.7157 2.19175 13.5324 1.04175 11.5824 1.04175H9.39907C7.45741 1.04175 7.27407 2.14175 7.14074 2.95008L6.96574 4.01675C6.19074 4.06675 5.41574 4.11675 4.64074 4.19175L2.94074 4.35842C2.59074 4.39175 2.34074 4.70008 2.37407 5.04175C2.40741 5.38342 2.70741 5.63342 3.05741 5.60008L4.75741 5.43342C9.12407 5.00008 13.5241 5.16675 17.9407 5.60842C17.9657 5.60842 17.9824 5.60842 18.0074 5.60842C18.3241 5.60842 18.5991 5.36675 18.6324 5.04175C18.6574 4.70008 18.4074 4.39175 18.0574 4.35842Z"
                                        fill="#FFC107" />
                                    <path
                                        d="M16.5245 6.78325C16.3245 6.57492 16.0495 6.45825 15.7661 6.45825H5.2328C4.94947 6.45825 4.66613 6.57492 4.47447 6.78325C4.2828 6.99159 4.17447 7.27492 4.19113 7.56659L4.7078 16.1166C4.79947 17.3833 4.91613 18.9666 7.82447 18.9666H13.1745C16.0828 18.9666 16.1995 17.3916 16.2911 16.1166L16.8078 7.57492C16.8245 7.27492 16.7161 6.99159 16.5245 6.78325Z"
                                        fill="#DC5E3A" />
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M8.48242 14.1667C8.48242 13.8216 8.76224 13.5417 9.10742 13.5417H11.8824C12.2276 13.5417 12.5074 13.8216 12.5074 14.1667C12.5074 14.5119 12.2276 14.7917 11.8824 14.7917H9.10742C8.76224 14.7917 8.48242 14.5119 8.48242 14.1667Z"
                                        fill="#FFC107" />
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M7.79102 10.8333C7.79102 10.4881 8.07084 10.2083 8.41602 10.2083H12.5827C12.9279 10.2083 13.2077 10.4881 13.2077 10.8333C13.2077 11.1784 12.9279 11.4583 12.5827 11.4583H8.41602C8.07084 11.4583 7.79102 11.1784 7.79102 10.8333Z"
                                        fill="#FFC107" />
                                </svg>
                                Hapus
                            </button>
                        </div>
                    </div>

                    <div class="table-row">
                        <div class="table-cell col-no">1.</div>
                        <div class="table-cell col-variable">Kejadian retensi urin pada pasien postpartum</div>
                        <div class="table-cell col-type">
                            <select class="dropdown-select">
                                <option value="IMPU" selected>Indikator Mutu Prioritas Unit (IMPU)</option>
                                <option value="IMPRS">Indikator Mutu Prioritas RS (IMPRS)</option>
                                <option value="INM">Indikator Nasional Mutu (INM)</option>
                            </select>
                        </div>
                        <div class="table-cell col-actions">
                            <button class="action-btn btn-edit">
                                <svg width="21" height="20" viewBox="0 0 21 20" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M18 18.3333H3C2.65833 18.3333 2.375 18.0499 2.375 17.7083C2.375 17.3666 2.65833 17.0833 3 17.0833H18C18.3417 17.0833 18.625 17.3666 18.625 17.7083C18.625 18.0499 18.3417 18.3333 18 18.3333Z"
                                        fill="#DC5E3A" />
                                    <path
                                        d="M16.3495 2.90005C14.7328 1.28338 13.1495 1.24172 11.4912 2.90005L10.4828 3.90838C10.3995 3.99172 10.3662 4.12505 10.3995 4.24172C11.0328 6.45005 12.7995 8.21672 15.0078 8.85005C15.0412 8.85838 15.0745 8.86672 15.1078 8.86672C15.1995 8.86672 15.2828 8.83338 15.3495 8.76672L16.3495 7.75838C17.1745 6.94172 17.5745 6.15005 17.5745 5.35005C17.5828 4.52505 17.1828 3.72505 16.3495 2.90005Z"
                                        fill="#DC5E3A" />
                                    <path
                                        d="M13.5089 9.60841C13.2673 9.49175 13.0339 9.37508 12.8089 9.24175C12.6256 9.13341 12.4506 9.01675 12.2756 8.89175C12.1339 8.80008 11.9673 8.66675 11.8089 8.53341C11.7923 8.52508 11.7339 8.47508 11.6673 8.40841C11.3923 8.17508 11.0839 7.87508 10.8089 7.54175C10.7839 7.52508 10.7423 7.46675 10.6839 7.39175C10.6006 7.29175 10.4589 7.12508 10.3339 6.93341C10.2339 6.80841 10.1173 6.62508 10.0089 6.44175C9.87559 6.21675 9.75892 5.99175 9.64226 5.75841C9.52559 5.50841 9.43392 5.26675 9.35059 5.04175L4.11726 10.2751C4.00892 10.3834 3.90892 10.5917 3.88392 10.7334L3.43392 13.9251C3.35059 14.4917 3.50892 15.0251 3.85892 15.3834C4.15892 15.6751 4.57559 15.8334 5.02559 15.8334C5.12559 15.8334 5.22559 15.8251 5.32559 15.8084L8.52559 15.3584C8.67559 15.3334 8.88392 15.2334 8.98392 15.1251L14.2173 9.89175C13.9839 9.80841 13.7589 9.71675 13.5089 9.60841Z"
                                        fill="#FFC107" />
                                </svg>
                                Edit
                            </button>
                            <button class="action-btn btn-save">
                                <svg width="21" height="20" viewBox="0 0 21 20" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M13.8327 7.49156V16.9582C13.8327 18.1666 12.966 18.6749 11.9077 18.0916L8.63269 16.2666C8.28269 16.0749 7.71601 16.0749 7.36601 16.2666L4.09101 18.0916C3.03268 18.6749 2.16602 18.1666 2.16602 16.9582V7.49156C2.16602 6.06656 3.33267 4.8999 4.75767 4.8999H11.241C12.666 4.8999 13.8327 6.06656 13.8327 7.49156Z"
                                        fill="#FFC107" />
                                    <path
                                        d="M18.8327 4.2584V13.7251C18.8327 14.9334 17.966 15.4417 16.9077 14.8584L13.8327 13.1417V7.49174C13.8327 6.06674 12.666 4.90009 11.241 4.90009H7.16602V4.2584C7.16602 2.8334 8.33267 1.66675 9.75767 1.66675H16.241C17.666 1.66675 18.8327 2.8334 18.8327 4.2584Z"
                                        fill="#337354" />
                                    <path
                                        d="M9.66732 9.37492H8.62565V8.33325C8.62565 7.99159 8.34232 7.70825 8.00065 7.70825C7.65898 7.70825 7.37565 7.99159 7.37565 8.33325V9.37492H6.33398C5.99232 9.37492 5.70898 9.65825 5.70898 9.99992C5.70898 10.3416 5.99232 10.6249 6.33398 10.6249H7.37565V11.6666C7.37565 12.0083 7.65898 12.2916 8.00065 12.2916C8.34232 12.2916 8.62565 12.0083 8.62565 11.6666V10.6249H9.66732C10.009 10.6249 10.2923 10.3416 10.2923 9.99992C10.2923 9.65825 10.009 9.37492 9.66732 9.37492Z"
                                        fill="#337354" />
                                </svg>
                                Simpan
                            </button>
                            <button class="action-btn btn-delete">
                                <svg width="21" height="20" viewBox="0 0 21 20" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M18.0574 4.35842C16.7157 4.22508 15.3741 4.12508 14.0241 4.05008V4.04175L13.8407 2.95841C13.7157 2.19175 13.5324 1.04175 11.5824 1.04175H9.39907C7.45741 1.04175 7.27407 2.14175 7.14074 2.95008L6.96574 4.01675C6.19074 4.06675 5.41574 4.11675 4.64074 4.19175L2.94074 4.35842C2.59074 4.39175 2.34074 4.70008 2.37407 5.04175C2.40741 5.38342 2.70741 5.63342 3.05741 5.60008L4.75741 5.43342C9.12407 5.00008 13.5241 5.16675 17.9407 5.60842C17.9657 5.60842 17.9824 5.60842 18.0074 5.60842C18.3241 5.60842 18.5991 5.36675 18.6324 5.04175C18.6574 4.70008 18.4074 4.39175 18.0574 4.35842Z"
                                        fill="#FFC107" />
                                    <path
                                        d="M16.5245 6.78325C16.3245 6.57492 16.0495 6.45825 15.7661 6.45825H5.2328C4.94947 6.45825 4.66613 6.57492 4.47447 6.78325C4.2828 6.99159 4.17447 7.27492 4.19113 7.56659L4.7078 16.1166C4.79947 17.3833 4.91613 18.9666 7.82447 18.9666H13.1745C16.0828 18.9666 16.1995 17.3916 16.2911 16.1166L16.8078 7.57492C16.8245 7.27492 16.7161 6.99159 16.5245 6.78325Z"
                                        fill="#DC5E3A" />
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M8.48242 14.1667C8.48242 13.8216 8.76224 13.5417 9.10742 13.5417H11.8824C12.2276 13.5417 12.5074 13.8216 12.5074 14.1667C12.5074 14.5119 12.2276 14.7917 11.8824 14.7917H9.10742C8.76224 14.7917 8.48242 14.5119 8.48242 14.1667Z"
                                        fill="#FFC107" />
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M7.79102 10.8333C7.79102 10.4881 8.07084 10.2083 8.41602 10.2083H12.5827C12.9279 10.2083 13.2077 10.4881 13.2077 10.8333C13.2077 11.1784 12.9279 11.4583 12.5827 11.4583H8.41602C8.07084 11.4583 7.79102 11.1784 7.79102 10.8333Z"
                                        fill="#FFC107" />
                                </svg>
                                Hapus
                            </button>
                        </div>
                    </div>

                    <div class="table-row">
                        <div class="table-cell col-no">1.</div>
                        <div class="table-cell col-variable">Kejadian retensi urin pada pasien postpartum</div>
                        <div class="table-cell col-type">
                            <select class="dropdown-select">
                                <option value="IMPU" selected>Indikator Mutu Prioritas Unit (IMPU)</option>
                                <option value="IMPRS">Indikator Mutu Prioritas RS (IMPRS)</option>
                                <option value="INM">Indikator Nasional Mutu (INM)</option>
                            </select>
                        </div>
                        <div class="table-cell col-actions">
                            <button class="action-btn btn-edit">
                                <svg width="21" height="20" viewBox="0 0 21 20" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M18 18.3333H3C2.65833 18.3333 2.375 18.0499 2.375 17.7083C2.375 17.3666 2.65833 17.0833 3 17.0833H18C18.3417 17.0833 18.625 17.3666 18.625 17.7083C18.625 18.0499 18.3417 18.3333 18 18.3333Z"
                                        fill="#DC5E3A" />
                                    <path
                                        d="M16.3495 2.90005C14.7328 1.28338 13.1495 1.24172 11.4912 2.90005L10.4828 3.90838C10.3995 3.99172 10.3662 4.12505 10.3995 4.24172C11.0328 6.45005 12.7995 8.21672 15.0078 8.85005C15.0412 8.85838 15.0745 8.86672 15.1078 8.86672C15.1995 8.86672 15.2828 8.83338 15.3495 8.76672L16.3495 7.75838C17.1745 6.94172 17.5745 6.15005 17.5745 5.35005C17.5828 4.52505 17.1828 3.72505 16.3495 2.90005Z"
                                        fill="#DC5E3A" />
                                    <path
                                        d="M13.5089 9.60841C13.2673 9.49175 13.0339 9.37508 12.8089 9.24175C12.6256 9.13341 12.4506 9.01675 12.2756 8.89175C12.1339 8.80008 11.9673 8.66675 11.8089 8.53341C11.7923 8.52508 11.7339 8.47508 11.6673 8.40841C11.3923 8.17508 11.0839 7.87508 10.8089 7.54175C10.7839 7.52508 10.7423 7.46675 10.6839 7.39175C10.6006 7.29175 10.4589 7.12508 10.3339 6.93341C10.2339 6.80841 10.1173 6.62508 10.0089 6.44175C9.87559 6.21675 9.75892 5.99175 9.64226 5.75841C9.52559 5.50841 9.43392 5.26675 9.35059 5.04175L4.11726 10.2751C4.00892 10.3834 3.90892 10.5917 3.88392 10.7334L3.43392 13.9251C3.35059 14.4917 3.50892 15.0251 3.85892 15.3834C4.15892 15.6751 4.57559 15.8334 5.02559 15.8334C5.12559 15.8334 5.22559 15.8251 5.32559 15.8084L8.52559 15.3584C8.67559 15.3334 8.88392 15.2334 8.98392 15.1251L14.2173 9.89175C13.9839 9.80841 13.7589 9.71675 13.5089 9.60841Z"
                                        fill="#FFC107" />
                                </svg>
                                Edit
                            </button>
                            <button class="action-btn btn-save">
                                <svg width="21" height="20" viewBox="0 0 21 20" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M13.8327 7.49156V16.9582C13.8327 18.1666 12.966 18.6749 11.9077 18.0916L8.63269 16.2666C8.28269 16.0749 7.71601 16.0749 7.36601 16.2666L4.09101 18.0916C3.03268 18.6749 2.16602 18.1666 2.16602 16.9582V7.49156C2.16602 6.06656 3.33267 4.8999 4.75767 4.8999H11.241C12.666 4.8999 13.8327 6.06656 13.8327 7.49156Z"
                                        fill="#FFC107" />
                                    <path
                                        d="M18.8327 4.2584V13.7251C18.8327 14.9334 17.966 15.4417 16.9077 14.8584L13.8327 13.1417V7.49174C13.8327 6.06674 12.666 4.90009 11.241 4.90009H7.16602V4.2584C7.16602 2.8334 8.33267 1.66675 9.75767 1.66675H16.241C17.666 1.66675 18.8327 2.8334 18.8327 4.2584Z"
                                        fill="#337354" />
                                    <path
                                        d="M9.66732 9.37492H8.62565V8.33325C8.62565 7.99159 8.34232 7.70825 8.00065 7.70825C7.65898 7.70825 7.37565 7.99159 7.37565 8.33325V9.37492H6.33398C5.99232 9.37492 5.70898 9.65825 5.70898 9.99992C5.70898 10.3416 5.99232 10.6249 6.33398 10.6249H7.37565V11.6666C7.37565 12.0083 7.65898 12.2916 8.00065 12.2916C8.34232 12.2916 8.62565 12.0083 8.62565 11.6666V10.6249H9.66732C10.009 10.6249 10.2923 10.3416 10.2923 9.99992C10.2923 9.65825 10.009 9.37492 9.66732 9.37492Z"
                                        fill="#337354" />
                                </svg>
                                Simpan
                            </button>
                            <button class="action-btn btn-delete">
                                <svg width="21" height="20" viewBox="0 0 21 20" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M18.0574 4.35842C16.7157 4.22508 15.3741 4.12508 14.0241 4.05008V4.04175L13.8407 2.95841C13.7157 2.19175 13.5324 1.04175 11.5824 1.04175H9.39907C7.45741 1.04175 7.27407 2.14175 7.14074 2.95008L6.96574 4.01675C6.19074 4.06675 5.41574 4.11675 4.64074 4.19175L2.94074 4.35842C2.59074 4.39175 2.34074 4.70008 2.37407 5.04175C2.40741 5.38342 2.70741 5.63342 3.05741 5.60008L4.75741 5.43342C9.12407 5.00008 13.5241 5.16675 17.9407 5.60842C17.9657 5.60842 17.9824 5.60842 18.0074 5.60842C18.3241 5.60842 18.5991 5.36675 18.6324 5.04175C18.6574 4.70008 18.4074 4.39175 18.0574 4.35842Z"
                                        fill="#FFC107" />
                                    <path
                                        d="M16.5245 6.78325C16.3245 6.57492 16.0495 6.45825 15.7661 6.45825H5.2328C4.94947 6.45825 4.66613 6.57492 4.47447 6.78325C4.2828 6.99159 4.17447 7.27492 4.19113 7.56659L4.7078 16.1166C4.79947 17.3833 4.91613 18.9666 7.82447 18.9666H13.1745C16.0828 18.9666 16.1995 17.3916 16.2911 16.1166L16.8078 7.57492C16.8245 7.27492 16.7161 6.99159 16.5245 6.78325Z"
                                        fill="#DC5E3A" />
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M8.48242 14.1667C8.48242 13.8216 8.76224 13.5417 9.10742 13.5417H11.8824C12.2276 13.5417 12.5074 13.8216 12.5074 14.1667C12.5074 14.5119 12.2276 14.7917 11.8824 14.7917H9.10742C8.76224 14.7917 8.48242 14.5119 8.48242 14.1667Z"
                                        fill="#FFC107" />
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M7.79102 10.8333C7.79102 10.4881 8.07084 10.2083 8.41602 10.2083H12.5827C12.9279 10.2083 13.2077 10.4881 13.2077 10.8333C13.2077 11.1784 12.9279 11.4583 12.5827 11.4583H8.41602C8.07084 11.4583 7.79102 11.1784 7.79102 10.8333Z"
                                        fill="#FFC107" />
                                </svg>
                                Hapus
                            </button>
                        </div>
                    </div>

                    <div class="table-row">
                        <div class="table-cell col-no">1.</div>
                        <div class="table-cell col-variable">Kejadian retensi urin pada pasien postpartum</div>
                        <div class="table-cell col-type">
                            <select class="dropdown-select">
                                <option value="IMPU" selected>Indikator Mutu Prioritas Unit (IMPU)</option>
                                <option value="IMPRS">Indikator Mutu Prioritas RS (IMPRS)</option>
                                <option value="INM">Indikator Nasional Mutu (INM)</option>
                            </select>
                        </div>
                        <div class="table-cell col-actions">
                            <button class="action-btn btn-edit">
                                <svg width="21" height="20" viewBox="0 0 21 20" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M18 18.3333H3C2.65833 18.3333 2.375 18.0499 2.375 17.7083C2.375 17.3666 2.65833 17.0833 3 17.0833H18C18.3417 17.0833 18.625 17.3666 18.625 17.7083C18.625 18.0499 18.3417 18.3333 18 18.3333Z"
                                        fill="#DC5E3A" />
                                    <path
                                        d="M16.3495 2.90005C14.7328 1.28338 13.1495 1.24172 11.4912 2.90005L10.4828 3.90838C10.3995 3.99172 10.3662 4.12505 10.3995 4.24172C11.0328 6.45005 12.7995 8.21672 15.0078 8.85005C15.0412 8.85838 15.0745 8.86672 15.1078 8.86672C15.1995 8.86672 15.2828 8.83338 15.3495 8.76672L16.3495 7.75838C17.1745 6.94172 17.5745 6.15005 17.5745 5.35005C17.5828 4.52505 17.1828 3.72505 16.3495 2.90005Z"
                                        fill="#DC5E3A" />
                                    <path
                                        d="M13.5089 9.60841C13.2673 9.49175 13.0339 9.37508 12.8089 9.24175C12.6256 9.13341 12.4506 9.01675 12.2756 8.89175C12.1339 8.80008 11.9673 8.66675 11.8089 8.53341C11.7923 8.52508 11.7339 8.47508 11.6673 8.40841C11.3923 8.17508 11.0839 7.87508 10.8089 7.54175C10.7839 7.52508 10.7423 7.46675 10.6839 7.39175C10.6006 7.29175 10.4589 7.12508 10.3339 6.93341C10.2339 6.80841 10.1173 6.62508 10.0089 6.44175C9.87559 6.21675 9.75892 5.99175 9.64226 5.75841C9.52559 5.50841 9.43392 5.26675 9.35059 5.04175L4.11726 10.2751C4.00892 10.3834 3.90892 10.5917 3.88392 10.7334L3.43392 13.9251C3.35059 14.4917 3.50892 15.0251 3.85892 15.3834C4.15892 15.6751 4.57559 15.8334 5.02559 15.8334C5.12559 15.8334 5.22559 15.8251 5.32559 15.8084L8.52559 15.3584C8.67559 15.3334 8.88392 15.2334 8.98392 15.1251L14.2173 9.89175C13.9839 9.80841 13.7589 9.71675 13.5089 9.60841Z"
                                        fill="#FFC107" />
                                </svg>
                                Edit
                            </button>
                            <button class="action-btn btn-save">
                                <svg width="21" height="20" viewBox="0 0 21 20" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M13.8327 7.49156V16.9582C13.8327 18.1666 12.966 18.6749 11.9077 18.0916L8.63269 16.2666C8.28269 16.0749 7.71601 16.0749 7.36601 16.2666L4.09101 18.0916C3.03268 18.6749 2.16602 18.1666 2.16602 16.9582V7.49156C2.16602 6.06656 3.33267 4.8999 4.75767 4.8999H11.241C12.666 4.8999 13.8327 6.06656 13.8327 7.49156Z"
                                        fill="#FFC107" />
                                    <path
                                        d="M18.8327 4.2584V13.7251C18.8327 14.9334 17.966 15.4417 16.9077 14.8584L13.8327 13.1417V7.49174C13.8327 6.06674 12.666 4.90009 11.241 4.90009H7.16602V4.2584C7.16602 2.8334 8.33267 1.66675 9.75767 1.66675H16.241C17.666 1.66675 18.8327 2.8334 18.8327 4.2584Z"
                                        fill="#337354" />
                                    <path
                                        d="M9.66732 9.37492H8.62565V8.33325C8.62565 7.99159 8.34232 7.70825 8.00065 7.70825C7.65898 7.70825 7.37565 7.99159 7.37565 8.33325V9.37492H6.33398C5.99232 9.37492 5.70898 9.65825 5.70898 9.99992C5.70898 10.3416 5.99232 10.6249 6.33398 10.6249H7.37565V11.6666C7.37565 12.0083 7.65898 12.2916 8.00065 12.2916C8.34232 12.2916 8.62565 12.0083 8.62565 11.6666V10.6249H9.66732C10.009 10.6249 10.2923 10.3416 10.2923 9.99992C10.2923 9.65825 10.009 9.37492 9.66732 9.37492Z"
                                        fill="#337354" />
                                </svg>
                                Simpan
                            </button>
                            <button class="action-btn btn-delete">
                                <svg width="21" height="20" viewBox="0 0 21 20" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M18.0574 4.35842C16.7157 4.22508 15.3741 4.12508 14.0241 4.05008V4.04175L13.8407 2.95841C13.7157 2.19175 13.5324 1.04175 11.5824 1.04175H9.39907C7.45741 1.04175 7.27407 2.14175 7.14074 2.95008L6.96574 4.01675C6.19074 4.06675 5.41574 4.11675 4.64074 4.19175L2.94074 4.35842C2.59074 4.39175 2.34074 4.70008 2.37407 5.04175C2.40741 5.38342 2.70741 5.63342 3.05741 5.60008L4.75741 5.43342C9.12407 5.00008 13.5241 5.16675 17.9407 5.60842C17.9657 5.60842 17.9824 5.60842 18.0074 5.60842C18.3241 5.60842 18.5991 5.36675 18.6324 5.04175C18.6574 4.70008 18.4074 4.39175 18.0574 4.35842Z"
                                        fill="#FFC107" />
                                    <path
                                        d="M16.5245 6.78325C16.3245 6.57492 16.0495 6.45825 15.7661 6.45825H5.2328C4.94947 6.45825 4.66613 6.57492 4.47447 6.78325C4.2828 6.99159 4.17447 7.27492 4.19113 7.56659L4.7078 16.1166C4.79947 17.3833 4.91613 18.9666 7.82447 18.9666H13.1745C16.0828 18.9666 16.1995 17.3916 16.2911 16.1166L16.8078 7.57492C16.8245 7.27492 16.7161 6.99159 16.5245 6.78325Z"
                                        fill="#DC5E3A" />
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M8.48242 14.1667C8.48242 13.8216 8.76224 13.5417 9.10742 13.5417H11.8824C12.2276 13.5417 12.5074 13.8216 12.5074 14.1667C12.5074 14.5119 12.2276 14.7917 11.8824 14.7917H9.10742C8.76224 14.7917 8.48242 14.5119 8.48242 14.1667Z"
                                        fill="#FFC107" />
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M7.79102 10.8333C7.79102 10.4881 8.07084 10.2083 8.41602 10.2083H12.5827C12.9279 10.2083 13.2077 10.4881 13.2077 10.8333C13.2077 11.1784 12.9279 11.4583 12.5827 11.4583H8.41602C8.07084 11.4583 7.79102 11.1784 7.79102 10.8333Z"
                                        fill="#FFC107" />
                                </svg>
                                Hapus
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection