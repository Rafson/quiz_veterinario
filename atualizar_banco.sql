-- Adicionar coluna tempo_segundos na tabela participantes
ALTER TABLE participantes ADD COLUMN tempo_segundos INT NOT NULL DEFAULT 0 AFTER pontuacao;

-- Atualizar índice para ordenar por pontuação e tempo
DROP INDEX idx_pontuacao ON participantes;
CREATE INDEX idx_ranking ON participantes(pontuacao DESC, tempo_segundos ASC);
