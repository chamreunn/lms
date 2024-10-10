<?php
$title = "ពិនិត្យលិខិតព្យួរ";
require_once 'src/common/header.php';

// Ensure the hold request data exists before accessing its keys
if (!empty($getHoldById)) {
    $startDate = $getHoldById['start_date'] ?? '';
    $endDate = $getHoldById['end_date'] ?? '';
    $reason = $getHoldById['reason'] ?? '';
    $attachment = $getHoldById['attachment'] ?? '';
} else {
    // Handle the case where no data was found for the given ID
    echo "<div class='alert alert-danger'>No data found for this request.</div>";
    exit();
}
?>

<!-- Page header -->
<div class="page-header d-print-none mt-0 mb-3">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col-auto">
                <!-- Page pre-title -->
                <div class="page-pretitle">
                    ទំព័រដើម
                </div>
                <h2 class="page-title">
                    <?php echo $title ?? ""; ?>
                </h2>
            </div>
        </div>
    </div>
</div>

<!-- Main container for displaying and editing hold request -->
<div class="container-xl">
    <div class="card">
        <div class="card-body">
            <h3 class="card-title">ព័ត៌មានលម្អិតលិខិតព្យួរ</h3>

            <!-- Edit form displaying current hold request data -->
            <form action="/elms/edit-hold" method="POST" enctype="multipart/form-data">
                <!-- Hidden input to store the request ID -->
                <input type="hidden" name="request_id" value="<?= $getHoldById['id'] ?? '' ?>">

                <div class="mb-3">
                    <label for="start_date" class="form-label">ថ្ងៃចាប់ផ្តើម</label>
                    <input type="text" class="form-control date-picker" id="start_date" name="start_date" value="<?= $startDate ?>" required>
                </div>

                <div class="mb-3">
                    <label for="end_date" class="form-label">ថ្ងៃបញ្ចប់</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" value="<?= $endDate ?>" required>
                </div>

                <div class="mb-3">
                    <label for="reason" class="form-label">មូលហេតុ</label>
                    <textarea class="form-control" id="reason" name="reason" rows="3" required><?= $reason ?></textarea>
                </div>

                <div class="mb-3">
                    <label for="attachment" class="form-label">ឯកសារភ្ជាប់ (ស្រេចចិត្ត)</label>
                    <input type="file" class="form-control" id="attachment" name="attachment" accept=".pdf,.docx">
                    <!-- Display current attachment if exists -->
                    <?php if (!empty($attachment)): ?>
                        <p class="mt-2">ឯកសារបច្ចុប្បន្ន៖ <a href="/public/uploads/hold-attachments/<?= $attachment ?>" target="_blank"><?= $attachment ?></a></p>
                    <?php endif; ?>
                </div>

                <button type="submit" class="btn btn-warning">ធ្វើការកែប្រែ</button>
            </form>
        </div>
    </div>
</div>

<?php include('src/common/footer.php'); ?>
