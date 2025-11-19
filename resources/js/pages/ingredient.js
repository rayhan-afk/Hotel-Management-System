$(function () {
    const currentRoute = window.location.pathname;
    if (!currentRoute.includes("ingredient")) return;

    const datatable = $("#ingredient-table").DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "/ingredient",
            type: "GET",
            data: function (d) {
                d.category_filter = $('#category_filter').val();
            },
            error: function (xhr) {
                console.error("Error:", xhr);
            },
        },
        columns: [
            {
                // 1. No
                data: "id",
                render: (data, type, row, meta) => meta.row + meta.settings._iDisplayStart + 1,
                orderable: false, searchable: false, className: "text-center align-middle"
            },
            { 
                // 2. Nama
                name: "name", data: "name", className: "fw-bold text-dark align-middle" 
            },
            { 
                // 3. Kategori
                name: "category", data: "category", className: "align-middle" 
            },
            { 
                // 4. Stok (Angka)
                name: "stock", data: "stock", className: "text-end pe-4 fw-bold align-middle" 
            },
            { 
                // 5. Satuan
                name: "unit", data: "unit", className: "align-middle" 
            },
            {
                // 6. Status (Badge)
                name: "stock", // Sort by stock
                data: "stock", 
                render: function (data) {
                    let stok = parseFloat(data);
                    if (stok === 0) return '<span class="badge bg-danger">Habis</span>';
                    if (stok < 20) return '<span class="badge bg-warning text-dark">Menipis</span>';
                    if (stok > 50) return '<span class="badge bg-success">Tersedia</span>';
                    return '<span class="badge bg-primary">Cukup</span>';
                },
                className: "align-middle"
            },
            { 
                // 7. Keterangan
                name: "description", data: "description", className: "align-middle" 
            },
            {
                // 8. Action
                data: "id",
                orderable: false, searchable: false, className: "text-center align-middle",
                render: function (id) {
                    return `
                        <button class="btn btn-sm btn-light border text-primary shadow-sm me-1" 
                            data-action="edit" data-id="${id}" title="Edit"><i class="fas fa-edit"></i></button>
                        <button class="btn btn-sm btn-light border text-danger shadow-sm delete-btn" 
                            data-id="${id}" title="Hapus"><i class="fas fa-trash-alt"></i></button>
                        <form id="delete-form-${id}" action="/ingredient/${id}" method="POST" style="display:none;">
                            <input type="hidden" name="_token" value="${$('meta[name="csrf-token"]').attr('content')}">
                            <input type="hidden" name="_method" value="DELETE">
                        </form>
                    `;
                }
            }
        ],
    });

    // Filter Listener
    $('#category_filter').on('change', function() {
        datatable.ajax.reload();
    });

    // Modal Logic
    const modal = new bootstrap.Modal(document.getElementById("main-modal"));

    $("#add-button").on("click", async function () {
        $("#mainModalLabel").text("Tambah Bahan Baku");
        $(".modal-body").html('Loading...');
        modal.show();
        const response = await $.get("/ingredient/create");
        $(".modal-body").html(response.view);
    });

    $(document).on("click", '[data-action="edit"]', async function () {
        const id = $(this).data("id");
        $("#mainModalLabel").text("Edit Bahan Baku");
        $(".modal-body").html('Loading...');
        modal.show();
        const response = await $.get(`/ingredient/${id}/edit`);
        $(".modal-body").html(response.view);
    });

    $("#btn-modal-save").on("click", function () {
        $("#form-save-ingredient").submit();
    });

    $(document).on("submit", "#form-save-ingredient", async function (e) {
        e.preventDefault();
        $(".is-invalid").removeClass("is-invalid");
        $(".error").text("");
        $("#btn-modal-save").attr("disabled", true).text("Menyimpan...");

        try {
            const response = await $.ajax({
                url: $(this).attr("action"),
                method: $(this).attr("method"),
                data: $(this).serialize()
            });
            modal.hide();
            Swal.fire({ icon: "success", title: "Berhasil!", text: response.message, timer: 1500, showConfirmButton: false });
            datatable.ajax.reload();
            
            // Reload page jika kategori baru ditambah agar muncul di filter (opsional)
            // location.reload(); 
        } catch (error) {
            if (error.status === 422) {
                const errors = error.responseJSON.errors;
                for (const [field, messages] of Object.entries(errors)) {
                    $(`#error_${field}`).text(messages[0]);
                    $(`[name="${field}"]`).addClass("is-invalid");
                }
            } else {
                Swal.fire("Error", "Terjadi kesalahan sistem.", "error");
            }
        } finally {
            $("#btn-modal-save").attr("disabled", false).text("Simpan");
        }
    });

    $(document).on("click", ".delete-btn", function () {
        const id = $(this).data("id");
        Swal.fire({
            title: "Hapus bahan ini?",
            text: "Data tidak bisa dikembalikan!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            confirmButtonText: "Ya, Hapus!"
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    await $.ajax({
                        url: $(`#delete-form-${id}`).attr("action"),
                        method: "POST",
                        data: $(`#delete-form-${id}`).serialize()
                    });
                    Swal.fire("Terhapus!", "", "success");
                    datatable.ajax.reload();
                } catch (e) {
                    Swal.fire("Gagal", "Gagal menghapus data.", "error");
                }
            }
        });
    });
});