@extends('layouts.app')

@section('styles')
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
@endsection

@section('content')
    <main class="main-content-section">
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

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Terjadi Kesalahan!</strong>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Toolbar: Tombol Tambah --}}
        <div class="toolbar">
            <button class="btn-add-outline" data-bs-toggle="modal" data-bs-target="#addIndicatorModal">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="16"></line>
                    <line x1="8" y1="12" x2="16" y2="12"></line>
                </svg>
                <span>Tambah Indikator Baru</span>
            </button>
        </div>

        {{-- Tabel Data --}}
        <div class="indicator-table-container">
            <h2 class="table-title">Daftar Master Indikator Mutu</h2>
            <div class="crud-table-wrapper">
                <table class="crud-table">
                    <thead>
                        <tr>
                            <th class="col-fit">No.</th>
                            <th class="text-start">Variabel Penilaian</th>
                            <th class="text-start">Jenis Indikator</th>
                            <th class="text-center">Standar</th>
                            <th class="text-center" style="width: 200px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($indikators->isEmpty())
                            <tr>
                                <td colspan="5" class="text-center py-4">Belum ada data indikator mutu.</td>
                            </tr>
                        @else
                            @php $rowNumber = 1; @endphp
                            @foreach ($indikators as $namaKategori => $items)
                                @foreach ($items as $indikator)
                                    <tr>
                                        <td class="text-center">{{ $rowNumber++ }}.</td>
                                        <td class="text-start">{{ $indikator->variabel }}</td>
                                        <td class="text-start">{{ $indikator->kategori }}</td>
                                        <td class="text-center">{{ $indikator->standar }}</td>
                                        <td>
                                            <div class="action-group">
                                                {{-- Tombol Edit --}}
                                                <button type="button" class="action-btn btn-edit" data-bs-toggle="modal" data-bs-target="#editModal-{{ $indikator->id_indikator }}">
                                                    <svg width="21" height="20" viewBox="0 0 21 20" fill="none" xmlns="http://www.w3.org/2000/svg">
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
                                                {{-- Tombol Hapus --}}
                                                <button type="button" class="action-btn btn-delete" data-bs-toggle="modal" data-bs-target="#deleteModal-{{ $indikator->id_indikator }}">
                                                    <svg width="21" height="20" viewBox="0 0 21 20" fill="none" xmlns="http://www.w3.org/2000/svg">
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
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    {{-- MODAL TAMBAH --}}
    <div class="modal fade" id="addIndicatorModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header modal-header-primary">
                    <h5 class="modal-title">Formulir Tambah Indikator Mutu Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('superadmin.indikator_mutu.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Jenis Indikator (Kategori)</label>
                            <select class="form-select" name="id_kategori" required>
                                <option value="" selected disabled>-- Pilih Jenis Indikator --</option>
                                @foreach ($kategoris as $kategori)
                                    <option value="{{ $kategori->id_kategori }}">{{ $kategori->kategori }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Variabel Penilaian</label>
                            <input type="text" class="form-control" name="variabel" placeholder="Contoh: Kelengkapan pengisian..." required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Standar</label>
                            <textarea class="form-control" name="standar" rows="3" placeholder="Contoh: 100%" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success" style="background-color: var(--primary-color); border:none;">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- LOOP MODAL EDIT & DELETE --}}
    @foreach ($indikators as $items)
        @foreach ($items as $indikator)
            {{-- Modal Edit --}}
            <div class="modal fade" id="editModal-{{ $indikator->id_indikator }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header modal-header-primary">
                            <h5 class="modal-title">Edit Indikator Mutu</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form class="edit-indicator-form" action="{{ route('superadmin.indikator_mutu.update', $indikator->id_indikator) }}" method="POST"
                            data-original-kategori="{{ $indikator->id_kategori }}"
                            data-original-variabel="{{ $indikator->variabel }}"
                            data-original-standar="{{ $indikator->standar }}">
                            @csrf
                            @method('PUT')
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Jenis Indikator</label>
                                    <select class="form-select" name="id_kategori" required>
                                        @foreach ($kategoris as $kategori)
                                            <option value="{{ $kategori->id_kategori }}" {{ $kategori->id_kategori == $indikator->id_kategori ? 'selected' : '' }}>
                                                {{ $kategori->kategori }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Variabel Penilaian</label>
                                    <input type="text" class="form-control" name="variabel" value="{{ $indikator->variabel }}" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Standar</label>
                                    <textarea class="form-control" name="standar" rows="3" required>{{ $indikator->standar }}</textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-primary" style="background-color: var(--primary-color); border:none;">Simpan Perubahan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Modal Delete --}}
            <div class="modal fade" id="deleteModal-{{ $indikator->id_indikator }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header modal-header-danger">
                            <h5 class="modal-title">Konfirmasi Hapus</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p>Anda yakin ingin menghapus indikator ini?</p>
                            <div class="alert alert-light border">
                                <strong>{{ $indikator->variabel }}</strong>
                            </div>
                            <small class="text-danger">Tindakan ini tidak dapat dibatalkan.</small>
                        </div>
                        <div class="modal-footer">
                            <form action="{{ route('superadmin.indikator_mutu.destroy', $indikator->id_indikator) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @endforeach

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const editForms = document.querySelectorAll('.edit-indicator-form');
            editForms.forEach(form => {
                form.addEventListener('submit', function (event) {
                    const originalKategori = form.dataset.originalKategori;
                    const originalVariabel = form.dataset.originalVariabel;
                    const originalStandar = form.dataset.originalStandar;

                    const currentKategori = form.querySelector('select[name="id_kategori"]').value;
                    const currentVariabel = form.querySelector('input[name="variabel"]').value;
                    const currentStandar = form.querySelector('textarea[name="standar"]').value;

                    if (originalKategori === currentKategori &&
                        originalVariabel.trim() === currentVariabel.trim() &&
                        originalStandar.trim() === currentStandar.trim()) {
                        event.preventDefault();
                        alert('Harus ada minimal 1 perubahan untuk menyimpan data.');
                    }
                });
            });
        });
    </script>
@endpush