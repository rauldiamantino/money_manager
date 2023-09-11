//------------------ Oculta alertas de criação de contas ------------------//
const $alert_create_account = document.querySelector('#alert_create_account')

// Oculta alerta após 2 segundos
if ($alert_create_account) {
  setTimeout(() => {
    $alert_create_account.style.display = 'none'
  }, 2000)
}

//---------------------- Recupera conta a ser editada e preenche inputs do modal ----------------------//

// Iniciar variáveis
let modal_account_title
let modal_button_ok_title
let modal_account_id
let modal_account_name

// Recuperar campos do modal
const recovery_modal = (modal) => {
  modal_account_title = modal.querySelector('.modal_account_title')
  modal_button_ok_title = modal.querySelector('.modal_button_ok_title')
  modal_account_name = modal.querySelector('.account_name')
  modal_account_id = modal.querySelector('.account_id')
}

// Limpar os campos se clicar novamente em adicionar
const clear_modal = () => {
  modal_account_title.innerText = 'Nova '
  modal_button_ok_title.innerText = 'Adicionar'
  modal_account_name.value = '';
  modal_account_id.value = '';
}

// Recuperar conta e anotar nos campos do modal
const links_edit_account = document.querySelectorAll('.link_edit_account')

links_edit_account.forEach(link_edit_account => {

  link_edit_account.addEventListener('click', () => {

    // Dados da conta
    let account_name = link_edit_account.getAttribute('data-account_name')
    let account_id = link_edit_account.getAttribute('data-account_id')

    const modal = document.querySelector('#modal_account')
    recovery_modal(modal)
    
    // Definir valores aos campos
    modal_account_title.innerText = 'Editar '
    modal_button_ok_title.innerText = 'Editar '
    modal_account_name.value = account_name
    modal_account_id.value = account_id
  })
})

// Exibe modal limpo para o usuário
const link_add_account = document.querySelector('.link_add_account')

recovery_modal(document)
link_add_account.addEventListener('click', clear_modal)