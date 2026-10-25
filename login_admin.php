<?php
session_start();

require_once __DIR__ . "/api/koneksi.php";

// Hitung jumlah warga
$queryWarga = $conn->query(
    "SELECT COUNT(*) AS jumlah FROM users WHERE role = 'warga'"
);
$jumlahWarga = $queryWarga->fetch_assoc()['jumlah'];

// Hitung jumlah petugas
$queryPetugas = $conn->query(
    "SELECT COUNT(*) AS jumlah FROM users WHERE role = 'petugas'"
);
$jumlahPetugas = $queryPetugas->fetch_assoc()['jumlah'];

// Hitung jumlah setoran
$querySetoran = $conn->query(
    "SELECT COUNT(*) AS jumlah FROM setoran"
);
$jumlahSetoran = $querySetoran->fetch_assoc()['jumlah'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#15803d">

    <title>Dashboard Admin | Bank Sampah</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #f5f7f5;
            color: #1f2937;
            min-height: 100vh;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        .topbar {
            background: #15803d;
            color: white;
            padding: 22px 6% 65px;
            border-radius: 0 0 28px 28px;
        }

        .topbar-inner {
            max-width: 1100px;
            margin: auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
        }

        .brand {
            font-size: 19px;
            font-weight: bold;
            letter-spacing: .2px;
        }

        .badge {
            background: rgba(255,255,255,.18);
            padding: 9px 13px;
            border-radius: 30px;
            font-size: 12px;
        }

        .welcome {
            max-width: 1100px;
            margin: 30px auto 0;
        }

        .welcome .small {
            color: #dcfce7;
            font-size: 14px;
            margin-bottom: 9px;
        }

        .welcome h1 {
            font-size: clamp(25px, 5vw, 34px);
            margin-bottom: 10px;
        }

        .welcome p {
            color: #e5f7e9;
            font-size: 14px;
            line-height: 1.6;
        }

        .container {
            width: 88%;
            max-width: 1100px;
            margin: -35px auto 35px;
            position: relative;
        }

        .section-title {
            font-size: 18px;
            margin: 25px 0 15px;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 16px;
        }

        .stat-card {
            background: white;
            border: 1px solid #e8eee8;
            border-radius: 17px;
            padding: 22px;
            box-shadow: 0 5px 18px rgba(20, 70, 30, .05);
        }

        .stat-icon {
            width: 43px;
            height: 43px;
            border-radius: 13px;
            display: grid;
            place-items: center;
            background: #dcfce7;
            font-size: 22px;
            margin-bottom: 17px;
        }

        .stat-card p {
            color: #64748b;
            font-size: 13px;
            margin-bottom: 9px;
        }

        .stat-card h2 {
            font-size: 27px;
            color: #166534;
        }

        .menu-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 15px;
        }

        .menu-card {
            background: white;
            border: 1px solid #e8eee8;
            border-radius: 16px;
            padding: 20px;
            min-height: 145px;
            transition: .2s;
        }

        .menu-card:hover {
            transform: translateY(-3px);
            border-color: #86efac;
            box-shadow: 0 8px 20px rgba(20, 70, 30, .07);
        }

        .menu-icon {
            width: 45px;
            height: 45px;
            border-radius: 14px;
            background: #f0fdf4;
            display: grid;
            place-items: center;
            font-size: 23px;
            margin-bottom: 14px;
        }

        .menu-card h3 {
            font-size: 15px;
            margin-bottom: 7px;
        }

        .menu-card p {
            font-size: 12px;
            color: #64748b;
            line-height: 1.5;
        }

        .info {
            margin-top: 25px;
            background: #ecfdf5;
            border: 1px solid #bbf7d0;
            padding: 19px;
            border-radius: 16px;
            display: flex;
            gap: 13px;
            align-items: flex-start;
        }

        .info-icon {
            font-size: 24px;
        }

        .info h3 {
            font-size: 15px;
            margin-bottom: 7px;
            color: #166534;
        }

        .info p {
            font-size: 13px;
            color: #476354;
            line-height: 1.7;
        }

        footer {
            text-align: center;
            color: #94a3b8;
            font-size: 12px;
            padding: 10px 15px 25px;
        }

        @media (max-width: 650px) {
            .topbar {
                padding: 20px 6% 58px;
                border-radius: 0 0 24px 24px;
            }

            .brand {
                font-size: 16px;
            }

            .badge {
                font-size: 11px;
                padding: 8px 10px;
            }

            .welcome {
                margin-top: 27px;
            }

            .container {
                width: 90%;
            }

            .stats {
                grid-template-columns: 1fr;
                gap: 12px;
            }

            .stat-card {
                display: flex;
                align-items: center;
                gap: 15px;
                padding: 17px;
            }

            .stat-icon {
                flex-shrink: 0;
                margin-bottom: 0;
            }

            .stat-card p {
                margin-bottom: 6px;
            }

            .stat-card h2 {
                font-size: 24px;
            }

            .menu-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 12px;
            }

            .menu-card {
                padding: 16px;
                min-height: 140px;
            }

            .menu-card h3 {
                font-size: 14px;
            }

            .section-title {
                font-size: 17px;
            }
        }

        @media (max-width: 350px) {
            .menu-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

    <header class="topbar">
        <div class="topbar-inner">
            <div class="brand">♻ Bank Sampah Warga</div>
            <div class="badge">ADMIN PANEL</div>
        </div>

        <div class="welcome">
            <div class="small">Halo, Admin! 👋</div>
            <h1>Dashboard</h1>
            <p>
                Selamat datang di sistem Bank Sampah Warga.
                Kelola data dan pantau aktivitas dari sini.
            </p>
        </div>
    </header>

    <main class="container">


        <div class="stats">
            <div class="stat-card">
                <div class="stat-icon">👥</div>
                <div>
                    <p>Jumlah Warga</p>
                    <h2><?= $jumlahWarga ?></h2>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">🧑‍💼</div>
                <div>
                    <p>Jumlah Petugas</p>
                    <h2><?= $jumlahPetugas?></h2>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">♻️</div>
                <div>
                    <p>Total Setoran</p>
                    <h2><?= $jumlahSetoran?></h2>
                </div>
            </div>
        </div>

        <h2 class="section-title">Menu Pengelolaan</h2>

        <div class="menu-grid">
            <a class="menu-card" href="data_warga.php">
                <div class="menu-icon">👥</div>
                <h3>Data Warga</h3>
                <p>Kelola informasi warga.</p>
            </a>

            <a class="menu-card" href="data_petugas.php">
                <div class="menu-icon">🧑‍💼</div>
                <h3>Data Petugas</h3>
                <p>Kelola akun petugas.</p>
            </a>

            <a class="menu-card" href="jenis_sampah.php">
                <div class="menu-icon">🌱</div>
                <h3>Jenis Sampah</h3>
                <p>Kelola jenis dan harga sampah.</p>
            </a>

            <a class="menu-card" href="data_setoran.php">
                <div class="menu-icon">📦</div>
                <h3>Data Setoran</h3>
                <p>Pantau data setoran sampah.</p>
            </a>

            <a class="menu-card" href="laporan.php">
                <div class="menu-icon">📊</div>
                <h3>Laporan</h3>
                <p>Lihat laporan aktivitas.</p>
            </a>

            <a class="menu-card" href="profil_admin.php">
                <div class="menu-icon">⚙️</div>
                <h3>Profil Admin</h3>
                <p>Informasi akun admin.</p>
            </a>
        </div>

        <div class="info">
            <div class="info-icon">💡</div>
            <div>
                <h3>Informasi Sistem</h3>
                <p>
                    Gunakan menu di atas untuk mengelola data
                    Bank Sampah Warga. Data statistik akan kita
                    sambungkan ke database MySQL pada langkah berikutnya.
                </p>
            </div>
        </div>

    </main>

    <footer>
        &copy; 2026 Bank Sampah Warga
    </footer>

</body>
</html>