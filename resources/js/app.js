import './bootstrap';

const toTopBtn = document.getElementById("toTopBtn");

// Tampilkan tombol saat discroll 200px ke bawah
window.onscroll = function () {
  if (document.body.scrollTop > 200 || document.documentElement.scrollTop > 200) {
    toTopBtn.classList.remove("hidden");
  } else {
    toTopBtn.classList.add("hidden");
  }
};

// Aksi scroll ke atas saat tombol diklik
toTopBtn.addEventListener("click", () => {
  window.scrollTo({ top: 0, behavior: "smooth" });
});
