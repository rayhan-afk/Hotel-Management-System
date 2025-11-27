$(function () {
    const currentRoute = window.location.pathname;
    if (!currentRoute.includes("check-out")) return;

    const tableElement = $("#checkout-table");
    let datatable = null;

    if (tableElement.length > 0) {
        datatable = tableElement.DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "/transaction/check-out",
                type: "GET",
            },
            columns: [
                { data: "id", name: "id", className: "fw-bold" },
                { data: "customer", name: "customer" },
                { data: "room", name: "room" },
                { data: "check_in", name: "check_in" },
                { data: "check_out", name: "check_out" },
                { 
                    data: "breakfast", 
                    name: "breakfast",
                    render: function(data) {
                        return (data === 'Yes' || data === 'Included') 
                            ? '<span class="badge bg-info text-dark">Ya</span>' 
                            : '<span class="badge bg-secondary">Tidak</span>';
                    }
                },
                { data: "total_price", name: "total_price", className: "fw-bold text-end" },
                {
                    data: "status",
                    name: "status",
                    render: function (data) {
                        return (data === "Paid")
                            ? '<span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Lunas</span>'
                            : '<span class="badge bg-warning text-dark">' + data + '</span>';
                    }
                },
                {
                    data: "action",
                    orderable: false,
                    searchable: false,
                    className: "text-center",
                    render: function (id) {
                        return `
                            <button class="btn btn-sm btn-success btn-checkout" data-id="${id}">
                                <i class="fas fa-check"></i> Checkout
                            </button>
                        `;
                    }
                },
            ],
            order: [[4, "asc"]]
        });
    }

    // === EVENT CHECKOUT (UPDATED) ===
    $(document).on("click", ".btn-checkout", function () {
        const id = $(this).data("id");

        Swal.fire({
            title: "Checkout Tamu?",
            text: "Data akan dipindahkan ke Laporan Kamar.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#28a745",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Ya, Checkout!",
            cancelButtonText: "Batal"
        }).then((result) => {
            if (result.isConfirmed) {
                
                $.ajax({
                    url: `/transaction/checkout/${id}`,
                    type: "POST",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr("content")
                    },
                    success: function (res) {
                        datatable.ajax.reload();
                        Swal.fire("Berhasil!", res.message, "success");
                    },
                    error: function () {
                        Swal.fire("Error", "Gagal melakukan checkout", "error");
                    }
                });
            }
        });
    });
});
