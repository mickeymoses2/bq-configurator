// script.js — core module logic with undo/redo, totals

// ---------- Data ----------
const MODULES = [
  { id: "shell", name: "Shell (Including Plaster & Paint)", children: [{ id: "shell1", name: "Shell Works", rate: 2510000, volume: 1 }] },
  { id: "roof", name: "Roof Structure & Cover", children: [{ id: "roof1", name: "Roofing Works", rate: 460000, volume: 1 }] },
  { id: "tiles", name: "All Tiles", children: [{ id: "tiles1", name: "Tiles Supply & Install", rate: 410000, volume: 1 }] },
  { id: "windows", name: "Windows", children: [{ id: "windows1", name: "Window Installations", rate: 150000, volume: 1 }] },
  { id: "kitchen", name: "Kitchen Fittings & Wardrobes", children: [{ id: "kitchens1", name: "Kitchen & Wardrobes", rate: 250000, volume: 1 }] },
  { id: "doorsInt", name: "Doors (Internal)", children: [{ id: "doorsInt1", name: "Internal Doors", rate: 105000, volume: 1 }] },
  { id: "doorsExt", name: "Doors (External)", children: [{ id: "doorsExt1", name: "External Doors", rate: 115000, volume: 1 }] },
  { id: "electrical", name: "Electricals", children: [{ id: "elect1", name: "Electrical Works", rate: 250000, volume: 1 }] },
  { id: "plumbing", name: "Plumbing", children: [{ id: "plumb1", name: "Plumbing Works", rate: 120000, volume: 1 }] },
  { id: "ceiling", name: "Ceiling", children: [{ id: "ceil1", name: "Ceiling Works", rate: 160000, volume: 1 }] },
  { id: "externalWorks", name: "External Works (Drainage & Biodigester)", children: [{ id: "extW1", name: "External Works", rate: 150000, volume: 1 }] },
  { id: "consultants", name: "Consultants", children: [{ id: "consult1", name: "Consultancy", rate: 120000, volume: 1 }] },
  { id: "approvals", name: "Approvals", children: [{ id: "approv1", name: "Approval Fees", rate: 150000, volume: 1 }] }
];

// State variables
let state = [];
let isInEditMode = false;
let status = 'pending';  // Default

// Config from PHP
const appConfig = window.APP_CONFIG || {};
const { 
    canEdit = true, 
    status: serverStatus = 'pending', 
    projectId = 0, 
    bqState 
} = appConfig;

// Apply server status if provided
if (serverStatus && serverStatus !== 'pending') {
    status = serverStatus;
}

window.PROJECT_ID = projectId;

// === HYDRATION ===
console.log("Raw bqState from PHP:", bqState);

if (bqState && typeof bqState === 'string' && bqState.trim() !== '') {
    try {
        const parsed = JSON.parse(bqState);
        if (Array.isArray(parsed) && parsed.length > 0) {
            state = parsed.map(m => ({
                ...m,
                children: m.children.map(c => ({
                    ...c,
                    included: c.included !== undefined ? c.included : true
                }))
            }));
            console.log("✅ Hydrated saved state successfully:", state);
        } else {
            console.warn("Parsed bqState is not a valid module array", parsed);
        }
    } catch (e) {
        console.error("❌ Failed to parse bqState JSON:", e, bqState);
    }
}

// Fallback to defaults
if (state.length === 0) {
    console.warn("No valid state loaded — using defaults");
    state = MODULES.map(m => ({
        ...m,
        children: m.children.map(c => ({ ...c, included: true }))
    }));
}

window.state = state

let undoStack = [];
let redoStack = [];

// ---------- DOM refs ----------
const modulesContainer = document.getElementById("modulesContainer");
const subtotalEl = document.getElementById("subtotal");
const grandTotalEl = document.getElementById("grandTotal");
const parentTotalEl = document.getElementById("parentTotal");

const undoBtn = document.getElementById("undoBtn");
const redoBtn = document.getElementById("redoBtn");
const resetBtn = document.getElementById("resetBtn");
const undoBtnFooter = document.getElementById("undoBtnFooter");
const redoBtnFooter = document.getElementById("redoBtnFooter");
const resetBtnFooter = document.getElementById("resetBtnFooter");
const submitBtn = document.getElementById("suBmit");

const mobileMenuBtn = document.getElementById("mobileMenuBtn");
const sidebar = document.getElementById("sidebar");
const drawerBackdrop = document.getElementById("drawerBackdrop");
const logoutBtn = document.getElementById("logoutBtn");
const editBtn = document.getElementById("editBtn");

// ---------- Helpers ----------
function formatKsh(n) {
  return `KSh ${Number(n).toLocaleString()}`;
}

function snapshotState() {
  return JSON.stringify(state);
}

function saveStateIfChanged(prevSnapshot) {
  const after = snapshotState();
  if (after !== prevSnapshot) {
    undoStack.push(prevSnapshot);
    if (undoStack.length > 50) undoStack.shift();
    redoStack = [];
  }
  updateUndoRedoButtons();
}

function restoreState(snapshot) {
  try {
    state = JSON.parse(snapshot);
  } catch (e) {
    console.error("Invalid snapshot", e);
    return;
  }
  renderModules();
  updateTotals();
  updateUndoRedoButtons();
}

function updateUndoRedoButtons() {
  if (undoBtn) undoBtn.disabled = undoStack.length === 0;
  if (redoBtn) redoBtn.disabled = redoStack.length === 0;
  if (undoBtnFooter) undoBtnFooter.disabled = undoStack.length === 0;
  if (redoBtnFooter) redoBtnFooter.disabled = redoStack.length === 0;
}

// --- Render modules ---
function renderModules() {
    if (!modulesContainer) return;

    const shouldBeDisabled = (status === "submitted" && !isInEditMode);

    // Lock overlay
    if (shouldBeDisabled) {
        modulesContainer.classList.add("bq-locked-overlay");
    } else {
        modulesContainer.classList.remove("bq-locked-overlay");
    }

    modulesContainer.innerHTML = "";

    state.forEach((module, index) => {
        const c = module.children[0];
        const section = document.createElement("section");

        const inputDisabled = shouldBeDisabled ? "disabled" : "";
        const cursorStyle = shouldBeDisabled ? "cursor-not-allowed" : "cursor-pointer";

        section.innerHTML = `
            <h2 class="text-lg mt-4 font-semibold text-black border-b border-gray-200 pb-2 mb-3">
                ${module.name}
            </h2>
            <div class="module-row row flex items-center justify-between gap-3 p-3 rounded-md transition 
                  ${cursorStyle} 
                  ${c.included ? "bg-[#E7F1FE]" : "bg-white"}"
                 data-index="${index}">
                <div class="flex items-center gap-3">
                    <input 
                        type="checkbox" 
                        class="module-checkbox" 
                        data-index="${index}"
                        ${c.included ? "checked" : ""} 
                        ${inputDisabled}
                    >
                    <span class="text-slate-700 flex gap-1">
                        <span>${c.name}</span>
                        <span class="calc-mobile">— ${c.rate.toLocaleString()} × ${c.volume}</span>
                    </span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="price-badge">${formatKsh(c.rate)}</span>
                    ${!c.included ? `<span class="text-red-600 text-sm font-semibold">Not Included</span>` : ""}
                </div>
            </div>
        `;
        modulesContainer.appendChild(section);
    });

    attachCheckboxHandlers();
    updateTotals();
}

// --- Handle checkbox toggles ---
function attachCheckboxHandlers() {
    const checkboxes = document.querySelectorAll(".module-checkbox");
    checkboxes.forEach(cb => {
        cb.addEventListener("change", e => {
            const idx = e.target.dataset.index;
            state[idx].children[0].included = e.target.checked;

            // Optional: autosave
            autosaveState();

            renderModules(); // update styling/colors
        });
    });
}

// --- Autosave function ---
function autosaveState() {
    if (!window.PROJECT_ID) return;

    fetch("/save_bq.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
            project_id: window.PROJECT_ID,
            projectTitle: window.PROJECT_TITLE ?? "Untitled Project",
            projectLocation: window.PROJECT_LOCATION ?? "",
            modules: state
        })
    }).then(res => res.json())
      .then(data => console.log("BQ autosaved", data))
      .catch(console.error);
}

// ---------- Totals ----------
function updateTotals() {
  const subtotal = state.reduce((sum, m) =>
    sum + m.children.reduce((s, c) => s + (c.included ? c.rate * c.volume : 0), 0)
  , 0);

  if (subtotalEl) subtotalEl.textContent = formatKsh(subtotal);
  if (grandTotalEl) grandTotalEl.textContent = formatKsh(subtotal);
  if (parentTotalEl) parentTotalEl.textContent = subtotal.toLocaleString();
}

// ---------- Delegated events ----------
function onModulesContainerClick(e) {
  const row = e.target.closest(".module-row");
  if (!row || !modulesContainer.contains(row)) return;
  if (e.target.matches("input[type='checkbox'], .module-checkbox")) return;

  const idx = parseInt(row.dataset.index);
  if (Number.isNaN(idx)) return;

  const prev = snapshotState();
  state[idx].children[0].included = !state[idx].children[0].included;
  saveStateIfChanged(prev);
  renderModules();
}

function onModulesContainerChange(e) {
  if (!e.target.matches(".module-checkbox")) return;
  const idx = parseInt(e.target.dataset.index);
  if (Number.isNaN(idx)) return;

  const prev = snapshotState();
  const checked = e.target.checked;
  if (state[idx].children[0].included !== checked) {
    state[idx].children[0].included = checked;
    saveStateIfChanged(prev);
    renderModules();
  }
}

modulesContainer?.addEventListener("click", onModulesContainerClick);
modulesContainer?.addEventListener("change", onModulesContainerChange);

// ---------- Undo / Redo / Reset ----------
function undo() {
  if (!undoStack.length) return;
  const curr = snapshotState();
  const prev = undoStack.pop();
  redoStack.push(curr);
  restoreState(prev);
}

function redo() {
  if (!redoStack.length) return;
  const curr = snapshotState();
  const next = redoStack.pop();
  undoStack.push(curr);
  restoreState(next);
}

function reset() {
  const prev = snapshotState();
  state = MODULES.map(m => ({
    ...m,
    children: m.children.map(c => ({ ...c, included: true }))
  }));
  saveStateIfChanged(prev);
  renderModules();
}

// ---------- Buttons ----------
undoBtn?.addEventListener("click", undo);
redoBtn?.addEventListener("click", redo);
resetBtn?.addEventListener("click", reset);
undoBtnFooter?.addEventListener("click", undo);
redoBtnFooter?.addEventListener("click", redo);
resetBtnFooter?.addEventListener("click", reset);

submitBtn?.addEventListener("click", () => {
  const clientName = document.getElementById("clientName")?.value || null;
  const projectTitle = document.getElementById("projectTitle")?.value || null;

  const dataToSave = { clientName, projectTitle, modules: state, submit: true };

  fetch("save_bq.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(dataToSave)
  })
  .then(res => res.json())
  .then(resp => {
    if (resp.status === "success") {
      // ✅ SHOW SUCCESS POPUP
      showSuccessPopup();
    } else {
      // ❌ SHOW ERROR POPUP
      showErrorPopup();
    }
  })
  .catch(err => {
    console.error("Save error:", err);
    // ❌ SHOW ERROR POPUP ON NETWORK ERROR
    showErrorPopup();
  });
});

// ---------- Sidebar + misc ----------
mobileMenuBtn?.addEventListener("click", () => {
  sidebar?.classList.toggle("-translate-x-full");
  drawerBackdrop?.classList.toggle("hidden");
  if (sidebar) sidebar.style.width = "75vw";
});

drawerBackdrop?.addEventListener("click", () => {
  sidebar?.classList.add("-translate-x-full");
  drawerBackdrop.classList.add("hidden");
});

logoutBtn?.addEventListener("click", () => {
  if (logoutBtn.tagName.toLowerCase() !== "a") {
    window.location.href = "logout.php";
  }
});

// ---------- Init ----------
document.addEventListener("DOMContentLoaded", () => {
  const submitBtn = document.getElementById("suBmit");
  const editBtn = document.getElementById("editBtn");

  // Button visibility
  if (status === "submitted") {
    if (submitBtn) submitBtn.style.display = "none";
    if (editBtn) editBtn.style.display = canEdit ? "inline-flex" : "none";
  } else {
    if (submitBtn) submitBtn.style.display = "inline-flex";
    if (editBtn) editBtn.style.display = "none";
  }

  // Edit button handler
  if (editBtn && canEdit) {
    editBtn.addEventListener("click", () => {
      isInEditMode = true;
      if (submitBtn) submitBtn.style.display = "inline-flex";
      if (editBtn) editBtn.style.display = "none";
      renderModules();  // Re-render to remove lock overlay
    });
  }

  renderModules();
  updateTotals();
  updateUndoRedoButtons();
});

// Debug
window.state = state;
window._debugStacks = () => ({ undo: undoStack.length, redo: redoStack.length });