import { Modal } from "bootstrap"
import { del, get, post } from "./ajax"

window.addEventListener('DOMContentLoaded', function () {
    const editCategoryModal = new Modal(document.getElementById('editCategoryModal'))

    document.querySelectorAll('.edit-category-btn').forEach(button => {
        button.addEventListener('click', function (event) {
            const categoryId = event.currentTarget.getAttribute('data-id')

            // TODO: Fetch category info from controller & pass it to this function
            get(`/categories/${categoryId}`)
            .then(response => response.json())
            .then(response => {
                openEditCategoryModal(editCategoryModal, response)
            })
        })
    })

    document.querySelector('.save-category-btn').addEventListener('click', function (event) {
        const categoryId = event.currentTarget.getAttribute('data-id')
        const domElement = editCategoryModal._element

        post(`/categories/${ categoryId }`, {
            name: domElement.querySelector('input[name="name"]').value,
        }, domElement)
        .then(response => {
            if(response.ok) {
                editCategoryModal.hide()
            }
        })
    })

    document.querySelectorAll('.delete-category-btn').forEach(button => {
        button.addEventListener('click', function (event) {
            const categoryId = event.currentTarget.getAttribute('data-id')

            if(confirm('Are you sure you want to delete ?')) {
                del(`/categories/${ categoryId }`)
            }

        })
    })
})

function openEditCategoryModal(modal, {id, name}) {
    const nameInput = modal._element.querySelector('input[name="name"]')

    nameInput.value = name

    modal._element.querySelector('.save-category-btn').setAttribute('data-id', id)

    modal.show()
}
