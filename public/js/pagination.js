
$(document).ready(function() {
    // Destroy existing DataTable if it exists
    if ($.fn.DataTable.isDataTable('#hiddenTable')) {
        $('#hiddenTable').DataTable().destroy();
    }

    // Initialize DataTables on the hidden table
    const table = $('#hiddenTable').DataTable({
        "pageLength": 5 // Default number of entries to show
    });

    // Function to update custom pagination
    function updatePagination() {
        const pageInfo = table.page.info();
        $('#pages').empty();
        
        for (let i = 0; i < pageInfo.pages; i++) {
            const isActive = i === pageInfo.page ? 'active' : '';
            $('#pages').append(`<a href="#" class="page-num ${isActive}" data-page="${i}">${i + 1}</a>`);
        }
    }

    // Custom Show Entries Dropdown
    $('#customShowEntries').on('change', function() {
        const len = $(this).val();
        table.page.len(len).draw();
    });

    // Custom Search Box
    $('#customSearch').on('keyup', function() {
        table.search($(this).val()).draw();
    });

    // Listen for DataTables events like sorting, searching, etc.
    table.on('draw', function() {
        // Clear custom HTML
        $('#customContent').empty();

        let pageInfo = table.page.info();
        let start = pageInfo.start;
        let end = pageInfo.end;
        let counter = 0;

        // Loop through all rows that match the current search query
        table.rows({ search: 'applied' }).every(function(rowIdx, tableLoop, rowLoop) {
            let item = this.data();
            if (item) {
                // Only add the row to customContent if it should be visible based on the current page and pageLength
                if (counter >= start && counter < end) {
                    $('#customContent').append(`
                        <div class="d-md-flex post-entry-2 half">
                            <a href="#" class="me-4 thumbnail">
                                <img src="${imgUrl}" alt="" class="img-fluid">
                            </a>
                            <div>
                                <div class="post-meta">
                                    <span class="date">${item[2]}</span>
                                    <span class="mx-1">&bullet;</span>
                                    <span>${item[3]}</span>
                                </div>
                                <h3>
                                    <a href="/${item[1]}/${item[2]}/${item[5]}">${item[0]}</a>
                                </h3>
                                <p>${item[4]} <b>Click for more information hidden behind this article.</b></p>
                            </div>
                        </div>
                    `);
                }
                counter++;
            }
        });

        // Update custom pagination and info
        $('#customInfo').html(`Showing ${start + 1} to ${end} of ${pageInfo.recordsDisplay} entries`);
        updatePagination();
    });

    // Go to page on click
    $(document).on('click', '.page-num', function(e) {
        e.preventDefault();
        const page = $(this).data('page');
        table.page(page).draw(false);
    });

    // Previous page
    $('#prev').on('click', function(e) {
        e.preventDefault();
        table.page('previous').draw(false);
    });

    // Next page
    $('#next').on('click', function(e) {
        e.preventDefault();
        table.page('next').draw(false);
    });

    // Trigger an initial draw to populate the custom HTML and pagination
    table.draw();
});