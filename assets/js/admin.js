document.addEventListener("DOMContentLoaded", function (event) {
    document.getElementById("improvebot-admin-form").addEventListener("submit", function (event) {
        event.preventDefault();
        var secret_input_1 = document.createElement("input");
        secret_input_1.type = "hidden";
        secret_input_1.name = "action";
        secret_input_1.value = "store_admin_data";
        this.append(secret_input_1);

        var secret_input_2 = document.createElement("input");
        secret_input_2.type = "hidden";
        secret_input_2.name = "security";
        secret_input_2.value = improve_bot_exchanger._nonce;
        this.append(secret_input_2);

        var formData = new FormData(this);

        var xhr = new XMLHttpRequest();
        xhr.withCredentials = true;

        xhr.addEventListener("readystatechange", function () {
            if (this.readyState === 4) {
                alert(this.responseText);
            }
        });

        xhr.open("POST", improve_bot_exchanger.ajax_url);

        xhr.send(formData);
    });
});