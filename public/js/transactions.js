// Alerta após tentar criar uma transação
const alert_add_transaction = document.querySelector('#alert_add_transaction')

if (alert_add_transaction) {
  display_alert(alert_add_transaction)
}

// Exibe alerta após 100ms
function display_alert(alert_div) {
  setTimeout(() => {
    alert(alert_div.innerText)
  }, 100)
}