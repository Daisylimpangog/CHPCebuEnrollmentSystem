<?php
require_once '../config.php';
require_once '../helpers.php';

if (!isLoggedIn() || isAdmin()) {
    redirect('../login.php');
}

if (!isset($_SESSION['current_enrollment_id'])) {
    redirect('dashboard.php');
}

$error = '';
$success = '';
$enrollment_id = $_SESSION['current_enrollment_id'];

// Get enrollment details
$enrollment_result = $conn->query("SELECT e.*, c.name as course_name, c.id as course_id FROM enrollments e JOIN courses c ON e.course_id = c.id WHERE e.id = $enrollment_id AND e.user_id = {$_SESSION['user_id']}");

if ($enrollment_result->num_rows === 0) {
    redirect('dashboard.php');
}

$enrollment = $enrollment_result->fetch_assoc();

// Handle form submission
if ($_POST && isset($_POST['submit_application'])) {
    if (!verifyToken($_POST['csrf_token'] ?? '')) {
        $error = 'Security token invalid';
    } else {
        // Validate required fields
        $required_fields = ['first_name', 'family_name', 'email_address', 'contact_number', 'date_of_birth', 'sex', 'marital_status'];
        $missing = array_filter($required_fields, fn($f) => empty($_POST[$f] ?? ''));
        
        if ($missing) {
            $error = 'Please fill in all required fields';
        } else {
            // Prepare data
            $data = [
                'enrollment_id' => $enrollment_id,
                'first_name' => sanitize($_POST['first_name'] ?? ''),
                'middle_name' => sanitize($_POST['middle_name'] ?? ''),
                'family_name' => sanitize($_POST['family_name'] ?? ''),
                'nickname' => sanitize($_POST['nickname'] ?? ''),
                'age' => intval($_POST['age'] ?? 0),
                'date_of_birth' => $_POST['date_of_birth'] ?? null,
                'place_of_birth' => sanitize($_POST['place_of_birth'] ?? ''),
                'sex' => sanitize($_POST['sex'] ?? ''),
                'marital_status' => sanitize($_POST['marital_status'] ?? ''),
                'citizenship' => sanitize($_POST['citizenship'] ?? ''),
                'religion' => sanitize($_POST['religion'] ?? ''),
                'present_address' => sanitize($_POST['present_address'] ?? ''),
                'provincial_address' => sanitize($_POST['provincial_address'] ?? ''),
                'contact_number' => sanitize($_POST['contact_number'] ?? ''),
                'email_address' => sanitize($_POST['email_address'] ?? ''),
                'occupation' => sanitize($_POST['occupation'] ?? ''),
                'company' => sanitize($_POST['company'] ?? ''),
                'telephone' => sanitize($_POST['telephone'] ?? ''),
                'name_of_spouse' => sanitize($_POST['name_of_spouse'] ?? ''),
                'no_of_children' => intval($_POST['no_of_children'] ?? 0),
                'educational_attainment' => sanitize($_POST['educational_attainment'] ?? ''),
                'course_degree' => sanitize($_POST['course_degree'] ?? ''),
                'ncii_certificate' => sanitize($_POST['ncii_certificate'] ?? ''),
                'university_college_school' => sanitize($_POST['university_college_school'] ?? ''),
                'year_graduated' => intval($_POST['year_graduated'] ?? 0),
                'program_to_pursue' => sanitize($_POST['program_to_pursue'] ?? ''),
                'how_did_you_know' => sanitize($_POST['how_did_you_know'] ?? ''),
                'how_will_you_finance' => sanitize($_POST['how_will_you_finance'] ?? ''),
                'terms_of_payment' => sanitize($_POST['terms_of_payment'] ?? '')
            ];
            
            // Build SQL
            $columns = implode(', ', array_keys($data));
            $values = implode("', '", array_map(fn($v) => $v === null ? 'NULL' : $conn->real_escape_string($v), array_values($data)));
            $values = str_replace("'NULL'", 'NULL', $values);
            
            $sql = "INSERT INTO enrollment_applications ($columns) VALUES ('$values')";
            
            if ($conn->query($sql)) {
                $success = 'Application submitted successfully! Redirecting to payment...';
                unset($_SESSION['current_enrollment_id']);
                header("Refresh:2; url=payment.php?enrollment_id=$enrollment_id");
            } else {
                $error = 'Error submitting application: ' . $conn->error;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enrollment Application Form</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f0fdf4;
        }
        
        .navbar {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .form-header {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .form-header h2 {
            color: #333;
            margin-bottom: 10px;
        }
        
        .course-info {
            background: #e8eef7;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .course-info p {
            color: #555;
            font-size: 14px;
        }
        
        .alert {
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 5px;
            font-size: 14px;
        }
        
        .alert-error {
            background: #fee;
            color: #c33;
            border: 1px solid #fcc;
        }
        
        .alert-success {
            background: #efe;
            color: #3c3;
            border: 1px solid #cfc;
        }
        
        .form-container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .form-section {
            margin-bottom: 30px;
        }
        
        .form-section h3 {
            background: #667eea;
            color: white;
            padding: 12px 15px;
            margin: -30px -30px 20px -30px;
            border-radius: 8px 8px 0 0;
            font-size: 16px;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .form-row.full {
            grid-template-columns: 1fr;
        }
        
        .form-row.three {
            grid-template-columns: 1fr 1fr 1fr;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
        }
        
        label {
            margin-bottom: 6px;
            color: #333;
            font-weight: 500;
            font-size: 14px;
        }
        
        label .required {
            color: #d32f2f;
        }
        
        input[type="text"],
        input[type="email"],
        input[type="number"],
        input[type="date"],
        textarea,
        select {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            font-family: inherit;
        }
        
        textarea {
            resize: vertical;
            min-height: 80px;
        }
        
        input:focus,
        textarea:focus,
        select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 2px rgba(102, 126, 234, 0.1);
        }
        
        .checkbox-group {
            display: flex;
            gap: 15px;
            margin-top: 10px;
        }
        
        .checkbox-group label {
            margin-bottom: 0;
            display: flex;
            align-items: center;
            font-weight: normal;
        }
        
        .checkbox-group input[type="checkbox"],
        .checkbox-group input[type="radio"] {
            margin-right: 6px;
        }
        
        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 30px;
        }
        
        .btn {
            padding: 12px 30px;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-submit {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            flex: 1;
        }
        
        .btn-submit:hover {
            transform: translateY(-2px);
        }
        
        .btn-cancel {
            background: #ddd;
            color: #333;
        }
        
        .btn-cancel:hover {
            background: #ccc;
        }
        
        .instruction {
            background: #f9f9f9;
            padding: 15px;
            border-left: 4px solid #667eea;
            margin-bottom: 20px;
            font-size: 13px;
            color: #555;
            line-height: 1.6;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>Enrollment Application Form</h1>
        <div>
            <span><?php echo $_SESSION['full_name']; ?></span>
            <a href="../logout.php" style="color: white; text-decoration: none; margin-left: 20px;">Logout</a>
        </div>
    </div>
    
    <div class="container">
        <div class="form-header">
            <h2>Application Form</h2>
            <p>Please fill in all required fields marked with an asterisk (*)</p>
        </div>
        
        <div class="course-info">
            <p><strong>Course:</strong> <?php echo $enrollment['course_name']; ?></p>
            <p><strong>Amount to Pay:</strong> ₱<?php echo number_format($enrollment['total_amount'], 2); ?></p>
        </div>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <div class="form-container">
            <div class="instruction">
                <strong>INSTRUCTION:</strong> Please do NOT leave blanks. Write N/A when not applicable.
            </div>
            
            <form method="POST">
                <!-- Personal Record Section -->
                <div class="form-section">
                    <h3>A. Personal Record</h3>
                    
                    <div class="form-row three">
                        <div class="form-group">
                            <label for="first_name">First Name <span class="required">*</span></label>
                            <input type="text" id="first_name" name="first_name" required>
                        </div>
                        <div class="form-group">
                            <label for="middle_name">Middle Name</label>
                            <input type="text" id="middle_name" name="middle_name">
                        </div>
                        <div class="form-group">
                            <label for="family_name">Family Name <span class="required">*</span></label>
                            <input type="text" id="family_name" name="family_name" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="nickname">Nickname</label>
                            <input type="text" id="nickname" name="nickname">
                        </div>
                        <div class="form-group">
                            <label for="age">Age</label>
                            <input type="number" id="age" name="age" min="0">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="date_of_birth">Date of Birth <span class="required">*</span></label>
                            <input type="date" id="date_of_birth" name="date_of_birth" required>
                        </div>
                        <div class="form-group">
                            <label for="place_of_birth">Place of Birth</label>
                            <input type="text" id="place_of_birth" name="place_of_birth">
                        </div>
                    </div>
                    
                    <div class="form-row full">
                        <div class="form-group">
                            <label>Sex <span class="required">*</span></label>
                            <div class="checkbox-group">
                                <label>
                                    <input type="radio" name="sex" value="Male" required> Male
                                </label>
                                <label>
                                    <input type="radio" name="sex" value="Female" required> Female
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Marital Status <span class="required">*</span></label>
                            <div class="checkbox-group">
                                <label>
                                    <input type="radio" name="marital_status" value="Single" required> Single
                                </label>
                                <label>
                                    <input type="radio" name="marital_status" value="Married" required> Married
                                </label>
                                <label>
                                    <input type="radio" name="marital_status" value="Widowed" required> Widowed
                                </label>
                                <label>
                                    <input type="radio" name="marital_status" value="Separated" required> Separated
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="citizenship">Citizenship</label>
                            <input type="text" id="citizenship" name="citizenship">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="religion">Religion</label>
                            <input type="text" id="religion" name="religion">
                        </div>
                    </div>
                    
                    <div class="form-row full">
                        <div class="form-group">
                            <label for="present_address">Present Address</label>
                            <textarea id="present_address" name="present_address"></textarea>
                        </div>
                    </div>
                    
                    <div class="form-row full">
                        <div class="form-group">
                            <label for="provincial_address">Provincial Address</label>
                            <textarea id="provincial_address" name="provincial_address"></textarea>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="contact_number">Contact Detail/Mobile Number <span class="required">*</span></label>
                            <input type="text" id="contact_number" name="contact_number" required>
                        </div>
                        <div class="form-group">
                            <label for="email_address">Email Address <span class="required">*</span></label>
                            <input type="email" id="email_address" name="email_address" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="occupation">Occupation</label>
                            <input type="text" id="occupation" name="occupation">
                        </div>
                        <div class="form-group">
                            <label for="company">Company</label>
                            <input type="text" id="company" name="company">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="telephone">Telephone</label>
                            <input type="text" id="telephone" name="telephone">
                        </div>
                        <div class="form-group">
                            <label for="no_of_children">No. of Children</label>
                            <input type="number" id="no_of_children" name="no_of_children" min="0">
                        </div>
                    </div>
                    
                    <div class="form-row full">
                        <div class="form-group">
                            <label for="name_of_spouse">Name of Spouse, if applicable</label>
                            <input type="text" id="name_of_spouse" name="name_of_spouse">
                        </div>
                    </div>
                </div>
                
                <!-- Academic Record Section -->
                <div class="form-section">
                    <h3>B. Academic Record</h3>
                    
                    <div class="form-row full">
                        <div class="form-group">
                            <label>Educational Attainment</label>
                            <div class="checkbox-group">
                                <label>
                                    <input type="checkbox" name="educational_attainment" value="College Graduate"> College Graduate
                                </label>
                                <label>
                                    <input type="checkbox" name="educational_attainment" value="Vocational Graduate"> Vocational Graduate
                                </label>
                                <label>
                                    <input type="checkbox" name="educational_attainment" value="Undergraduate"> Undergraduate
                                </label>
                                <label>
                                    <input type="checkbox" name="educational_attainment" value="High School Graduate"> High School Graduate
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="course_degree">Course/Degree</label>
                            <input type="text" id="course_degree" name="course_degree">
                        </div>
                        <div class="form-group">
                            <label for="ncii_certificate">NCII Certificate/s</label>
                            <input type="text" id="ncii_certificate" name="ncii_certificate">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="university_college_school">University/College/School</label>
                            <input type="text" id="university_college_school" name="university_college_school">
                        </div>
                        <div class="form-group">
                            <label for="year_graduated">Year Graduated</label>
                            <input type="number" id="year_graduated" name="year_graduated" min="1950" max="2100">
                        </div>
                    </div>
                </div>
                
                <!-- Additional Information Section -->
                <div class="form-section">
                    <h3>C. Additional Information</h3>
                    
                    <div class="form-row full">
                        <div class="form-group">
                            <label for="program_to_pursue">What program/s do you want to pursue?</label>
                            <textarea id="program_to_pursue" name="program_to_pursue"></textarea>
                        </div>
                    </div>
                    
                    <div class="form-row full">
                        <div class="form-group">
                            <label>How did you come to know about CHP/Cebu?</label>
                            <div class="checkbox-group">
                                <label>
                                    <input type="checkbox" name="how_did_you_know" value="Streaming/Signage"> Streaming/Signage
                                </label>
                                <label>
                                    <input type="checkbox" name="how_did_you_know" value="Friends/Relatives"> Friends/Relatives
                                </label>
                                <label>
                                    <input type="checkbox" name="how_did_you_know" value="Leaflets/Pamphlets"> Leaflets/Pamphlets
                                </label>
                                <label>
                                    <input type="checkbox" name="how_did_you_know" value="Website"> Website
                                </label>
                                <label>
                                    <input type="checkbox" name="how_did_you_know" value="Social Media"> Social Media
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-row full">
                        <div class="form-group">
                            <label>How will you finance your studies?</label>
                            <div class="checkbox-group">
                                <label>
                                    <input type="checkbox" name="how_will_you_finance" value="Personal"> Personal
                                </label>
                                <label>
                                    <input type="checkbox" name="how_will_you_finance" value="Parents"> Parents
                                </label>
                                <label>
                                    <input type="checkbox" name="how_will_you_finance" value="Relatives"> Relatives
                                </label>
                                <label>
                                    <input type="checkbox" name="how_will_you_finance" value="Employer"> Employer
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="terms_of_payment">Terms of Payment</label>
                            <select id="terms_of_payment" name="terms_of_payment">
                                <option value="">Select...</option>
                                <option value="Outright Cash">Outright Cash</option>
                                <option value="Installment">Installment</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <input type="hidden" name="csrf_token" value="<?php echo generateToken(); ?>">
                <input type="hidden" name="submit_application" value="1">
                
                <div class="button-group">
                    <a href="dashboard.php" class="btn btn-cancel">Cancel</a>
                    <button type="submit" class="btn btn-submit">Proceed to Payment</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
