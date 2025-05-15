function normalizarTexto(texto) {
    /* Converte para minúsculas, remove acentos e pontuação, normaliza espaços*/
    return texto
        .toLowerCase()
        .normalize("NFD") // Remove acentos
        .replace(/[\u0300-\u036f]/g, "") // Remove marcas diacríticas
        .replace(/[^a-z0-9\s]/g, "") // Remove pontuação
        .replace(/\s+/g, " ") // Normaliza espaços
        .trim();
}

function responderPergunta(pergunta) {
    const perguntaNormalizada = normalizarTexto(pergunta);

    // Mapeamento de padrões (usando RegEx) para respostas
    const respostas = [
        {
            padrao: /(^|\s)(oi|ola|bom dia)(\s|$)/,
            resposta: "Bom dia! Posso te ajudar com algo hoje?"
        },
        {
            padrao: /(^|\s)(sim|pode|claro)(\s|$)/,
            resposta: "Que tal, melhor jogo de ação ou melhor jogo de aventura?"
        },
        {
            padrao: /melhor jogo de acao/,
            resposta: "Um dos melhores jogos de ação é God of War."
        },
        {
            padrao: /melhor jogo de aventura/,
            resposta: "The Legend of Zelda: Breath of the Wild é altamente recomendado!"
        },
        {
            padrao: /(jogo mais popular|jogo famoso)/,
            resposta: "Atualmente, jogos como Fortnite e GTA V estão entre os mais populares."
        },
        {
            padrao: /seu jogo favorito/,
            resposta: "Não tenho favoritos, mas adoro ajudar com reviews!"
        }
    ];

    // Busca a primeira resposta que corresponde ao padrão
    for (let item of respostas) {
        if (perguntaNormalizada.match(item.padrao)) {
            return item.resposta;
        }
    }

    // Resposta padrão para perguntas não reconhecidas
    return "Desculpe, não entendi. Tente algo como 'melhor jogo de ação' ou 'requisitos de sistema'.";
}

// Testes
console.log(responderPergunta("Oi")); // Bom dia! Posso te ajudar com algo hoje?
console.log(responderPergunta("OLÁ BOM DIA!!!")); // Bom dia! Posso te ajudar com algo hoje?
console.log(responderPergunta("qual é o melhor jogo de ação?")); // Um dos melhores jogos de ação é God of War.
console.log(responderPergunta("jogo mais popular 2023")); // Atualmente, jogos como Fortnite e GTA V estão entre os mais populares.
console.log(responderPergunta("algo aleatório")); // Desculpe, não entendi...


// Funções para o chat
document.addEventListener("DOMContentLoaded", () => {
    const chatBox = document.getElementById("chat-box");
    const chatToggle = document.getElementById("chat-toggle");
    const chatInput = document.getElementById("chat-input");
    const chatMessages = document.getElementById("chat-messages");
    const sendButton = document.getElementById("send-button");

    // Alternar visibilidade do chat
    chatToggle.addEventListener("click", () => {
        if (chatBox.style.display === "none" || chatBox.style.display === "") {
            chatBox.style.display = "block";
            chatToggle.textContent = "−";
        } else {
            chatBox.style.display = "none";
            chatToggle.textContent = "💬";
        }
    });

    /*Esse aqui envia a mensagem*/
    sendButton.addEventListener("click", () => {
        const pergunta = chatInput.value.trim();
        if (pergunta) {
            // Adicionar pergunta do usuário
            const userMessage = document.createElement("div");
            userMessage.classList.add("message", "user-message");
            userMessage.textContent = pergunta;
            chatMessages.appendChild(userMessage);

            // Adicionar resposta da IA
            const resposta = responderPergunta(pergunta);
            const botMessage = document.createElement("div");
            botMessage.classList.add("message", "bot-message");
            botMessage.textContent = resposta;
            chatMessages.appendChild(botMessage);

            // Limpar input e rolar para o final
            chatInput.value = "";
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }
    });

    // Permitir envio com Enter
    chatInput.addEventListener("keypress", (e) => {
        if (e.key === "Enter") {
            sendButton.click();
        }
    });
});

