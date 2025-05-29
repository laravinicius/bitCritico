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
            padrao: /(^|\s)(oi|ola|bom dia|oii|oiii|oie|oie|oiiee|ooie|ooiiee|sim|simm|ssiimm)(\s|$)/,
            resposta: "Bom dia! Posso te ajudar com algo hoje?"
        },
        {
            padrao: /(^|\s)(nao|não|nnaaoo|naaooo|hoje nao|a principio nao)(\s|$)/,
            resposta: "Certo se precisar só chamar!"
        },
        {
            padrao: /(^|\s)(sim|pode|claro|yes|depende|por favor|certamente)(\s|$)/,
            resposta: "Que tal, melhor jogo de ação ou melhor jogo de aventura?"
        },
        {
            padrao: /(^|\s)(melhor jogo de acao)(\s|$)/,
            resposta: "Um dos melhores jogos de ação é God of War, Procure por ele e veja as melhores Reviews :D"
        },
        {
            padrao: /(^|\s)(melhor jogo de aventura)(\s|$)/,
            resposta: "The Legend of Zelda: Breath of the Wild é altamente recomendado!"
        },
        {
            padrao: /(^|\s)(jogo mais popular|jogo famoso)(\s|$)/,
            resposta: "Atualmente, jogos como Fortnite e GTA V estão entre os mais populares."
        },
        {
            padrao: /(^|\s)(seu jogo favorito)(\s|$)/,
            resposta: "Não tenho favoritos, mas adoro ajudar com reviews!"
        },
        {
            padrao: /(^|\s)(jogo mais jogado|jogo com mais players| jogo com mais pessoas)(\s|$)/,
            resposta: "Atualmente, em 2025, o jogo mais jogado do mundo é o Minecraft, com mais de 200 milhões de jogadores ativos mensais."
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

    chatInput.addEventListener("keypress", (e) => {
        if (e.key === "Enter") {
            sendButton.click();
        }
    });
});

