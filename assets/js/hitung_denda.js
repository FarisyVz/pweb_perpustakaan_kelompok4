// Fungsi hitung denda otomatis Rp 1.000 per hari
function prosesHitungDenda(idPinjamTerpilih, dataPinjaman, tglKembaliAktual) {
    const boxKalkulasi = document.getElementById('box-kalkulasi');
    
    if (idPinjamTerpilih === "") {
        boxKalkulasi.style.display = "none";
        return;
    }

    // Cari data transaksi yang sesuai di dalam array objek dataPinjaman
    const detail = dataPinjaman.find(item => item.id_pinjam == idPinjamTerpilih);
    
    if (detail) {
        const tglPinjam = new Date(detail.tgl_pinjam);
        const tglWajib = new Date(detail.tgl_wajib_kembali);
        const tglKembali = new Date(tglKembaliAktual);
        
        // 1. Hitung total lama peminjaman (hari)
        const selisihLamaWaktu = tglKembali.getTime() - tglPinjam.getTime();
        const lamaPinjam = Math.ceil(selisihLamaWaktu / (1000 * 60 * 60 * 24));
        
        // 2. Hitung keterlambatan (hari)
        const selisihTerlambatWaktu = tglKembali.getTime() - tglWajib.getTime();
        let hariTerlambat = Math.ceil(selisihTerlambatWaktu / (1000 * 60 * 60 * 24));
        if (hariTerlambat < 0) hariTerlambat = 0; 
        
        // 3. Hitung denda berdasarkan ketetapan baru: Rp 1.000/hari
        const tarifDenda = 1000;
        const totalDenda = hariTerlambat * tarifDenda;
        
        // Suntikkan hasil kalkulasi langsung ke elemen teks HTML view
        document.getElementById('text_tgl_pinjam').innerText = detail.tgl_pinjam;
        document.getElementById('text_tgl_wajib').innerText = detail.tgl_wajib_kembali;
        document.getElementById('text_lama_pinjam').innerText = lamaPinjam;
        document.getElementById('text_terlambat').innerText = hariTerlambat;
        document.getElementById('text_denda').innerText = "Rp " + totalDenda.toLocaleString('id-ID');
        
        boxKalkulasi.style.display = "block";
    }
}