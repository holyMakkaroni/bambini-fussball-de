const PluginManager = window.PluginManager;
PluginManager.override('Listing', () => import('./extension/listing.plugin.override'), '[data-listing]');
