<section id="survey-navigation" class="survey-nav-container">
    <h2 class="survey-title">SURVEI KEPUASAN MASYARAKAT</h2>
    <nav class="survey-tabs">
        {{-- Tab 1: Rekap --}}
        <a href="{{ route('superadmin.skm.rekap') }}"
            class="tab-item {{ request()->routeIs('superadmin.skm.rekap') ? 'active' : '' }}">
            Rekap SKM
        </a>

        {{-- Tab 2: Edit Pertanyaan --}}
        <a href="{{ route('superadmin.skm.edit2') }}"
            class="tab-item {{ request()->routeIs('superadmin.skm.edit2') ? 'active' : '' }}">
            Edit Pertanyaan
        </a>

        {{-- Tab 3: Hasil Survei --}}
        <a href="{{ route('superadmin.skm.hasil') }}"
            class="tab-item {{ request()->routeIs('superadmin.skm.hasil') ? 'active' : '' }}">
            Hasil Survei
        </a>
    </nav>
</section>