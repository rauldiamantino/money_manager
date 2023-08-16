const alert_create_account = document.querySelector('#alert_create_account')

if (alert_create_account) {
  display_alert(alert_create_account)
}

function display_alert(alert_div) {
  setTimeout(() => {
    alert(alert_div.innerText)
  }, 100)
}