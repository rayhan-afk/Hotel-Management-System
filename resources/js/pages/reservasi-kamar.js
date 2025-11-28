$(function () {
    const currentRoute = window.location.pathname;
    if (!currentRoute.includes("room-info/reservation")) return;

    const tableElement = $("#reservation-table");

    if (tableElement.length > 0) {
        const table = tableElement.DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "/room-info/reservation",
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
                        let badgeClass = 'bg-secondary';
                        if(data === 'Reservation') badgeClass = 'bg-warning text-dark';
                        if(data === 'Paid') badgeClass = 'bg-success';
                        
                        return `<span class="badge ${badgeClass} px-3 py-1 rounded-pill">${data}</span>`;
                    }
                },
                // 9. Aksi (Cancel)
                {
                    data: 'raw_id',
                    orderable: false,
                    searchable: false,
                    className: "text-center",
                    render: function(id) {
                        return `
                            <button class="btn btn-sm btn-danger btn-cancel rounded-circle shadow-sm" 
                                    data-id="${id}" 
                                    data-bs-toggle="tooltip" 
                                    title="Batalkan Reservasi">
                                <i class="fas fa-times"></i>
                            </button>
                        `;
                    }
                }
            ],
            order: [[3, 'asc']], // Urutkan berdasarkan Check-in terdekat
            drawCallback: function() {
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
                var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl)
                })
            }
        });

        // Event Cancel
        $(document).on('click', '.btn-cancel', function() {
            let transactionId = $(this).data('id');
            let csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            Swal.fire({
                title: 'Batalkan Reservasi?',
                text: "Status akan berubah menjadi 'Cancel'.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Batalkan!'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({ title: 'Memproses...', didOpen: () => { Swal.showLoading(); } });

                    $.ajax({
                        url: `/room-info/reservation/${transactionId}/cancel`,
                        type: 'POST',
                        data: { _token: csrfToken },
                        success: function(response) {
                            Swal.fire('Berhasil!', 'Reservasi dibatalkan.', 'success');
                            table.ajax.reload(null, false); 
                        },
                        error: function(xhr) {
                            Swal.fire('Gagal!', 'Terjadi kesalahan.', 'error');
                        }
                    });
                }
            });
        });
    }
});