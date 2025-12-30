import Plugin from 'src/plugin-system/plugin.class';

export default class GoogleAddressAutocompletePlugin extends Plugin {
    static options = {
        addressField: 'input[name*="street"]',
        zipField: 'input[name*="zipcode"]',
        cityField: 'input[name*="city"]',
        postNumberField: '.postnumber'
    };

    init() {
        this.initAutocomplete();
    }

    loadGooglePlaces(apiKey) {
        let script = document.createElement('script');
        script.src = `https://maps.googleapis.com/maps/api/js?key=${apiKey}&libraries=places&callback=Function.prototype`;
        script.async = true;

        if(typeof window.googlePlacesInitialized === 'undefined') {
            document.body.appendChild(script);
            window.googlePlacesInitialized = true;
        }

        return script;
    }

    initAutocomplete() {
        const apiKey = window.googleMapsApiKey || false;

        if(!apiKey) {
            console.warn('Address autocomplete not init. Please set api key in plugin settings!')
            return;
        }

        const script = this.loadGooglePlaces(apiKey);

        script.addEventListener("load", () => {
            let addressField = this.el.querySelector(this.options.addressField);

            if(!addressField) {
                return;
            }

            this.autocomplete = new google.maps.places.Autocomplete(addressField, {
                fields: ["name", "address_components"]
            });

            this.autocomplete.addListener("place_changed", this.fillAddress.bind(this));
        });
    }

    fillAddress() {
        const place = this.autocomplete.getPlace();

        let name = place.name || "";
        let street = "";
        let streetNumber = "";
        let zip = "";
        let city = "";

        const isPostStation = this.checkIsPostStation(name);

        for (const component of place.address_components) {
            const componentType = component.types[0];

            switch (componentType) {
                case "street_number":
                    streetNumber = component.long_name;
                    break;

                case "route":
                    street = component.long_name;
                    break;

                case "postal_code":
                    zip = `${component.long_name}${zip}`;
                    break;

                case "postal_code_suffix":
                    zip = `${zip}-${component.long_name}`;
                    break;

                case "locality":
                case "postal_town":
                    city = component.long_name;
                    break;

                default:
                    break;
            }
        }

        let addressField = this.el.querySelector(this.options.addressField);
        let zipField = this.el.querySelector(this.options.zipField);
        let cityField = this.el.querySelector(this.options.cityField);

        if (addressField) {
            addressField.value = isPostStation ? name : street + ' ' + streetNumber;
        }

        if (zipField) {
            zipField.value = zip;
        }

        if (cityField) {
            cityField.value = city;
        }

        const executePostNumber = this.togglePostNumber.bind(this, isPostStation);

        executePostNumber();
    }

    checkIsPostStation(address) {
        return !!(address && address.includes("Packstation"));
    }

    togglePostNumber(active = false) {
        let postNumberContainer = this.el.querySelector(this.options.postNumberField);
        let postNumberInput = postNumberContainer.querySelector('input');

        if(active) {
            postNumberContainer.style.display = 'block';
            postNumberInput.setAttribute('required', 'true');
        } else {
            postNumberContainer.style.display = 'none';
            postNumberInput.removeAttribute('required');
        }
    }
}
