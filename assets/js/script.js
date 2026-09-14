// Highlight selected shape/design option thumbnails on the product page.
document.querySelectorAll('.option-thumb input[type="radio"]').forEach((input) => {
  input.addEventListener("change", function () {
    const group = this.name;
    document.querySelectorAll(".option-thumb").forEach((thumb) => {
      const radio = thumb.querySelector(`input[name="${group}"]`);
      if (radio) {
        thumb.classList.toggle("selected", radio.checked);
      }
    });
  });
});

// Live preview: uploaded photo.
const photoInput = document.getElementById("custom_image");
if (photoInput) {
  photoInput.addEventListener("change", function () {
    const preview = document.getElementById("photo-preview");
    if (!preview) return;
    const file = this.files[0];
    if (file) {
      preview.src = URL.createObjectURL(file);
      preview.classList.remove("d-none");
    }
  });
}

// Live preview: personalized text.
const textInput = document.getElementById("custom_text");
if (textInput) {
  textInput.addEventListener("input", function () {
    const preview = document.getElementById("text-preview");
    if (preview) {
      preview.textContent = this.value || "(your text will appear here)";
    }
  });
}

// Cart quantity inputs: prevent submitting a quantity above available stock.
document.querySelectorAll("input[data-max-stock]").forEach((input) => {
  input.addEventListener("change", function () {
    const max = parseInt(this.dataset.maxStock, 10);
    const val = parseInt(this.value, 10);
    if (isNaN(val) || val < 1) {
      this.value = 1;
    } else if (val > max) {
      this.value = max;
      alert(`Only ${max} in stock. Quantity adjusted.`);
    }
  });
});
