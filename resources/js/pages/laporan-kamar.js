$(function () {
    const currentRoute = window.location.pathname;
    // Pastikan JS ini hanya jalan di halaman laporan kamar
    if (!currentRoute.includes("laporan/kamar")) return;

    console.log("Laporan Kamar JS Loaded");

    // Selector Tabel
    const tableElement = $("#laporan-kamar-table");
    let datatable = null;

    if (tableElement.length > 0) {
        datatable = tableElement.DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "/laporan/kamar",
                type: "GET",
                data: function (d) {
                    // Kirim parameter filter ke server
                    d.start_date = $("#start_date").val();
                    d.end_date = $("#end_date").val();
                },
                error: function (xhr, error, thrown) {
                    console.error("DataTables Error:", xhr.responseText);
                }
            },
            columns: [
                // 0. Tamu
                { 
                    data: "tamu", 
                    name: "customers.name", 
                    className: "fw-bold text-dark align-middle" 
                },
                // 1. Kamar
                { 
                    data: "kamar", 
                    name: "rooms.number", 
                    className: "align-middle" 
                },
                // 2. Check-In
                { 
                    data: "check_in", 
                    name: "transactions.check_in", 
                    className: "align-middle" 
                },
                // 3. Check-Out
                { 
                    data: "check_out", 
                    name: "transactions.check_out", 
                    className: "align-middle" 
                },
                // 4. Sarapan
                { 
                    data: "sarapan", 
                    name: "transactions.breakfast", 
                    className: "text-center align-middle",
                    render: function(data) {
                        if (data === 'Yes') {
                            return `<span class="badge bg-success bg-opacity-10 text-success border border-success px-3 py-2"><i class="fas fa-utensils me-1"></i> Ya</span>`;
                        }
                        return `<span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary px-3 py-2"><i class="fas fa-times me-1"></i> Tidak</span>`;
                    }
                },
                // 5. Total Harga
                { 
                    data: "total_harga", 
                    name: "transactions.total_price", 
                    className: "text-end fw-bold align-middle text-dark",
                    // Jika data dari server masih angka mentah, format di sini. 
                    // Tapi LaporanKamarRepository sudah kirim string Rp, jadi aman.
                },
                // 6. Status
                { 
                    data: "status", 
                    name: "transactions.status", 
                    className: "text-center align-middle",
                    render: function(data) {
                        // Selalu Paid sesuai logika baru
                        return `<span class="badge bg-success rounded-pill px-3 py-2"><i class="fas fa-check-circle me-1"></i> Lunas</span>`;
                    }
                }
            ],
            order: [[2, 'desc']], // Default urut berdasarkan Tanggal Check-In (Terbaru)
            language: {
                emptyTable: `<div class="d-flex flex-column align-items-center py-5 text-muted">
                                <i class="fas fa-folder-open fa-3x mb-3 opacity-25"></i>
                                <p class="mb-0 fw-bold">Belum ada data reservasi</p>
                                <small>Silakan lakukan reservasi terlebih dahulu.</small>
                            </div>`
            }
        });
    }

    // --- EVENT LISTENERS (Agar Tombol Berfungsi) ---

    // 1. Tombol Search / Filter
    $("#btn-filter").on("click", function (e) {
        e.preventDefault();
        if(datatable) {
            datatable.ajax.reload(); // Reload tabel dengan parameter tanggal baru
        }
    });

    // 2. Tombol Reset
    $("#btn-reset").on("click", function (e) {
        e.preventDefault();
        $("#start_date").val('');
        $("#end_date").val('');
        if(datatable) {
            datatable.ajax.reload(); // Reload tabel tanpa filter
        }
    });

    // 3. Tombol Export Excel
    $("#btn-export-kamar").on("click", function (e) {
        e.preventDefault();
        const startDate = $("#start_date").val();
        const endDate = $("#end_date").val();
        
        let url = "/laporan/kamar/export?";
        if(startDate) url += `start_date=${startDate}&`;
        if(endDate) url += `end_date=${endDate}`;
        
        // Redirect ke URL download
        window.location.href = url;
    });
});