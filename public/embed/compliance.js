(function () {
  const API_URL = "https://api-kia.test/api/compliance-text";

  function createWidget(el) {
    const carId = el.dataset.car;
    const trimId = el.dataset.trim || null;
    const powertrainId = el.dataset.powertrain || null;
    const template = el.dataset.template;

    if (!carId || !template) {
      console.warn("Kia Compliance: Missing required data attributes");
      return;
    }

    const shadow = el.attachShadow({ mode: "open" });

    const wrapper = document.createElement("div");

    wrapper.innerHTML = `
      <style>
        :host {
          font-family: var(--kia-font, inherit);
          color: var(--kia-color, inherit);
          font-size: var(--kia-font-size, inherit);
          line-height: var(--kia-line-height, 1.5);
        }

        .loading {
          opacity: 0.7;
          font-style: var(--kia-loading-style, italic);
        }

        .content {
          white-space: var(--kia-whitespace, pre-line);
        }
      </style>

      <div class="loading">Loading...</div>
    `;

    const loadingEl = wrapper.querySelector(".loading");

    shadow.appendChild(wrapper);

    const params = new URLSearchParams({
      car_id: carId,
      template: template,
    });

    if (trimId) params.append("trim_id", trimId);
    if (powertrainId) params.append("powertrain_id", powertrainId);

    fetch(`${API_URL}?${params.toString()}`)
      .then((res) => res.json())
      .then((data) => {
        loadingEl.classList.remove("loading");
        loadingEl.className = "content";

        loadingEl.textContent = data.text;
      })
      .catch(() => {
        loadingEl.textContent = "Failed to load compliance text.";
      });
  }

  function initAll() {
    const widgets = document.querySelectorAll(".kia-compliance");

    widgets.forEach((el) => {
      if (el.__kia_initialized) return;
      el.__kia_initialized = true;

      createWidget(el);
    });
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initAll);
  } else {
    initAll();
  }
})();