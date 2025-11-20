$(function () {
        const currentRoute = window.location.pathname;
        if (!currentRoute.includes("laporan/rapat")) return;

        const datatable = $("#laporan-rapat-table").DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "/laporan/rapat",
                type: "GET",
                data: function (d) {
                    d.tanggal_mulai = $("#tanggal_mulai").val();
                    d.tanggal_selesai = $("#tanggal_selesai").val();
                },
                error: function (xhr, status, error) {
                    console.error("Error fetching data:", error);
                },
            },
            columns: [
                { data: "instansi", name: "rapat_customers.instansi", className: "fw-bold text-dark align-middle" },
                { data: "tanggal", name: "tanggal_pemakaian", className: "align-middle" },
                { data: "waktu", name: "waktu_mulai", orderable: false, className: "align-middle" },
                { data: "paket", name: "ruang_rapat_pakets.name", className: "align-middle" },
                
                // Jumlah Peserta
                { data: "jumlah_peserta", name: "jumlah_peserta", className: "text-center align-middle" },
                
                // Total Pembayaran (Warna Standar/Hitam)
                { 
                    data: "total_pembayaran", 
                    name: "total_pembayaran", 
                    className: "text-end fw-bold align-middle", // Dihapus 'text-success'
                    render: function(data) {
                        const formatted = new Intl.NumberFormat('id-ID', {
                            style: 'currency',
                            currency: 'IDR',
                            minimumFractionDigits: 0
                        }).format(data);
                        return formatted;
                    }
                },
                
                // Status
                { 
                    data: "status", 
                    name: "status_pembayaran",
                    className: "text-center align-middle",
                    render: function(data) {
                        if (data === 'Paid') {
                            return `<span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill">Lunas</span>`;
                        } else if (data === 'DP') {
                            return `<span class="badge bg-warning bg-opacity-10 text-warning px-3 py-2 rounded-pill">DP</span>`;
                        } else {
                            return `<span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2 rounded-pill">${data}</span>`;
                        }
                    }
                }
            ],
            order: [[1, 'desc']]
        });

        $("#btn-filter").on("click", function (e) {
            e.preventDefault();
            datatable.ajax.reload(); 
        });

        $("#btn-reset").on("click", function (e) {
            e.preventDefault();
            $("#tanggal_mulai").val('');
            $("#tanggal_selesai").val('');
            datatable.ajax.reload(); 
        });
    });