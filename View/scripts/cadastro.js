function abrirModal() {
            document.getElementById('modalLogin').style.display = 'flex';
        }

        function fecharModal() {
            document.getElementById('modalLogin').style.display = 'none';
        }

        window.addEventListener('click', function(event) {
            const modal = document.getElementById('modalLogin');
            if (event.target === modal) {
                fecharModal();
            }
        });

        window.addEventListener('keydown', function(event) {
            if (event.key === "Escape") {
                fecharModal();
            }
        });


        //Validação do Cadatro e Login
            function validarEmail(email) {
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return regex.test(email);
        }



    function validarLogin() {
        const usuario = document.getElementById("loginUsuario").value.trim();
        const senha = document.getElementById("loginSenha").value.trim();

        if (usuario === "" || senha === "") {
            alert("Por favor, preencha todos os campos.");
            return false;
        }

        if (senha.length < 6) {
            alert("A senha deve ter pelo menos 6 caracteres.");
            return false;
        }

        // Redirecionar após validação
        window.location.href = "./View/Perfil.html";
        return false; // Impede envio do formulário
    }

    function validarCadastro() {
        const usuario = document.getElementById("cadUsuario").value.trim();
        const email = document.getElementById("cadEmail").value.trim();
        const senha = document.getElementById("cadSenha").value.trim();

        if (usuario === "" || email === "" || senha === "") {
            alert("Por favor, preencha todos os campos.");
            return false;
        }

        if (!validarEmail(email)) {
            alert("Insira um email válido.");
            return false;
        }

        if (senha.length < 6) {
            alert("A senha deve ter pelo menos 6 caracteres.");
            return false;
        }

        // Redirecionar após validação
        window.location.href = "./View/Perfil.html";
        return false; // Impede envio do formulário real
    }