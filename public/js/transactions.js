//---------------------- Oculta alertas de transações ----------------------//
const error_transaction = document.querySelector('#error_transaction')

// Oculta alerta após 2 segundos
if (error_transaction) {
  setTimeout(() => {
    error_transaction.style.display = 'none'
  }, 2000)
}

//---------------------- Recupera transação a ser editada e preenche inputs do modal ----------------------//

// Iniciar variáveis
let modal_transaction_title
let modal_button_ok_title
let modal_transaction_id
let modal_transaction_date
let modal_transaction_amount
let modal_transaction_description
let modal_transaction_account_name
let modal_transaction_category_name

// Recuperar campos do modal
const recovery_modal = (modal) => {
  modal_transaction_title = modal.querySelector('.modal_transaction_title')
  modal_button_ok_title = modal.querySelector('.modal_button_ok_title')
  modal_transaction_id = modal.querySelector('.transaction_id')
  modal_transaction_date = modal.querySelector('.transaction_date input')
  modal_transaction_amount = modal.querySelector('.transaction_amount input')
  modal_transaction_description = modal.querySelector('.transaction_description input')
  modal_transaction_account_name = modal.querySelectorAll('.accounts_select option')
  modal_transaction_category_name = modal.querySelectorAll('.categories_select option')
}

// Limpar os campos se clicar novamente em adicionar
const clear_modal = () => {
  modal_transaction_title.innerText = 'Nova '
  modal_button_ok_title.innerText = 'Adicionar'
  modal_transaction_account_name.innerText = ''
  modal_transaction_id.value = ''
  modal_transaction_date.value = ''
  modal_transaction_amount.value = ''
  modal_transaction_description.value = ''
  modal_transaction_account_name[0].selected = true
  modal_transaction_category_name[0].selected = true
}

// Recuperar transação e anotar nos campos do modal
const links_edit_transaction = document.querySelectorAll('.link_edit_transaction')

links_edit_transaction.forEach(link_edit_transaction => {
  link_edit_transaction.addEventListener('click', () => {

    // Dados da transação
    let transaction_id = link_edit_transaction.getAttribute('data-transaction_id')
    let transaction_date = link_edit_transaction.getAttribute('data-transaction_date')
    let transaction_amount = link_edit_transaction.getAttribute('data-transaction_amount')
    let transaction_description = link_edit_transaction.getAttribute('data-transaction_description')
    let transaction_account_name = link_edit_transaction.getAttribute('data-transaction_account_name')
    let transaction_category_name = link_edit_transaction.getAttribute('data-transaction_category_name')
    let transaction_type = link_edit_transaction.getAttribute('data-transaction_type')

    let modal = document.querySelector('#modal_income')

    if (transaction_type == 'E') {
      transaction_amount = transaction_amount * -1 
      modal = document.querySelector('#modal_expense')
    }

    recovery_modal(modal)
    
    // Definir valores aos campos
    modal_transaction_title.innerText = 'Editar '
    modal_button_ok_title.innerText = 'Editar '
    modal_transaction_account_name.innerText = transaction_account_name
    modal_transaction_id.value = transaction_id
    modal_transaction_date.value = transaction_date
    modal_transaction_amount.value = transaction_amount
    modal_transaction_description.value = transaction_description

    modal_transaction_account_name.forEach(account => {
      
      if (account.innerText == transaction_account_name) {
         account.selected = true
      }
    })

    modal_transaction_category_name.forEach(category => {
      
      if (category.innerText == transaction_category_name) {
        category.selected = true
      }
    })
  })
})

// Exibe modal limpo para o usuário
const links_add_transaction = document.querySelectorAll('.link_add_transaction')

recovery_modal(document)

links_add_transaction.forEach(link => {
  link.addEventListener('click', clear_modal);
})