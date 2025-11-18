@push('javascript')
<script>
    // Default pagination count
    let countPage = 10;
    // The AJAX URL is passed from the parent view
    const listUrl = '{{ $listUrl }}';

    $(document).ready(function () {
        // Initial list load
        list();

        // --- Event Listeners ---

        // Debounced search on input
        let searchTimeout;
        $('#search').on('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => list(1), 400); // Wait 400ms after user stops typing
        });

        // Immediate search on Enter key
        $('#search').on('keyup', function (e) {
            if (e.keyCode === 13) {
                clearTimeout(searchTimeout);
                list(1);
            }
        });

        // Handle clicks on pagination links inside the list container
        $('#list-container').on('click', '.pagination a', function(e) {
            e.preventDefault();
            let page = $(this).attr('href').split('page=')[1];
            list(page);
        });
    });

    /**
     * Sets the form action and item name for the delete confirmation modal.
     * This function is called from the onclick attribute in the list view.
     */
    function deleteItem(url, itemName) {
        $('#delete_form').attr('action', url);
        // A more generic message for the modal title
        $('#delete_modal .modal-title').html(`<i class="voyager-trash"></i> ¿Estás seguro de que quieres eliminar "${itemName}"?`);
    }

    /**
     * Fetches and renders the list via AJAX.
     */
    function list(page = 1) {
        const search = $('#search').val()?.trim() || '';
        const fullUrl = `${listUrl}?search=${encodeURIComponent(search)}&paginate=${countPage}&page=${page}`;

        // Show loading indicator
        $('#list-container').html(`
            <div class="text-center" style="padding: 40px">
                <i class="voyager-refresh voyager-2x voyager-spin"></i><br>Cargando...
            </div>
        `);

        $.ajax({
            url: fullUrl,
            type: 'GET',
            success: response => $('#list-container').html(response),
            error: (xhr) => {
                console.error('Error al cargar la lista:', xhr);
                $('#list-container').html(`<div class="alert alert-danger text-center">Error al cargar los datos. Por favor, intenta de nuevo.</div>`);
            }
        });
    }
</script>
@endpush
