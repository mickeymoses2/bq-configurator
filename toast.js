function showSuccessPopup() {
  setTimeout(() => {   // ← 500ms delay wrapper
    const popup = document.getElementById("successPopup");
    popup.classList.remove("hidden");

    // Loader → Tick animation
    const loader = document.getElementById("popupLoader");
    const tick = document.getElementById("popupTick");

    loader.classList.remove("hidden");
    tick.classList.add("hidden");

    setTimeout(() => {
      loader.style.opacity = "0";

      setTimeout(() => {
        loader.classList.add("hidden");
        tick.classList.remove("hidden");
        tick.style.opacity = "1";
      }, 300);

    }, 1500);
  }, 1800); // ← Delay added here
}

function showErrorPopup() {
  setTimeout(() => {   // Optional: add delay here too if needed
    const popup = document.getElementById("errorPopup");
    popup?.classList.remove("hidden");
  }, 900);
}

document.getElementById("popupCloseBtn")?.addEventListener("click", () => {
  document.getElementById("successPopup")?.classList.add("hidden");

  // reset for next use
  document.getElementById("popupLoader").style.opacity = "1";
  document.getElementById("popupLoader").classList.remove("hidden");
  document.getElementById("popupTick").classList.add("hidden");
});
