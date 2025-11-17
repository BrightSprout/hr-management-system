// DASHBOARD-PHP
lucide.createIcons();

// Mobile menu functionality
const mobileMenuBtn = document.getElementById('mobile-menu-btn');
const sidebar = document.getElementById('sidebar');
const closeSidebar = document.getElementById('close-sidebar');
const mobileOverlay = document.getElementById('mobile-overlay');
const sidenavLinks = document.querySelectorAll("#navigator > button.nav-item");

async function displayPendingTotal() {
  const leavesResponse  = await fetch("api/list-pending-leaves"); 
  const employeesResponse = await fetch("api/count-pending-employees"); 
  if (leavesResponse.ok && employeesResponse.ok) {
    const totalPending = Object.values(await leavesResponse.json()).length + (await employeesResponse.json()).total;
    document.querySelector(".register-approval-total").textContent = totalPending;
  }
}

const disposables = {};
const origAddListener = EventTarget.prototype.addEventListener;
const origRemoveListener = EventTarget.prototype.removeEventListener;
const origSetInterval = window.setInterval;
const origSetTimeout = window.setTimeout;

EventTarget.prototype.addEventListener = function(type, listener, options) {
  origAddListener.call(this, type, listener, options);
  (disposables[document.currentScript?.src]??=[]).push(() => origRemoveListener.call(this, type, listener, options));
};

window.setInterval = function(fn, delay, ...args) {
  const id = origSetInterval(fn, delay, ...args);
  (disposables[document.currentScript?.src]??=[]).push(() => clearInterval(id));
}

window.setTimeout = function(fn, delay, ...args) {
  const id = origSetTimeout(fn, delay, ...args);
  (disposables[document.currentScript?.src]??=[]).push(() => clearTimeout(id));
}

const pageCache = {};
const pageAssets = {};

mobileMenuBtn?.addEventListener('click', () => {
    sidebar.classList.remove('sidebar-closed');
    sidebar.classList.add('sidebar-open');
    mobileOverlay.classList.add('active');
});

closeSidebar?.addEventListener('click', () => {
    sidebar.classList.remove('sidebar-open');
    sidebar.classList.add('sidebar-closed');
    mobileOverlay.classList.remove('active');
});

mobileOverlay?.addEventListener('click', () => {
    sidebar.classList.remove('sidebar-open');
    sidebar.classList.add('sidebar-closed');
    mobileOverlay.classList.remove('active');
});

function unloadAssets(scriptsKeepList, stylesKeepList, mainPath) {
  document.querySelectorAll("link[data-dynamic], script[data-dynamic]").forEach(el => {
    const isCSS = el.tagName === 'LINK';
    const url = isCSS ? el.href : el.src; 
    const filename = url.split("/").pop();
    if (mainPath && el.dataset.mainPath != mainPath)
      return;
    if (!(isCSS ? stylesKeepList.includes(filename) : (scriptsKeepList.includes(filename) || scriptsKeepList.includes(url)))) {
      el.remove();
      if (!isCSS)
        for (let disposable of (disposables[url] ?? []))
          disposable();
    }
  });
}

function loadAssets(scripts, styles,mainPath) {
  for (let scriptSrc of scripts) {
    const src = scriptSrc.startsWith("http") ? scriptSrc :  "public/assets/js/" + scriptSrc;
    if (document.querySelector(`script[data-dynamic="true"][src="${src}"]`))
      continue;
    const scriptEl = document.createElement("script");
    scriptEl.src = src;
    scriptEl.async = false;
    scriptEl.dataset.dynamic = 'true';
    if (mainPath)
      scriptEl.dataset.mainPath = mainPath;
    document.body.appendChild(scriptEl);
  }

  for (let styleHref of styles) {
    if (document.querySelector(`link[data-dynamic="true"][src="public/assets/css/${styleHref}"]`))
      continue;
    const linkEl = document.createElement("link");
    linkEl.rel = "stylesheet";
    linkEl.href = "public/assets/css/" + styleHref;
    linkEl.dataset.dynamic = 'true';
    if (mainPath)
      linkEl.dataset.mainPath = mainPath;
    document.head.appendChild(linkEl);
  }
}

function renderPage(page, html, scripts, styles, isSub) {
  const contentArea = document.getElementById(isSub ? "secondary-content-area": "main-content-area");
  contentArea.innerHTML = html;
  contentArea.setAttribute("data-path",page);
  lucide.createIcons();
  unloadAssets(scripts, styles, isSub ? page.split("/")[0] : null);
  loadAssets(scripts, styles, isSub ? page.split("/")[0] : null);
}

async function retrievePage(page) {
  let html;
  if (!pageCache[page]) {
    const response = await fetch(`dashboard_content?page=${page}`);
    html = await response.text();
    pageCache[page] = html;
  } else html = pageCache[page];
  return html;
}

async function loadPage(page) {
  document.querySelector("#navigator > button.nav-item.active")?.classList.remove("active");
  document.querySelector(`#navigator > button.nav-item[data-navfor="?page=${page}"]`)?.classList.add("active");

  try {
    if (!(await fetch("api/auth_check")).ok) throw new Error("Authentication Failed!");
    if (page.includes("/")) {
      const main = page.split("/")[0];
      const mainHTML = await retrievePage(main); 
      const subHTML = await retrievePage(page); 
      const mainAssets = pageAssets[main];
      const subAssets = pageAssets[page];
      if (document.querySelector("#main-content-area").dataset.path !== main)
        renderPage(main, mainHTML, mainAssets.scripts, mainAssets.styles);
      renderPage(page, subHTML, subAssets.scripts, subAssets.styles, true);
    } else { 
      const html = await retrievePage(page); 
      const { scripts, styles } = pageAssets[page];
      if (document.querySelector("#main-content-area").dataset.path !== page)
        renderPage(page, html, scripts, styles);
    }
  } catch(error) {
    location.assign("login");
  }
  
  if (document.body.dataset.userRole == "GMAC")
    displayPendingTotal();
}

async function navigatePage(href) {
  await loadPage((new URLSearchParams(href)).get("page"));
  history.pushState(null, '', href);
}

document.addEventListener('DOMContentLoaded', async () => {
    const urlParams = new URLSearchParams(window.location.search);
    const page = urlParams.get('page') || 'dashboard';
    const response = await fetch("public/assets/dashboard-assets.json");
    const json = await response.json();
    if (document.body.dataset.userRole == "STAFF")
      json.dashboard.scripts = ["view-staff.js"];
    Object.assign(pageAssets, json);
    loadPage(page);
});

window.addEventListener('popstate', () => {
    const urlParams = new URLSearchParams(window.location.search);
    const page = urlParams.get('page') || 'dashboard';
    loadPage(page);
});

for (let link of sidenavLinks) {
  const href = link.dataset.navfor;
  link.addEventListener("click", function(e) {
    navigatePage(href); 
  });
}

function previewProfileImage(event) {
    const input = event.target;
    const image = document.getElementById('profileImagePreview');
    const icon = document.getElementById('defaultProfileIcon');

    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function (e) {
            image.src = e.target.result;
            image.classList.remove('hidden');
            icon.classList.add('hidden');
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// END OF DASHBOARD-PHP

