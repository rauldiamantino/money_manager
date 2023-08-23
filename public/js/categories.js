// Alerta após tentar criar uma categoria
const alert_create_category = document.querySelector('#alert_create_category')

if (alert_create_category) {
  display_alert(alert_create_category)
}

// Exibe alerta após 100ms
function display_alert(alert_div) {
  setTimeout(() => {
    alert(alert_div.innerText)
  }, 100)
}