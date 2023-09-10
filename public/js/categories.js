//------------------ Oculta alertas de criação de categorias ------------------//
const $alert_create_category = document.querySelector('#alert_create_category')

// Oculta alerta após 2 segundos
if ($alert_create_category) {
  setTimeout(() => {
    $alert_create_category.style.display = 'none'
  }, 2000)
}