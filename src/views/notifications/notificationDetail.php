<?php
// Initialize session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: /elms/login");
    exit();
}

ob_start();
$title = "គណនីរបស់ខ្ញុំ";
include('src/common/header.php');

function convertToKhmerDate($date)
{
    $days = ['Sun' => 'អាទិត្យ', 'Mon' => 'ចន្ទ', 'Tue' => 'អង្គារ', 'Wed' => 'ពុធ', 'Thu' => 'ព្រហស្បតិ៍', 'Fri' => 'សុក្រ', 'Sat' => 'សៅរ៍'];
    $months = ['January' => 'មករា', 'February' => 'កុម្ភៈ', 'March' => 'មិនា', 'April' => 'មេសា', 'May' => 'ឧសភា', 'June' => 'មិថុនា', 'July' => 'កក្កដា', 'August' => 'សីហា', 'September' => 'កញ្ញា', 'October' => 'តុលា', 'November' => 'វិច្ឆិកា', 'December' => 'ធ្នូ'];

    $dayOfWeek = $days[date('D', strtotime($date))];
    $day = convertToKhmerNumber(date('j', strtotime($date)));
    $month = $months[date('F', strtotime($date))];
    $year = convertToKhmerNumber(date('Y', strtotime($date)));

    return $dayOfWeek . ' ' . $day . ' ' . $month . ', ' . $year;
}

function convertToKhmerNumber($number)
{
    $khmerNumbers = ['0' => '០', '1' => '១', '2' => '២', '3' => '៣', '4' => '៤', '5' => '៥', '6' => '៦', '7' => '៧', '8' => '៨', '9' => '៩'];
    return strtr($number, $khmerNumbers);
}

?>

<div class="card-header mb-3">
    <h2 class="mb-0">សារជូនដំណឹង</h2>
</div>
<div class="card-body">
    <?php if (empty($notifications)) : ?>
        <div class="d-flex flex-column justify-content-center align-items-center" style="min-height: 150px;">
            <img src="public/img/icons/svgs/empty.svg" alt="No data" class="mb-0" style="max-width: 350px;">
            <span class="text-muted mb-3">មិនមានសារជូនដំណឹង</span>
        </div>
    <?php else : ?>
        <ul class="list-group">
            <?php
            // Sort notifications by date
            usort($notifications, function ($a, $b) {
                return strtotime($b['created_at']) - strtotime($a['created_at']);
            });

            // Group notifications by date
            $currentDate = null;
            foreach ($notifications as $notification) :
                $notificationDate = date('Y-m-d', strtotime($notification['created_at']));
                if ($notificationDate !== $currentDate) {
                    $currentDate = $notificationDate;
            ?>
                    <small class="mb-2"><?= convertToKhmerDate($currentDate) ?></small>
                <?php } ?>
                <div class="card mb-3">
                    <div class="list-group list-group-flush list-group-hoverable <?= $notification['status'] == 'unread' ? 'bg-grey' : 'bg-light' ?>">
                        <div class="list-group-item">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="status-dot status-dot-animated <?= $notification['status'] == 'unread' ? 'bg-red' : 'bg-green' ?> d-block"></span>
                                </div>
                                <div class="col-auto">
                                    <img class="avatar" src="<?= $notification['profile_picture'] ?>" alt="" style="object-fit: cover;">
                                </div>
                                <div class="col text-truncate">
                                    <a href="#" class="text-body d-block h4"><?= $notification['khmer_name'] ?></a>
                                    <div class="d-block text-secondary text-truncate mt-n1">
                                        <?= $notification['message'] ?>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <a href="notificationDetail.php?id=<?= $notification['id'] ?>" class="list-group-item-actions">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-chevron-right">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M9 6l6 6l-6 6" />
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>


<?php include('src/common/footer.php'); ?>