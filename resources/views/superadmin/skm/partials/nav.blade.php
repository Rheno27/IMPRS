<section id="survey-navigation" class="survey-nav-container">
    <h2 class="survey-title">SURVEI KEPUASAN MASYARAKAT</h2>
    <nav class="survey-tabs">
        <a href="{{ route('superadmin.skm.rekap') }}"
            class="tab-item {{ request()->routeIs('superadmin.skm.rekap') ? 'active' : '' }}">
            Rekap SKM
        </a>

        <a href="{{ route('superadmin.skm.edit2') }}"
            class="tab-item {{ request()->routeIs('superadmin.skm.edit2') ? 'active' : '' }}">
            Edit Pertanyaan
        </a>

        <a href="{{ route('superadmin.skm.hasil') }}"
            class="tab-item {{ request()->routeIs('superadmin.skm.hasil') ? 'active' : '' }}">
            Hasil Survei
        </a>
    </nav>
</section>