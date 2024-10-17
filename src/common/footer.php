</div>
</div>
</div>
</div>

<?php require_once 'modals.php'; ?>

<!-- Tabler Core -->
<script src="public/dist/js/tabler.min.js?1668287865" defer></script>
<script src="public/dist/js/demo.min.js?1668287865" defer></script>

<script src="public/dist/libs/apexcharts/dist/apexcharts.min.js?1668287865" defer></script>
<script src="public/dist/libs/jsvectormap/dist/js/jsvectormap.min.js?1668287865" defer></script>
<script src="public/dist/libs/jsvectormap/dist/maps/world.js?1668287865" defer></script>
<script src="public/dist/libs/jsvectormap/dist/maps/world-merc.js?1668287865" defer></script>
<script src="public/dist/libs/tom-select/dist/js/tom-select.base.js?1668287865" defer></script>

<script src="public/dist/libs/nouislider/dist/nouislider.min.js?1668287865" defer></script>
<script src="public/dist/libs/litepicker/dist/litepicker.js?1668287865" defer></script>
<script src="public/dist/libs/tom-select/dist/js/tom-select.base.min.js?1668287865" defer></script>
<!-- other link  -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<!-- datatables  -->
<script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>

<!-- Script to hide the loader with delay -->
<script>
    window.addEventListener('load', function () {
        // Add a delay before removing the loader
        setTimeout(function () {
            var loader = document.getElementById('loader-wrapper');
            document.body.classList.remove('loading');
            loader.style.display = 'none';
        }, 500); // Delay for 0.5 second (500 milliseconds)
    });
</script>
<!-- end  -->

<script>
    document.addEventListener("DOMContentLoaded", function () {
        window.Litepicker && new Litepicker({
            element: document.getElementById("datepicker-inline"),
            buttonText: {
                previousMonth: `<!-- Download SVG icon from http://tabler-icons.io/i/chevron-left -->
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <polyline points="15 6 9 12 15 18"/>
                </svg>`,
                nextMonth: `<!-- Download SVG icon from http://tabler-icons.io/i/chevron-right -->
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <polyline points="9 6 15 12 9 18"/>
                </svg>`,
            },
            inlineMode: true,
            startDate: new Date(), // Set the active date to today
            autoApply: true,
            singleMode: true,
        });
    });
</script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Check if element exists before initializing TomSelect
        const leaveTypeElement = document.getElementById("leave_type");
        if (leaveTypeElement) {
            new TomSelect(leaveTypeElement, {
                copyClassesToDropdown: false,
                dropdownClass: "dropdown-menu ts-dropdown",
                optionClass: "dropdown-item",
                controlInput: "<input>",
                render: {
                    item: function (data, escape) {
                        return data.customProperties ?
                            `<div><span class="dropdown-item-indicator">${data.customProperties}</span>${escape(data.text)}</div>` :
                            `<div>${escape(data.text)}</div>`;
                    },
                    option: function (data, escape) {
                        return data.customProperties ?
                            `<div><span class="dropdown-item-indicator">${data.customProperties}</span>${escape(data.text)}</div>` :
                            `<div>${escape(data.text)}</div>`;
                    },
                },
            });

            leaveTypeElement.addEventListener('change', function () {
                const selectedOption = this.options[this.selectedIndex];
                const leaveTypeName = selectedOption.getAttribute('data-leave-name');
                document.getElementById('leave_type_name').value = leaveTypeName;
            });
        }

        // Check if elements with class 'date-picker' exist before initializing Litepicker
        const dateInputs = document.querySelectorAll('.date-picker');
        dateInputs.forEach(input => {
            new Litepicker({
                element: input,
                singleMode: true,
                format: "YYYY-MM-DD",
                lang: 'kh',
                buttonText: {
                    previousMonth: `<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><polyline points="15 6 9 12 15 18" /></svg>`,
                    nextMonth: `<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><polyline points="9 6 15 12 9 18" /></svg>`,
                }
            });
        });

        // Check if elements with class 'leave-picker' exist before initializing Litepicker
        const leaveDateInputs = document.querySelectorAll('.leave-picker');
        leaveDateInputs.forEach(input => {
            new Litepicker({
                element: input,
                singleMode: true,
                format: "YYYY-MM-DD",
                lang: 'kh',
                minDate: new Date(), // Disables past dates by setting the min date to today
                buttonText: {
                    previousMonth: `<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><polyline points="15 6 9 12 15 18" /></svg>`,
                    nextMonth: `<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><polyline points="9 6 15 12 9 18" /></svg>`,
                }
            });
        });


        // Initialize Flatpickr for elements with the 'time-picker' class
        const timeInputs = document.querySelectorAll(".time-picker");
        timeInputs.forEach(timeInput => {
            flatpickr(timeInput, {
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                time_24hr: false,
                defaultHour: 12,
                defaultMinute: 0,
                locale: 'km',
                allowInput: true, // Allow users to type directly into the input field
                minuteIncrement: 1 // Set minute intervals to 1 (instead of 5)
            });
        });

        // Initial setup to ensure signature file input is visible if checkbox is checked
        const signatureCheckbox = document.getElementById('signature');
        if (signatureCheckbox && signatureCheckbox.checked) {
            const signatureFileInput = document.getElementById('signatureFile');
            if (signatureFileInput) {
                signatureFileInput.style.display = 'block';
            }
        }
    });

    function toggleFileInput(checkbox, fileInputId) {
        const fileInput = document.getElementById(fileInputId);
        if (fileInput) {
            fileInput.style.display = checkbox.checked ? 'block' : 'none';
        }
    }

    function displayFileName(inputId, labelId) {
        const input = document.getElementById(inputId);
        const fileNameLabel = document.getElementById(labelId);
        if (input && fileNameLabel) {
            fileNameLabel.textContent = input.files[0] ? input.files[0].name : '';
        }
    }
</script>

<script>
    // @formatter:off
    document.addEventListener("DOMContentLoaded", function () {
        var el;
        window.TomSelect && (new TomSelect(el = document.getElementById('select-status'), {
            copyClassesToDropdown: false,
            dropdownClass: 'dropdown-menu ts-dropdown',
            optionClass: 'dropdown-item',
            controlInput: '<input>',
            render: {
                item: function (data, escape) {
                    if (data.customProperties) {
                        return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape(data.text) + '</div>';
                    }
                    return '<div>' + escape(data.text) + '</div>';
                },
                option: function (data, escape) {
                    if (data.customProperties) {
                        return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape(data.text) + '</div>';
                    }
                    return '<div>' + escape(data.text) + '</div>';
                },
            },
        }));
    });
    // @formatter:on
</script>

<!-- ts select  -->
<script>
    // @formatter:off
    document.addEventListener("DOMContentLoaded", function () {
        var elements = document.querySelectorAll(".ts-select"); // Select all elements with the class ts-select
        elements.forEach(function (el) {
            window.TomSelect &&
                new TomSelect(el, {
                    copyClassesToDropdown: false,
                    dropdownClass: "dropdown-menu ts-dropdown",
                    optionClass: "dropdown-item",
                    controlInput: "<input>",
                    render: {
                        item: function (data, escape) {
                            if (data.customProperties) {
                                return '<div><span class="dropdown-item-indicator">' + data.customProperties + "</span>" + escape(data.text) + "</div>";
                            }
                            return "<div>" + escape(data.text) + "</div>";
                        },
                        option: function (data, escape) {
                            if (data.customProperties) {
                                return '<div><span class="dropdown-item-indicator">' + data.customProperties + "</span>" + escape(data.text) + "</div>";
                            }
                            return "<div>" + escape(data.text) + "</div>";
                        },
                    },
                });
        });
    });
    // @formatter:on
</script>

<!-- datatable sort  -->
<script>
    document.querySelectorAll('.table-sort').forEach(function (header) {
        header.addEventListener('click', function () {
            let table = header.closest('.sortable-table');
            let columnIndex = [...header.parentNode.children].indexOf(header);
            let ascending = !header.classList.contains('asc');
            sortTable(table, columnIndex, ascending);
            header.classList.toggle('asc', ascending);
        });
    });

    function sortTable(table, columnIndex, ascending) {
        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));
        rows.sort((a, b) => {
            const cellA = a.children[columnIndex].innerText.trim();
            const cellB = b.children[columnIndex].innerText.trim();
            return ascending ? cellA.localeCompare(cellB) : cellB.localeCompare(cellA);
        });
        rows.forEach(row => tbody.appendChild(row));
    }
</script>

<!-- for select people dropdown  -->
<script>
    // @formatter:off
    document.addEventListener("DOMContentLoaded", function () {
        // Select all elements with the class 'select-people'
        const selectElements = document.querySelectorAll('.select-people');

        selectElements.forEach(el => {
            window.TomSelect && (new TomSelect(el, {
                copyClassesToDropdown: false,
                dropdownClass: 'dropdown-menu ts-dropdown',
                optionClass: 'dropdown-item',
                controlInput: '<input>',
                render: {
                    item: function (data, escape) {
                        if (data.customProperties) {
                            return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape(data.text) + '</div>';
                        }
                        return '<div>' + escape(data.text) + '</div>';
                    },
                    option: function (data, escape) {
                        if (data.customProperties) {
                            return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape(data.text) + '</div>';
                        }
                        return '<div>' + escape(data.text) + '</div>';
                    },
                },
            }));
        });
    });
    // @formatter:on
</script>

</body>

</html>