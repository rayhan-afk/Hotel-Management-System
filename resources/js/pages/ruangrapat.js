$(function () {
        const currentRoute = window.location.pathname;
        
        // 1. Pastikan kode hanya berjalan di halaman /ruangrapat
        if (!currentRoute.split("/").includes("ruangrapat")) {
            return; 
        }

        // 2. Inisialisasi DataTable
        const datatable = $("#ruangrapat-table").DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: `/ruangrapat`, // Memanggil RuangRapatController@index
                type: "GET",
                error: function (xhr, status, error) {
                    console.error("Gagal memuat DataTable:", error);
                },
            },
            columns: [
                {
                    name: "DT_RowIndex", data: "DT_RowIndex",
                    orderable: false, searchable: false,
                },
                { name: "name", data: "name" }, // Sesuai DB
                { 
                    name: "isi_paket", data: "isi_paket",
                    orderable: false, searchable: false 
                },
                { 
                    name: "fasilitas", data: "fasilitas",
                    orderable: false, searchable: false
                },
                {
                    name: "harga", data: "harga",
                    render: function (price) {
                        return `<div>${new Intl.NumberFormat().format(price)}</div>`;
                    },
                },
                {
                    name: "id", data: "id",
                    orderable: false, searchable: false,
                    render: function (paketId) { // Ganti 'roomId'
                        return `
                            <button class="btn btn-light btn-sm rounded shadow-sm border"
                                data-action="edit-ruangrapat" data-ruangrapat-id="${paketId}"
                                data-bs-toggle="tooltip" data-bs-placement="top" title="Edit Paket">
                                <i class="fas fa-edit"></i>
                            </button>
                            
                            <form class="btn btn-sm delete-ruangrapat" method="POST"
                                id="delete-ruangrapat-form-${paketId}"
                                action="/ruangrapat/${paketId}">
                                <a class="btn btn-light btn-sm rounded shadow-sm border delete"
                                    href="#" paket-id="${paketId}" data-bs-toggle="tooltip"
                                    data-bs-placement="top" title="Delete Paket">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            </form>
                            
                            <a class="btn btn-light btn-sm rounded shadow-sm border"
                                href="/ruangrapat/${paketId}"
                                data-bs-toggle="tooltip" data-bs-placement="top"
                                title="Detail Paket">
                                <i class="fas fa-info-circle"></i>
                            </a>
                        `;
                    },
                },
            ],
        });

        // 3. Inisialisasi Modal (Modal-nya ada di master.blade.php)
        const modal = new bootstrap.Modal($("#main-modal"), {
            backdrop: true, keyboard: true, focus: true,
        });

        // 4. Event Handlers (disesuaikan untuk 'ruangrapat')
        $(document)
            .on("click", ".delete", function (e) {
                e.preventDefault(); 
                var paket_id = $(this).attr("paket-id");
                const swalWithBootstrapButtons = Swal.mixin({
                    customClass: {
                        confirmButton: "btn btn-success",
                        cancelButton: "btn btn-danger",
                    },
                    buttonsStyling: false,
                });

                swalWithBootstrapButtons
                    .fire({
                        title: "Are you sure?",
                        text: "Paket akan dihapus, Anda tidak bisa mengembalikannya!",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonText: "Yes, delete it!",
                        cancelButtonText: "No, cancel! ",
                        reverseButtons: true,
                    })
                    .then((result) => {
                        if (result.isConfirmed) {
                            $(`#delete-ruangrapat-form-${paket_id}`).submit();
                        }
                    });
            })
            // INI ADALAH FUNGSI YANG ANDA CARI (untuk klik "Tambah Paket Baru")
            .on("click", "#add-button", async function () {
                modal.show();
                $("#main-modal .modal-body").html(`Fetching data...`);
                
                try {
                    // Memanggil route GET /ruangrapat/create
                    const response = await $.get(`/ruangrapat/create`);
                    if (!response) {
                        return;
                    }
                    // Menampilkan form (create.blade.php) di dalam modal
                    $("#main-modal .modal-title").text("Buat Paket Baru");
                    $("#main-modal .modal-body").html(response.view);
                } catch (e) {
                    console.error("Error saat fetch /ruangrapat/create:", e);
                    $("#main-modal .modal-body").html(`Error: ${e.statusText}`);
                }
            })
            .on("click", "#btn-modal-save", function () {
                $("#form-save-ruangrapat").submit();
            })
            .on("submit", "#form-save-ruangrapat", async function (e) {
                e.preventDefault();
                $("#btn-modal-save").attr("disabled", true);
                try {
                    const response = await $.ajax({
                        url: $(this).attr("action"),
                        data: $(this).serialize(),
                        method: $(this).attr("method"),
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                        },
                    });

                    if (!response) return;

                    Swal.fire({
                        position: "top-end", icon: "success",
                        title: response.message, showConfirmButton: false, timer: 1500,
                    });

                    modal.hide();
                    datatable.ajax.reload();
                } catch (e) {
                    if (e.status === 422) {
                        console.log(e);
                        Swal.fire({
                            icon: "error", title: "Oops...",
                            text: e.responseJSON.message,
                        });
                    }
                } finally {
                    $("#btn-modal-save").attr("disabled", false);
                }
            })
            .on("click", '[data-action="edit-ruangrapat"]', async function () {
                modal.show();
                $("#main-modal .modal-body").html(`Fetching data...`);
                
                const paketId = $(this).data("ruangrapat-id");
                
                try {
                    const response = await $.get(`/ruangrapat/${paketId}/edit`);
                    if (!response) return;

                    $("#main-modal .modal-title").text("Edit Paket");
                    $("#main-modal .modal-body").html(response.view);
                } catch (e) {
                     console.error("Error saat fetch /ruangrapat/edit:", e);
                    $("#main-modal .modal-body").html(`Error: ${e.statusText}`);
                }
            })
            .on("submit", ".delete-ruangrapat", async function (e) {
                e.preventDefault();
                
                try {
                    const response = await $.ajax({
                        url: $(this).attr("action"),
                        method: "DELETE", // Kirim sebagai request DELETE
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                        },
                    });

                    if (!response) return;

                    Swal.fire({
                        position: "top-end", icon: "success",
                        title: response.message, showConfirmButton: false, timer: 1500,
                    });

                    datatable.ajax.reload();
                } catch (e) {
                    Swal.fire({
                        icon: "error", title: "Oops...",
                        text: "Gagal menghapus data!",
                    });
                }
            });
    });