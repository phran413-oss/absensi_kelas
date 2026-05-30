<?php
// ─── DATA SISWA (nomor urut => [nama, gender, hadir, sakit, izin, alfa]) ────
$siswa = [
     1 => ["Ajda Nailla Syafiqah",          "P", 92, 1, 0, 0],
     2 => ["Aliyah Salsabilah",              "P", 91, 2, 0, 0],
     3 => ["Apupah Syabaniah",               "P", 89, 4, 0, 0],
     4 => ["Aqila Maulida Risma",            "P", 91, 2, 0, 0],
     5 => ["Arkan Naufal Putu H",            "L", 90, 1, 2, 0],
     6 => ["Athallah Malik P",               "L", 92, 1, 0, 0],
     7 => ["Aufa Athallah N.M.I.L",          "L", 91, 2, 0, 0],
     8 => ["Aurel Maryce Inarkombu",         "P", 85, 7, 0, 1],
     9 => ["Aurora Eka Marpaung",            "P", 89, 4, 0, 0],
    10 => ["Brian Ralphael Tumiwa",          "L", 89, 4, 0, 0],
    11 => ["Desta Dwi Natta",                "L", 86, 2, 5, 0],
    12 => ["Dian Safitri",                   "P", 91, 2, 0, 0],
    13 => ["Erika Fitria",                   "P", 90, 1, 2, 0],
    14 => ["Farah Aulia",                    "P", 91, 2, 0, 0],
    15 => ["Fifi Aulia",                     "P", 90, 1, 2, 0],
    16 => ["Gilang Farel R.S",               "L", 93, 0, 0, 0],
    17 => ["Husein Syahputra",               "L", 86, 5, 2, 0],
    18 => ["Ibrahim Firdaus",                "L", 86, 6, 1, 0],
    19 => ["Khaniza Ipak Q.",                "P", 92, 0, 1, 0],
    20 => ["Milani Janeeta A.",              "P", 90, 2, 1, 0],
    21 => ["M.C Ichsan",                     "L", 92, 0, 1, 0],
    22 => ["M.Hazami Idris",                 "L", 91, 2, 0, 0],
    23 => ["M.Raihan Mauliddan Budiman",     "L", 89, 3, 1, 0],
    24 => ["M.Zovanka Fadhillah",            "L", 71, 9,10, 3],
    25 => ["Nuraini Hanny Azzahra",          "P", 87, 5, 0, 1],
    26 => ["Putra Pratama",                  "L", 83, 7, 2, 1],
    27 => ["Ridho Tri Maulana",              "L", 86, 5, 2, 0],
    28 => ["Rizki Ridho Arfandi",            "L", 90, 3, 0, 0],
    29 => ["Salma Utami",                    "P", 91, 1, 1, 0],
    30 => ["Saskiya Syafrina",               "P", 92, 0, 1, 0],
    31 => ["Satrya Audioslave Lipenno",      "L", 89, 4, 0, 0],
    32 => ["Shalma Apriliani",               "P", 93, 0, 0, 0],
    33 => ["Siti Nur Aisyah",               "P", 91, 2, 0, 0],
    34 => ["Syalwa Rizki Haryanto",          "P", 90, 3, 0, 0],
    35 => ["Yulia Rahmawati",               "P", 92, 1, 0, 0],
    36 => ["Zhafran Zaka Lingga",            "L", 90, 3, 0, 0],
];

// ─── DATA LOGIN (nama => NIS) ────────────────────────────────────────────────
$users = [
    "Admin"   => "000000",
    "Guru"    => "111111",
];
// Tambahkan siswa juga bisa login dengan NIS masing-masing
foreach ($siswa as $no => [$nama,,,,]) {
    $users[$nama] = str_pad($no, 5, '0', STR_PAD_LEFT); // NIS = 00001, 00002, dst
}

session_start();

// ─── Handle Login ─────────────────────────────────────────────────────────────
$login_error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $input_nama = trim($_POST['nama'] ?? '');
    $input_nis  = trim($_POST['nis']  ?? '');
    $matched = false;
    foreach ($users as $nama => $nis) {
        if (strtolower($input_nama) === strtolower($nama) && $input_nis === $nis) {
            $_SESSION['logged_in'] = true;
            $_SESSION['user_name'] = $nama;
            $matched = true; break;
        }
    }
    if (!$matched) $login_error = 'Nama atau NIS tidak ditemukan.';
}
if (isset($_POST['logout'])) {
    session_destroy();
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

$logged_in = $_SESSION['logged_in'] ?? false;

// ─── Hitung ringkasan ──────────────────────────────────────────────────────────
$tot_H=$tot_S=$tot_I=$tot_A=0;
foreach ($siswa as $row) { $tot_H+=$row[2]; $tot_S+=$row[3]; $tot_I+=$row[4]; $tot_A+=$row[5]; }
$grand = $tot_H+$tot_S+$tot_I+$tot_A;
$jml_L = count(array_filter($siswa, fn($r)=>$r[1]==='L'));
$jml_P = count($siswa) - $jml_L;

function pct($v,$t){ return $t>0 ? round($v/$t*100,1) : 0; }
function bar_color($p){ return $p>=90?'#16a34a':($p>=75?'#d97706':'#dc2626'); }
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Absensi Siswa</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;600;700;800&family=JetBrains+Mono:wght@400;700&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{
  --navy:#0f172a;--navy2:#1e293b;--navy3:#334155;
  --blue:#3b82f6;--blue-lt:#eff6ff;
  --H:#16a34a;--H-bg:#dcfce7;--H-bd:#86efac;
  --S:#d97706;--S-bg:#fef3c7;--S-bd:#fcd34d;
  --I:#3b82f6;--I-bg:#dbeafe;--I-bd:#93c5fd;
  --A:#ef4444;--A-bg:#fee2e2;--A-bd:#fca5a5;
  --surface:#fff;--bg:#f1f5f9;--border:#e2e8f0;
  --text:#0f172a;--muted:#64748b;
  --r:12px;--shadow:0 2px 8px rgba(0,0,0,.07),0 8px 24px rgba(0,0,0,.06);
}
body{font-family:'Sora',sans-serif;background:var(--bg);color:var(--text);min-height:100vh}

/* ── LOGIN PAGE ──────────────────────────────────────────────────────── */
.login-wrap{
  min-height:100vh;display:flex;align-items:center;justify-content:center;
  background:linear-gradient(135deg,#0f172a 0%,#1e3a8a 50%,#1d4ed8 100%);
  padding:24px;
}
.login-card{
  background:#fff;border-radius:20px;padding:44px 40px;width:100%;max-width:400px;
  box-shadow:0 24px 60px rgba(0,0,0,.3);
  animation:fadeUp .5s ease both;
}
@keyframes fadeUp{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:none}}
.login-logo{
  width:56px;height:56px;background:linear-gradient(135deg,#1e3a8a,#3b82f6);
  border-radius:14px;display:flex;align-items:center;justify-content:center;
  font-size:26px;margin:0 auto 20px;
}
.login-card h1{font-size:22px;font-weight:800;text-align:center;margin-bottom:4px}
.login-card p{font-size:13px;color:var(--muted);text-align:center;margin-bottom:28px}
.field{margin-bottom:16px}
.field label{display:block;font-size:12px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.6px;margin-bottom:6px}
.field input{
  width:100%;padding:11px 14px;border:1.5px solid var(--border);border-radius:8px;
  font-family:inherit;font-size:14px;outline:none;transition:border-color .2s;
}
.field input:focus{border-color:var(--blue)}
.login-btn{
  width:100%;padding:12px;background:linear-gradient(135deg,#1e3a8a,#3b82f6);
  color:#fff;border:none;border-radius:8px;font-family:inherit;font-size:15px;
  font-weight:700;cursor:pointer;transition:opacity .2s;margin-top:4px;
}
.login-btn:hover{opacity:.9}
.login-err{
  background:#fee2e2;border:1px solid #fca5a5;color:#dc2626;
  border-radius:8px;padding:10px 14px;font-size:13px;margin-bottom:16px;text-align:center;
}
.nis-hint{
  margin-top:20px;padding:12px 14px;background:#f8fafc;border:1px solid var(--border);
  border-radius:8px;font-size:12px;color:var(--muted);line-height:1.6;
}
.nis-hint strong{color:var(--text)}

/* ── MAIN APP ────────────────────────────────────────────────────────── */
.topbar{
  background:linear-gradient(90deg,#0f172a 0%,#1e3a8a 60%,#2563eb 100%);
  color:#fff;padding:16px 24px;
  display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;
  position:sticky;top:0;z-index:100;
  box-shadow:0 2px 12px rgba(0,0,0,.2);
}
.topbar-left{display:flex;align-items:center;gap:12px}
.topbar-icon{width:38px;height:38px;background:rgba(255,255,255,.15);border-radius:9px;display:flex;align-items:center;justify-content:center;font-size:18px}
.topbar-title{font-size:16px;font-weight:800}
.topbar-sub{font-size:11px;opacity:.7;margin-top:1px}
.topbar-right{display:flex;align-items:center;gap:10px}
.user-pill{background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.25);border-radius:100px;padding:5px 14px;font-size:12px;font-weight:600}
.logout-btn{background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.25);color:#fff;border-radius:7px;padding:6px 14px;font-size:12px;font-weight:600;cursor:pointer;font-family:inherit;transition:background .2s}
.logout-btn:hover{background:rgba(255,255,255,.2)}

.container{max-width:1280px;margin:0 auto;padding:24px 16px 60px}

/* ── STAT CARDS ──────────────────────────────────────────────────────── */
.stats-row{display:grid;grid-template-columns:repeat(auto-fit,minmax(130px,1fr));gap:12px;margin-bottom:24px}
.stat-card{background:var(--surface);border:1px solid var(--border);border-radius:var(--r);padding:16px 18px;box-shadow:var(--shadow);position:relative;overflow:hidden}
.stat-card::before{content:'';position:absolute;left:0;top:0;bottom:0;width:4px;border-radius:4px 0 0 4px}
.stat-card.H::before{background:var(--H)}.stat-card.S::before{background:var(--S)}
.stat-card.I::before{background:var(--I)}.stat-card.A::before{background:var(--A)}
.stat-card.total::before{background:var(--blue)}
.stat-lbl{font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.7px;color:var(--muted);margin-bottom:6px}
.stat-val{font-family:'JetBrains Mono',monospace;font-size:28px;font-weight:700;line-height:1}
.stat-card.H .stat-val{color:var(--H)}.stat-card.S .stat-val{color:var(--S)}
.stat-card.I .stat-val{color:var(--I)}.stat-card.A .stat-val{color:var(--A)}
.stat-card.total .stat-val{color:var(--blue)}
.stat-sub{font-size:11px;color:var(--muted);margin-top:4px}

/* ── CHART + TABLE LAYOUT ─────────────────────────────────────────────── */
.main-grid{display:grid;grid-template-columns:260px 1fr;gap:20px;align-items:start}
@media(max-width:860px){.main-grid{grid-template-columns:1fr}}

/* ── DONUT CHART ─────────────────────────────────────────────────────── */
.chart-panel{background:var(--surface);border:1px solid var(--border);border-radius:var(--r);padding:24px;box-shadow:var(--shadow);position:sticky;top:80px}
.chart-panel h3{font-size:13px;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:var(--muted);margin-bottom:20px}
.donut-wrap{position:relative;width:180px;height:180px;margin:0 auto 20px}
.donut-wrap canvas{display:block}
.donut-center{position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;pointer-events:none}
.donut-center .big{font-family:'JetBrains Mono',monospace;font-size:28px;font-weight:700;color:var(--text)}
.donut-center .tiny{font-size:10px;color:var(--muted);font-weight:600;text-transform:uppercase;letter-spacing:.5px}
.chart-legend{display:flex;flex-direction:column;gap:10px}
.legend-row{display:flex;align-items:center;justify-content:space-between;font-size:13px}
.legend-left{display:flex;align-items:center;gap:8px}
.legend-dot{width:12px;height:12px;border-radius:3px;flex-shrink:0}
.legend-dot.H{background:var(--H)}.legend-dot.S{background:var(--S)}
.legend-dot.I{background:var(--I)}.legend-dot.A{background:var(--A)}
.legend-pct{font-family:'JetBrains Mono',monospace;font-weight:700;font-size:12px;color:var(--muted)}

/* ── TABLE PANEL ─────────────────────────────────────────────────────── */
.table-panel{background:var(--surface);border:1px solid var(--border);border-radius:var(--r);box-shadow:var(--shadow);overflow:hidden}
.panel-head{padding:16px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;flex-wrap:wrap;gap:10px}
.panel-head h3{font-size:14px;font-weight:700;margin-right:auto}
.search-box{padding:7px 12px;border:1.5px solid var(--border);border-radius:7px;font-family:inherit;font-size:13px;outline:none;transition:border-color .2s;min-width:180px}
.search-box:focus{border-color:var(--blue)}
.filter-sel{padding:7px 10px;border:1.5px solid var(--border);border-radius:7px;font-family:inherit;font-size:13px;outline:none;background:var(--bg);cursor:pointer}
.print-btn{padding:7px 16px;background:var(--navy);color:#fff;border:none;border-radius:7px;font-family:inherit;font-size:12px;font-weight:700;cursor:pointer;white-space:nowrap;transition:background .2s}
.print-btn:hover{background:var(--navy3)}

.tbl-scroll{overflow-x:auto}
table{width:100%;border-collapse:collapse;font-size:13px}
thead th{background:var(--navy);color:#fff;padding:11px 12px;text-align:center;font-size:11px;font-weight:700;letter-spacing:.5px;text-transform:uppercase;white-space:nowrap}
thead th.left{text-align:left}
tbody tr{border-bottom:1px solid var(--border);transition:background .15s}
tbody tr:last-child{border-bottom:none}
tbody tr:hover{background:var(--blue-lt)}
tbody tr.hidden{display:none}
td{padding:10px 12px;text-align:center;vertical-align:middle}
td.no{font-family:'JetBrains Mono',monospace;font-weight:600;color:var(--muted);width:36px;font-size:12px}
td.nama{text-align:left;font-weight:600;white-space:nowrap}
td.jk{font-size:11px;font-weight:800;letter-spacing:.5px}
td.jk.L{color:#2563eb}td.jk.P{color:#db2777}
.badge{display:inline-flex;align-items:center;justify-content:center;min-width:36px;padding:3px 8px;border-radius:5px;font-family:'JetBrains Mono',monospace;font-weight:700;font-size:12px}
.badge.H{background:var(--H-bg);color:var(--H);border:1px solid var(--H-bd)}
.badge.S{background:var(--S-bg);color:var(--S);border:1px solid var(--S-bd)}
.badge.I{background:var(--I-bg);color:var(--I);border:1px solid var(--I-bd)}
.badge.A{background:var(--A-bg);color:var(--A);border:1px solid var(--A-bd)}
.pct-wrap{display:flex;align-items:center;gap:6px;min-width:90px}
.pbar{flex:1;height:6px;border-radius:99px;background:#e2e8f0;overflow:hidden}
.pfill{height:100%;border-radius:99px}
.pval{font-size:11px;font-weight:700;font-family:'JetBrains Mono',monospace;color:var(--muted);white-space:nowrap;min-width:38px;text-align:right}
tfoot td{background:#f8fafc;font-weight:700;font-size:12px;padding:10px 12px;border-top:2px solid var(--border);text-align:center}
tfoot td.left{text-align:left;font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:.4px}

/* ── PRINT ─────────────────────────────────────────────────────────── */
@media print{
  .topbar,.chart-panel,.panel-head{display:none!important}
  .main-grid{display:block}.container{padding:0}
  .table-panel{border:none;box-shadow:none}
  body{background:#fff}
  .badge{-webkit-print-color-adjust:exact;print-color-adjust:exact}
}
</style>
</head>
<body>

<?php if (!$logged_in): ?>
<!-- ══════════════════════ LOGIN PAGE ══════════════════════ -->
<div class="login-wrap">
  <div class="login-card">
    <div class="login-logo">📋</div>
    <h1>Absensi Siswa</h1>
    <p>Masuk untuk melihat rekap kehadiran</p>

    <?php if ($login_error): ?>
      <div class="login-err">⚠️ <?= htmlspecialchars($login_error) ?></div>
    <?php endif; ?>

    <form method="POST">
      <div class="field">
        <label>Nama Lengkap</label>
        <input type="text" name="nama" placeholder="Tulis nama kamu…" required autocomplete="off">
      </div>
      <div class="field">
        <label>NIS</label>
        <input type="text" name="nis" placeholder="Nomor induk siswa…" required autocomplete="off">
      </div>
      <button class="login-btn" name="login">Masuk →</button>
    </form>

    <div class="nis-hint">
      💡 <strong>NIS Siswa</strong> = nomor urut 5 digit (misal siswa ke-1 → <strong>00001</strong>)<br>
      Guru/Admin: gunakan NIS yang telah diberikan.
    </div>
  </div>
</div>

<?php else: ?>
<!-- ══════════════════════ MAIN APP ══════════════════════════ -->

<!-- Topbar -->
<div class="topbar">
  <div class="topbar-left">
    <div class="topbar-icon">📋</div>
    <div>
      <div class="topbar-title">Rekap Absensi Bulanan</div>
      <div class="topbar-sub">Data kehadiran siswa per semester</div>
    </div>
  </div>
  <div class="topbar-right">
    <div class="user-pill">👤 <?= htmlspecialchars($_SESSION['user_name']) ?></div>
    <form method="POST" style="margin:0">
      <button class="logout-btn" name="logout">Keluar</button>
    </form>
  </div>
</div>

<div class="container">

  <!-- Stat Cards -->
  <div class="stats-row">
    <div class="stat-card total">
      <div class="stat-lbl">Total Siswa</div>
      <div class="stat-val"><?= count($siswa) ?></div>
      <div class="stat-sub"><?= $jml_L ?>L · <?= $jml_P ?>P</div>
    </div>
    <div class="stat-card H">
      <div class="stat-lbl">Total Hadir</div>
      <div class="stat-val"><?= number_format($tot_H) ?></div>
      <div class="stat-sub"><?= pct($tot_H,$grand) ?>% keseluruhan</div>
    </div>
    <div class="stat-card S">
      <div class="stat-lbl">Total Sakit</div>
      <div class="stat-val"><?= number_format($tot_S) ?></div>
      <div class="stat-sub"><?= pct($tot_S,$grand) ?>% keseluruhan</div>
    </div>
    <div class="stat-card I">
      <div class="stat-lbl">Total Izin</div>
      <div class="stat-val"><?= number_format($tot_I) ?></div>
      <div class="stat-sub"><?= pct($tot_I,$grand) ?>% keseluruhan</div>
    </div>
    <div class="stat-card A">
      <div class="stat-lbl">Total Alfa</div>
      <div class="stat-val"><?= number_format($tot_A) ?></div>
      <div class="stat-sub"><?= pct($tot_A,$grand) ?>% keseluruhan</div>
    </div>
  </div>

  <!-- Chart + Table -->
  <div class="main-grid">

    <!-- Donut Chart -->
    <div class="chart-panel">
      <h3>Proporsi Bulanan</h3>
      <div class="donut-wrap">
        <canvas id="donutChart" width="180" height="180"></canvas>
        <div class="donut-center">
          <div class="big"><?= $grand ?></div>
          <div class="tiny">Total</div>
        </div>
      </div>
      <div class="chart-legend">
        <div class="legend-row">
          <div class="legend-left"><div class="legend-dot H"></div><span>Hadir</span></div>
          <span class="legend-pct"><?= $tot_H ?> (<?= pct($tot_H,$grand) ?>%)</span>
        </div>
        <div class="legend-row">
          <div class="legend-left"><div class="legend-dot S"></div><span>Sakit</span></div>
          <span class="legend-pct"><?= $tot_S ?> (<?= pct($tot_S,$grand) ?>%)</span>
        </div>
        <div class="legend-row">
          <div class="legend-left"><div class="legend-dot I"></div><span>Izin</span></div>
          <span class="legend-pct"><?= $tot_I ?> (<?= pct($tot_I,$grand) ?>%)</span>
        </div>
        <div class="legend-row">
          <div class="legend-left"><div class="legend-dot A"></div><span>Alfa</span></div>
          <span class="legend-pct"><?= $tot_A ?> (<?= pct($tot_A,$grand) ?>%)</span>
        </div>
      </div>
    </div>

    <!-- Table Panel -->
    <div class="table-panel">
      <div class="panel-head">
        <h3>Daftar Absensi Siswa</h3>
        <input class="search-box" type="text" id="search" placeholder="🔍 Cari nama…" oninput="filter()">
        <select class="filter-sel" id="filterJK" onchange="filter()">
          <option value="">Semua</option>
          <option value="L">Laki-laki</option>
          <option value="P">Perempuan</option>
        </select>
        <button class="print-btn" onclick="window.print()">🖨️ Cetak</button>
      </div>
      <div class="tbl-scroll">
        <table id="tbl">
          <thead>
            <tr>
              <th>No</th>
              <th class="left">Nama Siswa</th>
              <th>JK</th>
              <th style="background:#155e1c">Hadir</th>
              <th style="background:#92400e">Sakit</th>
              <th style="background:#1e40af">Izin</th>
              <th style="background:#991b1b">Alfa</th>
              <th>% Hadir</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($siswa as $no => [$nama,$jk,$h,$s,$i,$a]): ?>
              <?php $total = $h+$s+$i+$a; $p = pct($h,$total); $c = bar_color($p); ?>
              <tr data-nama="<?= strtolower($nama) ?>" data-jk="<?= $jk ?>">
                <td class="no"><?= $no ?></td>
                <td class="nama"><?= htmlspecialchars($nama) ?></td>
                <td class="jk <?= $jk ?>"><?= $jk==='L'?'L':'P' ?></td>
                <td><span class="badge H"><?= $h ?></span></td>
                <td><span class="badge S"><?= $s ?></span></td>
                <td><span class="badge I"><?= $i ?></span></td>
                <td><span class="badge A"><?= $a ?></span></td>
                <td>
                  <div class="pct-wrap">
                    <div class="pbar"><div class="pfill" style="width:<?= $p ?>%;background:<?= $c ?>"></div></div>
                    <span class="pval" style="color:<?= $c ?>"><?= $p ?>%</span>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
          <tfoot>
            <tr>
              <td colspan="3" class="left">Σ Total</td>
              <td style="color:var(--H)"><?= $tot_H ?></td>
              <td style="color:var(--S)"><?= $tot_S ?></td>
              <td style="color:var(--I)"><?= $tot_I ?></td>
              <td style="color:var(--A)"><?= $tot_A ?></td>
              <td style="font-family:'JetBrains Mono',monospace;font-size:12px"><?= pct($tot_H,$grand) ?>%</td>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>

  </div><!-- /main-grid -->
</div><!-- /container -->

<script>
// ── Filter ──────────────────────────────────────────────────────────────────
function filter(){
  const q  = document.getElementById('search').value.toLowerCase();
  const jk = document.getElementById('filterJK').value;
  document.querySelectorAll('#tbl tbody tr').forEach(tr=>{
    const okN = tr.dataset.nama.includes(q);
    const okJ = !jk || tr.dataset.jk===jk;
    tr.classList.toggle('hidden', !(okN&&okJ));
  });
}

// ── Donut Chart (pure canvas, no library) ───────────────────────────────────
(function(){
  const canvas = document.getElementById('donutChart');
  const ctx    = canvas.getContext('2d');
  const data   = [<?=$tot_H?>,<?=$tot_S?>,<?=$tot_I?>,<?=$tot_A?>];
  const colors = ['#16a34a','#d97706','#3b82f6','#ef4444'];
  const total  = data.reduce((a,b)=>a+b,0);
  if(!total) return;

  const cx=90,cy=90,R=80,r=52;
  let angle = -Math.PI/2;

  data.forEach((val,i)=>{
    const slice = (val/total)*2*Math.PI;
    ctx.beginPath();
    ctx.moveTo(cx,cy);
    ctx.arc(cx,cy,R,angle,angle+slice);
    ctx.closePath();
    ctx.fillStyle = colors[i];
    ctx.fill();
    angle += slice;
  });

  // hole
  ctx.beginPath();
  ctx.arc(cx,cy,r,0,2*Math.PI);
  ctx.fillStyle='#fff';
  ctx.fill();

  // gap lines
  angle = -Math.PI/2;
  data.forEach(val=>{
    const slice=(val/total)*2*Math.PI;
    ctx.beginPath();
    ctx.moveTo(cx,cy);
    ctx.lineTo(cx+R*Math.cos(angle),cy+R*Math.sin(angle));
    ctx.strokeStyle='#fff';
    ctx.lineWidth=2;
    ctx.stroke();
    angle+=slice;
  });
})();
</script>

<?php endif; ?>
</body>
</html>
