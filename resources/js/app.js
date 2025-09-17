import './bootstrap';

const toTopBtn = document.getElementById("toTopBtn");
let scrollTimer;

// Pantau saat user scroll
window.addEventListener("scroll", () => {
  // Selama masih scroll → sembunyikan tombol
  toTopBtn.classList.add("hidden");

  // Reset timer tiap kali ada gerakan scroll
  clearTimeout(scrollTimer);

  // Setelah berhenti scroll 300ms → munculkan tombol
  scrollTimer = setTimeout(() => {
    // Cek posisi, misal hanya muncul kalau sudah >100px dari atas
    if (document.documentElement.scrollTop > 100 || document.body.scrollTop > 100) {
      toTopBtn.classList.remove("hidden");
    }
  }, 300);
});

// Klik tombol → scroll halus ke atas
toTopBtn.addEventListener("click", () => {
  window.scrollTo({ top: 0, behavior: "smooth" });
});
