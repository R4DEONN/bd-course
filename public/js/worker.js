const table = document.getElementById('tbody');

console.log('mew')
subscribeDeleteButtons()
subscribeEditButtons()
// addButton.addEventListener('click', () => location.href = './worker/add');

function subscribeDeleteButtons() {
    const deleteButtons = document.querySelectorAll('.delete-button');
    deleteButtons.forEach(button => {
        button.addEventListener('click', async (e) => {
            e.stopPropagation();
            const workerId = button.getAttribute('data-worker-id');
            const promise = fetch(`/worker/delete/${workerId}`, {
                method: 'DELETE',
            });

            promise
                .then((data) => {
                    return data.text();
                })
                .then((data) => {
                    table.innerHTML = data;
                })
                .then(() => {
                    subscribeDeleteButtons();
                    subscribeEditButtons();
                })
                .catch((error) => {

                })
        })
    });
}

function subscribeEditButtons() {
    const editButtons = document.querySelectorAll('.edit-button');
    editButtons.forEach(button => {
        button.addEventListener('click', async (e) => {
            e.stopPropagation();
            const workerId = button.getAttribute('data-worker-id');
            const departmentId = button.getAttribute('data-department-id');
            location.href = `/department/${departmentId}/worker/edit/${workerId}`;
        })
    });
}