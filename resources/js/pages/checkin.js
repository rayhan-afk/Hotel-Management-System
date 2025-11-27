$(function () {
    const currentRoute = window.location.pathname;
    if (!currentRoute.includes("check-in")) return;

    const tableElement = $("#checkin-table");
    let datatable = null;

    if (tableElement.length > 0) {
        datatable = tableElement.DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "/transaction/check-in",
                type: "GET",
            },
            columns: [
                { data: "id", name: "id", className: "fw-bold" },
                { data: "customer", name: "customer" },
                { data: "room", name: "room" }, // Sudah ada HTML dari repo (type kamar text-muted)
                { data: "check_in", name: "check_in" },
                { data: "check_out", name: "check_out" },
                { 
                    data: "breakfast", 
                    name: "breakfast",
                    render: function(data) {
                        // Badge sederhana untuk Sarapan
                        if(data === 'Yes' || data === 'Included') {
                            return '<span class="badge bg-info text-dark">Ya</span>';
                        }
                        return '<span class="badge bg-secondary">Tidak</span>';
                    }
                },
                { 
                    data: "total_price", 
                    name: "total_price",
                    className: "fw-bold text-end" // Rata kanan untuk uang
                },
                {
                    data: "status",
                    name: "status",
                    render: function (data) {
                        // Ubah status 'Paid' menjadi Badge Lunas
                        if (data === "Paid") {
                            return '<span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Lunas</span>';
                        }
                        return '<span class="badge bg-warning text-dark">' + data + '</span>';
                    }
                },
                {
                    data: "action",
                    orderable: false,
                    searchable: false,
                    className: "text-center",
                    render: function (id) {
                        return `
                            <button class="btn btn-sm btn-primary btn-edit me-1" data-id="${id}" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-danger btn-delete" data-id="${id}" title="Cancel/Delete">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        `;
                    }
                },
            ],
            order: [[3, "asc"]] // Default urut check-in terdekat
        });
    }

    // --- EVENT: KLIK TOMBOL EDIT (Tidak Berubah) ---
    $(document).on("click", ".btn-edit", function () {
        const id = $(this).data("id");
        $("#editCheckinModal").modal("show");
        $.get(`/transaction/check-in/${id}/edit`, function (html) {
            $("#editCheckinBody").html(html);
        }).fail(function() {
            $("#editCheckinBody").html("<p class='text-danger'>Gagal memuat data.</p>");
        });
    });

    // --- EVENT: SUBMIT FORM UPDATE (Tidak Berubah) ---
    $(document).on("submit", "#form-edit-checkin", function (e) {
        e.preventDefault();
        const form = $(this);
        const btn = form.find("button[type=submit]");
        btn.prop("disabled", true).text("Menyimpan...");

        $.ajax({
            url: form.attr("action"),
            type: "POST",
            data: form.serialize(),
            success: function (res) {
                $("#editCheckinModal").modal("hide");
                datatable.ajax.reload();
                Swal.fire("Berhasil", res.message, "success");
            },
            error: function (xhr) {
                btn.prop("disabled", false).text("Simpan Perubahan");
                Swal.fire("Error", "Gagal memperbarui data", "error");
            }
        });
    });

    // --- EVENT: KLIK TOMBOL DELETE (Tidak Berubah) ---
    $(document).on("click", ".btn-delete", function () {
        const id = $(this).data("id");
        Swal.fire({
            title: "Batalkan Reservasi?",
            text: "Data ini akan dihapus permanen!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Ya, Hapus!",
            cancelButtonText: "Batal"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/transaction/check-in/${id}`,
                    type: "POST",
                    data: {
                        _method: "DELETE",
                        _token: $('meta[name="csrf-token"]').attr("content")
                    },
                    success: function (res) {
                        datatable.ajax.reload();
                        Swal.fire("Terhapus!", res.message, "success");
                    },
                    error: function () {
                        Swal.fire("Error", "Gagal menghapus data", "error");
                    }
                });
            }
        });
    });
});