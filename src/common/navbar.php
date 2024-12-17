<?php
// Extract the current page path without query parameters
$current_page = basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
?>

<header class="navbar navbar-expand-md navbar-light d-print-none">
    <div class="container-xl">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu"
            aria-controls="navbar-menu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <h1 class="navbar-brand d-none-navbar-horizontal pe-0 pe-md-3">
            <a href=".">
                <img src="public/img/icons/brands/logo2.png" width="310" height="32" alt="អង្គភាពសវនកម្មផ្ទៃក្នុង"
                    class="navbar-brand-image">
            </a>
        </h1>
        <div class="navbar-nav flex-row order-md-last">
            <div class="d-flex me-3">
                <!-- qrcode  -->
                <a href="/elms/qrcode"
                    class="nav-link <?= ($current_page == 'qrcode') ? 'bg-primary-lt fw-bold' : '' ?> p-2"
                    title="QR Code សម្រាប់ស្កេនវត្តមាន" data-bs-toggle="tooltip" data-bs-placement="bottom">
                    <!-- Download SVG icon from http://tabler-icons.io/i/moon -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="icon icon-tabler icons-tabler-outline icon-tabler-qrcode m-0">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M4 4m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" />
                        <path d="M7 17l0 .01" />
                        <path d="M14 4m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" />
                        <path d="M7 7l0 .01" />
                        <path d="M4 14m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" />
                        <path d="M17 7l0 .01" />
                        <path d="M14 14l3 0" />
                        <path d="M20 14l0 .01" />
                        <path d="M14 14l0 3" />
                        <path d="M14 20l3 0" />
                        <path d="M17 17l3 0" />
                        <path d="M20 17l0 3" />
                    </svg>
                    <span class="mx-1">ស្កេនវត្តមាន</span>
                </a>
                <a href="?theme=dark" class="nav-link px-0 hide-theme-dark" title="Enable dark mode"
                    data-bs-toggle="tooltip" data-bs-placement="bottom">
                    <!-- Download SVG icon from http://tabler-icons.io/i/moon -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                        stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M12 3c.132 0 .263 0 .393 0a7.5 7.5 0 0 0 7.92 12.446a9 9 0 1 1 -8.313 -12.454z" />
                    </svg>
                </a>
                <a href="?theme=light" class="nav-link px-0 hide-theme-light" title="Enable light mode"
                    data-bs-toggle="tooltip" data-bs-placement="bottom">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                        stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <circle cx="12" cy="12" r="4" />
                        <path
                            d="M3 12h1m8 -9v1m8 8h1m-9 8v1m-6.4 -15.4l.7 .7m12.1 -.7l-.7 .7m0 11.4l.7 .7m-12.1 -.7l-.7 .7" />
                    </svg>
                </a>
                <div class="nav-item dropdown d-none d-md-flex">
                    <a href="#" class="nav-link px-0 show" data-bs-toggle="dropdown" tabindex="-1"
                        aria-label="Show notifications" aria-expanded="true">
                        <!-- Bell Icon -->
                        <svg id="notification-bell" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" class="icon">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <path
                                d="M10 5a2 2 0 1 1 4 0a7 7 0 0 1 4 6v3a4 4 0 0 0 2 3h-16a4 4 0 0 0 2 -3v-3a7 7 0 0 1 4 -6">
                            </path>
                            <path d="M9 17v1a3 3 0 0 0 6 0v-1"></path>
                        </svg>
                        <!-- Notification Badge -->
                        <span id="notification-badge" class="badge bg-red" style="display: none;"></span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-arrow dropdown-menu-end dropdown-menu-card"
                        data-bs-popper="static">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">សារជូនដំណឹង</h3>
                            </div>
                            <div class="list-group list-group-flush list-group-hoverable" id="notification-list">
                                <!-- Notifications will be injected here -->
                            </div>
                            <div class="card-footer">
                                <a href="/elms/allnotifications" class="btn btn-primary w-100">មើលទាំងអស់</a>
                            </div>
                        </div>
                    </div>
                    <!-- Toast container -->
                    <div class="toast-container position-fixed top-0 start-50 translate-middle-x p-3"
                        id="toast-container">
                    </div>
                </div>
            </div>
            <div class="nav-item dropdown">
                <a href="#" class="nav-link d-flex lh-1 p-0" data-bs-toggle="dropdown" aria-label="Open user menu">
                    <img src="<?= $_SESSION['user_profile'] ?>" class="avatar" alt="User Avatar"
                        style="object-fit: cover;">
                    <div class="d-none d-xl-block ps-2">
                        <h3 class="mb-0 text-primary">
                            <?= $_SESSION['user_khmer_name'] ?>
                        </h3>
                        <span class="small text-muted"><?= $_SESSION['email'] ?></span>
                    </div>
                </a>


                <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow" style="width: 300px;">
                    <div class="d-flex flex-column align-items-center justify-content-center text-center">
                        <!-- Profile Picture -->
                        <img src="<?= $_SESSION['user_profile'] ?>" class="avatar avatar-lg mb-3 mt-2 me-0"
                            alt="Profile Picture" style="object-fit: cover;">

                        <!-- User Name and Position -->
                        <div class="d-none d-xl-block ps-2">
                            <h3 class="text-primary mb-0">
                                <?= $_SESSION['user_khmer_name'] ?>
                            </h3>
                            <span
                                class="badge <?= isset($_SESSION['position_color']) ? htmlspecialchars($_SESSION['position_color'], ENT_QUOTES, 'UTF-8') : 'badge-default' ?>"
                                style="margin-top: 4px;">
                                <?= isset($_SESSION['position']) ? htmlspecialchars($_SESSION['position'], ENT_QUOTES, 'UTF-8') : 'Position' ?>
                            </span>
                        </div>
                    </div>

                    <div class="dropdown-divider"></div>

                    <!-- Links for Account Management -->
                    <a href="/elms/edit_user_detail" class="dropdown-item">
                        <svg xmlns=" http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="icon icon-tabler icons-tabler-outline icon-tabler-user-circle">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                            <path d="M12 10m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" />
                            <path d="M6.168 18.849a4 4 0 0 1 3.832 -2.849h4a4 4 0 0 1 3.834 2.855" />
                        </svg>
                        <span class="mx-1">គណនីរបស់ខ្ញុំ</span>
                    </a>


                    <a href="/elms/setting_security?user_id=<?= htmlspecialchars($_SESSION['user_id'], ENT_QUOTES, 'UTF-8') ?>"
                        class="dropdown-item">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="icon icon-tabler icons-tabler-outline icon-tabler-settings">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path
                                d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065z" />
                            <path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" />
                        </svg>
                        <span class="mx-1">ការកំណត់</span>
                    </a>

                    <a href="/elms/usage" class="dropdown-item">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="icon icon-tabler icons-tabler-outline icon-tabler-info-circle me-0 mx-0">
                            <circle cx="12" cy="12" r="10" />
                            <line x1="12" y1="16" x2="12" y2="12" />
                            <line x1="12" y1="8" x2="12.01" y2="8" />
                        </svg>
                        <span class="mx-1">របៀបប្រើប្រាស់ប្រព័ន្ធ</span>
                    </a>

                    <div class="dropdown-divider"></div>

                    <a href="/elms/logout" class="dropdown-item text-danger">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="icon icon-tabler icons-tabler-outline icon-tabler-logout">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M14 8v-2a2 2 0 0 0 -2 -2h-7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2 -2v-2" />
                            <path d="M9 12h12l-3 -3" />
                            <path d="M18 15l3 -3" />
                        </svg>
                        <span class="mx-1">ចាកចេញ</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>

<script>
    const USER_ID = <?= json_encode($_SESSION['user_id']); ?>; // Assuming user_id is available in PHP
    let displayedNotifications = new Set(JSON.parse(sessionStorage.getItem('displayedNotifications') || '[]'));

    // Request notification permission
    function requestNotificationPermission() {
        if (Notification.permission === 'default') {
            Notification.requestPermission().then(permission => {
                if (permission !== 'granted') {
                    console.warn('Notifications are blocked.');
                }
            });
        } else if (Notification.permission === 'denied') {
            console.warn('Notifications are denied by the user.');
        }
    }

    requestNotificationPermission();

    // Function to toggle the notification badge visibility
    function toggleNotificationBadge(unreadCount) {
        const notificationBadge = document.getElementById('notification-badge');
        if (unreadCount > 0) {
            notificationBadge.style.display = 'inline-block'; // Show the badge
            notificationBadge.textContent = unreadCount; // Display unread count
        } else {
            notificationBadge.style.display = 'none'; // Hide the badge
        }
    }

    // Function to animate the bell icon when new notifications arrive
    function animateBellIcon() {
        const bellIcon = document.getElementById('notification-bell');
        if (bellIcon) {
            bellIcon.classList.add('animate__animated', 'animate__headShake'); // Add animation class

            // Remove the animation class after it completes
            setTimeout(() => {
                bellIcon.classList.remove('animate__animated', 'animate__headShake');
            }, 1000); // Animation duration is 1 second
        }
    }

    // Function to fetch notifications and update the UI
    function fetchNotifications(userId) {
        fetch(`/elms/notifications/?user_id=${userId}`)
            .then(response => response.json())
            .then(data => {
                const notificationList = document.getElementById('notification-list');
                notificationList.innerHTML = ''; // Clear the existing notifications

                if (data.success && data.data.length > 0) {
                    let unreadCount = 0;

                    data.data.forEach(notification => {
                        updateNotificationList(notification);
                        if (!notification.is_read) {
                            unreadCount++; // Count unread notifications
                        }

                        // Check for new notifications and animate the bell
                        if (!displayedNotifications.has(notification.id)) {
                            displayToast(notification);
                            animateBellIcon();
                            displayedNotifications.add(notification.id);
                        }
                    });

                    // Update the badge visibility and count
                    toggleNotificationBadge(unreadCount);

                    // Persist displayed notifications in sessionStorage
                    sessionStorage.setItem('displayedNotifications', JSON.stringify([...displayedNotifications]));
                } else {
                    // Show "no notifications" message
                    const noNotificationMessage = document.createElement('div');
                    noNotificationMessage.classList.add('text-center', 'py-3');

                    noNotificationMessage.innerHTML = `
                    <img src="public/img/icons/svgs/empty.svg" alt="No notifications" class="img-fluid mb-2" style="max-width: 450px;">
                    <div class="text-secondary">No notifications available.</div>
                `;
                    notificationList.appendChild(noNotificationMessage);

                    // Hide the badge
                    toggleNotificationBadge(0);
                }
            })
            .catch(error => console.error('Error fetching notifications:', error));
    }

    // Function to display toast notification
    function displayToast(notification) {
        if (Notification.permission !== 'granted') return;

        const toastContainer = document.getElementById('toast-container');
        const newToast = document.createElement('div');

        newToast.classList.add('toast', 'animate__animated', 'animate__slideInDown', 'show');
        newToast.setAttribute('role', 'alert');
        newToast.setAttribute('aria-live', 'assertive');
        newToast.setAttribute('aria-atomic', 'true');
        newToast.setAttribute('data-bs-autohide', 'true');

        newToast.innerHTML = `
        <div class="toast-header">
            <span class="avatar avatar-xs me-2" style="background-image: url(${notification.url})"></span>
            <strong class="me-auto">${notification.title}</strong>
            <small>${notification.created_at || 'Just now'}</small>
            <button type="button" class="ms-2 btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
            ${notification.message}
        </div>
    `;

        // Append the toast to the container
        toastContainer.appendChild(newToast);

        // Play notification sound
        playNotificationSound();

        // Remove toast after 6 seconds with slide-up animation
        setTimeout(() => {
            newToast.classList.remove('animate__slideInDown'); // Remove the slide-in animation
            newToast.classList.add('animate__slideOutUp');    // Add the slide-up animation
            setTimeout(() => {
                newToast.remove(); // Remove the toast after animation ends
            }, 500);  // Wait for the slide-up animation to finish (500ms)
        }, 6000);  // Keep the toast for 6 seconds
    }

    // Function to play notification sound
    function playNotificationSound() {
        const audio = new Audio('public/sounds/notification/notification.mp3'); // Add your sound file
        audio.play();
    }

    // Function to update the notification list in the dropdown
    function updateNotificationList(notification) {
        const notificationList = document.getElementById('notification-list');

        const notificationItem = document.createElement('div');
        notificationItem.classList.add('list-group-item', notification.is_read ? 'read' : 'unread');
        notificationItem.innerHTML = `
            <div class="row align-items-center" style="width: 450px">
                <div class="col-auto">
                    <span class="status-dot ${notification.is_read === 1 ? 'bg-gray' : 'status-dot-animated bg-red'} d-block"></span>
                </div>
                <div class="col text-truncate">
                    <a href="${notification.url}" class="text-body d-block mb-2 d-flex align-content-center" onclick="markAsRead(${notification.id})">
                        <div class="avatar avatar-xs me-2" style="background-image: url(${notification.profile})"></div>
                        <span class="d-flex flex-column">
                            ${notification.title}
                        </span>
                    </a>
                    <div class="d-block text-secondary text-truncate mt-n1">
                        ${notification.message}
                    </div>
                    <small class="text-muted d-block text-end mt-1">${notification.created_at}</small>
                </div>
            </div>
        `;

        // Append the notification item to the dropdown list
        notificationList.appendChild(notificationItem);
    }

    // Function to mark a notification as read
    function markAsRead(notificationId) {
        fetch(`/elms/notifications/mark-as-read`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ notification_id: notificationId }),
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const notificationItem = document.querySelector(`.list-group-item.unread`);
                    if (notificationItem) {
                        notificationItem.classList.remove('unread');
                        notificationItem.classList.add('read');
                        const statusDot = notificationItem.querySelector('.status-dot');
                        if (statusDot) {
                            statusDot.classList.remove('status-dot-animated', 'bg-red');
                            statusDot.classList.add('bg-gray');
                        }
                    }
                }
            })
            .catch(error => console.error('Error marking notification as read:', error));
    }

    // Poll every 5 seconds for new notifications
    setInterval(() => fetchNotifications(USER_ID), 5000);

    // Initial load of all notifications (old + new)
    fetchNotifications(USER_ID);
</script>