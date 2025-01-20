document.addEventListener('DOMContentLoaded', function() {
    // Handle delete confirmation with AJAX
    const deleteModal = document.getElementById('deleteModal');
    let itemIdToDelete = null;

    deleteModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        itemIdToDelete = button.getAttribute('data-item-id');
    });

    document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
        const currentUrl = window.location.href;
        const url = currentUrl.includes('manage_all_items.php') ? 'manage_all_items.php' : 'manage_items.php';

        fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams({
                    delete_id: itemIdToDelete
                })
            })
            .then(response => response.json())
            .then(data => {
                console.log(data); // Debugging: Log the response data
                if (data.status === 'success') {
                    window.location.href = `${url}?delete_status=success`;
                } else {
                    window.location.href = `${url}?delete_status=error`;
                }
            })
            .catch(error => {
                console.error('Error:', error); // Debugging: Log any errors
                window.location.href = `${url}?delete_status=error`;
            });
    });
});