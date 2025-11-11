$(function () {
    // Cek apakah sedang berada di route yang benar
    const currentRoute = window.location.pathname;
    if (!currentRoute.split("/").includes("ruangrapat")) return;

    // Inisialisasi DataTable
    const datatable = $("#ruangrapat-table").DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: `/ruangrapat`,
            type: "GET",
            data: function (d) {
                // Tambahkan parameter filter di sini jika nanti diperlukan
                // d.filter_nama = $("#filter_nama").val();
            },
            error: function (xhr, status, error) {
                console.error("Error fetching data:", error);
            },
        },
        columns: [
            {
                // Kolom Nomor Urut
                name: "id",
                data: "id",
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                },
                orderable: false,
                searchable: false
            },
            {
                name: "name",
                data: "name",
            },
            {
                name: "isi_paket",
                data: "isi_paket",
                render: function(data) {
                    // Membatasi panjang teks jika terlalu panjang
                    return data.length > 50 ? data.substr(0, 50) + '...' : data;
                }
            },
            {
                name: "fasilitas",
                data: "fasilitas",
                 render: function(data) {
                    return data.length > 50 ? data.substr(0, 50) + '...' : data;
                }
            },
            {
                name: "harga",
                data: "harga",
                render: function (harga) {
                    // Format mata uang Rupiah di sisi client
                    return `<div>Rp ${new Intl.NumberFormat('id-ID').format(harga)}</div>`;
                },
            },
            {
                // Kolom Aksi
                name: "id",
                data: "id",
                orderable: false,
                searchable: false,
                render: function (id) {
                    return `
                        <button class="btn btn-light btn-sm rounded shadow-sm border"
                            data-action="edit-ruangrapat" data-id="${id}"
                            data-bs-toggle="tooltip" data-bs-placement="top" title="Edit Paket">
                            <i class="fas fa-edit"></i>
                        </button>
                        <form class="btn btn-sm delete-ruangrapat" method="POST"
                            id="delete-ruangrapat-form-${id}"
                            action="/ruangrapat/${id}" style="display: inline-block;">
                            <input type="hidden" name="_token" value="${$('meta[name="csrf-token"]').attr('content')}">
                            <input type="hidden" name="_method" value="DELETE">
                            <a class="btn btn-light btn-sm rounded shadow-sm border delete"
                                href="#" data-id="${id}" data-bs-toggle="tooltip"
                                data-bs-placement="top" title="Hapus Paket">
                                <i class="fas fa-trash-alt"></i>
                            </a>
                        </form>
                    `;
                },
            },
        ],
        order: [[1, 'asc']] // Default sort berdasarkan kolom Nama Paket
    });

    // Inisialisasi Modal Bootstrap
    const modal = new bootstrap.Modal($("#main-modal"), {
        backdrop: true,
        keyboard: true,
        focus: true,
    });

    // Event Listener Global
    $(document)
        // --- Tombol Hapus (Tampilkan Konfirmasi SweetAlert) ---
        .on("click", ".delete", function (e) {
            e.preventDefault();
            var id = $(this).data("id");
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: "btn btn-success",
                    cancelButton: "btn btn-danger",
                },
                buttonsStyling: false,
            });

            swalWithBootstrapButtons
                .fire({
                    title: "Apakah Anda yakin?",
                    text: "Data paket ruang rapat akan dihapus dan tidak dapat dikembalikan!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Ya, hapus!",
                    cancelButtonText: "Batal",
                    reverseButtons: true,
                })
                .then((result) => {
                    if (result.isConfirmed) {
                        // Submit form penghapusan yang sesuai dengan ID
                        $(`#delete-ruangrapat-form-${id}`).submit();
                    }
                });
        })
        // --- Tombol Tambah Data (Buka Modal) ---
        .on("click", "#add-button", async function () {
            modal.show();
            $("#main-modal .modal-title").text("Tambah Paket Ruang Rapat");
            $("#main-modal .modal-body").html(`<div class="text-center my-5"><span class="spinner-border text-primary"></span><br>Loading...</div>`);

            try {
                const response = await $.get(`/ruangrapat/create`);
                if (response && response.view) {
                    $("#main-modal .modal-body").html(response.view);
                    // Inisialisasi komponen tambahan jika ada (misal: Select2, Summernote)
                    // $('.select2').select2();
                } else {
                     $("#main-modal .modal-body").html(`<div class="alert alert-danger">Gagal memuat form.</div>`);
                }
            } catch (error) {
                console.error(error);
                $("#main-modal .modal-body").html(`<div class="alert alert-danger">Terjadi kesalahan jaringan.</div>`);
            }
        })
        // --- Tombol Simpan di Modal ---
        .on("click", "#btn-modal-save", function () {
            // Submit form yang ada di dalam modal
            $("#form-save-ruangrapat").submit();
        })
        // --- Proses Submit Form Simpan/Update ---
        .on("submit", "#form-save-ruangrapat", async function (e) {
            e.preventDefault();
            if (typeof CustomHelper !== 'undefined') CustomHelper.clearError();
            $("#btn-modal-save").attr("disabled", true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...');

            try {
                const response = await $.ajax({
                    url: $(this).attr("action"),
                    data: $(this).serialize(),
                    method: $(this).attr("method"),
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                    },
                });

                Swal.fire({
                    position: "top-end",
                    icon: "success",
                    title: response.message || "Berhasil disimpan!",
                    showConfirmButton: false,
                    timer: 1500,
                });

                modal.hide();
                datatable.ajax.reload(); // Reload tabel
            } catch (e) {
                if (e.status === 422) {
                    // Error validasi Laravel
                    if (typeof CustomHelper !== 'undefined') {
                        CustomHelper.errorHandlerForm(e);
                    } else {
                        // Fallback jika CustomHelper tidak ada
                        alert("Terjadi kesalahan validasi. Periksa kembali inputan Anda.");
                    }
                } else {
                    Swal.fire({
                        icon: "error",
                        title: "Oops...",
                        text: e.responseJSON?.message || "Terjadi kesalahan pada server.",
                    });
                }
            } finally {
                $("#btn-modal-save").attr("disabled", false).text('Simpan');
            }
        })
        // --- Tombol Edit (Buka Modal Edit) ---
        .on("click", '[data-action="edit-ruangrapat"]', async function () {
            modal.show();
            $("#main-modal .modal-title").text("Edit Paket Ruang Rapat");
            $("#main-modal .modal-body").html(`<div class="text-center my-5"><span class="spinner-border text-primary"></span><br>Loading data...</div>`);

            const id = $(this).data("id");

            try {
                const response = await $.get(`/ruangrapat/${id}/edit`);
                if (response && response.view) {
                    $("#main-modal .modal-body").html(response.view);
                     // Re-inisialisasi komponen jika diperlukan
                } else {
                     $("#main-modal .modal-body").html(`<div class="alert alert-danger">Gagal memuat data.</div>`);
                }
            } catch (error) {
                $("#main-modal .modal-body").html(`<div class="alert alert-danger">Terjadi kesalahan saat mengambil data.</div>`);
            }
        })
        // --- Proses Submit Penghapusan ---
        .on("submit", ".delete-ruangrapat", async function (e) {
            e.preventDefault();
            try {
                const response = await $.ajax({
                    url: $(this).attr("action"),
                    data: $(this).serialize(), // Akan menyertakan _method=DELETE dan _token
                    method: "POST",
                });

                Swal.fire({
                    position: "top-end",
                    icon: "success",
                    title: response.message || "Data berhasil dihapus!",
                    showConfirmButton: false,
                    timer: 1500,
                });

                datatable.ajax.reload();
            } catch (e) {
                Swal.fire({
                    icon: "error",
                    title: "Gagal",
                    text: e.responseJSON?.message || "Tidak dapat menghapus data saat ini.",
                });
            }
        });
});