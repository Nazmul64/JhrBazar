<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Seller Registration – Jhr Bazar</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Sora:wght@600;700;800&display=swap" rel="stylesheet"/>

    <style>
        :root {
            --primary: #10b981; /* Green from Login page */
            --primary-hover: #059669;
            --step-inactive: #e2e8f0;
            --step-active: #10b981;
            --step-current: #10b981;
            --text-main: #1e293b;
            --text-muted: #64748b;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f8fafc;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }

        .registration-wrapper {
            width: 100%;
            max-width: 900px;
            background: #fff;
            border-radius: 30px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            border: 1px solid rgba(0,0,0,0.05);
        }

        .reg-header {
            padding: 40px 40px 20px;
            text-align: center;
        }

        .reg-header h2 {
            font-family: 'Sora', sans-serif;
            font-weight: 800;
            color: #1e293b;
            font-size: 32px;
            margin-bottom: 10px;
        }

        .reg-header p {
            color: #64748b;
            font-size: 16px;
        }

        /* ── Steps Progress ── */
        .steps-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 80px 40px;
            position: relative;
        }

        .steps-line {
            position: absolute;
            top: 40px;
            left: 100px;
            right: 100px;
            height: 2px;
            background: var(--step-inactive);
            z-index: 1;
        }

        .step-item {
            position: relative;
            z-index: 2;
            display: flex;
            flex-direction: column;
            align-items: center;
            flex: 1;
        }

        .step-circle {
            width: 40px;
            height: 40px;
            background: #fff;
            border: 2px solid var(--step-inactive);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: var(--text-muted);
            margin-bottom: 10px;
            transition: all 0.3s;
        }

        .step-label {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-muted);
        }

        .step-item.active .step-circle {
            background: var(--step-current);
            border-color: var(--step-current);
            color: #fff;
        }

        .step-item.active .step-label {
            color: var(--step-current);
        }

        .step-item.completed .step-circle {
            background: var(--step-active);
            border-color: var(--step-active);
            color: #fff;
        }

        .step-item.completed .step-label {
            color: var(--step-active);
        }

        /* ── Form Styling ── */
        .reg-body {
            padding: 0 80px 40px;
        }

        .form-step {
            display: none;
            animation: fadeIn 0.5s ease;
        }

        .form-step.active {
            display: block;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .form-label {
            font-size: 14px;
            font-weight: 600;
            color: #475569;
            margin-bottom: 8px;
        }

        .form-label span {
            color: #ef4444;
        }

        .form-control, .form-select {
            padding: 14px 20px;
            border-radius: 12px;
            border: 1.5px solid #e2e8f0;
            font-size: 15px;
            background: #fff;
            transition: all 0.3s;
        }

        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1);
        }

        /* ── Navigation Buttons ── */
        .reg-footer {
            display: flex;
            gap: 20px;
            margin-top: 30px;
        }

        .btn-next, .btn-submit {
            flex: 1;
            padding: 16px;
            background: var(--primary);
            border: none;
            border-radius: 15px;
            color: #fff;
            font-weight: 700;
            font-size: 16px;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-next:hover, .btn-submit:hover {
            background: var(--primary-hover);
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(16, 185, 129, 0.3);
        }

        .btn-prev {
            flex: 1;
            padding: 16px;
            background: #eef2f7;
            border: none;
            border-radius: 15px;
            color: #475569;
            font-weight: 700;
            transition: all 0.3s;
        }

        .btn-prev:hover {
            background: #e2e8f0;
        }

        /* ── Upload Box ── */
        .upload-box {
            border: 2px dashed #e2e8f0;
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            position: relative;
        }

        .upload-box:hover {
            border-color: var(--primary);
            background: rgba(16, 185, 129, 0.02);
        }

        .upload-box i {
            font-size: 30px;
            color: #94a3b8;
            display: block;
            margin-bottom: 10px;
        }

        /* ── Category Select ── */
        .category-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }

        .cat-item {
            border: 1.5px solid #e2e8f0;
            padding: 15px;
            border-radius: 12px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
        }

        .cat-item i {
            display: block;
            font-size: 20px;
            margin-bottom: 5px;
        }

        .cat-item.selected {
            border-color: var(--primary);
            background: rgba(16, 185, 129, 0.05);
            color: var(--primary);
        }
    </style>
</head>
<body>

<div class="registration-wrapper">
    <div class="reg-header">
        <h2>Seller Registration</h2>
        <p>Fill in the details below to create your seller account</p>
    </div>

    <!-- ── Steps Progress ── -->
    <div class="steps-container">
        <div class="steps-line"></div>
        <div class="step-item active" id="step-head-1">
            <div class="step-circle">1</div>
            <div class="step-label">Basic Info</div>
        </div>
        <div class="step-item" id="step-head-2">
            <div class="step-circle">2</div>
            <div class="step-label">Business</div>
        </div>
        <div class="step-item" id="step-head-3">
            <div class="step-circle">3</div>
            <div class="step-label">Store</div>
        </div>
        <div class="step-item" id="step-head-4">
            <div class="step-circle">4</div>
            <div class="step-label">Documents</div>
        </div>
    </div>

    <div class="reg-body">
        @if($errors->any())
            <div class="alert alert-danger border-0 shadow-sm mb-4" style="border-radius: 15px;">
                <ul class="mb-0 small fw-bold">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('register.seller.submit') }}" method="POST" id="reg-form" enctype="multipart/form-data">
            @csrf

            <!-- ── STEP 1: Basic Info ── -->
            <div class="form-step active" id="step-1">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">First Name <span>*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="First Name" required value="{{ old('name') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Last Name <span>*</span></label>
                        <input type="text" name="last_name" class="form-control" placeholder="Last Name" required value="{{ old('last_name') }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Email <span>*</span></label>
                        <input type="email" name="email" class="form-control" placeholder="Email Address" required value="{{ old('email') }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Phone <span>*</span></label>
                        <input type="text" name="phone" class="form-control" placeholder="Phone Number" required value="{{ old('phone') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Password <span>*</span></label>
                        <input type="password" name="password" class="form-control" placeholder="Password" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Confirm Password <span>*</span></label>
                        <input type="password" name="password_confirmation" class="form-control" placeholder="Confirm Password" required>
                    </div>
                </div>
                <div class="reg-footer">
                    <button type="button" class="btn-next" onclick="nextStep(2)">Next <i class="bi bi-arrow-right"></i></button>
                </div>
            </div>

            <!-- ── STEP 2: Business Info ── -->
            <div class="form-step" id="step-2">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">Business Type <span>*</span></label>
                        <select name="business_type" class="form-select">
                            <option value="">Select type</option>
                            <option value="individual" {{ old('business_type') == 'individual' ? 'selected' : '' }}>Individual</option>
                            <option value="registered" {{ old('business_type') == 'registered' ? 'selected' : '' }}>Registered Company</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Business Name <span>*</span></label>
                        <input type="text" name="business_name" class="form-control" placeholder="Business Name" value="{{ old('business_name') }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Business Address <span>*</span></label>
                        <textarea name="business_address" class="form-control" rows="3" placeholder="Business Address">{{ old('business_address') }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">City <span>*</span></label>
                        <input type="text" name="city" class="form-control" placeholder="City" value="{{ old('city') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Postal Code <span>*</span></label>
                        <input type="text" name="postal_code" class="form-control" placeholder="Postal Code" value="{{ old('postal_code') }}">
                    </div>
                </div>
                <div class="reg-footer">
                    <button type="button" class="btn-prev" onclick="prevStep(1)">Previous</button>
                    <button type="button" class="btn-next" onclick="nextStep(3)">Next <i class="bi bi-arrow-right"></i></button>
                </div>
            </div>

            <!-- ── STEP 3: Store Info ── -->
            <div class="form-step" id="step-3">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">Store Name <span>*</span></label>
                        <input type="text" name="store_name" class="form-control" placeholder="Store Name" value="{{ old('store_name') }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Store URL <span>*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">marketplace.com/store/</span>
                            <input type="text" name="store_url" class="form-control border-start-0" placeholder="store-slug" value="{{ old('store_url') }}">
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Store Description <span>*</span></label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Tell us about your store">{{ old('description') }}</textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Categories <span>*</span></label>
                        <div class="category-grid">
                            <div class="cat-item" onclick="toggleCat(this, 'Electronics')"><i class="bi bi-laptop"></i> Electronics</div>
                            <div class="cat-item" onclick="toggleCat(this, 'Fashion')"><i class="bi bi-bag"></i> Fashion</div>
                            <div class="cat-item" onclick="toggleCat(this, 'Home')"><i class="bi bi-house"></i> Home</div>
                            <div class="cat-item" onclick="toggleCat(this, 'Sports')"><i class="bi bi-trophy"></i> Sports</div>
                        </div>
                        <input type="hidden" name="categories" id="selected_cats">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Store Logo</label>
                        <div class="upload-box" onclick="document.getElementById('logo_input').click()">
                            <img id="logo_preview" src="" class="img-fluid rounded mb-2 d-none" style="max-height: 100px;">
                            <div id="logo_placeholder">
                                <i class="bi bi-cloud-arrow-up"></i>
                                <span id="logo_filename">Choose file No file chosen</span>
                                <p class="small text-muted mt-1">Click to upload logo</p>
                            </div>
                            <input type="file" name="logo" id="logo_input" hidden onchange="previewImage(this, 'logo_preview', 'logo_placeholder', 'logo_filename')">
                        </div>
                    </div>
                </div>
                <div class="reg-footer">
                    <button type="button" class="btn-prev" onclick="prevStep(2)">Previous</button>
                    <button type="button" class="btn-next" onclick="nextStep(4)">Next <i class="bi bi-arrow-right"></i></button>
                </div>
            </div>

            <!-- ── STEP 4: Documents & Bank ── -->
            <div class="form-step" id="step-4">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">National ID <span>*</span></label>
                        <div class="upload-box" onclick="document.getElementById('nid_input').click()">
                            <img id="nid_preview" src="" class="img-fluid rounded mb-2 d-none" style="max-height: 120px;">
                            <div id="nid_placeholder">
                                <i class="bi bi-file-earmark-image"></i>
                                <span id="nid_filename">Choose file No file chosen</span>
                                <p class="small text-muted mt-1">Upload National ID</p>
                            </div>
                            <input type="file" name="national_id_card" id="nid_input" hidden onchange="previewImage(this, 'nid_preview', 'nid_placeholder', 'nid_filename')">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Bank Name <span>*</span></label>
                        <select name="bank_name" class="form-select">
                            <option value="">Select Bank</option>
                            @foreach($banks as $bank)
                                <option value="{{ $bank->name }}" {{ old('bank_name') == $bank->name ? 'selected' : '' }}>{{ $bank->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Branch <span>*</span></label>
                        <input type="text" name="bank_branch" class="form-control" placeholder="Branch Name" value="{{ old('bank_branch') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Account Number <span>*</span></label>
                        <input type="text" name="bank_account_number" class="form-control" placeholder="Account Number" value="{{ old('bank_account_number') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Account Holder <span>*</span></label>
                        <input type="text" name="bank_account_holder" class="form-control" placeholder="Account Holder Name" value="{{ old('bank_account_holder') }}">
                    </div>
                    <div class="col-12 mt-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="terms" required>
                            <label class="form-check-label small" for="terms">
                                I agree to <a href="#" class="text-primary text-decoration-none fw-bold">Terms & Conditions</a> *
                            </label>
                        </div>
                    </div>
                </div>
                <div class="reg-footer">
                    <button type="button" class="btn-prev" onclick="prevStep(3)">Previous</button>
                    <button type="submit" class="btn-submit">Submit <i class="bi bi-check-circle"></i></button>
                </div>
            </div>

        </form>
    </div>
</div>

<script>
    let currentStep = 1;
    let selectedCats = [];

    function nextStep(step) {
        // Validation could be added here
        document.getElementById('step-' + currentStep).classList.remove('active');
        document.getElementById('step-head-' + currentStep).classList.add('completed');
        document.getElementById('step-head-' + currentStep).classList.remove('active');
        
        currentStep = step;
        
        document.getElementById('step-' + currentStep).classList.add('active');
        document.getElementById('step-head-' + currentStep).classList.add('active');
    }

    function prevStep(step) {
        document.getElementById('step-' + currentStep).classList.remove('active');
        document.getElementById('step-head-' + currentStep).classList.remove('active');
        
        currentStep = step;
        
        document.getElementById('step-' + currentStep).classList.add('active');
        document.getElementById('step-head-' + currentStep).classList.remove('completed');
    }

    function toggleCat(el, cat) {
        el.classList.toggle('selected');
        if(selectedCats.includes(cat)) {
            selectedCats = selectedCats.filter(c => c !== cat);
        } else {
            selectedCats.push(cat);
        }
        document.getElementById('selected_cats').value = JSON.stringify(selectedCats);
    }

    function updateFilename(input, targetId) {
        const filename = input.files[0] ? input.files[0].name : 'No file chosen';
        document.getElementById(targetId).textContent = filename;
    }

    function previewImage(input, previewId, placeholderId, filenameId) {
        const file = input.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById(previewId);
                preview.src = e.target.result;
                preview.classList.remove('d-none');
                document.getElementById(placeholderId).classList.add('d-none');
                document.getElementById(filenameId).textContent = file.name;
            }
            reader.readAsDataURL(file);
        }
    }
</script>

</body>
</html>
