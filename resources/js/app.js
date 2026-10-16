import "./bootstrap";
import "preline";

import Alpine from "alpinejs";

window.Alpine = Alpine;

Alpine.start();

// =========================================
// Start Export
// ========================================
// Fonction pour créer une copie de la table sans la colonne "Action"
function getTableWithoutActionColumn() {
    const tableClone = $("#export-table").clone();

    // Supprimer les colonnes "Action" (th et td ayant la classe action-column)
    tableClone
        .find("th.action-column, td.action-column, div.action-column")
        .remove();

    tableClone.find("td, th").each(function () {
        const cell = $(this);

        // Remplacer les icônes Font Awesome
        if (cell.find("i.fa-solid.fa-check").length) {
            cell.html("présent");
        } else if (cell.find("i.fa-solid.fa-xmark").length) {
            cell.html("x");
        }

        // Remplacer les icônes SVG
        if (cell.find("svg.bi-exclamation-circle").length) {
            cell.html("En retard");
        }

        // Remplacer les avatars
        if (cell.find("img").length) {
            const employeeName = cell.text().trim();
            cell.text(employeeName || "Employé");
        }
    });

    return tableClone[0];
}

// Exporter en Excel
$("#export-excel").click(function () {
    // Créer une table sans la colonne "Action"
    const tableWithoutAction = getTableWithoutActionColumn();
    const wb = XLSX.utils.table_to_book(tableWithoutAction, {
        sheet: "Sheet1",
    });
    XLSX.writeFile(wb, "export.xlsx");
});

// Exporter en CSV
$("#export-csv").click(function () {
    // Créer une table sans la colonne "Action"
    const tableWithoutAction = getTableWithoutActionColumn();

    const wb = XLSX.utils.table_to_book(tableWithoutAction, {
        sheet: "Sheet1",
    });
    const ws = wb.Sheets["Sheet1"];

    const csv = XLSX.utils.sheet_to_csv(ws);

    const blob = new Blob([csv], {
        type: "text/csv;charset=utf-8;",
    });
    const link = document.createElement("a");
    link.href = URL.createObjectURL(blob);
    link.download = "export.csv";
    link.click();
});

// Exporter en PDF
$("#export-pdf").click(function () {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF({
        orientation: "landscape",
        unit: "px",
        format: "a4",
    });

    // Créer une table sans la colonne "Action"
    const tableWithoutAction = getTableWithoutActionColumn();

    // Utiliser autoTable pour convertir la table HTML en PDF
    doc.autoTable({
        html: tableWithoutAction,
        startY: 20,
        margin: { top: 20, left: 20, right: 20, bottom: 20 },
        theme: "grid",
        tableWidth: "wrap",
        styles: {
            fontSize: 8,
            cellPadding: 3,
            valign: "middle",
            halign: "center",
            overflow: "linebreak",
        },
        columnStyles: {
            0: { halign: "left" },
        },
        didDrawCell: (data) => {},
    });

    doc.save("export.pdf");
});

// =========================================
// End Export
// ========================================
