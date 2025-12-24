// =====================
// EXTRACT CURRENT STATE
// =====================
function getBQTableData() {
  if (!window.state) return [];

  const rows = [];

  window.state.forEach(module => {
    module.children.forEach(c => {
      rows.push([
        module.name,
        c.name,
        c.rate.toLocaleString(),
        c.volume,
        c.included ? "Yes" : "No",
        (c.included ? c.rate * c.volume : 0).toLocaleString()
      ]);
    });
  });

  return rows;
}

// =====================
// GENERATE PDF (TABLE)
// =====================
function generatePdf() {
  const { jsPDF } = window.jspdf;

  const doc = new jsPDF({ orientation: "portrait", unit: "mm", format: "a4" });

  const tableData = getBQTableData();

  doc.setFontSize(16);
  doc.text(window.projectTitle || "Bill of Quantities", 14, 20);

  doc.autoTable({
    startY: 30,
    head: [["Module", "Item", "Rate", "Qty", "Included", "Total"]],
    body: tableData
  });

  doc.save("BQ.pdf");
}

// =====================
// EVENT LISTENER
// =====================
document.getElementById("downloadPdfBtn")?.addEventListener("click", generatePdf);



function disableBQ() {
  document.querySelectorAll("input, select, textarea, button")
    .forEach(el => {
      if (!el.classList.contains("edit-btn")) el.disabled = true;
    });
}

function enableBQ() {
  document.querySelectorAll("input, select, textarea")
    .forEach(el => el.disabled = false);
}
