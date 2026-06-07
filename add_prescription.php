<?php
require_once '../../includes/auth.php';
requireRole('doctor');
require_once '../../config/database.php';
require_once '../../config/csrf.php';

$consultation_id = (int)($_GET['id'] ?? $_POST['consultation_id'] ?? 0);
$doctor_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT patient_id, status FROM consultations WHERE id=? AND doctor_id=?");
$stmt->bind_param("ii", $consultation_id, $doctor_id);
$stmt->execute();
$consult = $stmt->get_result()->fetch_assoc();

if (!$consult || !in_array($consult['status'], ['completed','ongoing'])) {
    redirect('/modules/doctor/dashboard.php', 'Invalid consultation', 'error');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) redirect("/modules/doctor/add_prescription.php?id=$consultation_id", 'Invalid token', 'error');
    $medicines = json_encode($_POST['medicines'] ?? []);
    $notes = trim($_POST['notes'] ?? '');
    $stmt = $conn->prepare("INSERT INTO prescriptions (consultation_id, patient_id, doctor_id, prescription_date, medicines, notes) VALUES (?, ?, ?, CURDATE(), ?, ?)");
    $stmt->bind_param("iiiss", $consultation_id, $consult['patient_id'], $doctor_id, $medicines, $notes);
    $stmt->execute();
    redirect('/modules/doctor/dashboard.php', 'Prescription saved', 'success');
}

$med_result = $conn->query("SELECT id, name, dosage_instructions, category FROM medicines ORDER BY name ASC");
$medicines_list = [];
while ($row = $med_result->fetch_assoc()) { $medicines_list[] = $row; }
$medicines_json = json_encode($medicines_list);

$csrf = generateCSRFToken();
$pageTitle = "Add Prescription";
include '../../includes/header.php';
?>
<div class="card">
    <h2>✍️ Add Prescription</h2>
    <form method="POST" id="prescForm">
        <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
        <input type="hidden" name="consultation_id" value="<?= $consultation_id ?>">
        <div id="medContainer"></div>
        <button type="button" class="btn btn-secondary" onclick="addMedicine()" style="margin-bottom:20px;">+ Add Another Medicine</button>
        <div class="form-group">
            <label>Doctor's Notes</label>
            <textarea name="notes" placeholder="Any additional notes for the patient..." rows="3"></textarea>
        </div>
        <button type="submit" class="btn btn-primary">💾 Save Prescription</button>
    </form>
</div>
<style>
.med-row { display:grid; grid-template-columns:2.2fr 1.4fr 1.4fr 1fr auto; gap:10px; margin-bottom:12px; background:var(--bg); padding:14px; border-radius:var(--radius-sm); border:1px solid var(--border); align-items:flex-end; }
.med-label { display:block; font-size:0.75rem; font-weight:600; color:var(--text-muted); margin-bottom:5px; text-transform:uppercase; letter-spacing:0.04em; }
.med-row select, .med-row input { width:100%; padding:9px 12px; border:1px solid var(--border); border-radius:var(--radius-sm); background:var(--card); color:var(--text); font-size:0.88rem; font-family:inherit; }
.med-row select:focus, .med-row input:focus { outline:none; border-color:var(--primary); box-shadow:0 0 0 3px var(--primary-light); }
.custom-name-wrap { display:none; margin-top:6px; }
.custom-name-wrap input { border:1px dashed var(--primary) !important; }
@media(max-width:768px){ .med-row { grid-template-columns:1fr; } }
</style>
<script>
const medicineData = <?= $medicines_json ?>;
let count = 0;
function escHtml(s){ return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
function updateRemoveButtons(){
    const btns = document.querySelectorAll('.btn-remove');
    btns.forEach(b => b.style.display = btns.length === 1 ? 'none' : 'inline-flex');
}
function onMedSelect(sel, idx){
    const customWrap = document.getElementById('custom-wrap-'+idx);
    const customInput = document.getElementById('custom-name-'+idx);
    const dosageInput = document.getElementById('dosage-'+idx);
    if(sel.value === '__other__'){
        customWrap.style.display = 'block';
        customInput.required = true;
        if(dosageInput) dosageInput.value = '';
    } else {
        customWrap.style.display = 'none';
        customInput.required = false;
        const opt = sel.options[sel.selectedIndex];
        const d = opt.getAttribute('data-dosage')||'';
        if(dosageInput && d) dosageInput.value = d;
    }
}
function buildRow(idx){
    const opts = medicineData.map(m=>`<option value="${escHtml(m.name)}" data-dosage="${escHtml(m.dosage_instructions)}">${escHtml(m.name)} (${escHtml(m.category)})</option>`).join('');
    const div = document.createElement('div');
    div.className='med-row'; div.id='med-'+idx;
    div.innerHTML=`
        <div>
            <label class="med-label">Medicine</label>
            <select name="medicines[${idx}][name]" id="sel-${idx}" onchange="onMedSelect(this,${idx})" required>
                <option value="">-- Select Medicine --</option>${opts}
                <option disabled>──────────────</option>
                <option value="__other__">✏️ Other (type manually)</option>
            </select>
            <div class="custom-name-wrap" id="custom-wrap-${idx}">
                <input type="text" id="custom-name-${idx}" placeholder="Type medicine name...">
            </div>
        </div>
        <div><label class="med-label">Dosage</label><input type="text" name="medicines[${idx}][dosage]" id="dosage-${idx}" placeholder="e.g. 1 tablet"></div>
        <div><label class="med-label">Frequency</label>
            <select name="medicines[${idx}][frequency]">
                <option value="">-- Select --</option>
                <option>Once daily</option><option>Twice daily</option><option>3 times daily</option><option>Every 4-6 hours</option><option>As needed</option>
            </select>
        </div>
        <div><label class="med-label">Duration</label><input type="text" name="medicines[${idx}][duration]" placeholder="e.g. 7 days"></div>
        <div><button type="button" class="btn btn-danger btn-sm btn-remove" onclick="removeMed(${idx})">✕ Remove</button></div>`;
    return div;
}
function addMedicine(){ document.getElementById('medContainer').appendChild(buildRow(count)); count++; updateRemoveButtons(); }
function removeMed(idx){ const r=document.getElementById('med-'+idx); if(r){ r.remove(); updateRemoveButtons(); } }
document.getElementById('prescForm').addEventListener('submit', function(){
    document.querySelectorAll('.med-row').forEach(row=>{
        const sel = row.querySelector('select[id^="sel-"]');
        const customInput = row.querySelector('[id^="custom-name-"]');
        if(sel && sel.value==='__other__' && customInput && customInput.value.trim()){
            const hidden = document.createElement('input');
            hidden.type='hidden'; hidden.name=sel.name; hidden.value=customInput.value.trim();
            sel.removeAttribute('name');
            row.appendChild(hidden);
        }
    });
});
addMedicine();
</script>
<?php include '../../includes/footer.php'; ?>
