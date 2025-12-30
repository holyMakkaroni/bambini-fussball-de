import EasyCreditRatenkaufExpressCheckoutOverride from './plugins/easycredit-override.plugin';
import GoogleAddressAutocompletePlugin from "./plugins/google-address-autocomplete.plugin";
import LoginRegisterTogglePlugin from "./plugins/login-register-toggle.plugin";
import EasyCreditRatenkaufCheckout from "EasyCreditRatenkaufCheckout";


const PluginManager = window.PluginManager;
const registeredPlugins = PluginManager.getPluginList();

if (registeredPlugins.hasOwnProperty('EasyCreditRatenkaufCheckoutExpress')) {
  PluginManager.override('EasyCreditRatenkaufCheckoutExpress', EasyCreditRatenkaufExpressCheckoutOverride, 'easycredit-express-button');
}

PluginManager.register('EasyCreditRatenkaufCheckout', EasyCreditRatenkaufCheckout, '.is-act-checkoutpage');
PluginManager.register('GoogleAddressAutocompletePlugin', GoogleAddressAutocompletePlugin, '[data-google-address-autocomplete]')
PluginManager.register('LoginRegisterTogglePlugin', LoginRegisterTogglePlugin, '[login-register-toggle]')