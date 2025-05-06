<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Komplain</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.6.5/flowbite.min.css" />
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen p-8">
    <div class="max-w-3xl w-full bg-white shadow-lg rounded-lg p-8">
        <div class="text-center mb-6">
            <img src="logo.png" alt="Logo" class="mx-auto h-16">
            <h1 class="text-xl font-semibold text-gray-700 mt-4">Form Komplain</h1>
        </div>
        <form action="proses_komplain.php" method="POST" enctype="multipart/form-data" class="space-y-6">
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
                    <option value="SISTEM_FPOS">SISTEM FPOS</option>
                    <option value="STOCK">STOCK</option>
                    <option value="KONEKSI_INTERNET">KONEKSI INTERNET</option>
                    <option value="MEMBER">MEMBER</option>
                    <option value="KALKULASI">KALKULASI</option>
                    <option value="INPUTAN">INPUTAN</option>
                    <option value="PO">PO</option>
                    <option value="RETUR">RETUR</option>
                    <option value="REFUND">REFUND</option>
                    <option value="MS_EXCEL">MS EXCEL</option>
                    <option value="MS_WORD">MS WORD</option>
                    <option value="PRINTER">PRINTER</option>
                    <option value="KOMPUTER">KOMPUTER</option>
                </select>
            </div>
            <div>
                <label for="keterangan" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Keterangan</label>
                <textarea id="keterangan" name="keterangan" rows="3" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"></textarea>
            </div>
            <div>
                <label for="user_komplain" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">User Komplain</label>
                <input type="text" id="user_komplain" name="user_komplain" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
            </div>
            <div>
                <label for="cabang" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Cabang</label>
                <select id="cabang" name="cabang" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option value="AHMAD YANI">AHMAD YANI</option>
                    <option value="AISYAH">AISYAH</option>
                    <option value="BEBEDAHAN">BEBEDAHAN</option>
                    <option value="BKR">BKR</option>
                    <option value="BRP">BRP</option>
                    <option value="CILENDEK">CILENDEK</option>
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
                    <option value="MEGA MUTIARA">MEGA MUTIARA</option>
                    <option value="PADASUKA">PADASUKA</option>
                    <option value="PARHON">PARHON</option>
                    <option value="PASEH">PASEH</option>
                    <option value="PURBARATU">PURBARATU</option>
                    <option value="SAMBONG">SAMBONG</option>
                    <option value="SILIWANGI">SILIWANGI</option>
                    <option value="TAMANSARI">TAMANSARI</option>
                </select>
            </div>
            <div>
                <label for="id_chat" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">ID Chat</label>
                <input type="text" id="id_chat" name="id_chat" readonly class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
            </div>
            <div>
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white" for="file_input">Upload file</label>
                <input class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400" id="file_input" type="file">
            </div>
            <div class="flex justify-center">
                <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Kirim</button>
            </div>
        </form>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.6.5/flowbite.min.js"></script>
    <script>
    document.addEventListener("DOMContentLoaded", function () {
        let today = new Date().toISOString().split('T')[0];
        document.getElementById("tanggal").value = today;

        fetch("get_last_id.php")
            .then(response => response.json())
            .then(data => {
                document.getElementById("id_tiket").value = data.id_tiket || "1001";
            })
            .catch(error => console.error("Error fetching last ID:", error));

        const idChatMapping = {
            "AHMAD YANI": "1001",
            "AISYAH": "7821603325",
            "BEBEDAHAN": "1003",
            "BKR": "1004",
            "BRP": "1005",
            "CILENDEK": "1006",
            "CIAWI": "1007",
            "CIGEUREUNG": "1008",
            "CIKALANG": "1009"
        };

        document.getElementById("cabang").addEventListener("change", function () {
            let selectedCabang = this.value;
            document.getElementById("id_chat").value = idChatMapping[selectedCabang] || "";
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