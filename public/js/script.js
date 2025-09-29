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

    const prevBtn = document.querySelector(".arrow-btn.prev");
    const nextBtn = document.querySelector(".arrow-btn.next");

    if (prevBtn && nextBtn) {
        prevBtn.addEventListener("click", () => {
            currentIndex = (currentIndex - 1 + months.length) % months.length;
            renderMonths();
        });

        nextBtn.addEventListener("click", () => {
            currentIndex = (currentIndex + 1) % months.length;
            renderMonths();
        });
    }

    renderMonths();

    // === Categories Picker ===
    const categories = ["IMPU", "INM", "IMPRS"];

    let currentIndexx = 1; // biar default "INM" di tengah
    const categoryContainer = document.querySelector(".categories");

    function renderCategories() {
        if (!categoryContainer) return;
        categoryContainer.innerHTML = "";

        const prev =
            (currentIndexx - 1 + categories.length) % categories.length;
        const curr = currentIndexx;
        const next = (currentIndexx + 1) % categories.length;

        [prev, curr, next].forEach((idx, i) => {
            const btn = document.createElement("button");
            btn.classList.add("category-btn");
            btn.textContent = categories[idx];

            if (i === 1) {
                btn.classList.add("active");
            } else {
                btn.addEventListener("click", () => {
                    currentIndexx = idx;
                    renderCategories();
                });
            }

            categoryContainer.appendChild(btn);
        });
    }

    const prevBtnn = document.querySelector(".arrow-btn.prev-cat");
    const nextBtnn = document.querySelector(".arrow-btn.next-cat");

    if (prevBtnn && nextBtnn) {
        prevBtnn.addEventListener("click", () => {
            currentIndexx =
                (currentIndexx - 1 + categories.length) % categories.length;
            renderCategories();
        });

        nextBtnn.addEventListener("click", () => {
            currentIndexx = (currentIndexx + 1) % categories.length;
            renderCategories();
        });
    }

    renderCategories();

    const yearBtn = document.getElementById("yearBtn");
    const yearPanel = document.getElementById("yearPanel");
    const yearGrid = document.getElementById("yearGrid");
    const selectedYear = document.getElementById("selectedYear");
    const yearRange = document.getElementById("yearRange");

    let currentYear = new Date().getFullYear();
    let startYear = Math.floor(currentYear / 10) * 10;

    function renderYears() {
        yearGrid.innerHTML = "";
        yearRange.textContent = `${startYear} - ${startYear + 8}`;
        for (let y = startYear; y < startYear + 9; y++) {
            const btn = document.createElement("button");
            btn.textContent = y;
            if (y === currentYear) btn.classList.add("active");
            btn.onclick = () => {
                currentYear = y;
                selectedYear.textContent = y;
                yearPanel.classList.remove("open");
                renderYears();
            };
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

    renderYears();
});
