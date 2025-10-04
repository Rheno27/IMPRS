document.addEventListener("DOMContentLoaded", () => {
    // === Months Picker ===
    const months = [
        "Januari",
        "Februari",
        "Maret",
        "April",
        "Mei",
        "Juni",
        "Juli",
        "Agustus",
        "September",
        "Oktober",
        "November",
        "Desember",
    ];

    let currentIndex = new Date().getMonth();
    const monthContainer = document.querySelector(".months");

    function renderMonths() {
        if (!monthContainer) return;
        monthContainer.innerHTML = "";

        const prev = (currentIndex - 1 + months.length) % months.length;
        const curr = currentIndex;
        const next = (currentIndex + 1) % months.length;

        [prev, curr, next].forEach((idx, i) => {
            const btn = document.createElement("button");
            btn.classList.add("month-btn");
            btn.textContent = months[idx];

            if (i === 1) {
                btn.classList.add("active");
            } else {
                btn.addEventListener("click", () => {
                    const bulan = idx + 1;
                    const tahun = new Date().getFullYear();
                    window.location.href = `/admin/dashboard?bulan=${bulan}&tahun=${tahun}`;
                });
            }

            monthContainer.appendChild(btn);
        });
    }

    renderMonths();

    const yearBtn = document.getElementById("yearBtn");
    const yearPanel = document.getElementById("yearPanel");
    const yearGrid = document.getElementById("yearGrid");
    const selectedYear = document.getElementById("selectedYear");
    const yearRange = document.getElementById("yearRange");

    let currentYear =
        typeof selectedYearFromServer !== "undefined"
            ? selectedYearFromServer
            : new Date().getFullYear();
    let startYear = Math.floor(currentYear / 10) * 10;

    function renderYears() {
        // Pastikan elemennya ada sebelum melanjutkan
        if (!yearGrid || !yearRange || !selectedYear) return;

        yearGrid.innerHTML = "";
        yearRange.textContent = `${startYear} - ${startYear + 8}`;

        for (let y = startYear; y < startYear + 9; y++) {
            const btn = document.createElement("button");
            btn.textContent = y;
            if (y === currentYear) btn.classList.add("active");

            // --- BAGIAN YANG DIPERBARUI ---
            // Saat tombol tahun diklik:
            btn.onclick = () => {
                // 1. Buat objek URL dari alamat halaman saat ini.
                // Ini akan mempertahankan parameter yang sudah ada (seperti 'kategori').
                const currentUrl = new URL(window.location.href);

                // 2. Atur atau perbarui parameter 'tahun' dengan tahun yang baru dipilih.
                currentUrl.searchParams.set("tahun", y);

                // 3. Arahkan (reload) browser ke URL yang baru.
                window.location.href = currentUrl.toString();
            };
            // --- AKHIR BAGIAN YANG DIPERBARUI ---

            yearGrid.appendChild(btn);
        }
    }

    document.getElementById("prevYears").onclick = () => {
        startYear -= 10;
        renderYears();
    };
    document.getElementById("nextYears").onclick = () => {
        startYear += 10;
        renderYears();
    };

    yearBtn.onclick = () => {
        yearPanel.classList.toggle("open");
    };

    document.addEventListener("click", (e) => {
        if (!yearBtn.contains(e.target) && !yearPanel.contains(e.target)) {
            yearPanel.classList.remove("open");
        }
    });

    const selectedYearSpan = document.getElementById("selectedYear");
    if (selectedYearSpan) {
        selectedYearSpan.textContent = currentYear;
    }

    renderYears();
});
