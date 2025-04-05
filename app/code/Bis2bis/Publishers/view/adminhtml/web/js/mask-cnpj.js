define(['jquery'], function($) {
    "use strict";

    // Função que aplica a máscara no CNPJ
    function maskCnpj(value) {
        // Remove todos os caracteres que não são dígitos
        value = value.replace(/\D/g, '');

        // Aplica a máscara 99.999.999/9999-99
        if (value.length > 2) {
            value = value.substring(0, 2) + '.' + value.substring(2);
        }
        if (value.length > 6) {
            value = value.substring(0, 6) + '.' + value.substring(6);
        }
        if (value.length > 10) {
            value = value.substring(0, 10) + '/' + value.substring(10);
        }
        if (value.length > 15) {
            value = value.substring(0, 15) + '-' + value.substring(15);
        }
        // Garante que o valor máximo tenha 18 caracteres
        if (value.length > 18) {
            value = value.substring(0, 18);
        }
        return value;
    }

    // A cada alteração (input) no campo com name "cnpj", aplica a máscara
    $(document).on('input', 'input[name="cnpj"]', function() {
        var $this = $(this);
        var value = $this.val();
        $this.val(maskCnpj(value));
    });
});
