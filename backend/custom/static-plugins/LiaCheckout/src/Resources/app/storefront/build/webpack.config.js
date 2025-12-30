// <plugin root>/src/Resources/app/administration/build/webpack.config.js
module.exports = (params) => {
  const vendorDir = params.basePath.replace('/lia/checkout/src', '')

  return {
    resolve: {
      alias: {
        EasyCreditRatenkaufCheckout: `${vendorDir}/store.shopware.com/easycreditratenkauf/src/Resources/app/storefront/src/checkout/checkout.js`,
      }
    }
  };
};