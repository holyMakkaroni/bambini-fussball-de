const PluginManager = window.PluginManager
const Plugin = PluginManager.getPlugin('EasyCreditRatenkaufCheckoutExpress')
const EasyCreditRatenkaufExpressCheckout = Plugin.get('class')

export default class EasyCreditRatenkaufExpressCheckoutOverride extends EasyCreditRatenkaufExpressCheckout {
  init() {
    this.el.addEventListener('submit', async () => {

      var form
      if (form = await super.replicateBuyForm()) {
        form.submit()
        return
      }

      if (
        this.el.closest('.is-ctl-checkout.is-act-cartpage') || this.el.closest('.is-ctl-checkout.is-act-checkoutpage')  || this.el.closest('.cart-offcanvas')
      ) {
        window.location.href = '/easycredit/express'
        return
      }

      alert('Der easycredit-Ratenkauf konnte nicht gestartet werden.')
    });
  }
}
