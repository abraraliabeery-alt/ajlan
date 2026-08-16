(() => {
  const root = document.documentElement;
  const themeButton = document.querySelector('.theme-toggle');
  const setTheme = (theme) => {
    root.dataset.theme = theme;
    localStorage.setItem('ajlan-theme', theme);
    if (themeButton) themeButton.setAttribute('aria-label', theme === 'dark' ? 'Light mode' : 'Dark mode');
  };
  themeButton?.addEventListener('click', () => setTheme(root.dataset.theme === 'dark' ? 'light' : 'dark'));

  const menuButton = document.querySelector('.menu-toggle');
  const mainNav = document.querySelector('.main-nav');
  menuButton?.addEventListener('click', () => {
    const open = mainNav?.classList.toggle('open');
    menuButton.setAttribute('aria-expanded', String(Boolean(open)));
  });

  const search = document.querySelector('#plot-search');
  const cards = [...document.querySelectorAll('#property-grid .property-card')];
  const count = document.querySelector('#result-count');
  const noResults = document.querySelector('#no-results');
  search?.addEventListener('input', () => {
    const query = search.value.toLowerCase().replaceAll('/', '-').trim();
    let visible = 0;
    cards.forEach((card) => {
      const code = (card.dataset.code || '').replaceAll('/', '-');
      const matches = !query || code.includes(query) || card.textContent.toLowerCase().includes(query);
      card.hidden = !matches;
      if (matches) visible += 1;
    });
    if (count) count.textContent = String(visible);
    if (noResults) noResults.hidden = visible !== 0;
  });

  const dialog = document.querySelector('#lightbox');
  const dialogImage = dialog?.querySelector('img');
  document.querySelectorAll('[data-lightbox]').forEach((button) => {
    button.addEventListener('click', () => {
      if (!dialog || !dialogImage) return;
      dialogImage.src = button.dataset.lightbox;
      dialogImage.alt = button.dataset.alt || '';
      dialog.showModal();
    });
  });
  dialog?.querySelector('.lightbox-close')?.addEventListener('click', () => dialog.close());
  dialog?.addEventListener('click', (event) => { if (event.target === dialog) dialog.close(); });
})();
