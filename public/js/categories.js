//------------------ Oculta alertas de criação de categorias ------------------//
const $alert_create_category = document.querySelector('#alert_create_category')

// Oculta alerta após 2 segundos
if ($alert_create_category) {
  setTimeout(() => {
    $alert_create_category.style.display = 'none'
  }, 2000)
}

//---------------------- Recupera categoria a ser editada e preenche inputs do modal ----------------------//

// Iniciar variáveis
let modal_category_title
let modal_button_ok_title
let modal_category_id
let modal_category_name

// Recuperar campos do modal
const recovery_modal = (modal) => {
  modal_category_title = modal.querySelector('.modal_category_title')
  modal_button_ok_title = modal.querySelector('.modal_button_ok_title')
  modal_category_name = modal.querySelector('.category_name')
  modal_category_id = modal.querySelector('.category_id')
}

// Limpar os campos se clicar novamente em adicionar
const clear_modal = () => {
  modal_category_title.innerText = 'Nova '
  modal_button_ok_title.innerText = 'Adicionar'
  modal_category_name.value = '';
  modal_category_id.value = '';
}

// Recuperar categoria e anotar nos campos do modal
const links_edit_category = document.querySelectorAll('.link_edit_category')

links_edit_category.forEach(link_edit_category => {

  link_edit_category.addEventListener('click', () => {

    // Dados da categoria
    let category_name = link_edit_category.getAttribute('data-category_name')
    let category_id = link_edit_category.getAttribute('data-category_id')

    const modal = document.querySelector('#modal_category')
    recovery_modal(modal)
    
    // Definir valores aos campos
    modal_category_title.innerText = 'Editar '
    modal_button_ok_title.innerText = 'Editar '
    modal_category_name.value = category_name
    modal_category_id.value = category_id
  })
})

// Exibe modal limpo para o usuário
const link_add_category = document.querySelector('.link_add_category')

recovery_modal(document)
link_add_category.addEventListener('click', clear_modal)