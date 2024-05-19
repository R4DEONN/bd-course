const addButton = document.getElementById('addButton');
const table = document.getElementById('tbody');

console.log('mew')
subscribeDeleteButtons()
subscribeEditButtons()
addButton.addEventListener('click', () => location.href = './department/add');

function subscribeDeleteButtons() {
    const deleteButtons = document.querySelectorAll('.delete-button');
    deleteButtons.forEach(button => {
        button.addEventListener('click', async (e) => {
            e.stopPropagation();
            const departmentId = button.getAttribute('data-department-id');
            const promise = fetch(`/department/delete/${departmentId}`, {
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
                    subscribeEditButtons();
                    subscribeDeleteButtons();
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
            const departmentId = button.getAttribute('data-department-id');
            location.href = `/department/edit/${departmentId}`;
        })
    });
}