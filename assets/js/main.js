(() => {
  "use strict";
  const menuButton = document.querySelector(".menu-toggle");
  const nav = document.querySelector("#primary-nav");
  const search = document.querySelector("#service-search");
  const filterButtons = [...document.querySelectorAll("[data-filter]")];
  const cards = [...document.querySelectorAll(".service-card")];
  const status = document.querySelector("#filter-status");
  const emptyState = document.querySelector("#empty-state");
  const year = document.querySelector("#current-year");
  let activeFilter = "todos";

  if (year) year.textContent = String(new Date().getFullYear());

  if (menuButton && nav) {
    menuButton.addEventListener("click", () => {
      const isOpen = nav.classList.toggle("is-open");
      menuButton.setAttribute("aria-expanded", String(isOpen));
      const label = menuButton.querySelector(".sr-only");
      if (label) label.textContent = isOpen ? "Fechar menu" : "Abrir menu";
    });
    nav.querySelectorAll("a").forEach((link) => {
      link.addEventListener("click", () => {
        nav.classList.remove("is-open");
        menuButton.setAttribute("aria-expanded", "false");
        const label = menuButton.querySelector(".sr-only");
        if (label) label.textContent = "Abrir menu";
      });
    });
    document.addEventListener("keydown", (event) => {
      if (event.key === "Escape" && nav.classList.contains("is-open")) {
        nav.classList.remove("is-open");
        menuButton.setAttribute("aria-expanded", "false");
        menuButton.focus();
      }
    });
  }

  function applyFilters() {
    const query = (search?.value || "").trim().toLocaleLowerCase("pt-BR");
    let visible = 0;
    cards.forEach((card) => {
      const categoryMatches = activeFilter === "todos" || card.dataset.category === activeFilter;
      const text = (card.dataset.search + " " + card.textContent).toLocaleLowerCase("pt-BR");
      const queryMatches = !query || text.includes(query);
      const show = categoryMatches && queryMatches;
      card.hidden = !show;
      if (show) visible += 1;
    });
    if (emptyState) emptyState.hidden = visible !== 0;
    if (status) status.textContent = visible === cards.length && activeFilter === "todos" && !query
      ? "Exibindo todos os serviços."
      : `Exibindo ${visible} de ${cards.length} componentes.`;
  }

  filterButtons.forEach((button) => {
    button.addEventListener("click", () => {
      activeFilter = button.dataset.filter || "todos";
      filterButtons.forEach((item) => {
        const active = item === button;
        item.classList.toggle("is-active", active);
        item.setAttribute("aria-pressed", String(active));
      });
      applyFilters();
    });
  });
  if (search) search.addEventListener("input", applyFilters);
})();