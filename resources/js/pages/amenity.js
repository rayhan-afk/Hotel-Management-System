$(function () {
    const currentRoute = window.location.pathname;
    if (!currentRoute.split("/").includes("amenity")) return;

    const datatable = $("#amenity-table").DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: `/amenity`,
            type: "GET",
            error: function (xhr, status, error) {
                console.error("Error fetching data:", error);
            },
        },
        columns: [
            {
                // 1. No (MATIKAN SORTING)
                data: "id",
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                },
                orderable: false,   // <--- PENTING: Matikan sort
                searchable: false,  // <--- PENTING: Matikan search
                className: "text-center align-middle"
            },
            {
                // 2. Nama Barang
                name: "nama_barang",
                data: "nama_barang",
                className: "fw-bold text-dark align-middle"
            },
            {
                // 3. Stok (Hanya Angka)
                name: "stok",
                data: "stok",
                className: "text-end pe-4 fw-bold align-middle"
            },
            {
                // 4. Satuan
                name: "satuan",
                data: "satuan",
                className: "align-middle"
            },
            {
                // 5. Status (Badge Warna)
                name: "stok", 
                data: "stok", 
                render: function (data) {
                    let stok = parseInt(data);
                    
                    // 1. Stok 0 = Habis
                    if (stok === 0) {
                        return '<span class="badge bg-danger">Habis</span>';
                    } 
                    // 2. Kurang dari 20 = Menipis
                    else if (stok < 20) {
                        return `<span class="badge bg-warning text-dark">Menipis</span>`;
                    } 
                    // 3. Lebih dari 50 = Tersedia
                    else if (stok > 50) {
                        return `<span class="badge bg-success">Tersedia</span>`;
                    } 
                    // 4. Sisanya (20-50) = Cukup
                    else {
                        return `<span class="badge bg-primary">Cukup</span>`;
                    }
                },
                className: "align-middle"
            },
            {
                // 6. Keterangan
                name: "keterangan",
                data: "keterangan",
                className: "align-middle"
            },
            {
                // 7. Action (MATIKAN SORTING)
                name: "id", // name diperlukan untuk mapping
                data: "id",
                orderable: false,   // <--- PENTING: Matikan sort
                searchable: false,  // <--- PENTING: Matikan search
                className: "text-center align-middle",
                render: function (id) {
                    return `
                        <button class="btn btn-sm btn-light border text-primary shadow-sm me-1" 
                            data-action="edit-amenity" data-id="${id}"
                            data-bs-toggle="tooltip" data-bs-placement="top" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <form class="btn btn-sm delete-amenity" method="POST"
                            id="delete-amenity-form-${id}"
                            action="/amenity/${id}" style="display:inline; padding:0;">
                            <a class="btn btn-light btn-sm rounded shadow-sm border delete"
                                href="#" data-id="${id}" data-bs-toggle="tooltip"
                                data-bs-placement="top" title="Delete">
                                <i class="fas fa-trash-alt"></i>
                            </a>
                        </form>
                    `;
                },
            },
        ],
    });

    // --- LOGIKA MODAL & BUTTONS (Sama seperti sebelumnya) ---
    
    const modal = new bootstrap.Modal($("#main-modal"), {
        backdrop: true,
        keyboard: true,
        focus: true,
    });

    $(document)
        .on("click", ".delete", function (e) {
            e.preventDefault();
            var id = $(this).data("id");
            Swal.fire({
                title: "Yakin ingin menghapus?",
                text: "Data tidak bisa dikembalikan!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#F2C2B8",
                cancelButtonColor: "#8FB8E1",
                confirmButtonText: "Ya, Hapus!",
                cancelButtonText: "Batal",
                customClass: {
                    confirmButton: 'text-50200C',
                    cancelButton: 'text-50200C'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $(`#delete-amenity-form-${id}`).trigger('submit');
                }
            });
        })
        .on("click", "#add-button", async function () {
            modal.show();
            $("#main-modal .modal-body").html(`Fetching data...`);
            const response = await $.get(`/amenity/create`);
            $("#main-modal .modal-title").text("Add New Amenity");
            $("#main-modal .modal-body").html(response.view);
        })
        .on("click", '[data-action="edit-amenity"]', async function () {
            modal.show();
            $("#main-modal .modal-body").html(`Fetching data...`);
            const id = $(this).data("id");
            const response = await $.get(`/amenity/${id}/edit`);
            $("#main-modal .modal-title").text("Edit Amenity");
            $("#main-modal .modal-body").html(response.view);
        })
        .on("click", "#btn-modal-save", function () {
            $("#form-save-amenity").submit();
        })
        .on("submit", "#form-save-amenity", async function (e) {
            e.preventDefault();
            if (typeof CustomHelper !== 'undefined') CustomHelper.clearError();
            $("#btn-modal-save").attr("disabled", true).text("Saving...");
            
            try {
                const response = await $.ajax({
                    url: $(this).attr("action"),
                    data: $(this).serialize(),
                    method: $(this).attr("method"),
                    headers: { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") },
                });
                
                Swal.fire({ icon: "success", title: response.message, showConfirmButton: false, timer: 1500 });
                modal.hide();
                datatable.ajax.reload();
            } catch (e) {
                if (e.status === 422 && typeof CustomHelper !== 'undefined') {
                    CustomHelper.errorHandlerForm(e);
                } else {
                    Swal.fire({ icon: "error", title: "Error", text: "Something went wrong!" });
                }
            } finally {
                $("#btn-modal-save").attr("disabled", false).text("Save");
            }
        })
        .on("submit", ".delete-amenity", async function (e) {
            e.preventDefault();
            try {
                const response = await $.ajax({
                    url: $(this).attr("action"),
                    data: $(this).serialize(),
                    method: "POST",
                    headers: { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") },
                });
                Swal.fire({ icon: "success", title: response.message, showConfirmButton: false, timer: 1500 });
                datatable.ajax.reload();
            } catch (e) {
                Swal.fire({ icon: "error", title: "Error", text: "Failed to delete data." });
            }
        });
});