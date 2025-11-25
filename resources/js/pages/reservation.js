$(function () {
    // Cek apakah kita berada di halaman konfirmasi reservasi
    const reservationForm = $('#reservation-form');
    if (reservationForm.length === 0) return;

    // Ambil data dasar dari atribut data- HTML
    // Ini lebih aman dan rapi daripada mencetak variabel PHP di tengah blok script JS
    const roomPrice = parseFloat(reservationForm.data('room-price'));
    const dayDifference = parseInt(reservationForm.data('duration'));
    const breakfastRate = 140000; // Harga Sarapan per malam

    // Fungsi Format Rupiah
    function formatRupiah(angka) {
        return new Intl.NumberFormat('id-ID', { 
            style: 'currency', 
            currency: 'IDR', 
            minimumFractionDigits: 0 
        }).format(angka);
    }

    // Event Listener saat Dropdown Sarapan berubah
    $('#breakfast').on('change', function() {
        const isBreakfast = $(this).val() === 'Yes';
        
        // Hitung Biaya Sarapan
        // Asumsi: Biaya sarapan dikali jumlah malam
        const breakfastTotal = isBreakfast ? (breakfastRate * dayDifference) : 0;
        
        // Hitung Grand Total (Harga Kamar Dasar + Total Sarapan)
        // roomPrice di sini diasumsikan harga per malam, jadi harus dikali durasi dulu
        // Tapi biasanya data-room-price yang dikirim dari controller bisa berupa total atau per malam.
        // Mari kita asumsikan data-room-price adalah HARGA PER MALAM (sesuai logika controller sebelumnya).
        const roomTotalCost = roomPrice * dayDifference;
        const grandTotal = roomTotalCost + breakfastTotal;
        
        // Hitung DP (15%)
        const dpAmount = grandTotal * 0.15;

        // Update UI (Tampilan)
        if (isBreakfast) {
            $('#row_breakfast').fadeIn();
            $('#display_breakfast_total').text(formatRupiah(breakfastTotal));
        } else {
            $('#row_breakfast').fadeOut();
        }

        $('#display_total_price').text(formatRupiah(grandTotal));
        $('#display_dp').text(formatRupiah(dpAmount));
    });
});