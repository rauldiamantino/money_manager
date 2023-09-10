//------------------ Oculta alertas de criação de categorias ------------------//
const $alert_create_account = document.querySelector('#alert_create_account')

// Oculta alerta após 2 segundos
if ($alert_create_account) {
  setTimeout(() => {
    $alert_create_account.style.display = 'none'
  }, 2000)
}