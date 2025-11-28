$(function () {
    const currentRoute = window.location.pathname;
    if (!currentRoute.includes("room-info/cleaning")) return;

    const tableElement = $("#cleaning-room-table");

    if (tableElement.length > 0) {
        tableElement.DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "/room-info/cleaning",
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
                    className: "fw-bold"
                },
                // 3. Kamar
                { 
                    name: "rooms.number", 
                    data: "room_info",
                    render: function(data) {
                        return `
                            <div class="d-flex flex-column">
                                <span class="fw-bold text-primary">${data.number}</span>
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
                // 8. Status (Lunas)
                { 
                    name: "transactions.status", 
                    data: "status",
                    className: "text-center",
                    render: function(data) {
                        return `<span class="badge bg-info text-dark px-3 py-1 rounded-pill">${data}</span>`;
                    }
                },
            ],
            order: [[4, 'desc']], // Urutkan berdasarkan Check-out terbaru
        });
    }
});