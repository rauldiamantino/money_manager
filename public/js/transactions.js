const alert_add_transaction = document.querySelector('#alert_add_transaction')

if (alert_add_transaction) {
  display_alert(alert_add_transaction)
}

function display_alert(alert_div) {
  setTimeout(() => {
    alert(alert_div.innerText)
  }, 100)
}