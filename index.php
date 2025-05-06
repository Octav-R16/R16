<?php
session_start(); // Memulai session
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Komplain</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.6.5/flowbite.min.css" />
    <style>
    /* Background Image */
    body {
        background-image: url('rm222batch2-mind-03.jpg');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
    }

    /* Overlay Effect */
    .bg-black {
        background-color: rgba(0, 0, 0, 0.5);
    }
</style>
</head>
<body class="relative flex items-center justify-center min-h-screen p-8 bg-cover bg-center bg-no-repeat">
    <div class="absolute inset-0 bg-black bg-opacity-50"></div>
    <div class="max-w-3xl w-full bg-white shadow-lg rounded-lg p-8 relative z-10">
        <div class="text-center mb-6">
<img src="logo.png" alt="Logo" class="mx-auto w-auto max-h-32">


            <h1 class="text-xl font-semibold text-gray-700 mt-4">Form Komplain</h1>
        </div>
        <form action="proses_tiket.php" method="POST" enctype="multipart/form-data" class="space-y-6">
            <div>
                <label for="id_tiket" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">ID Tiket</label>
                <input type="text" id="id_tiket" name="id_tiket" readonly class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
            </div>
            <div>
                <label for="tanggal" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tanggal</label>
                <input type="date" id="tanggal" name="tanggal" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
            </div>
            <div>
                <label for="jenis_komplain" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Jenis Komplain</label>
                <select id="jenis_komplain" name="jenis_komplain" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option value="" disabled selected>Pilih Jenis Komplain</option>
                    <option value="IT - SISTEM_FPOS">SISTEM FPOS</option>
                    <option value="IT - AKUN">AKUN</option>
                    <option value="IT - STOCK">STOCK</option>
                    <option value="IT - KONEKSI_INTERNET">KONEKSI INTERNET</option>
                    <option value="IT - MEMBER">MEMBER</option>
                    <option value="IT - BANK-DATA">BANK-DATA</option>
                    <option value="IT - FINGER">FINGER</option>
                    <option value="IT - SCANNER">SCANNER</option>
                    <option value="IT - SOFTWARE">SOFTWARE</option>
                    <option value="IT - PO">PO</option>
                    <option value="IT - MOUSE">MOUSE</option>
                    <option value="IT - KEYBOARD">KEYBOARD</option>
                    <option value="IT - CCTV">CCTV</option>
                    <option value="IT - MS_EXCEL">MS EXCEL</option>
                    <option value="IT - MS_WORD">MS WORD</option>
                    <option value="IT - PRINTER">PRINTER</option>
                    <option value="IT - KOMPUTER">KOMPUTER</option>
                    <option value="IT - HARDWARE">HARDWARE</option>
					<option value="IT - KALKULASI">KALKULASI</option>
                    <option value="OP - AC_BOCOR">AC BOCOR</option>
                    <option value="OP - ATAP BOCOR">ATAP BOCOR</option>
					<option value="MARKETING - HARGA">HARGA</option>
					<option value="MARKETING - PROFIT">PROFIT</option>
					<option value="MARKETING - PROMO">PROMO</option>
					<option value="MARKETING - SEWA">SEWA</option>
					<option value="MARKETING - BARANG BARU LISTING">BARANG BARU LISTING</option>
					<option value="MARKETING - MEMBER">MEMBER</option>
					<option value="MARKETING - BANNER">BANNER</option>
					<option value="MARKETING - CLAIM_CASHBACK">CLAIM CASHBACK</option>
					<option value="MARKETING - GIMMICK">GIMMICK</option>
					<option value="BUYER - INTERNAL_DELIVERY">INTERNAL DELIVERY</option>
					<option value="BUYER - INTERNAL_RECEIVING">INTERNAL RECEIVING</option>
					<option value="BUYER - REQUEST_ORDER">REQUEST ORDER</option>
					<option value="BUYER - PO_REGULER">PO REGULER</option>
					<option value="BUYER - PO_TARGET">PO TARGET</option>
					<option value="BUYER - STOK_MAKS">STOK MAKS</option>
					<option value="BUYER - RETURN">RETURN</option>
                    <option value="BUYER - STOCK_UP_DOWN">STOCK UP DOWN</option>
					<option value="BUYER - MINIMAL_ORDER">MINIMAL ORDER</option>
					<option value="BUYER - CYCLE_ORDER">CYCLE ORDER</option>
					<option value="BUYER - FOLLOW_UP_SUPPLIER">FOLLOW UP SUPPLIER</option>
					<option value="FINANCE - EDC_ERROR">EDC (ERROR EDC/QRIS)</option>
					<option value="FINANCE - KOIN">KOIN</option>
					<option value="FINANCE - INPUTAN_PEMBAYARAN_HUTANG">INPUTAN PEMBAYARAN HUTANG</option>
					<option value="FINANCE - BONUS">BONUS</option>
					<option value="FINANCE - ONPAY">ONPAY</option>
					<option value="FINANCE - POTONGAN_RETURN">POTONGAN RETURN</option>
					<option value="FINANCE - SELISIH_SETORAN_OMSET">SELISIH SETIRAN OMSET</option>
					<option value="FINANCE - INPUTAN_RETURN_PAJAK">INPUTAN RETURN PAJAK</option>
					<option value="FINANCE - SUMBANGAN">SUMBANGAN</option>
					<option value="FINANCE - PROPOSAL">PROPOSAL</option>
					<option value="FINANCE - ABSENSI">ABSENSI</option>
					<option value="AUDIT - SALAH_APPROVE">SALAH APPROVE</option>
					<option value="AUDIT - KONFIRMASI_HASIL_SO">KONFIRMASI HASIL SO</option>
					<option value="AUDIT - BARCODE/SKU_DOUBLE">BARCODE / SKU DOUBLE</option>
                    <optgroup label="Revisi">
                        <option value="IT - INPUTAN">INPUTAN</option>
                    </optgroup>
                </select>
            </div>
            <!-- ⬇️ Tambahkan bagian ini setelah select jenis_komplain -->
            <div id="password-wrapper" class="hidden">
                <label for="buyer_password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Password Buyer</label>
                <input type="password" id="buyer_password" name="buyer_password" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-red-500 dark:focus:border-red-500">
            </div>
            <div>
                <label for="keterangan" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Keterangan</label>
                <textarea id="keterangan" name="keterangan" rows="3" placeholder="Isi Keterangan Komplain *" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"></textarea>
            </div>
            <div>
                <label for="user_komplain" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">User Komplain</label>
                <input type="text" id="user_komplain" name="user_komplain" placeholder="Masukkan Nama Komplain *" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
            </div>
            <div>
                <label for="cabang" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Cabang</label>
                <select id="cabang" name="cabang" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option value="" disabled selected>Pilih Toko</option>
                    <option value="AHMAD YANI">AHMAD YANI</option>
                    <option value="AISYAH">AISYAH</option>
                    <option value="BEBEDAHAN">BEBEDAHAN</option>
                    <option value="BKR">BKR</option>
                    <option value="BRP">BRP</option>
                    <option value="CIAWI">CIAWI</option>
                    <option value="CIGEUREUNG">CIGEUREUNG</option>
                    <option value="CIKALANG">CIKALANG</option>
                    <option value="CILEMBANG">CILEMBANG</option>
                    <option value="CILENDEK">CILENDEK</option>
                    <option value="CINEHEL">CINEHEL</option>
                    <option value="CISALAK">CISALAK</option>
                    <option value="GARUDA">GARUDA</option>
                    <option value="GEGERNOONG">GEGERNOONG</option>
                    <option value="GOBRAS">GOBRAS</option>
                    <option value="GUNUNG KALONG">GUNUNG KALONG</option>
                    <option value="IBRAHIM ADJIE">IBRAHIM ADJIE</option>
                    <option value="INDIHIANG">INDIHIANG</option>
                    <option value="JIWA BESAR">JIWA BESAR</option>
                    <option value="JUANDA">JUANDA</option>
                    <option value="KALANGSARI">KALANGSARI</option>
                    <option value="KAWALU">KAWALU</option>
                    <option value="MANGKUBUMI">MANGKUBUMI</option>
                    <option value="MANONJAYA">MANONJAYA</option>
                    <option value="MEGA">MEGA</option>
                    <option value="PADASUKA">PADASUKA</option>
                    <option value="PARHON">PARHON</option>
                    <option value="PASEH">PASEH</option>
                    <option value="PURBARATU">PURBARATU</option>
                    <option value="SAMBONG">SAMBONG</option>
                    <option value="SILIWANGI">SILIWANGI</option>
                    <option value="TAMANSARI">TAMANSARI</option>
                    <option value="DC JUANDA">DC JUANDA</option>
                    <option value="OFFICE">OFFICE</option>
                </select>
            </div>
            <div>
                <label for="id_chat" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">ID Chat</label>
                <input type="text" id="id_chat" name="id_chat" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
            </div>
            <div>
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white" for="file_input">Upload file</label>
                <input class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400" id="file_input" type="file" name="file_input" accept=".jpg,.jpeg,.png,.gif,.pdf">
            </div>
            <div class="flex justify-center">
                <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Kirim</button>
            </div>

            
            <div class="flex justify-center">
                <?php if (isset($_SESSION['UserID'])): ?> <!-- Cek apakah session UserID ada, menandakan pengguna sudah login -->
                    <a href="proses_komplain.php"
                        class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-green-600 dark:hover:bg-green-700 focus:outline-none dark:focus:ring-green-800">
                       Proses Komplain
                    </a>
                <?php else: ?>
                    <!-- Jika belum login, tombol tidak muncul -->
                <?php endif; ?>
            </div>
        </form>
        <?php include 'footer.php'; ?>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.6.5/flowbite.min.js"></script>
    <script>
    document.addEventListener("DOMContentLoaded", function () {
    let today = new Date().toISOString().split('T')[0];
    document.getElementById("tanggal").value = today;

    // Ambil ID Tiket terakhir dari get_last_id.php
    fetch("get_last_id.php")
        .then(response => response.json())
        .then(data => {
            document.getElementById("id_tiket").value = data.id_tiket || "";
        })
        .catch(error => console.error("Error fetching last ID:", error));

        const idChatMapping = {
            "AHMAD YANI": "5854822428",
            "AISYAH": "7821603325",
            "BEBEDAHAN": "339774107",
            "BKR": "5987071031",
            "BRP": "400204821",
            "CIAWI": "1936665367",
            "CIGEUREUNG": "6132274526",
            "CIKALANG": "339447592",
            "CILEMBANG": "1007538566",
            "CILENDEK": "1048120258",
            "CINEHEL": "877220566",
            "CISALAK": "360119297",
            "GARUDA": "5910558282",
            "GEGERNOONG": "1536337900",
            "GOBRAS": "2121610356",
            "GUNUNG KALONG": "6888983575",
            "IBRAHIM ADJIE": "5813640186",
            "INDIHIANG": "250560857",
            "JIWA BESAR": "5104113332",
            "JUANDA": "1676213443",
            "KALANGSARI": "398149146",
            "KAWALU": "716082816",
            "MANGKUBUMI": "729169300",
            "MANONJAYA": "1230414052",
            "MEGA": "7918755919",
            "PADASUKA": "310619065",
            "PARHON": "1849547997",
            "PASEH": "625302390",
            "PURBARATU": "521865448",
            "SAMBONG": "790020248",
            "SILIWANGI": "466137406",
            "DC JUANDA": "6198495664",
            "TAMANSARI": "796106044"
        };

        document.getElementById("cabang").addEventListener("change", function () {
            let selectedCabang = this.value;
            document.getElementById("id_chat").value = idChatMapping[selectedCabang] || "";
        });
    });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
        // kode sebelumnya...
    
        const jenisKomplain = document.getElementById("jenis_komplain");
        const passwordWrapper = document.getElementById("password-wrapper");
        const passwordInput = document.getElementById("buyer_password");
    
        const butuhPassword = ["IT - INPUTAN", "IT-KALKULASI", "BUYER-RETUR", "BUYER-STOCK_UP_DOWN"];
    
        jenisKomplain.addEventListener("change", function () {
            const selected = this.value;
            if (butuhPassword.includes(selected)) {
                passwordWrapper.classList.remove("hidden");
                passwordInput.setAttribute("required", true);
            } else {
                passwordWrapper.classList.add("hidden");
                passwordInput.removeAttribute("required");
                passwordInput.value = ""; // reset input
            }
        });
    
        document.querySelector("form").addEventListener("submit", function (e) {
            const selected = jenisKomplain.value;
            const password = passwordInput.value;
            const correctPassword = "buyer123"; // GANTI ini dengan password rahasia kamu
    
            if (butuhPassword.includes(selected) && password !== correctPassword) {
                e.preventDefault();
                alert("Password buyer salah. Akses ditolak.");
            }
        });
    });
    </script>
    <style>
        .input-field {
            width: 100%;
            padding: 8px;
            border-radius: 8px;
            border: 1px solid #d1d5db;
            outline: none;
            transition: border-color 0.3s;
        }
        .input-field:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 4px rgba(59, 130, 246, 0.5);
        }
    </style>
</body>
</html>