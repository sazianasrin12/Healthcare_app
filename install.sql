CREATE DATABASE IF NOT EXISTS digital_healthcare;
USE digital_healthcare;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('patient','doctor','admin') DEFAULT 'patient',
    phone VARCHAR(20),
    zoom_meeting_id VARCHAR(100),
    zoom_meeting_password VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS consultations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    doctor_id INT NOT NULL,
    date DATE,
    time TIME,
    message TEXT,
    status ENUM('pending','accepted','ongoing','completed','rejected') DEFAULT 'pending',
    zoom_meeting_id VARCHAR(100),
    zoom_meeting_pwd VARCHAR(100),
    start_time DATETIME,
    end_time DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES users(id),
    FOREIGN KEY (doctor_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS prescriptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    consultation_id INT NOT NULL,
    patient_id INT NOT NULL,
    doctor_id INT NOT NULL,
    prescription_date DATE,
    medicines JSON,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (consultation_id) REFERENCES consultations(id)
);

CREATE TABLE IF NOT EXISTS reminders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    medicine_name VARCHAR(100),
    dosage VARCHAR(50),
    reminder_time TIME,
    frequency VARCHAR(50),
    start_date DATE,
    end_date DATE,
    status ENUM('pending','taken','missed') DEFAULT 'pending',
    FOREIGN KEY (patient_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS medicines (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    category VARCHAR(50),
    price DECIMAL(10,2),
    description TEXT,
    side_effects TEXT,
    dosage_instructions TEXT,
    warnings TEXT
);

-- Insert 5 sample doctors (Password for all: password)
INSERT INTO users (name, email, password, role, phone) VALUES
(' Rahman Islam', 'doctor1@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'doctor', '01711111111'),
(' Sarah Ahmed', 'doctor2@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'doctor', '01722222222'),
(' Fatima Begum', 'doctor3@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'doctor', '01733333333'),
(' Ayesha Khan', 'doctor4@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'doctor', '01744444444'),
(' Rahim Uddin', 'doctor5@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'doctor', '01755555555');

-- Insert 5 sample patients (Password for all: password)
INSERT INTO users (name, email, password, role, phone) VALUES
('Umme Aymon', 'patient1@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'patient', '01811111111'),
('Esha', 'patient2@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'patient', '01822222222'),
('Farida Banu', 'patient3@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'patient', '01833333333'),
('Fahim Uddin', 'patient4@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'patient', '01844444444'),
('Amina Akter', 'patient5@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'patient', '01855555555');

-- Insert 20 sample medicines for easy prescribing
INSERT INTO medicines (name, category, price, description, dosage_instructions, side_effects, warnings) VALUES
('Paracetamol 500mg', 'Analgesic', 5.00, 'Fever and mild to moderate pain relief.', '1-2 tablets every 4-6 hours', 'Rare liver damage', 'Do not exceed 4g/day. Avoid with alcohol.'),
('Amoxicillin 500mg', 'Antibiotic', 12.50, 'Treats a wide variety of bacterial infections.', '1 capsule 3 times daily', 'Nausea, diarrhea, rash', 'Contraindicated in penicillin allergy.'),
('Ibuprofen 400mg', 'NSAID', 8.00, 'Reduces inflammation, pain, and fever.', '1 tablet 3 times daily with food', 'Stomach upset, heartburn', 'Avoid in severe kidney disease or ulcers.'),
('Omeprazole 20mg', 'Proton Pump Inhibitor', 15.00, 'Reduces stomach acid production.', '1 capsule daily before breakfast', 'Headache, abdominal pain', 'Long-term use may affect bone density.'),
('Cetirizine 10mg', 'Antihistamine', 6.00, 'Relieves allergy symptoms like runny nose and itching.', '1 tablet daily', 'Drowsiness, dry mouth', 'Use caution when driving.'),
('Metformin 500mg', 'Antidiabetic', 10.00, 'Controls high blood sugar in type 2 diabetes.', '1 tablet twice daily with meals', 'Nausea, metallic taste', 'Monitor kidney function regularly.'),
('Amlodipine 5mg', 'Antihypertensive', 18.00, 'Lowers high blood pressure.', '1 tablet daily', 'Swelling of ankles, dizziness', 'Do not stop abruptly.'),
('Azithromycin 500mg', 'Antibiotic', 25.00, 'Treats respiratory and skin bacterial infections.', '1 tablet daily for 3 days', 'Diarrhea, stomach pain', 'May cause QT prolongation.'),
('Salbutamol 100mcg Inhaler', 'Bronchodilator', 45.00, 'Relieves asthma and COPD symptoms.', '1-2 puffs as needed', 'Tremor, fast heartbeat', 'Rinse mouth after use.'),
('Losartan 50mg', 'Antihypertensive', 20.00, 'Treats high blood pressure and protects kidneys.', '1 tablet daily', 'Dizziness, high potassium', 'Avoid during pregnancy.'),
('Pantoprazole 40mg', 'Proton Pump Inhibitor', 16.00, 'Treats GERD and stomach ulcers.', '1 tablet daily before meals', 'Headache, flatulence', 'Interacts with some antivirals.'),
('Diclofenac 50mg', 'NSAID', 9.00, 'Relieves pain and inflammation in arthritis.', '1 tablet 2-3 times daily after food', 'Stomach pain, indigestion', 'Avoid in heart failure patients.'),
('Loratadine 10mg', 'Antihistamine', 7.00, 'Non-drowsy relief for hay fever and allergies.', '1 tablet daily', 'Dry mouth, fatigue', 'Generally well tolerated.'),
('Ciprofloxacin 500mg', 'Antibiotic', 22.00, 'Treats urinary tract and severe bacterial infections.', '1 tablet twice daily', 'Nausea, tendon pain', 'Avoid in children and pregnant women.'),
('Atorvastatin 20mg', 'Statin', 30.00, 'Lowers cholesterol and reduces heart disease risk.', '1 tablet daily at night', 'Muscle pain, liver enzyme changes', 'Report unexplained muscle weakness.'),
('Montelukast 10mg', 'Leukotriene Receptor Antagonist', 35.00, 'Prevents asthma attacks and allergy symptoms.', '1 tablet daily in the evening', 'Headache, mood changes', 'Monitor for behavioral changes.'),
('Furosemide 40mg', 'Diuretic', 8.50, 'Reduces fluid retention and swelling.', '1 tablet daily in the morning', 'Frequent urination, dehydration', 'Monitor potassium levels.'),
('Ranitidine 150mg', 'H2 Blocker', 12.00, 'Reduces stomach acid for heart relief.', '1 tablet twice daily', 'Constipation, headache', 'Check local regulations on availability.'),
('Vitamin C 500mg', 'Supplement', 5.50, 'Supports immune system and tissue repair.', '1 tablet daily', 'Mild stomach upset', 'High doses may cause kidney stones.'),
('Vitamin D3 1000 IU', 'Supplement', 11.00, 'Supports bone health and calcium absorption.', '1 capsule daily with food', 'Rarely, high calcium levels', 'Monitor if taking other calcium supplements.');
