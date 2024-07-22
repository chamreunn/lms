<?php include('src/common/header.php') ?>
<div class="card">
    <h2>Register</h2>
    <form method="POST" action="elms/register">
        <div class="mb-3">
            <label for="email" class="form-label">Email address</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" class="form-control" id="username" name="username" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <div class="mb-3">
            <label for="role" class="form-label">Role</label>
            <select class="form-select tomselect" id="role" name="role" required>
                <option value="employee">Employee</option>
                <option value="office_manager">Office Manager</option>
                <option value="department_manager">Department Manager</option>
                <option value="unit_manager">Unit Manager</option>
                <option value="admin">Admin</option>
                <option value="intern">Intern</option>
                <option value="contractor">Contractor</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="office" class="form-label">Office</label>
            <select class="form-select" id="office" name="office" required>
                <option value="head_office">Head Office</option>
                <option value="branch_office_1">Branch Office 1</option>
                <option value="branch_office_2">Branch Office 2</option>
                <option value="regional_office">Regional Office</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="department" class="form-label">Department</label>
            <select class="form-select" id="department" name="department" required>
                <option value="hr">Human Resources</option>
                <option value="it">IT</option>
                <option value="finance">Finance</option>
                <option value="marketing">Marketing</option>
                <option value="sales">Sales</option>
                <option value="logistics">Logistics</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Register</button>
    </form>
</div>

<?php include('src/common/footer.php') ?>