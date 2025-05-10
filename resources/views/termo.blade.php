<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <title>Termo Clone</title>
    <style>
        :root {
            /* Tema Claro (padrão) */
            --cor-fundo: #f4f4f4;
            --cor-texto: #333;
            --cor-borda: #ccc;
            --cor-celula-vazia: #fff;
            --cor-celula-preenchida-borda: #aaa;
            --cor-correta: #6aaa64;
            --cor-presente: #c9b458;
            --cor-ausente: #787c7e;
            --cor-tecla-fundo: #d3d6da;
            --cor-texto-feedback: #fff;
            --cor-selecionada: #007bff;
            --cor-botao: #007bff;
            --cor-botao-hover: #0056b3;
            --tamanho-celula: 60px;
            --margem-celula: 5px;
            --tempo-transicao: 0.2s;
            --tempo-animacao-flip: 0.6s;
            --tempo-animacao-shake: 0.5s;
        }

        [data-theme="dark"] {
            --cor-fundo: #121213;
            --cor-texto: #fff;
            --cor-borda: #3a3a3c;
            --cor-celula-vazia: #121213;
            --cor-celula-preenchida-borda: #565758;
            --cor-correta: #538d4e;
            --cor-presente: #b59f3b;
            --cor-ausente: #3a3a3c;
            --cor-tecla-fundo: #818384;
            --cor-texto-feedback: #fff;
            --cor-selecionada: #4a9eff;
            --cor-botao: #4a9eff;
            --cor-botao-hover: #2d7fd9;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: var(--cor-fundo);
            color: var(--cor-texto);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
            min-height: 100vh;
            margin: 0;
            padding: 10px;
            box-sizing: border-box;
        }

        .termo-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
            max-width: 500px;
            /* Limita a largura máxima */
            flex-grow: 1;
        }

        h1 {
            margin-top: 0;
            margin-bottom: 20px;
            font-size: 2rem;
            text-align: center;
        }

        .message-area {
            height: 30px;
            margin-bottom: 15px;
            font-weight: bold;
            text-align: center;
            color: var(--cor-texto);
            visibility: hidden;
            /* Começa escondida */
            opacity: 0;
            transition: opacity var(--tempo-transicao) ease;
        }

        .message-area.show {
            visibility: visible;
            opacity: 1;
        }

        .grid-container {
            display: grid;
            grid-template-rows: repeat(6, var(--tamanho-celula));
            gap: var(--margem-celula);
            margin-bottom: 30px;
            width: calc(5 * (var(--tamanho-celula) + var(--margem-celula)) - var(--margem-celula));
            /* Largura exata para 5 células */
            max-width: 100%;
            /* Garante que não ultrapasse a tela */
        }

        .grid-row {
            display: grid;
            grid-template-columns: repeat(5, var(--tamanho-celula));
            gap: var(--margem-celula);
        }

        .grid-cell {
            width: var(--tamanho-celula);
            height: var(--tamanho-celula);
            border: 2px solid var(--cor-borda);
            background-color: var(--cor-celula-vazia);
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 2rem;
            font-weight: bold;
            text-transform: uppercase;
            transition: border-color var(--tempo-transicao) ease;
            perspective: 1000px;
            /* Para animação 3D */
        }

        .grid-cell.filled {
            border-color: var(--cor-celula-preenchida-borda);
        }

        /* Animação de Flip ao revelar */
        .grid-cell .cell-inner {
            position: relative;
            width: 100%;
            height: 100%;
            text-align: center;
            transition: transform var(--tempo-animacao-flip) ease;
            transform-style: preserve-3d;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .grid-cell.flip .cell-inner {
            transform: rotateX(180deg);
        }

        .grid-cell .cell-front,
        .grid-cell .cell-back {
            position: absolute;
            width: 100%;
            height: 100%;
            -webkit-backface-visibility: hidden;
            /* Safari */
            backface-visibility: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
        }

        .grid-cell .cell-front {
            /* Conteúdo visível antes do flip (letra digitada) */
            background-color: var(--cor-celula-vazia);
            /* Garante que não tenha fundo */
            color: var(--cor-texto);
        }

        .grid-cell .cell-back {
            /* Conteúdo visível após o flip (cor de feedback) */
            transform: rotateX(180deg);
            color: var(--cor-texto-feedback);
            /* Texto branco no feedback */
        }

        /* Cores de Feedback */
        .grid-cell.correct .cell-back {
            background-color: var(--cor-correta);
            border-color: var(--cor-correta);
        }

        .grid-cell.present .cell-back {
            background-color: var(--cor-presente);
            border-color: var(--cor-presente);
        }

        .grid-cell.absent .cell-back {
            background-color: var(--cor-ausente);
            border-color: var(--cor-ausente);
        }

        /* Remove a borda externa quando a célula tem cor de feedback */
        .grid-cell.correct,
        .grid-cell.present,
        .grid-cell.absent {
            border-color: transparent;
        }


        /* Animação Shake para Linha Inválida */
        @keyframes shake {

            0%,
            100% {
                transform: translateX(0);
            }

            10%,
            30%,
            50%,
            70%,
            90% {
                transform: translateX(-5px);
            }

            20%,
            40%,
            60%,
            80% {
                transform: translateX(5px);
            }
        }

        .grid-row.shake {
            animation: shake var(--tempo-animacao-shake) ease-in-out;
        }


        .keyboard-container {
            width: 100%;
            max-width: 500px;
            /* Mesma largura máx do grid */
            margin-top: auto;
            /* Empurra para baixo */
        }

        .keyboard-row {
            display: flex;
            justify-content: center;
            margin-bottom: 8px;
        }

        .key {
            font-family: inherit;
            font-weight: bold;
            font-size: 0.9rem;
            border: none;
            padding: 0;
            margin: 0 3px;
            height: 50px;
            border-radius: 4px;
            cursor: pointer;
            background-color: var(--cor-tecla-fundo);
            color: var(--cor-texto);
            display: flex;
            justify-content: center;
            align-items: center;
            text-transform: uppercase;
            min-width: 30px;
            /* Largura mínima */
            flex-grow: 1;
            /* Ocupa espaço igualmente */
            transition: background-color var(--tempo-transicao) ease, color var(--tempo-transicao) ease;
        }

        .key:hover {
            filter: brightness(0.9);
        }

        .key.wide {
            flex-grow: 1.5;
            /* Teclas Enter/Bksp maiores */
            font-size: 0.8rem;
        }

        /* Cores do Teclado */
        .key.correct {
            background-color: var(--cor-correta);
            color: var(--cor-texto-feedback);
        }

        .key.present {
            background-color: var(--cor-presente);
            color: var(--cor-texto-feedback);
        }

        .key.absent {
            background-color: var(--cor-ausente);
            color: var(--cor-texto-feedback);
        }


        .new-game-button {
            margin-top: 20px;
            padding: 10px 20px;
            font-size: 1rem;
            cursor: pointer;
        }

        /* Responsividade básica */
        @media (max-width: 400px) {
            :root {
                --tamanho-celula: 50px;
                /* Diminui células em telas pequenas */
                --margem-celula: 3px;
            }

            .key {
                height: 45px;
                min-width: 25px;
                margin: 0 2px;
            }

            h1 {
                font-size: 1.8rem;
            }

            .grid-cell {
                font-size: 1.8rem;
            }
        }

        /* Adiciona estilos para a célula selecionada */
        .grid-cell.selected {
            border-color: var(--cor-selecionada);
            border-width: 3px;
        }

        .theme-toggle {
            position: fixed;
            top: 20px;
            right: 20px;
            background: transparent;
            color: var(--cor-texto);
            border: 2px solid var(--cor-texto);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            transition: all 0.3s ease;
        }

        .theme-toggle:hover {
            background: var(--cor-texto);
            color: var(--cor-fundo);
        }

        /* Estilo do Loader */
        .loader-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .loader {
            width: 50px;
            height: 50px;
            border: 5px solid var(--cor-fundo);
            border-top: 5px solid var(--cor-botao);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .loader-container.show {
            display: flex;
        }
    </style>
</head>

<body>
    <div class="loader-container" id="loader-container">
        <div class="loader"></div>
    </div>

    <div class="termo-container">
        <button class="theme-toggle" id="theme-toggle" aria-label="Alternar tema">
            <i class="fas fa-moon"></i>
        </button>
        <h1>Termo Clone</h1>

        <div class="message-area" id="message-area">Mensagem aqui</div>

        <div class="grid-container" id="grid-container">
        </div>

        <div class="keyboard-container" id="keyboard-container">
        </div>

        <button class="new-game-button" id="new-game-btn" style="display: none;">Novo Jogo</button>
    </div>


    <script>
        document.addEventListener('DOMContentLoaded', async () => {

            // --- Configurações & Estado do Jogo ---
            const NUM_TENTATIVAS = 6;
            const TAMANHO_PALAVRA = 5;
            let palavrasUsadas = new Set(); // Histórico de palavras já usadas
            let palavraSecreta = '';
            let tentativaAtual = 0;
            let letraAtual = 1;
            let gridState = [];
            let jogoTerminou = false;

            // --- Elementos do DOM ---
            const gridContainer = document.getElementById('grid-container');
            const keyboardContainer = document.getElementById('keyboard-container');
            const messageArea = document.getElementById('message-area');
            const newGameBtn = document.getElementById('new-game-btn');

            // --- Funções Principais ---
            function normalizarTexto(texto) {
                return texto.toLowerCase()
                    .normalize('NFD')
                    .replace(/[\u0300-\u036f]/g, '') // Remove acentos
                    .replace(/ç/g, 'c'); // Substitui ç por c
            }

            function mostrarLoader(mostrar) {
                const loaderContainer = document.getElementById('loader-container');
                if (mostrar) {
                    loaderContainer.classList.add('show');
                } else {
                    loaderContainer.classList.remove('show');
                }
            }

            async function buscarPalavraRandom() {
                try {
                    mostrarLoader(true);
                    const response = await fetch('http://localhost:8000/api/palavra-aleatoria');
                    const data = await response.json();
                    return normalizarTexto(data.data.palavra);
                } catch (error) {
                    console.error('Erro ao buscar palavra random:', error);
                    throw error;
                } finally {
                    mostrarLoader(false);
                }
            }

            async function buscarNovaPalavra() {
                let tentativas = 0;
                const maxTentativas = 50; // Limite de tentativas para evitar loop infinito

                while (tentativas < maxTentativas) {
                    const palavra = await buscarPalavraRandom();
                    if (palavra.length === 5 && !palavrasUsadas.has(palavra)) {
                        palavrasUsadas.add(palavra);
                        return palavra;
                    }
                    tentativas++;
                }

                throw new Error('Não foi possível encontrar uma nova palavra válida');
            }

            async function iniciarJogo() {
                try {
                    palavraSecreta = await buscarNovaPalavra();
                    console.log("Palavra Secreta (Debug):", palavraSecreta);
                    tentativaAtual = 0;
                    letraAtual = 0;
                    jogoTerminou = false;
                    gridState = Array(NUM_TENTATIVAS).fill(null).map(() => Array(TAMANHO_PALAVRA).fill(''));

                    criarGrid();
                    criarTeclado();
                    limparMensagem();
                    newGameBtn.style.display = 'none';

                    // Seleciona a primeira célula ao iniciar
                    atualizarCelulaSelecionada();
                } catch (error) {
                    console.error('Erro ao iniciar jogo:', error);
                    mostrarMensagem("Erro ao iniciar jogo. Tente novamente.", false);
                    newGameBtn.style.display = 'block';
                }
            }

            function criarGrid() {
                gridContainer.innerHTML = ''; // Limpa grid anterior
                for (let i = 0; i < NUM_TENTATIVAS; i++) {
                    const row = document.createElement('div');
                    row.classList.add('grid-row');
                    row.setAttribute('id', `row-${i}`);
                    for (let j = 0; j < TAMANHO_PALAVRA; j++) {
                        const cell = document.createElement('div');
                        cell.classList.add('grid-cell');
                        cell.setAttribute('id', `cell-${i}-${j}`);
                        // Estrutura interna para animação de flip
                        cell.innerHTML = `
                            <div class="cell-inner">
                                <div class="cell-front"></div>
                                <div class="cell-back"></div>
                            </div>`;

                        // Adiciona evento de clique na célula
                        cell.addEventListener('click', () => {
                            if (i === tentativaAtual && !jogoTerminou) {
                                letraAtual = j; // Atualiza a posição atual para a célula clicada
                                // Destaca a célula selecionada
                                document.querySelectorAll('.grid-cell').forEach(c => c.classList.remove(
                                    'selected'));
                                cell.classList.add('selected');
                            }
                        });

                        row.appendChild(cell);
                    }
                    gridContainer.appendChild(row);
                }
            }

            function criarTeclado() {
                keyboardContainer.innerHTML = ''; // Limpa teclado anterior
                const layoutTeclado = [
                    "QWERTYUIOP",
                    "ASDFGHJKL",
                    "ENTERZXCVBNMBKSP" // BKSP para Backspace
                ];

                layoutTeclado.forEach(linha => {
                    const rowDiv = document.createElement('div');
                    rowDiv.classList.add('keyboard-row');

                    // Processa cada caractere da linha
                    let i = 0;
                    while (i < linha.length) {
                        const char = linha[i];
                        const keyBtn = document.createElement('button');
                        keyBtn.classList.add('key');
                        let keyVal = char;
                        let displayVal = char;

                        // Verifica se é uma tecla especial
                        if (char === 'E' && linha.substring(i, i + 5) === 'ENTER') {
                            keyVal = 'ENTER';
                            displayVal = 'Enter';
                            keyBtn.classList.add('wide');
                            i += 5; // Pula os próximos 4 caracteres
                        } else if (char === 'B' && linha.substring(i, i + 4) === 'BKSP') {
                            keyVal = 'BACKSPACE';
                            displayVal = '⌫';
                            keyBtn.classList.add('wide');
                            i += 4; // Pula os próximos 3 caracteres
                        } else {
                            i++; // Avança apenas 1 caractere
                        }

                        keyBtn.textContent = displayVal;
                        keyBtn.dataset.key = keyVal;

                        // Adiciona listener ao botão
                        keyBtn.addEventListener('click', () => processarInput(keyVal));

                        rowDiv.appendChild(keyBtn);
                    }
                    keyboardContainer.appendChild(rowDiv);
                });
            }

            async function processarInput(key) {
                if (jogoTerminou) return;

                key = key.toUpperCase(); // Garante caixa alta

                if (key === 'BACKSPACE') {
                    apagarLetra();
                } else if (key === 'ENTER') {
                    await submeterTentativa();
                } else if (key === 'ARROWLEFT') {
                    moverSelecao(-1); // Move para esquerda
                } else if (key === 'ARROWRIGHT') {
                    moverSelecao(1); // Move para direita
                } else if (key === 'ARROWUP') {
                    moverSelecaoVertical(-1); // Move para cima
                } else if (key === 'ARROWDOWN') {
                    moverSelecaoVertical(1); // Move para baixo
                } else if (key.length === 1 && key >= 'A' && key <= 'Z') {
                    adicionarLetra(key);
                }
            }

            function adicionarLetra(letra) {
                if (tentativaAtual < NUM_TENTATIVAS && !jogoTerminou) {
                    // Verifica se a posição atual é válida
                    if (letraAtual < TAMANHO_PALAVRA) {
                        gridState[tentativaAtual][letraAtual] = letra;
                        const cell = obterCelula(tentativaAtual, letraAtual);
                        cell.querySelector('.cell-front').textContent = letra;
                        cell.classList.add('filled');

                        // Move para a próxima célula vazia ou para o final
                        letraAtual++;
                        while (letraAtual < TAMANHO_PALAVRA && gridState[tentativaAtual][letraAtual] !== '') {
                            letraAtual++;
                        }

                        // Atualiza a célula selecionada
                        atualizarCelulaSelecionada();
                    }
                }
            }

            function apagarLetra() {
                const cell = obterCelula(tentativaAtual, letraAtual);
                if (cell) {
                    gridState[tentativaAtual][letraAtual] = '';
                    const front = cell.querySelector('.cell-front');
                    if (front) {
                        front.textContent = '';
                    }
                    cell.classList.remove('filled');
                    letraAtual--;
                    if (letraAtual < 0) {
                        letraAtual = 0;
                    }
                    // Atualiza a célula selecionada
                    atualizarCelulaSelecionada();
                }
            }

            function atualizarCelulaSelecionada() {
                // Remove a seleção de todas as células
                document.querySelectorAll('.grid-cell').forEach(c => c.classList.remove('selected'));
                // Se a posição atual é válida, seleciona a célula
                if (letraAtual < TAMANHO_PALAVRA) {
                    const cell = obterCelula(tentativaAtual, letraAtual);
                    cell?.classList.add('selected');
                } else if (letraAtual === TAMANHO_PALAVRA) {
                    const cell = obterCelula(tentativaAtual, TAMANHO_PALAVRA - 1);
                    cell?.classList.add('selected');
                    letraAtual = TAMANHO_PALAVRA - 1;
                }
            }

            async function verificarPalavra(palavra) {
                try {
                    mostrarLoader(true);
                    const palavraNormalizada = normalizarTexto(palavra);
                    const response = await fetch(
                        `http://localhost:8000/api/verificar-palavra?palavra=${palavraNormalizada}`);
                    const data = await response.json();
                    if (!response.ok) {
                        console.error('Erro ao verificar palavra:', response.status, data.message);
                        return false;
                    }
                    return data.success;
                } catch (error) {
                    console.error('Erro ao verificar palavra:', error);
                    return false;
                } finally {
                    mostrarLoader(false);
                }
            }

            async function submeterTentativa() {
                // Verifica se todas as células estão preenchidas
                const todasPreenchidas = gridState[tentativaAtual].every(letra => letra !== '');
                if (!todasPreenchidas) {
                    mostrarMensagem("Letras insuficientes!");
                    agitarLinha(tentativaAtual);
                    return;
                }

                const palavraTentada = gridState[tentativaAtual].join('');
                const palavraNormalizada = normalizarTexto(palavraTentada);

                // Verifica se a palavra existe
                const palavraExiste = await verificarPalavra(palavraTentada);
                if (!palavraExiste) {
                    mostrarMensagem("Palavra não existe no dicionário!");
                    agitarLinha(tentativaAtual);
                    return;
                }

                processarResultadoTentativa(palavraTentada);
            }

            function processarResultadoTentativa(palavraTentada) {
                const palavraTentadaNormalizada = normalizarTexto(palavraTentada);
                const palavraSecretaNormalizada = normalizarTexto(palavraSecreta);
                const feedback = calcularFeedback(palavraTentadaNormalizada, palavraSecretaNormalizada);
                aplicarFeedbackVisual(tentativaAtual, feedback);

                // Verifica vitória
                if (palavraTentadaNormalizada === palavraSecretaNormalizada) {
                    mostrarMensagem("Parabéns, você acertou!", true);
                    jogoTerminou = true;
                    newGameBtn.style.display = 'block';
                    return;
                }

                // Próxima tentativa
                tentativaAtual++;
                letraAtual = 0;

                // Atualiza a célula selecionada para a nova linha
                atualizarCelulaSelecionada();

                // Verifica derrota
                if (tentativaAtual === NUM_TENTATIVAS) {
                    mostrarMensagem(`Você perdeu! A palavra era: ${palavraSecreta}`, false);
                    jogoTerminou = true;
                    newGameBtn.style.display = 'block';
                }
            }

            function calcularFeedback(tentativa, secreta) {
                const feedback = Array(TAMANHO_PALAVRA).fill('absent'); // Começa tudo como ausente
                const letraTentada = tentativa.split('');
                const letraSecreta = secreta.split('');
                const contagemSecreta = {}; // Para lidar com letras repetidas

                // Conta ocorrências na palavra secreta
                for (const letra of letraSecreta) {
                    contagemSecreta[letra] = (contagemSecreta[letra] || 0) + 1;
                }

                // 1ª Passada: Marcar corretas (verde)
                for (let i = 0; i < TAMANHO_PALAVRA; i++) {
                    if (letraTentada[i] === letraSecreta[i]) {
                        feedback[i] = 'correct';
                        contagemSecreta[letraTentada[i]]--; // Decrementa contagem
                    }
                }

                // 2ª Passada: Marcar presentes (amarelo)
                for (let i = 0; i < TAMANHO_PALAVRA; i++) {
                    // Só processa se não for 'correct'
                    if (feedback[i] !== 'correct') {
                        // Verifica se a letra existe na palavra secreta E se ainda há ocorrências dela não usadas
                        if (letraSecreta.includes(letraTentada[i]) && contagemSecreta[letraTentada[i]] > 0) {
                            feedback[i] = 'present';
                            contagemSecreta[letraTentada[i]]--; // Decrementa contagem
                        }
                    }
                }
                return feedback; // Array com 'correct', 'present', 'absent'
            }


            function aplicarFeedbackVisual(numLinha, feedback) {
                const linha = document.getElementById(`row-${numLinha}`);
                const celulas = linha.querySelectorAll('.grid-cell');
                const teclado = keyboardContainer.querySelectorAll('.key');

                feedback.forEach((status, index) => {
                    const cell = celulas[index];
                    const letra = gridState[numLinha][index]; // Pega a letra digitada

                    // Atraso para animação de flip sequencial
                    setTimeout(() => {
                        cell.classList.add('flip'); // Inicia a animação
                        // A cor é aplicada no .cell-back via CSS quando a classe de status é adicionada
                        cell.classList.add(status);

                        // Atualiza a letra no .cell-back (que ficará visível após o flip)
                        cell.querySelector('.cell-back').textContent = letra;

                        // Atualiza Teclado
                        atualizarCorTeclado(letra, status);

                    }, index * 300); // Atraso de 300ms entre cada célula
                });
            }

            function atualizarCorTeclado(letra, status) {
                const key = keyboardContainer.querySelector(`.key[data-key="${letra}"]`);
                if (!key) return;

                const statusPrioridade = {
                    correct: 3,
                    present: 2,
                    absent: 1
                };
                const statusAtualKey = key.classList.contains('correct') ? 'correct' :
                    key.classList.contains('present') ? 'present' :
                    key.classList.contains('absent') ? 'absent' :
                    ''; // Nenhum status ainda

                // Só atualiza se o novo status for mais importante ou se não houver status
                if (!statusAtualKey || statusPrioridade[status] > statusPrioridade[statusAtualKey]) {
                    // Remove status antigos antes de adicionar o novo
                    key.classList.remove('correct', 'present', 'absent');
                    key.classList.add(status);
                }
            }

            function obterCelula(linha, coluna) {
                return document.getElementById(`cell-${linha}-${coluna}`);
            }

            function mostrarMensagem(msg, sucesso = null) {
                messageArea.textContent = msg;
                messageArea.classList.add('show'); // Torna visível

                // Muda a cor da mensagem opcionalmente
                if (sucesso === true) {
                    messageArea.style.color = 'green';
                } else if (sucesso === false) {
                    messageArea.style.color = 'red';
                } else {
                    messageArea.style.color = 'var(--cor-texto)'; // Cor padrão
                }

                // Esconde a mensagem após alguns segundos se não for fim de jogo
                if (!jogoTerminou && sucesso === null) {
                    setTimeout(limparMensagem, 2000);
                }
            }

            function limparMensagem() {
                messageArea.textContent = '';
                messageArea.classList.remove('show');
                messageArea.style.color = 'var(--cor-texto)'; // Reseta cor
            }

            function agitarLinha(numLinha) {
                const linha = document.getElementById(`row-${numLinha}`);
                linha.classList.add('shake');
                // Remove a classe após a animação terminar
                setTimeout(() => {
                    linha.classList.remove('shake');
                }, 500); // Duração da animação de shake
            }

            function moverSelecao(direcao) {
                const novaPosicao = letraAtual + direcao;
                if (novaPosicao >= 0 && novaPosicao < TAMANHO_PALAVRA) {
                    letraAtual = novaPosicao;
                    atualizarCelulaSelecionada();
                }
            }

            function moverSelecaoVertical(direcao) {
                const novaTentativa = tentativaAtual + direcao;
                if (novaTentativa >= 0 && novaTentativa < NUM_TENTATIVAS) {
                    // Só permite mover para linhas que já foram preenchidas ou a linha atual
                    const linhaAtual = gridState[tentativaAtual];
                    const linhaDestino = gridState[novaTentativa];

                    // Verifica se a linha de destino está preenchida ou é a próxima linha vazia
                    const linhaDestinoPreenchida = linhaDestino.some(letra => letra !== '');
                    const ehProximaLinhaVazia = novaTentativa === tentativaAtual + 1 && !linhaDestinoPreenchida;

                    if (linhaDestinoPreenchida || ehProximaLinhaVazia) {
                        tentativaAtual = novaTentativa;
                        // Mantém a mesma posição horizontal
                        atualizarCelulaSelecionada();
                    }
                }
            }

            // --- Event Listeners ---
            // Listener para teclado físico
            document.addEventListener('keydown', async (event) => {
                if (event.target !== document.body) return;

                const key = event.key.toUpperCase();
                if (key === 'ARROWLEFT' || key === 'ARROWRIGHT' ||
                    key === 'ARROWUP' || key === 'ARROWDOWN') {
                    event.preventDefault(); // Previne o scroll da página
                }
                await processarInput(key);
            });

            // Listener para o botão de Novo Jogo
            newGameBtn.addEventListener('click', iniciarJogo);

            // Adiciona funcionalidade de tema
            const themeToggle = document.getElementById('theme-toggle');
            const prefersDarkScheme = window.matchMedia('(prefers-color-scheme: dark)');

            // Verifica se há um tema salvo no localStorage
            const savedTheme = localStorage.getItem('termo-theme');
            if (savedTheme) {
                document.documentElement.setAttribute('data-theme', savedTheme);
                updateThemeButton(savedTheme);
            } else if (prefersDarkScheme.matches) {
                document.documentElement.setAttribute('data-theme', 'dark');
                updateThemeButton('dark');
            }

            themeToggle.addEventListener('click', () => {
                const currentTheme = document.documentElement.getAttribute('data-theme');
                const newTheme = currentTheme === 'dark' ? 'light' : 'dark';

                document.documentElement.setAttribute('data-theme', newTheme);
                localStorage.setItem('termo-theme', newTheme);
                updateThemeButton(newTheme);
            });

            function updateThemeButton(theme) {
                const icon = themeToggle.querySelector('i');
                icon.className = theme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
            }

            // --- Inicialização ---
            await iniciarJogo();

        });
    </script>

</body>

</html>
