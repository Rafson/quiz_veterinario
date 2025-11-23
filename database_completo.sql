-- ========================================
-- SCRIPT COMPLETO DO BANCO DE DADOS
-- Quiz Veterinário
-- Data: 20/11/2025
-- ========================================

-- Criar banco de dados
CREATE DATABASE IF NOT EXISTS QuizVeterinario CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE QuizVeterinario;

-- ========================================
-- TABELA: questoes
-- ========================================
DROP TABLE IF EXISTS questoes;
CREATE TABLE questoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numero INT NOT NULL,
    pergunta TEXT NOT NULL,
    explicacao TEXT,
    opcao_a VARCHAR(255) NOT NULL,
    opcao_b VARCHAR(255) NOT NULL,
    opcao_c VARCHAR(255) NOT NULL,
    opcao_d VARCHAR(255) NOT NULL,
    resposta_correta CHAR(1) NOT NULL,
    UNIQUE KEY (numero)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- TABELA: participantes
-- ========================================
DROP TABLE IF EXISTS participantes;
CREATE TABLE participantes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome_completo VARCHAR(255) NOT NULL,
    pontuacao INT NOT NULL,
    tempo_segundos INT NOT NULL DEFAULT 0,
    data_realizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_ranking (pontuacao DESC, tempo_segundos ASC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- TABELA: respostas
-- ========================================
DROP TABLE IF EXISTS respostas;
CREATE TABLE respostas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    participante_id INT NOT NULL,
    questao_id INT NOT NULL,
    resposta_escolhida CHAR(1) NOT NULL,
    acertou BOOLEAN NOT NULL,
    FOREIGN KEY (participante_id) REFERENCES participantes(id) ON DELETE CASCADE,
    FOREIGN KEY (questao_id) REFERENCES questoes(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- INSERIR AS 25 QUESTÕES
-- ========================================
INSERT INTO questoes (numero, pergunta, explicacao, opcao_a, opcao_b, opcao_c, opcao_d, resposta_correta) VALUES
(1, 'O SNC é composto principalmente por:', NULL, 'Cérebro e nervos periféricos', 'Medula espinhal e nervos cranianos', 'Cérebro e medula espinhal', 'Encéfalo e gânglios nervosos', 'C'),
(2, 'A medula espinhal está localizada dentro:', NULL, 'Do canal vertebral', 'Da cavidade craniana', 'Do tórax', 'Do abdômen', 'A'),
(3, 'O tronco encefálico inclui:', NULL, 'Tálamo, hipotálamo e córtex', 'Mesencéfalo, ponte e bulbo', 'Ponte, cerebelo e córtex', 'Bulbo, cerebelo e gânglios', 'B'),
(4, 'Qual estrutura protege o SNC fisicamente?', NULL, 'Pele', 'Vértebras e crânio', 'Tendões', 'Peritônio', 'B'),
(5, 'As meninges são camadas de proteção do SNC. Qual é a ordem da mais externa para a mais interna?', NULL, 'Pia-máter, duramáter, aracnoide', 'Aracnoide, duramáter, pia-máter', 'Duramáter, aracnoide, pia-máter', 'Duramáter, pia-máter, aracnoide', 'C'),
(6, 'O líquido cefalorraquidiano (LCR) tem função de:', NULL, 'Produzir hormônios', 'Nutrir ossos', 'Proteger, nutrir e amortecer o SNC', 'Lubrificar articulações', 'C'),
(7, 'Qual parte do SNC é essencial para reflexos básicos?', NULL, 'Córtex cerebral', 'Cerebelo', 'Medula espinhal', 'Hipotálamo', 'C'),
(8, 'O hipotálamo controla principalmente:', NULL, 'Audição', 'Temperatura corporal e sede', 'Movimento', 'Memória', 'B'),
(9, 'O bulbo faz parte do:', NULL, 'Telencéfalo', 'Diencéfalo', 'Tronco encefálico', 'Metencéfalo apenas', 'C'),
(10, 'Qual estrutura conecta os hemisférios cerebrais?', NULL, 'Hipotálamo', 'Corpo caloso', 'Tálamo', 'Ponte', 'B'),
(11, 'A função principal da medula espinhal/espinal é:', NULL, 'Produzir hormônios', 'Controlar emoções', 'Transmitir impulsos entre o cérebro e o corpo', 'Filtrar sangue', 'C'),
(12, 'A presença de ataxia proprioceptiva é mais comumente associada a lesões em:', 'A ataxia proprioceptiva é um distúrbio do equilíbrio que resulta de uma perturbação no controle exercido por diferentes sistemas, permitindo monitorar o progresso dos movimentos. Essa condição se manifesta como instabilidade da posição de pé e da marcha, aumentando quando o paciente fecha os olhos ou está no escuro.', 'Sistema vestibular periférico', 'Medula espinhal', 'Sistema digestório', 'Sistema cardiorrespiratório', 'B'),
(13, 'Nistagmo vertical é mais sugestivo de lesão:', 'O nistagmo vertical é um movimento involuntário e rítmico dos olhos na direção vertical, que pode ser classificado em nistagmo vertical para cima ou para baixo, dependendo da direção dos movimentos oculares. Essa condição pode ser causada por distúrbios do sistema vestibular, como a esclerose múltipla e o AVC, ou por intoxicações, doenças do ouvido interno e efeitos colaterais de medicamentos. O diagnóstico e tratamento do nistagmo vertical dependem da identificação da causa subjacente, que pode incluir lesões cerebrais, distúrbios do labirinto e problemas congênitos.', 'Vestibular periférica', 'Vestibular central', 'Cardíaca', 'Renal', 'B'),
(14, 'Head tilt (inclinação da cabeça) é um sinal típico de:', 'O Head Tilt, também conhecido como inclinação de cabeça, é um sintoma preocupante em animais domésticos, indicativo de problemas neurológicos que podem afetar sua qualidade de vida. O diagnóstico envolve uma avaliação detalhada e pode incluir exames de imagem e testes neurológicos. O tratamento depende da causa subjacente e pode incluir antibióticos, cirurgia ou terapias de suporte.', 'Lesão vestibular', 'Lesão medular cervical', 'Lesão cerebelar', 'Hipoglicemia', 'A'),
(15, 'Tetraparesia com reflexos preservados indica lesão em:', 'A tetraparesia em cães com reflexos preservados pode ser causada por várias condições, incluindo doenças autoimunes como a miastenia gravis e a polirradiculoneurite aguda. Essas condições podem resultar em fraqueza muscular generalizada, mas a presença de reflexos segmentares e a sensibilidade superficial e profunda pode indicar que a função motora não está completamente comprometida. O diagnóstico e tratamento adequados são essenciais para a recuperação do animal.', 'Neurônio motor inferior', 'Neurônio motor superior', 'Nervos periféricos', 'Junção neuromuscular', 'B'),
(16, 'Mioclonias são especialmente características de qual doença?', 'Mioclonia é uma contração muscular involuntária que resulta em movimentos bruscos, rápidos e irregulares. Esses movimentos podem ocorrer em um único músculo ou em grupos musculares, e são frequentemente descritos como espasmos ou sacudidelas. A mioclonia pode ser um distúrbio neuromuscular e pode variar em intensidade e duração.', 'Cinomose', 'Raiva paralítica', 'Botulismo', 'Polirradiculoneurite', 'A'),
(17, 'Anisocoria súbita com perda de visão pode indicar lesão em:', 'A anisocoria é a diferença no tamanho das pupilas, e pode ser causada por condições como uveíte anterior, glaucoma, lesões oculares, ou distúrbios neurológicos. Se a anisocoria ocorrer de forma súbita, é uma emergência que deve ser tratada rapidamente para evitar danos permanentes à visão do cão.', 'Retina', 'Nervo óptico ou nervo oculomotor', 'Medula lombar', 'Córtex auditivo', 'B'),
(18, 'Paresia flácida com reflexos ausentes é típica de:', 'A paralisia flácida em cães com reflexos ausentes é uma condição neurológica que resulta na perda de função motora voluntária, levando a músculos fracos e sem tônus. Essa condição pode ser causada por traumas, infecções, doenças autoimunes ou toxinas. Os sintomas incluem fraqueza muscular, perda de reflexos e incapacidade de mover os membros afetados. O tratamento pode envolver medicamentos, cirurgia e reabilitação animal para melhorar a mobilidade e a independência do cão.', 'Lesões de neurônio motor superior', 'Lesões cerebelares', 'Lesões de neurônio motor inferior', 'Lesões vestibulares', 'C'),
(19, 'Dor intensa na coluna ao manipular sugere:', 'A dor intensa na coluna ao manipular um cachorro pode ser um sinal de problemas subjacentes que requerem atenção veterinária. As causas podem incluir hérnia de disco, traumas, artrite, ou outras condições que afetam a coluna vertebral. É crucial observar sinais como dificuldade para se mover, postura anormal, gemidos ou mudanças no comportamento.', 'Problema gastrointestinal', 'Lesão medular ou meningite', 'Doença cardíaca', 'Doença respiratória', 'B'),
(20, 'Um cão que apresenta dificuldade para fechar o olho e paralisia facial tem provável lesão do:', 'A paralisia facial em cães é uma condição comum que pode ser causada por várias razões, incluindo lesões ao nervo facial, inflamações no ouvido interno ou médio, traumas, ou até mesmo tumores. Os sinais incluem dificuldade para fechar os olhos, queda facial, e problemas na mastigação e na percepção visual. A causa mais comum é a paralisia idiopática do nervo facial, que pode ser idiopática ou resultar de inflamações, como otite.', 'Nervo trigêmeo (V)', 'Nervo facial (VII)', 'Nervo hipoglosso (XII)', 'Nervo acessório (XI)', 'B'),
(21, 'A perda de propriocepção consciente indica que há lesão em:', 'A perda de propriocepção consciente em cães pode ser um sinal de problemas neurológicos mais sérios. Essa condição pode resultar em dificuldades para reconhecer a posição e o movimento das partes do corpo, o que pode levar a tropeços, escorregões e quedas. É crucial que qualquer alteração na forma como o cão caminha, tropeça ou apoia as patas seja acompanhada de um diagnóstico veterinário.', 'Vias ascendentes somestésicas', 'Vias motoras periféricas', 'Músculo esquelético', 'Sistema endócrino', 'A'),
(22, 'Posições anormais da cabeça (decorticação ou descerebração) indicam lesão grave em:', 'A decorticação é caracterizada por uma postura anormal onde a pessoa apresenta os braços dobrados, os punhos cerrados e as pernas esticadas, enquanto a descerebração implica na manutenção dos braços e das pernas esticados, com os dedos dos pés apontando para baixo e a cabeça e o pescoço esticados para trás. Ambas as condições são sinais de danos no trajeto dos nervos entre o cérebro e a medula espinal, e podem ser causadas por hemorragia intracraniana, tumor cerebral, acidente vascular cerebral, entre outras.', 'Medula lombar', 'Tronco encefálico', 'Cerebelo', 'Sistema vestibular', 'B'),
(23, 'Um cão com disfonia (alteração de latido) pode ter lesão no nervo:', 'A disfonia em cães é uma condição que pode ser causada por várias razões, incluindo problemas de saúde, traumas emocionais ou até mesmo condições genéticas. Os sintomas incluem alterações na voz do cão, como rouquidão ou perda total da voz, e podem ser acompanhados de outros sinais de disfonia. É importante observar se a disfonia está acompanhada de outros sintomas ou se é algo isolado, e consultar um veterinário para um diagnóstico e tratamento adequados.', 'Vago (X)', 'Olfatório (I)', 'Trigêmeo (V)', 'Acessório (XI)', 'A'),
(24, 'Estrabismo ventrolateral está associado a paralisia do nervo:', 'O estrabismo ventrolateral em cães é caracterizado pelo desvio dos olhos para a parte inferior do rosto, como se o cão estivesse olhando para o chão. Essa condição pode ser causada por problemas neurológicos, traumas ou doenças degenerativas.', 'Abducente (VI)', 'Óptico (II)', 'Troclear (IV)', 'Oculomotor (III)', 'D'),
(25, 'Um cão que pressiona a cabeça contra a parede ("head pressing") tem forte indicação de:', 'O head pressing em cães é um comportamento compulsivo onde o animal pressiona a cabeça contra uma parede ou objeto sem um motivo aparente. Este comportamento pode ser um sinal de que o cão está sofrendo com danos no sistema nervoso e deve ser tratado com urgência. As causas podem variar, incluindo problemas neurológicos, intoxicações, traumatismos cranianos, tumores, hidrocefalia, deficiências nutricionais e doenças infecciosas.', 'Doença hepática – encefalopatia', 'Doença pulmonar', 'Doença renal', 'Dermatite', 'A');

-- ========================================
-- VERIFICAÇÕES
-- ========================================

-- Verificar questões inseridas
SELECT COUNT(*) AS total_questoes FROM questoes;

-- Verificar estrutura das tabelas
SHOW TABLES;

-- Verificar colunas da tabela participantes
SHOW COLUMNS FROM participantes;

-- ========================================
-- FIM DO SCRIPT
-- ========================================
