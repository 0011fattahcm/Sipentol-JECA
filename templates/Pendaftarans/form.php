<?php
/** @var \App\View\AppView $this */

$this->assign('title', 'Form Pendaftaran');

$nama   = $identity ? (string)$identity->get('nama_lengkap') : 'Peserta';
$email  = $identity ? (string)$identity->get('email') : '';
$status = $identity ? (string)$identity->get('status') : 'pendaftaran';

$statusText = 'Pendaftaran';
$badgeClass = 'bg-indigo-50 text-indigo-700 ring-indigo-200';

$initials = 'U';
if ($nama) {
    $parts = preg_split('/\s+/', trim($nama));
    $first = mb_substr($parts[0] ?? '', 0, 1);
    $last  = mb_substr($parts[count($parts)-1] ?? '', 0, 1);
    $initials = mb_strtoupper($first . ($last ?: ''));
}

$nav = [
    ['label' => 'Dashboard',        'href' => $this->Url->build('/dashboard'),  'active' => false, 'enabled' => true],
    ['label' => 'Form Pendaftaran', 'href' => $this->Url->build('/pendaftaran'),'active' => true,  'enabled' => true],
    ['label' => 'Tes Online',       'href' => '#', 'active' => false, 'enabled' => false],
    ['label' => 'Daftar Ulang',     'href' => '#', 'active' => false, 'enabled' => false],
    ['label' => 'Onboarding',       'href' => '#', 'active' => false, 'enabled' => false],
];

$input =
    'w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm ' .
    'placeholder:text-slate-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 outline-none';

$label = 'text-sm font-semibold text-slate-800';
$help  = 'text-xs text-slate-500 mt-1';
$ttlValue = '';
if (!empty($pendaftaran->tanggal_lahir)) {
    $ttl = $pendaftaran->tanggal_lahir;

    // Cake FrozenDate/Date biasanya punya method format(), walau kadang cast string-nya jadi format lokal.
    if (is_object($ttl) && method_exists($ttl, 'format')) {
        $ttlValue = $ttl->format('Y-m-d');
    } else {
        $raw = (string)$ttl;

        // kalau ada timestamp, ambil 10 karakter awal
        if (preg_match('/^\d{4}-\d{2}-\d{2}/', $raw)) {
            $ttlValue = substr($raw, 0, 10);
        } else {
            // fallback untuk format umum yang suka kejadian (lokal)
            $dt = \DateTime::createFromFormat('d/m/Y', $raw)
               ?: \DateTime::createFromFormat('m/d/Y', $raw);
            $ttlValue = $dt ? $dt->format('Y-m-d') : '';
        }
    }
}


?>

<div class="min-h-screen bg-slate-950">
    <div class="pointer-events-none fixed inset-0 overflow-hidden">
        <div class="absolute -top-40 -left-40 h-96 w-96 rounded-full bg-indigo-500/20 blur-3xl"></div>
        <div class="absolute top-40 -right-40 h-96 w-96 rounded-full bg-sky-500/20 blur-3xl"></div>
        <div class="absolute bottom-0 left-1/3 h-96 w-96 rounded-full bg-emerald-500/10 blur-3xl"></div>
    </div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <!-- Mobile Topbar -->
        <div class="lg:hidden flex items-center justify-between mb-6">
            <div>
                <div class="text-white text-xl font-semibold">SiPentol</div>
                <div class="text-slate-300 text-sm">Form Pendaftaran</div>
            </div>
            <button
                type="button"
                class="inline-flex items-center justify-center rounded-xl bg-white/10 hover:bg-white/15 text-white px-4 py-2 ring-1 ring-white/10"
                onclick="document.getElementById('mobileSidebar').classList.remove('hidden')"
            >
                Menu
            </button>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <?= $this->element('dashboard/sidebar', compact('identity','nama','email','initials','badgeClass','statusText','nav') + ['active' => 'pendaftaran']) ?>

            <!-- MAIN -->
            <section class="lg:col-span-9 space-y-6">

                <!-- Header -->
                <div class="rounded-2xl bg-white/5 ring-1 ring-white/10 backdrop-blur-xl p-6">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div>
                            <div class="text-slate-300 text-sm">SiPentol</div>
                            <h1 class="text-2xl md:text-3xl font-bold text-white tracking-tight">Form Pendaftaran</h1>
                            <p class="text-slate-300 mt-2">Lengkapi data Anda. Form ini dapat diedit kapan saja.</p>
                        </div>

                        <div class="flex items-center gap-3">
                            <a href="<?= $this->Url->build('/dashboard') ?>"
                               class="inline-flex items-center justify-center rounded-xl bg-white/10 hover:bg-white/15 text-white px-4 py-3 text-sm font-semibold ring-1 ring-white/10 transition">
                                Kembali ke Dashboard
                            </a>
                            <button type="submit" form="pendaftaranForm"
                               class="inline-flex items-center justify-center rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white px-5 py-3 text-sm font-semibold shadow-lg shadow-indigo-600/20 transition">
                                Simpan
                            </button>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl bg-white ring-1 ring-black/5 shadow-xl shadow-black/10 p-4">
                    <div class="text-sm text-slate-600">
                        Field bertanda <span class="text-rose-600 font-semibold">*</span> wajib diisi.
                    </div>
                </div>

                <?= $this->Form->create($pendaftaran, [
    'id' => 'pendaftaranForm',
    'type' => 'file',
    'class' => 'space-y-6',
]) ?>
                    <!-- BIODATA -->
                    <div class="rounded-2xl bg-white ring-1 ring-black/5 shadow-xl shadow-black/10 p-6">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h2 class="text-lg font-bold text-slate-900">Biodata</h2>
                                <p class="text-sm text-slate-600 mt-1">Isi sesuai identitas Anda.</p>
                            </div>
                            <span class="text-xs font-semibold text-slate-500 bg-slate-100 rounded-full px-3 py-1">Wajib</span>
                        </div>

                        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="<?= $label ?>">NIK <span class="text-rose-600">*</span></label>
                               <input type="text" name="nik" class="<?= $input ?>"value="<?= h($pendaftaran->nik ?? '') ?>" placeholder="Masukkan NIK" required>
                            </div>

                            <div>
                                <label class="<?= $label ?>">Nama Lengkap <span class="text-rose-600">*</span></label>
                                <input type="text" name="nama_lengkap" class="<?= $input ?>"value="<?= h($pendaftaran->nama_lengkap ?? '') ?>" placeholder="Nama sesuai KTP" required>
                            </div>

                            <div>
                                <label class="<?= $label ?>">Jenis Kelamin <span class="text-rose-600">*</span></label>
                                <select name="jenis_kelamin" class="<?= $input ?>" required>
                                    <option value="">-- pilih --</option>
                               <option value="L" <?= (($pendaftaran->jenis_kelamin ?? '') === 'L') ? 'selected' : '' ?>>Laki-laki</option>
                               <option value="P" <?= (($pendaftaran->jenis_kelamin ?? '') === 'P') ? 'selected' : '' ?>>Perempuan</option>
                                </select>
                            </div>

                            <div>
                                <label class="<?= $label ?>">Tanggal Lahir <span class="text-rose-600">*</span></label>
<input id="tglLahir" type="date" name="tanggal_lahir" class="<?= $input ?>" value="<?= h($ttlValue) ?>" required>
                            </div>

                            <div>
                                <label class="<?= $label ?>">Usia <span class="text-rose-600">*</span> <span class="text-xs text-slate-500 font-normal">(otomatis)</span></label>
                                <input id="usia" type="number" name="usia" class="<?= $input ?> bg-slate-50" value="<?= h($pendaftaran->usia ?? '') ?>"readonly required>
                                <div class="<?= $help ?>">Diisi otomatis dari tanggal lahir.</div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <div>
                                    <label class="<?= $label ?>">Tinggi Badan (cm) <span class="text-rose-600">*</span></label>
                                    <input type="number" name="tinggi_badan" class="<?= $input ?>"value="<?= h($pendaftaran->tinggi_badan ?? '') ?>"placeholder="Contoh: 170" required>
                                </div>
                                <div>
                                    <label class="<?= $label ?>">Berat Badan (kg) <span class="text-rose-600">*</span></label>
                                    <input type="number" name="berat_badan" class="<?= $input ?>"value="<?= h($pendaftaran->berat_badan ?? '') ?>"placeholder="Contoh: 60" required>
                                </div>
                            </div>

                      <div class="md:col-span-2">
    <label class="<?= $label ?>">Alamat Lengkap <span class="text-rose-600">*</span></label>
    <textarea id="alamatLengkap" name="alamat_lengkap" rows="3" class="<?= $input ?>" required><?= h($pendaftaran->alamat_lengkap ?? '') ?></textarea>
</div>

<div class="md:col-span-2">
    <div class="flex items-center justify-between gap-4">
        <label class="<?= $label ?>">Domisili Saat Ini <span class="text-rose-600">*</span></label>
        <label class="inline-flex items-center gap-2 text-sm text-slate-600 select-none">
            <input id="sameDom" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500/30">
            Sama dengan alamat lengkap
        </label>
    </div>
    <textarea id="domisili" name="domisili_saat_ini" rows="3" class="<?= $input ?>" required><?= h($pendaftaran->domisili_saat_ini ?? '') ?></textarea>
</div>

                        </div>
                    </div>

                    <!-- PENDIDIKAN -->
                    <div class="rounded-2xl bg-white ring-1 ring-black/5 shadow-xl shadow-black/10 p-6">
                        <div>
                            <h2 class="text-lg font-bold text-slate-900">Pendidikan</h2>
                            <p class="text-sm text-slate-600 mt-1">Pendidikan terakhir yang Anda selesaikan.</p>
                        </div>

                        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="<?= $label ?>">Jenjang Terakhir <span class="text-rose-600">*</span></label>
                   <select name="pendidikan_jenjang" class="<?= $input ?>" required>
    <option value="">-- pilih --</option>

    <option value="SMA" <?= (($pendaftaran->pendidikan_jenjang ?? '') === 'SMA') ? 'selected' : '' ?>>SMA</option>
    <option value="SMK" <?= (($pendaftaran->pendidikan_jenjang ?? '') === 'SMK') ? 'selected' : '' ?>>SMK</option>

    <option value="D1" <?= (($pendaftaran->pendidikan_jenjang ?? '') === 'D1') ? 'selected' : '' ?>>D1</option>
    <option value="D2" <?= (($pendaftaran->pendidikan_jenjang ?? '') === 'D2') ? 'selected' : '' ?>>D2</option>
    <option value="D3" <?= (($pendaftaran->pendidikan_jenjang ?? '') === 'D3') ? 'selected' : '' ?>>D3</option>

    <option value="S1" <?= (($pendaftaran->pendidikan_jenjang ?? '') === 'S1') ? 'selected' : '' ?>>S1</option>
    <option value="S2" <?= (($pendaftaran->pendidikan_jenjang ?? '') === 'S2') ? 'selected' : '' ?>>S2</option>
</select>
                            </div>

                            <div>
                                <label class="<?= $label ?>">Nama Instansi <span class="text-rose-600">*</span></label>
                                <input type="text" name="pendidikan_instansi" class="<?= $input ?>" value="<?= h($pendaftaran->pendidikan_instansi ?? '') ?>"required>
                            </div>

                            <div>
                                <label class="<?= $label ?>">Jurusan <span class="text-rose-600">*</span></label> 
                                <input type="text" name="pendidikan_jurusan" class="<?= $input ?>" value="<?= h($pendaftaran->pendidikan_jurusan ?? '') ?>"required>
                            </div>

                            <div>
                                <label class="<?= $label ?>">Tahun Kelulusan <span class="text-rose-600">*</span></label>
                                <input type="number" name="pendidikan_tahun_kelulusan" class="<?= $input ?>" value="<?= h($pendaftaran->pendidikan_tahun_kelulusan ?? '') ?>" required>
                            </div>
                        </div>
                    </div>

                    <!-- KELUARGA -->
                    <div class="rounded-2xl bg-white ring-1 ring-black/5 shadow-xl shadow-black/10 p-6">
                        <div>
                            <h2 class="text-lg font-bold text-slate-900">Keluarga</h2>
                            <p class="text-sm text-slate-600 mt-1">Data keluarga inti dan wali (opsional).</p>
                        </div>

                        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="<?= $label ?>">Nama Ayah <span class="text-rose-600">*</span></label>
                                <input type="text" name="ayah_nama" class="<?= $input ?>" value="<?= h($pendaftaran->ayah_nama ?? '') ?>" required>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <div>
                                    <label class="<?= $label ?>">Usia <span class="text-rose-600">*</span></label>
                                    <input type="number" name="ayah_usia" class="<?= $input ?>" value="<?= h($pendaftaran->ayah_usia ?? '') ?>" required>
                                </div>
                                <div>
                                    <label class="<?= $label ?>">Pekerjaan <span class="text-rose-600">*</span></label>
                                    <input type="text" name="ayah_pekerjaan" class="<?= $input ?>" value="<?= h($pendaftaran->ayah_pekerjaan ?? '') ?>" required>
                                </div>
                            </div>

                            <div>
                                <label class="<?= $label ?>">Nama Ibu <span class="text-rose-600">*</span></label>
                                <input type="text" name="ibu_nama" class="<?= $input ?>" value="<?= h($pendaftaran->ibu_nama ?? '') ?>" required>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <div>
                                    <label class="<?= $label ?>">Usia <span class="text-rose-600">*</span></label>
                                    <input type="number" name="ibu_usia" class="<?= $input ?>" value="<?= h($pendaftaran->ibu_usia ?? '') ?>" required>
                                </div>
                                <div>
                                    <label class="<?= $label ?>">Pekerjaan <span class="text-rose-600">*</span></label>
                                    <input type="text" name="ibu_pekerjaan" class="<?= $input ?>" value="<?= h($pendaftaran->ibu_pekerjaan ?? '') ?>" required>
                                </div>
                            </div>

                            <div>
                                <label class="<?= $label ?>">Nama Wali</label>
                                <input type="text" name="wali_nama" class="<?= $input ?>" value="<?= h($pendaftaran->wali_nama ?? '') ?>" placeholder="Opsional">
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <div>
                                    <label class="<?= $label ?>">Usia</label>
                                    <input type="number" name="wali_usia" class="<?= $input ?>" value="<?= h($pendaftaran->wali_usia ?? '') ?>" placeholder="Opsional">
                                </div>
                                <div>
                                    <label class="<?= $label ?>">Pekerjaan</label>
                                    <input type="text" name="wali_pekerjaan" class="<?= $input ?>" value="<?= h($pendaftaran->wali_pekerjaan ?? '') ?>" placeholder="Opsional">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- KONTAK -->
                    <div class="rounded-2xl bg-white ring-1 ring-black/5 shadow-xl shadow-black/10 p-6">
                        <div>
                            <h2 class="text-lg font-bold text-slate-900">Kontak</h2>
                            <p class="text-sm text-slate-600 mt-1">Pastikan bisa dihubungi.</p>
                        </div>

                        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="<?= $label ?>">Email <span class="text-rose-600">*</span></label>
                                <input type="email" name="email" class="<?= $input ?> bg-slate-50" value="<?= h($pendaftaran->email ?? $email) ?>">
                                <div class="<?= $help ?>">Mengikuti email akun login.</div>
                            </div>

                            <div>
                                <label class="<?= $label ?>">Whatsapp <span class="text-rose-600">*</span></label>
                <input type="text" name="whatsapp" class="<?= $input ?> bg-slate-50" value="<?= h($pendaftaran->whatsapp ?? '') ?>" required>
                            </div>

                            <div>
                                <label class="<?= $label ?>">Link Instagram</label>
                                <input type="url" name="instagram" class="<?= $input ?>" value="<?= h($pendaftaran->instagram ?? '') ?>" placeholder="https://instagram.com/username">
                            </div>

                            <div>
                                <label class="<?= $label ?>">Link Facebook</label>
                                <input type="url" name="facebook" class="<?= $input ?>" placeholder="https://facebook.com/username">
                            </div>
                        </div>
                    </div>

                    <!-- UPLOAD -->
                    <div class="rounded-2xl bg-white ring-1 ring-black/5 shadow-xl shadow-black/10 p-6">
                        <div>
                            <h2 class="text-lg font-bold text-slate-900">Unggah File</h2>
                            <p class="text-sm text-slate-600 mt-1">Format sesuai ketentuan. File wajib diunggah.</p>
                        </div>

                        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="<?= $label ?>">Pas Foto Terbaru (JPG/PNG/JPEG) <span class="text-rose-600">*</span></label>
                                <input type="file" name="pas_foto" class="<?= $input ?> py-2" accept=".jpg,.jpeg,.png"<?= empty($pendaftaran->pas_foto_path) ? 'required' : '' ?>>
                                    <?php if (!empty($pendaftaran->pas_foto_path)): ?>
                                    <div class="<?= $help ?>">Sudah terunggah: <?= h($pendaftaran->pas_foto_path) ?> (upload ulang jika ingin ganti)</div>
                                    <?php endif; ?>
                                    <?php if (!empty($pendaftaran->pas_foto_path)): ?>
    <a href="<?= $this->Url->build('/' . ltrim($pendaftaran->pas_foto_path, '/')) ?>"
       target="_blank"
       class="inline-flex items-center justify-center rounded-xl bg-slate-900 text-white px-4 py-2 text-sm font-semibold mt-2">
        Lihat File
    </a>
<?php endif; ?>
                                <div class="<?= $help ?>">Akan digunakan sebagai foto profil.</div>
                            </div>

                            <div>
                                <label class="<?= $label ?>">Scan KTP (PDF) <span class="text-rose-600">*</span></label>
<input type="file"
       name="ktp_pdf"
       class="<?= $input ?> py-2"
       accept=".pdf"
       <?= empty($pendaftaran->ktp_pdf_path) ? 'required' : '' ?>>
                                    <?php if (!empty($pendaftaran->ktp_pdf_path)): ?>
                                    <div class="<?= $help ?>">Sudah terunggah: <?= h($pendaftaran->ktp_pdf_path) ?> (upload ulang jika ingin ganti)</div>
                                    <?php endif; ?>
                            <?php if (!empty($pendaftaran->ktp_pdf_path)): ?>
    <a href="<?= $this->Url->build('/' . ltrim($pendaftaran->ktp_pdf_path, '/')) ?>"
       target="_blank"
       class="inline-flex items-center justify-center rounded-xl bg-slate-900 text-white px-4 py-2 text-sm font-semibold mt-2">
        Lihat File
    </a>
<?php endif; ?>


                            </div>

                            <div>
                                <label class="<?= $label ?>">Scan Ijazah (PDF) <span class="text-rose-600">*</span></label>
<input type="file" name="ijazah_pdf" class="<?= $input ?> py-2" accept=".pdf" <?= empty($pendaftaran->ijazah_pdf_path) ? 'required' : '' ?>>
                                    <?php if (!empty($pendaftaran->ijazah_pdf_path)): ?>
                                    <div class="<?= $help ?>">Sudah terunggah: <?= h($pendaftaran->ijazah_pdf_path) ?> (upload ulang jika ingin ganti)</div>
                                    <?php endif; ?>
                                    <?php if (!empty($pendaftaran->ijazah_pdf_path)): ?>
    <a href="<?= $this->Url->build('/' . ltrim($pendaftaran->ijazah_pdf_path, '/')) ?>"
       target="_blank"
       class="inline-flex items-center justify-center rounded-xl bg-slate-900 text-white px-4 py-2 text-sm font-semibold mt-2">
        Lihat File
    </a>
<?php endif; ?>

                            </div>

                            <div>
                                <label class="<?= $label ?>">Scan Transkrip Nilai (PDF) <span class="text-rose-600">*</span></label>
<input type="file" name="transkrip_pdf" class="<?= $input ?> py-2" accept=".pdf" <?= empty($pendaftaran->transkrip_pdf_path) ? 'required' : '' ?>>
                                    <?php if (!empty($pendaftaran->transkrip_pdf_path)): ?>
                                    <div class="<?= $help ?>">Sudah terunggah: <?= h($pendaftaran->transkrip_pdf_path) ?> (upload ulang jika ingin ganti)</div>
                                    <?php endif; ?>
                                    <?php if (!empty($pendaftaran->transkrip_pdf_path)): ?>
    <a href="<?= $this->Url->build('/' . ltrim($pendaftaran->transkrip_pdf_path, '/')) ?>"
       target="_blank"
       class="inline-flex items-center justify-center rounded-xl bg-slate-900 text-white px-4 py-2 text-sm font-semibold mt-2">
        Lihat File
    </a>
<?php endif; ?>
                            
                                </div>
                        </div>
                    </div>
<!-- SUMBER INFORMASI PENDAFTARAN -->
<div class="rounded-2xl bg-white ring-1 ring-black/5 shadow-xl shadow-black/10 p-6">
  <div class="flex items-start justify-between gap-4">
    <div>
      <h2 class="text-lg font-bold text-slate-900 uppercase tracking-wide">Sumber Informasi Pendaftaran</h2>
      <p class="text-sm text-slate-600 mt-1">Wajib memilih salah satu sumber.</p>
    </div>
    <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">
      Wajib
    </span>
  </div>

  <?php
    $src = (string)($pendaftaran->info_sumber ?? '');
    $refVal = (string)($pendaftaran->info_referral_code ?? '');
    $instVal = (string)($pendaftaran->info_instansi_nama ?? '');
    $lainVal = (string)($pendaftaran->info_sumber_lain ?? '');

    $radio = function(string $val) use ($src) {
      return $src === $val ? 'checked' : '';
    };
  ?>

  <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-8">

    <!-- Kolom 1 -->
    <div class="space-y-4">
      <label class="flex items-center gap-3 text-sm text-slate-900">
        <input type="radio" name="info_sumber" value="website" class="h-4 w-4" <?= $radio('website') ?>>
        Website
      </label>
      <label class="flex items-center gap-3 text-sm text-slate-900">
        <input type="radio" name="info_sumber" value="facebook" class="h-4 w-4" <?= $radio('facebook') ?>>
        Facebook
      </label>
      <label class="flex items-center gap-3 text-sm text-slate-900">
        <input type="radio" name="info_sumber" value="instagram" class="h-4 w-4" <?= $radio('instagram') ?>>
        Instagram
      </label>
      <label class="flex items-center gap-3 text-sm text-slate-900">
        <input type="radio" name="info_sumber" value="tiktok" class="h-4 w-4" <?= $radio('tiktok') ?>>
        Tiktok
      </label>
    </div>

    <!-- Kolom 2 -->
    <div class="space-y-5">
      <div>
        <label class="flex items-center gap-3 text-sm text-slate-900">
          <input type="radio" name="info_sumber" value="jeca_group" class="h-4 w-4" <?= $radio('jeca_group') ?>>
          JECA Group
        </label>
        <div class="mt-2">
          <input
            type="text"
            id="referralInput"
            name="info_referral_code_input"
            placeholder="Masukkan kode referral"
            class="<?= $input ?>"
            value="<?= h($refVal) ?>"
          >
        </div>
      </div>

      <div>
        <label class="flex items-center gap-3 text-sm text-slate-900">
          <input type="radio" name="info_sumber" value="jeca_relations" class="h-4 w-4" <?= $radio('jeca_relations') ?>>
          JECA Relations
        </label>
        <div class="mt-2">
          <input
            type="text"
            id="referralInput2"
            name="info_referral_code_input"
            placeholder="Masukkan kode referral"
            class="<?= $input ?>"
            value="<?= h($refVal) ?>"
          >
        </div>
      </div>
    </div>

    <!-- Kolom 3 -->
    <div class="space-y-5">
      <div>
        <label class="flex items-center gap-3 text-sm text-slate-900">
          <input type="radio" name="info_sumber" value="instansi" class="h-4 w-4" <?= $radio('instansi') ?>>
          Instansi
        </label>
        <div class="mt-2">
          <input
            type="text"
            id="instansiInput"
            name="info_instansi_nama_input"
            placeholder="Masukkan Nama Instansi"
            class="<?= $input ?>"
            value="<?= h($instVal) ?>"
          >
        </div>
      </div>

      <div>
        <label class="flex items-center gap-3 text-sm text-slate-900">
          <input type="radio" name="info_sumber" value="lain" class="h-4 w-4" <?= $radio('lain') ?>>
          Sumber lain
        </label>
        <div class="mt-2">
          <input
            type="text"
            id="lainInput"
            name="info_sumber_lain_input"
            placeholder="Sebutkan"
            class="<?= $input ?>"
            value="<?= h($lainVal) ?>"
          >
        </div>
      </div>
    </div>
  </div>

  <p class="mt-5 text-xs text-slate-500">
    <span class="text-rose-600 font-semibold">*</span> Wajib memilih salah satu sumber. Jika memilih JECA Group/JECA Relations, isi kode referral.
  </p>
</div>

<script>
(function(){
  const radios = document.querySelectorAll('input[name="info_sumber"]');
  const ref1 = document.getElementById('referralInput');
  const ref2 = document.getElementById('referralInput2');
  const inst = document.getElementById('instansiInput');
  const lain = document.getElementById('lainInput');

  function setDisabled(el, disabled){
    if (!el) return;
    el.disabled = disabled;
    el.classList.toggle('opacity-50', disabled);
    el.classList.toggle('pointer-events-none', disabled);
  }

  function sync(){
    const checked = document.querySelector('input[name="info_sumber"]:checked');
    const v = checked ? checked.value : '';

    // default disable semua
    setDisabled(ref1, true);
    setDisabled(ref2, true);
    setDisabled(inst, true);
    setDisabled(lain, true);

    if (v === 'jeca_group') setDisabled(ref1, false);
    if (v === 'jeca_relations') setDisabled(ref2, false);
    if (v === 'instansi') setDisabled(inst, false);
    if (v === 'lain') setDisabled(lain, false);
  }

  radios.forEach(r => r.addEventListener('change', sync));
  sync();
})();
</script>

                    <!-- Sticky bottom action (mobile) -->
                    <div class="lg:hidden sticky bottom-4">
                        <button type="submit"
                            class="w-full rounded-2xl bg-indigo-600 hover:bg-indigo-500 text-white px-5 py-4 text-sm font-semibold shadow-lg shadow-indigo-600/20 transition">
                            Simpan
                        </button>
                    </div>

                <?= $this->Form->end() ?>

            </section>
        </div>
    </div>

<?= $this->element('dashboard/mobile_sidebar', compact('identity','nama','email','initials','badgeClass','statusText','nav') + ['active' => 'pendaftaran']) ?>
</div>

<script>
(function () {
    const tgl = document.getElementById('tglLahir');
    const usia = document.getElementById('usia');
    const sameDom = document.getElementById('sameDom');
const alamat = document.getElementById('alamatLengkap');
const dom = document.getElementById('domisili');

    function calcAge(dateStr) {
        if (!dateStr) return '';
        const dob = new Date(dateStr);
        if (isNaN(dob.getTime())) return '';
        const today = new Date();
        let age = today.getFullYear() - dob.getFullYear();
        const m = today.getMonth() - dob.getMonth();
        if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) age--;
        return age >= 0 ? age : '';
    }

    if (tgl && usia) {
        tgl.addEventListener('change', () => usia.value = calcAge(tgl.value));
        // init
// init: jangan timpa usia dari DB jika sudah ada
if (!usia.value) usia.value = calcAge(tgl.value);
    }

    if (sameDom && alamat && dom) {
        function sync() {
            if (sameDom.checked) {
                dom.value = alamat.value;
                dom.setAttribute('readonly', 'readonly');
                dom.classList.add('bg-slate-50');
            } else {
                dom.removeAttribute('readonly');
                dom.classList.remove('bg-slate-50');
            }
        }
        sameDom.addEventListener('change', sync);
        alamat.addEventListener('input', () => { if (sameDom.checked) dom.value = alamat.value; });
        sync();
    }
})();
</script>
