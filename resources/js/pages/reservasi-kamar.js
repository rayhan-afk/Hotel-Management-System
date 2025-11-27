$(document).ready(function () {
    // Definisi Table
    const table = $('#reservation-table').DataTable({
        processing: true,
        serverSide: true,
        searchDelay: 500,
        ajax: {
            url: "/room-info/reservation",
            type: "GET"
        },
        columns: [
            { 
                data: null, 
                sortable: false,
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            { 
                data: 'customer_name', 
                name: 'customers.name',
                class: 'fw-bold'
            },
            { 
                data: 'room_info', 
                name: 'rooms.number',
                render: function(data) {
                    return `
                        <div class="d-flex flex-column">
                            <span class="fw-bold text-primary">Kamar ${data.number}</span>
                            <small class="text-muted">${data.type}</small>
                        </div>
                    `;
                }
            },
            { data: 'check_in', name: 'transactions.check_in' },
            { data: 'check_out', name: 'transactions.check_out' },
            { 
                data: 'breakfast', 
                name: 'transactions.id',
                class: 'text-center',
                orderable: false,
                render: function(data) {
                    return data == 1 
                        ? '<span class="badge bg-success"><i class="fas fa-utensils me-1"></i>Yes</span>' 
                        : '<span class="badge bg-secondary">No</span>';
                }
            },
            { 
                data: 'total_price', 
                name: 'rooms.price',
                class: 'text-end fw-bold',
                render: function(data) {
                    return new Intl.NumberFormat('id-ID', { 
                        style: 'currency', 
                        currency: 'IDR',
                        minimumFractionDigits: 0 
                    }).format(data);
                }
            },
            { 
                data: 'status', 
                name: 'transactions.status',
                class: 'text-center',
                render: function(data) {
                    let badgeClass = 'bg-secondary';
                    if(data === 'Reservation') badgeClass = 'bg-warning text-dark';
                    if(data === 'Paid') badgeClass = 'bg-success';
                    if(data === 'Cancel') badgeClass = 'bg-danger';

                    return `<span class="badge ${badgeClass} px-3 py-2 rounded-pill">${data}</span>`;
                }
            },
            // === BAGIAN TOMBOL AKSI ===
            {
                data: 'raw_id',
                orderable: false,
                searchable: false,
                class: 'text-center',
                render: function(id) {
                    return `
                        <button class="btn btn-sm btn-danger btn-cancel" data-id="${id}" title="Batalkan Reservasi">
                            <i class="fas fa-times-circle"></i> Batal
                        </button>
                    `;
                }
            }
        ],
        order: [[3, 'asc']], 
        language: {
            search: "",
            searchPlaceholder: "Cari tamu, kamar...",
            paginate: {
                next: '<i class="fas fa-chevron-right"></i>',
                previous: '<i class="fas fa-chevron-left"></i>'
            }
        }
    });

    // === LOGIC TOMBOL BATAL (SWEETALERT + AJAX) ===
    $(document).on('click', '.btn-cancel', function() {
        let transactionId = $(this).data('id');
        let csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Tampilkan Konfirmasi Cantik
        Swal.fire({
            title: 'Batalkan Reservasi?',
            text: "Status akan berubah menjadi 'Cancel' dan kamar akan tersedia kembali.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Batalkan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Tampilkan Loading
                Swal.fire({
                    title: 'Memproses...',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading(); }
                });

                // Kirim Request ke Server
                $.ajax({
                    url: `/room-info/reservation/${transactionId}/cancel`, // Route yang akan kita buat
                    type: 'POST',
                    data: {
                        _token: csrfToken
                    },
                    success: function(response) {
                        Swal.fire(
                            'Berhasil!',
                            'Reservasi telah dibatalkan.',
                            'success'
                        );
                        // Reload tabel otomatis tanpa refresh halaman
                        table.ajax.reload(null, false); 
                    },
                    error: function(xhr) {
                        Swal.fire(
                            'Gagal!',
                            'Terjadi kesalahan saat membatalkan reservasi.',
                            'error'
                        );
                    }
                });
            }
        });
    });

    $('.dataTables_filter input').addClass('form-control').css('width', '250px');
});