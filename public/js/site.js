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

  const lbGroups = {};
  document.querySelectorAll('[data-lightbox]').forEach((el) => {
    const group = el.dataset.lbGroup || 'default';
    (lbGroups[group] ||= []).push({
      src: el.dataset.lightbox,
      type: el.dataset.lbType || 'image',
      poster: el.querySelector('img')?.currentSrc || el.querySelector('img')?.src || '',
      alt: el.dataset.alt || '',
      el,
    });
  });

  Object.entries(lbGroups).forEach(([group, items]) => {
    const dialog = document.querySelector(`[data-lb-dialog="${group}"]`) || document.querySelector('#lightbox');
    if (!dialog || dialog.dataset.lbBound) return;
    dialog.dataset.lbBound = '1';

    const img = dialog.querySelector('img');
    const video = dialog.querySelector('video');
    const counter = dialog.querySelector('.lightbox-counter');
    const prev = dialog.querySelector('.lightbox-prev');
    const next = dialog.querySelector('.lightbox-next');
    let index = 0;

    const show = (i) => {
      index = (i + items.length) % items.length;
      const item = items[index];
      const isVideo = item.type === 'video' && video;
      if (img) { img.style.display = isVideo ? 'none' : ''; img.src = isVideo ? '' : item.src; img.alt = item.alt; }
      if (video) {
        video.style.display = isVideo ? '' : 'none';
        if (isVideo) { video.src = item.src; if (item.poster) video.poster = item.poster; }
        else { video.pause(); video.removeAttribute('src'); video.load(); }
      }
      if (counter) counter.textContent = `${index + 1} / ${items.length}`;
      const multi = items.length > 1;
      if (prev) prev.style.display = multi ? '' : 'none';
      if (next) next.style.display = multi ? '' : 'none';
    };

    items.forEach((item, i) => item.el.addEventListener('click', () => { show(i); dialog.showModal(); }));
    prev?.addEventListener('click', () => show(index - 1));
    next?.addEventListener('click', () => show(index + 1));
    dialog.querySelector('.lightbox-close')?.addEventListener('click', () => dialog.close());
    dialog.addEventListener('click', (e) => { if (e.target === dialog) dialog.close(); });
    dialog.addEventListener('close', () => { video?.pause(); });
    dialog.addEventListener('keydown', (e) => {
      if (e.key === 'ArrowLeft') show(index - 1);
      if (e.key === 'ArrowRight') show(index + 1);
    });
  });
})();
