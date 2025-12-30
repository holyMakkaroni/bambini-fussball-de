const { Mixin } = Shopware;

Mixin.register('lia-slugify', {
    methods: {
        slugify(string) {
            if (!string) {
                return string;
            }

            return string.toString().toLowerCase()
                .replace(/\s+/g, '-')
                .replace(/á/gi,"a")
                .replace(/ä/gi,"ae")
                .replace(/é/gi,"e")
                .replace(/í/gi,"i")
                .replace(/ó/gi,"o")
                .replace(/ö/gi,"oe")
                .replace(/ú/gi,"u")
                .replace(/ü/gi,"ue")
                .replace(/ñ/gi,"n")
                .replace(/[^\w\-]+/g, '')
                .replace(/--+/g, '');
        }
    }
});
