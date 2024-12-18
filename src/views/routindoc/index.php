<?php
$pretitle = "ទំព័ដើម";
$title = "របាយការណ៍ប្រចាំថ្ងៃ";
$customButton = '
    <div class="d-flex">
        <a href="#" data-bs-toggle="modal" data-bs-target="#report"
            class="btn btn-primary d-none d-sm-inline-block">
            <!-- Download SVG icon from http://tabler-icons.io/i/plus -->
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="icon icon-tabler icons-tabler-outline icon-tabler-plus">
                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                <path d="M12 5l0 14" />
                <path d="M5 12l14 0" />
            </svg>
            <span>បង្កើត' . $title . '</span>
        </a>
        <a href="#" data-bs-toggle="modal" data-bs-target="#report" class="btn btn-primary d-sm-none btn-icon" data-bs-toggle="modal"
            data-bs-target="#apply-late-out" aria-expanded="false">
            <!-- Download SVG icon from http://tabler-icons.io/i/plus -->
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
        </a>
    </div>
';
require_once 'src/common/header.php';
?>

<?php
// Get the current month and year
$currentMonth = date('F'); // Current month in full format (e.g., "December")
$currentYear = date('Y');  // Current year (e.g., "2024")
?>

<div class="card mb-3">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="text-primary mb-0"><?= $title ?></h3>
        <input type="text" class="form-control w-25" id="reportSearch" placeholder="Search reports..."
            oninput="filterReports()">
    </div>

    <div class="card-body">
        <?php
        // Group reports by year, month, and week (1st, 2nd, 3rd, 4th)
        $groupedReports = [];
        if (!empty($reports) && is_array($reports)) {
            foreach ($reports as $report) {
                $timestamp = strtotime($report['date']);
                $year = date('Y', $timestamp);
                $month = date('F', $timestamp); // Full month name

                // Calculate the week number within the month (1st, 2nd, 3rd, 4th week)
                $dayOfMonth = date('j', $timestamp);
                $weekOfMonth = ceil($dayOfMonth / 7); // Week number (1, 2, 3, or 4)

                // Group by year, month, and week of the month
                $groupedReports[$year][$month][$weekOfMonth][] = $report;
            }
        }
        ?>

        <?php if (!empty($groupedReports)): ?>
            <?php foreach ($groupedReports as $year => $months): ?>
                <div class="mb-0">
                    <h3 class="text-primary h1 fw-bolder" style="font-family: --apple-system--"><?= $year ?></h3>

                    <?php foreach ($months as $month => $weeks): ?>
                        <div class="accordion" id="accordion<?= $year . $month ?>">
                            <div class="accordion-item mb-3">
                                <h2 class="accordion-header" id="heading<?= $year . $month ?>">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapse<?= $year . $month ?>" aria-expanded="false"
                                        aria-controls="collapse<?= $year . $month ?>"
                                        <?= ($month === $currentMonth && $year == $currentYear) ? 'aria-expanded="true"' : '' ?>
                                        >
                                        <strong><?= $month ?></strong>
                                    </button>
                                </h2>
                                <div id="collapse<?= $year . $month ?>" class="accordion-collapse collapse"
                                    aria-labelledby="heading<?= $year . $month ?>" data-bs-parent="#accordion<?= $year . $month ?>"
                                    <?= ($month === $currentMonth && $year == $currentYear) ? 'class="accordion-collapse collapse show"' : '' ?>>
                                    <div class="accordion-body">
                                        <?php foreach ($weeks as $week => $reports): ?>
                                            <div class="card mt-3">
                                                <h5 class="card-header bg-primary text-white">
                                                    សប្តាហ៍ទី <?= $week ?>
                                                </h5>
                                                <div class="table-responsive">
                                                    <table class="table table-striped table-hover">
                                                        <thead>
                                                            <tr>
                                                                <th>Date</th>
                                                                <th>Description</th>
                                                                <th>Document</th>
                                                                <th>Note</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                        <?php foreach ($reports as $report): ?>
                                                            <tr class="report-row">
                                                                <td>
                                                                    <?php
                                                                    // Ensure the created_at value is set and properly formatted
                                                                    if (!empty($report['created_at'])) {
                                                                        $createdAt = $report['created_at'];
                                                                        echo date('d-M-Y h:i:s A', strtotime($createdAt));
                                                                    } else {
                                                                        echo 'N/A'; // Fallback if created_at is not set
                                                                    }
                                                                    ?>
                                                                </td>
                                                                <td>
                                                                    <?= htmlspecialchars($report['description'] ?? 'No description available', ENT_QUOTES, 'UTF-8') ?>
                                                                </td>
                                                                <td>
                                                                    <?php if (!empty($report['document'])): ?>
                                                                        <a href="<?= htmlspecialchars($report['document'], ENT_QUOTES, 'UTF-8') ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                                                            View
                                                                        </a>
                                                                    <?php else: ?>
                                                                        <span class="text-muted">No document</span>
                                                                    <?php endif; ?>
                                                                </td>
                                                                <td>
                                                                    <?= htmlspecialchars($report['note'] ?? 'No notes available', ENT_QUOTES, 'UTF-8') ?>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No reports found or invalid data.</p>
        <?php endif; ?>
    </div>
</div>

<script>
    // Ensure the current month accordion is open on page load
    document.addEventListener('DOMContentLoaded', function() {
        const currentMonthAccordion = document.querySelector('.accordion-button[aria-expanded="true"]');
        if (currentMonthAccordion) {
            const collapseId = currentMonthAccordion.getAttribute('data-bs-target').substring(1);
            const currentCollapse = document.getElementById(collapseId);
            const currentCollapseButton = currentCollapse.previousElementSibling.querySelector('.accordion-button');
            currentCollapse.classList.add('show');
            currentCollapseButton.setAttribute('aria-expanded', 'true');
        }
    });

    // Filter reports by year, month, or week based on input
    function filterReports() {
        const query = document.getElementById('reportSearch').value.toLowerCase();
        const rows = document.querySelectorAll('.report-row');

        rows.forEach(row => {
            const date = row.querySelector('td:nth-child(1)').textContent.toLowerCase();
            const description = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
            const document = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
            const note = row.querySelector('td:nth-child(4)').textContent.toLowerCase();

            if (date.includes(query) || description.includes(query) || document.includes(query) || note.includes(query)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }
</script>


<?php
// Include the footer file
require_once 'src/common/footer.php';
?>