$(function () {
    const currentRoute = window.location.pathname;
    // Cek route agar script ini hanya jalan di halaman check-in
    if (!currentRoute.includes("transaction/check-in")) return;

    const tableElement = $("#checkin-table");

    if (tableElement.length > 0) {
        const table = tableElement.DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "/transaction/check-in",
                type: "GET",
                error: function (xhr, status, error) {
                    console.error("Datatable Error:", error);
                },
            },
            columns: [
                // 1. No
                { 
                    data: null, 
                    sortable: false,
                    render: function (data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                // 2. Tamu
                { 
                    name: "customers.name", 
                    data: "customer_name",
                    className: "fw-bold text-primary"
                },
                // 3. Kamar
                { 
                    name: "rooms.number", 
                    data: "room_info",
                    render: function(data) {
                        return `
                            <div class="d-flex flex-column">
                                <span class="fw-bold text-dark">${data.number}</span>
                                <span class="text-muted small">${data.type}</span>
                            </div>
                        `;
                    }
                },
                // 4. Check In
                { name: "transactions.check_in", data: "check_in" },
                // 5. Check Out
                { name: "transactions.check_out", data: "check_out" },
                // 6. Sarapan
                { 
                    name: "transactions.id", // Dummy
                    data: "breakfast",
                    className: "text-center",
                    orderable: false,
                    render: function(data) {
                        return data == 1 
                            ? '<span class="badge bg-success"><i class="fas fa-check me-1"></i>Yes</span>' 
                            : '<span class="badge bg-secondary">No</span>';
                    }
                },
                // 7. Total Harga
                { 
                    name: "rooms.price", 
                    data: "total_price",
                    className: "text-end fw-bold",
                    render: function(data) {
                        return new Intl.NumberFormat('id-ID', { 
                            style: 'currency', 
                            currency: 'IDR',
                            minimumFractionDigits: 0 
                        }).format(data);
                    }
                },
                // 8. Status
                { 
                    name: "transactions.status", 
                    data: "status",
                    className: "text-center",
                    render: function(data) {
                        // Biasanya status Check-in warnanya biru/primary
                        return `<span class="badge bg-primary px-3 py-1 rounded-pill">${data}</span>`;
                    }
                },
                // 9. Aksi (Edit)
                {
                    data: 'raw_id',
                    orderable: false,
                    searchable: false,
                    className: "text-center",
                    render: function(id) {
                        return `
                            <button class="btn btn-sm btn-info text-white btn-edit rounded-circle shadow-sm" 
                                    data-id="${id}" 
                                    data-bs-toggle="tooltip" 
                                    title="Edit Data Check-in">
                                <i class="fas fa-edit"></i>
                            </button>
                        `;
                    }
                }
            ],
            order: [[3, 'desc']], // Urutkan berdasarkan Check-in terbaru
            drawCallback: function() {
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
                var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl)
                })
            }
        });

        // Event Edit (Menampilkan Modal)
        $(document).on('click', '.btn-edit', function() {
            let id = $(this).data('id');
            let modal = new bootstrap.Modal(document.getElementById('editCheckinModal'));
            
            // Tampilkan loading dulu
            $('#editCheckinBody').html('<div class="text-center py-3"><div class="spinner-border text-primary"></div></div>');
            modal.show();

            // Fetch form edit dari server
            $.get(`/transaction/check-in/${id}/edit`, function(response) {
                $('#editCheckinBody').html(response);
            }).fail(function() {
                $('#editCheckinBody').html('<div class="alert alert-danger">Gagal memuat data.</div>');
            });
        });
    }
});