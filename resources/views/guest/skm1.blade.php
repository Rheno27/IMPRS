@extends('layouts.app')

@section('content')
    <main>
        <section class="form-section">
            @if ($errors->any())
                <div class="custom-alert error" style="display: block; margin-bottom: 20px;">
                    <ul style="margin: 0; padding-left: 20px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

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
                                    <label for="no_rm" class="form-label">Nomor Responden (No. RM)<span class="required-asterisk">*</span></label>
                                    <input type="number" id="no_rm" name="no_rm" class="input-field" required value="{{ old('no_rm') }}">
                                </div>
                                <div class="form-group">
                                    <label for="umur" class="form-label">Umur<span class="required-asterisk">*</span></label>
                                    <input type="number" id="umur" name="umur" class="input-field" required value="{{ old('umur') }}">
                                </div>
                            </div>
                            
                            {{-- KOLOM 2 --}}
                            <div class="form-column">
                                <div class="form-group">
                                    <label for="id_ruangan" class="form-label">Ruangan<span class="required-asterisk">*</span></label>
                                    <div class="select-wrapper">
                                        <select id="id_ruangan" name="id_ruangan" class="form-select" required>
                                            <option value="">Pilih Ruangan</option>
                                            @foreach ($ruangan as $item)
                                                <option value="{{ $item->id_ruangan }}" {{ old('id_ruangan') == $item->id_ruangan ? 'selected' : '' }}>
                                                    {{ $item->nama_ruangan }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <img src="{{ asset('image/arrow-circle-down.svg') }}" class="select-arrow" alt="arrow">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="jenis_kelamin" class="form-label">Jenis Kelamin<span class="required-asterisk">*</span></label>
                                    <div class="select-wrapper">
                                        <select id="jenis_kelamin" name="jenis_kelamin" class="form-select" required>
                                            <option value="">Pilih Jenis Kelamin</option>
                                            <option value="Laki-laki" {{ old('jenis_kelamin') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                            <option value="Perempuan" {{ old('jenis_kelamin') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                        </select>
                                        <img src="{{ asset('image/arrow-circle-down.svg') }}" class="select-arrow" alt="arrow">
                                    </div>
                                </div>
                            </div>
                            
                            {{-- KOLOM 3 --}}
                            <div class="form-column">
                                <div class="form-group">
                                    <label for="pendidikan" class="form-label">Pendidikan Terakhir<span class="required-asterisk">*</span></label>
                                    <div class="select-wrapper">
                                        <select id="pendidikan" name="pendidikan" class="form-select" required>
                                            <option value="">Pilih Pendidikan</option>
                                            <option value="Tidak Sekolah" {{ old('pendidikan') == 'Tidak Sekolah' ? 'selected' : '' }}>Tidak Sekolah</option>
                                            <option value="SD" {{ old('pendidikan') == 'SD' ? 'selected' : '' }}>SD</option>
                                            <option value="SMP" {{ old('pendidikan') == 'SMP' ? 'selected' : '' }}>SMP</option>
                                            <option value="SMA" {{ old('pendidikan') == 'SMA' ? 'selected' : '' }}>SMA</option>
                                            <option value="D3" {{ old('pendidikan') == 'D3' ? 'selected' : '' }}>D3</option>
                                            <option value="S1" {{ old('pendidikan') == 'S1' ? 'selected' : '' }}>S1</option>
                                            <option value="S2" {{ old('pendidikan') == 'S2' ? 'selected' : '' }}>S2</option>
                                        </select>
                                        <img src="{{ asset('image/arrow-circle-down.svg') }}" class="select-arrow" alt="arrow">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="pekerjaan" class="form-label">Pekerjaan Utama<span class="required-asterisk">*</span></label>
                                    <div class="select-wrapper">
                                        <select id="pekerjaan" name="pekerjaan" class="form-select" required>
                                            <option value="">Pilih Pekerjaan</option>
                                            <option value="PNS" {{ old('pekerjaan') == 'PNS' ? 'selected' : '' }}>PNS</option>
                                            <option value="Swasta" {{ old('pekerjaan') == 'Swasta' ? 'selected' : '' }}>Swasta</option>
                                            <option value="Wirausaha" {{ old('pekerjaan') == 'Wirausaha' ? 'selected' : '' }}>Wirausaha</option>
                                            <option value="Lainnya" {{ old('pekerjaan') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                                        </select>
                                        <img src="{{ asset('image/arrow-circle-down.svg') }}" class="select-arrow" alt="arrow">
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