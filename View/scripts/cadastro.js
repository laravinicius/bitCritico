function abrirModal() {
    document.getElementById('modalLogin').style.display = 'flex';
    abrirLogin(); // abrir a tela de login ao abrir o modal
}

function fecharModal() {
    document.getElementById('modalLogin').style.display = 'none';
}

// Alternar entre login e cadastro
function abrirCadastro() {
    document.getElementById('loginModal').style.display = 'none';
    document.getElementById('cadastroModal').style.display = 'block';
}

function abrirLogin() {
    document.getElementById('cadastroModal').style.display = 'none';
    document.getElementById('loginModal').style.display = 'block';
}

// Fechar modal clicando fora
window.addEventListener('click', function(event) {
    const modal = document.getElementById('modalLogin');
    if (event.target === modal) {
        fecharModal();
    }
});

// Fechar modal com Esc
window.addEventListener('keydown', function(event) {
    if (event.key === "Escape") {
        fecharModal();
    }
});

// Validação de e-mail
function validarEmail(email) {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(email);
}

// Validação do login
function validarLogin(event) {
    event.preventDefault(); // Impede o envio do formulário

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

    /* Redirecionar após validação
    window.location.href = "./View/Perfil.html";
    return false;*/
}

// Validação do cadastro
function validarCadastro(event) {
    event.preventDefault(); // Impede o envio do formulário

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

    /* Redirecionar após validação
    window.location.href = "./View/Perfil.html";
    return false;*/
}