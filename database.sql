-- Criação do banco de dados
CREATE DATABASE IF NOT EXISTS QuizVeterinario CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE QuizVeterinario;

-- Tabela de questões
CREATE TABLE IF NOT EXISTS questoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numero INT NOT NULL,
    pergunta TEXT NOT NULL,
    opcao_a VARCHAR(255) NOT NULL,
    opcao_b VARCHAR(255) NOT NULL,
    opcao_c VARCHAR(255) NOT NULL,
    opcao_d VARCHAR(255) NOT NULL,
    resposta_correta CHAR(1) NOT NULL,
    UNIQUE KEY (numero)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de participantes
CREATE TABLE IF NOT EXISTS participantes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome_completo VARCHAR(255) NOT NULL,
    pontuacao INT NOT NULL,
    data_realizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_pontuacao (pontuacao DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de respostas
CREATE TABLE IF NOT EXISTS respostas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    participante_id INT NOT NULL,
    questao_id INT NOT NULL,
    resposta_escolhida CHAR(1) NOT NULL,
    acertou BOOLEAN NOT NULL,
    FOREIGN KEY (participante_id) REFERENCES participantes(id) ON DELETE CASCADE,
    FOREIGN KEY (questao_id) REFERENCES questoes(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Inserir as 20 questões
INSERT INTO questoes (numero, pergunta, opcao_a, opcao_b, opcao_c, opcao_d, resposta_correta) VALUES
(1, 'A cinomose é causada por:', 'Bactéria Bordetella bronchiseptica', 'Vírus Morbillivirus', 'Protozoário Neospora caninum', 'Fungos dermatofíticos', 'B'),
(2, 'Sinal neurológico clássico da cinomose em fase tardia:', 'Paralisia laríngea', 'Mioclonias', 'Convulsão tônica isolada', 'Ataxia vestibular pura', 'B'),
(3, 'Uma forma comum de transmissão da cinomose é:', 'Picada de mosquito', 'Contato com secreções respiratórias de animais infectados', 'Leite materno contaminado', 'Parasitas externos', 'B'),
(4, 'Sobre a vacinação contra cinomose em cães, é correto afirmar que:', 'Apenas uma dose é suficiente para imunidade vitalícia', 'O protocolo começa geralmente entre 6–8 semanas', 'Não há necessidade de reforço anual', 'Deve ser aplicada somente em cães adultos', 'B'),
(5, 'Reações vacinais mais comuns incluem:', 'Anafilaxia sempre', 'Nódulo no local e leve febre', 'Necrose grave no local da aplicação', 'Convulsões obrigatórias', 'B'),
(6, 'Uma reação vacinal grave e imediata é:', 'Sonolência', 'Hiporexia', 'Edema facial e vômito súbito', 'Diarreia leve no dia seguinte', 'C'),
(7, 'Lesões medulares toracolombares frequentemente causam:', 'Tetraparesia', 'Paraparesia ou paraplegia', 'Ataxia vestibular', 'Polidipsia', 'B'),
(8, 'Na lesão medular, o reflexo patelar hiperativo indica:', 'Lesão do neurônio motor superior', 'Lesão do neurônio motor inferior', 'Normalidade', 'Desidratação', 'A'),
(9, 'Raiva é transmitida principalmente por:', 'Contato com fezes contaminadas', 'Mordedura de animal infectado', 'Fômites', 'Água contaminada', 'B'),
(10, 'Principal reservatório urbano da raiva no Brasil:', 'Gatos', 'Morcegos', 'Cavalos', 'Galinhas', 'B'),
(11, 'A raiva afeta principalmente:', 'Trato gastrointestinal', 'Sistema respiratório', 'Sistema nervoso central', 'Sistema urinário', 'C'),
(12, 'Sinal típico da raiva na fase furiosa:', 'Ataxia leve', 'Paralisia facial', 'Agressividade e hiperexcitabilidade', 'Hiporexia persistente', 'C'),
(13, 'Encefalite pode ser causada por:', 'Deficiência de cálcio', 'Vírus, bactérias, fungos ou parasitas', 'Fraturas de tíbia', 'Displasia coxofemoral', 'B'),
(14, 'Um sinal clínico comum da encefalite é:', 'Poliúria', 'Convulsões', 'Alopecia localizada', 'Tártaro dentário', 'B'),
(15, 'Para diferenciar lesão medular de encefalite, é útil avaliar:', 'Dermatite', 'Força motora, reflexos e propriocepção', 'Peso corporal', 'Cor do pelo', 'B'),
(16, 'Em suspeita de raiva, o procedimento correto é:', 'Tentar conter o animal e medicá-lo', 'Manipular sem EPIs', 'Notificar a vigilância sanitária imediatamente', 'Dar banho para remover o vírus', 'C'),
(17, 'Na cinomose, o sistema que mais sofre nos casos graves é:', 'Endócrino', 'Digestório', 'Respiratório e neurológico', 'Urinário', 'C'),
(18, 'Sobre reações vacinais, é correto afirmar:', 'Cães pequenos nunca apresentam reação', 'Reações graves são raras, porém possíveis', 'É normal convulsionar após toda vacina', 'O cão deve ser sedado antes da vacinação', 'B'),
(19, 'Lesão medular cervical pode causar:', 'Tetraparesia', 'Paralisia apenas dos membros posteriores', 'Falta de apetite isolada', 'Tosse seca', 'A'),
(20, 'Encefalite viral em cães pode surgir como complicação de:', 'Cinomose', 'Giardíase', 'Dermatite alérgica', 'Otite externa leve', 'A');
