$(function () {
    const currentRoute = window.location.pathname;
    // Cek route agar script ini hanya jalan di halaman check-out
    if (!currentRoute.includes("transaction/check-out")) return;

    const tableElement = $("#checkout-table");

    if (tableElement.length > 0) {
        const table = tableElement.DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "/transaction/check-out",
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
                        // Status Check-in biasanya hijau/active
                        return `<span class="badge bg-success px-3 py-1 rounded-pill">${data}</span>`;
                    }
                },
                // 9. Aksi (Proses Checkout)
                {
                    data: 'raw_id',
                    orderable: false,
                    searchable: false,
                    className: "text-center",
                    render: function(id) {
                        return `
                            <button class="btn btn-sm btn-warning text-dark btn-checkout rounded-pill shadow-sm fw-bold px-3" 
                                    data-id="${id}" 
                                    data-bs-toggle="tooltip" 
                                    title="Proses Checkout">
                                <i class="fas fa-sign-out-alt me-1"></i> Checkout
                            </button>
                        `;
                    }
                }
            ],
            order: [[4, 'asc']], // Urutkan berdasarkan Check-out terdekat (yang harus segera keluar)
            drawCallback: function() {
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
                var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl)
                })
            }
        });

        // Event Checkout
        $(document).on('click', '.btn-checkout', function() {
            let id = $(this).data('id');
            let csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            Swal.fire({
                title: 'Proses Checkout?',
                text: "Status akan berubah menjadi 'Checked Out' dan kamar akan dibersihkan.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Proses!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({ title: 'Memproses...', didOpen: () => { Swal.showLoading(); } });

                    $.ajax({
                        url: `/transaction/checkout/${id}`, // Pastikan route ini ada di web.php
                        type: 'POST',
                        data: { _token: csrfToken },
                        success: function(response) {
                            Swal.fire('Berhasil!', 'Tamu berhasil checkout.', 'success');
                            table.ajax.reload(null, false); 
                        },
                        error: function(xhr) {
                            Swal.fire('Gagal!', 'Terjadi kesalahan saat memproses.', 'error');
                        }
                    });
                }
            });
        });
    }
});