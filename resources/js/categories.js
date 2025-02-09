import { Modal } from "bootstrap"
import { del, get, post } from "./ajax"
import DataTable from "datatables.net-dt";

window.addEventListener('DOMContentLoaded', function () {
    const editCategoryModal = new Modal(document.getElementById('editCategoryModal'))

    const table = new DataTable('#categoriesTable', {
        serverSide: true,
        ajax: '/categories/load',
        orderMulti: false,
        columns: [
            {data: "name"},
            {data: "createdAt"},
            {data: "updatedAt"},
            {
                sortable: false,
                data: row => `
                    <div class="d-flex flex-">
                        <button type="submit" class="btn btn-outline-primary delete-category-btn" data-id="${ row.id }">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                              <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"/>
                              <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"/>
                            </svg>
                        </button>
                        <button class="ms-2 btn btn-outline-primary edit-category-btn" data-id="${ row.id }">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil" viewBox="0 0 16 16">
                              <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325"/>
                            </svg>
                        </button>
                    </div>
                `
            }
        ]
    });

    document.querySelector('#categoriesTable').addEventListener('click', function (event) {
        const editBtn   = event.target.closest('.edit-category-btn')
        const deleteBtn = event.target.closest('.delete-category-btn')

        if (editBtn) {
            const categoryId = editBtn.getAttribute('data-id')

            get(`/categories/${ categoryId }`)
                .then(response => response.json())
                .then(response => openEditCategoryModal(editCategoryModal, response))
        } else {
            const categoryId = deleteBtn.getAttribute('data-id')

            if (confirm('Are you sure you want to delete this category?')) {
                del(`/categories/${ categoryId }`).then(response => {
                    if (response.ok) {
                        table.draw()
                    }
                })
            }
        }
    })

    // document.querySelectorAll('.edit-category-btn').forEach(button => {
    //     button.addEventListener('click', function (event) {
    //         const categoryId = event.currentTarget.getAttribute('data-id')

    //         // TODO: Fetch category info from controller & pass it to this function
    //         get(`/categories/${categoryId}`)
    //         .then(response => response.json())
    //         .then(response => {
    //             openEditCategoryModal(editCategoryModal, response)
    //         })
    //     })
    // })

    document.querySelector('.save-category-btn').addEventListener('click', function (event) {
        const categoryId = event.currentTarget.getAttribute('data-id')
        const domElement = editCategoryModal._element

        post(`/categories/${ categoryId }`, {
            name: domElement.querySelector('input[name="name"]').value,
        }, domElement)
        .then(response => {
            if(response.ok) {
                table.draw()
                editCategoryModal.hide()
            }
        })
    })

    // document.querySelectorAll('.delete-category-btn').forEach(button => {
    //     button.addEventListener('click', function (event) {
    //         const categoryId = event.currentTarget.getAttribute('data-id')

    //         if(confirm('Are you sure you want to delete ?')) {
    //             del(`/categories/${ categoryId }`)
    //         }

    //     })
    // })
})

function openEditCategoryModal(modal, {id, name}) {
    const nameInput = modal._element.querySelector('input[name="name"]')

    nameInput.value = name

    modal._element.querySelector('.save-category-btn').setAttribute('data-id', id)

    modal.show()
}
