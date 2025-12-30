import Plugin from 'src/plugin-system/plugin.class';

export default class LoginRegisterTogglePlugin extends Plugin {
    static options = {
        loginForm: '.login-form',
        registerForm: '.register-form',
        toggleButton: '.login-register-toggle',
        emailField: 'input[type*="email"]',
    };

    init() {
        const loginToggle = this.el.querySelector(this.options.loginForm + ' ' + this.options.toggleButton)
        const registerToggle = this.el.querySelector(this.options.registerForm + ' ' + this.options.toggleButton)

        if(!loginToggle || !registerToggle) {
            return
        }

        let _this = this;

        loginToggle.addEventListener('click', () => {
            const emailInput = _this.el.querySelector(`${_this.options.loginForm} ${_this.options.emailField}`)

            if (emailInput) {
                const currentEmail = emailInput.value;
                const registerEmailInput = _this.el.querySelector(`${_this.options.registerForm} ${_this.options.emailField}`);

                if (currentEmail) {
                    if (registerEmailInput) {
                        registerEmailInput.value = currentEmail;
                    }
                } else {
                    if (registerEmailInput) {
                        registerEmailInput.value = '';
                    }
                }
            }

            this.el.querySelector(this.options.loginForm).style.display = 'none'
            this.el.querySelector(this.options.registerForm).style.display = 'block'
        })

        registerToggle.addEventListener('click', () => {
            const emailInput = _this.el.querySelector(`${_this.options.registerForm} ${_this.options.emailField}`)

            if (emailInput) {
                const currentEmail = emailInput.value;
                const registerEmailInput = _this.el.querySelector(`${_this.options.loginForm} ${_this.options.emailField}`);

                if (currentEmail) {
                    if (registerEmailInput) {
                        registerEmailInput.value = currentEmail;
                    }
                } else {
                    if (registerEmailInput) {
                        registerEmailInput.value = '';
                    }
                }
            }

            this.el.querySelector(this.options.loginForm).style.display = 'block'
            this.el.querySelector(this.options.registerForm).style.display = 'none'
        })
    }
}
